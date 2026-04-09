<?php
session_start();
include('server/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['user_id'])) {
        header("Location: login.php?error=" . urlencode("Please login first"));
        exit();
    }

    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        header("Location: account.php?error=" . urlencode("Invalid request token"));
        exit();
    }

    $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
    $user_phone = trim($_POST['user_phone'] ?? '');
    $user_city = trim($_POST['user_city'] ?? '');
    $user_address = trim($_POST['user_address'] ?? '');
    $payment_method = $_POST['payment_method'] ?? '';
    $user_id = (int)$_SESSION['user_id'];

    $allowed_methods = ['easypaisa', 'jazzcash', 'bank', 'cod'];
    if ($order_id <= 0 || !in_array($payment_method, $allowed_methods, true)) {
        header("Location: account.php?error=" . urlencode("Invalid order/payment request"));
        exit();
    }

    // Verify order belongs to logged-in user and is unpaid
    $checkStmt = $conn->prepare("SELECT order_id FROM orders WHERE order_id = ? AND user_id = ? AND order_status = 'not paid' LIMIT 1");
    $checkStmt->bind_param("ii", $order_id, $user_id);
    $checkStmt->execute();
    $orderExists = $checkStmt->get_result()->fetch_assoc();
    $checkStmt->close();

    if (!$orderExists) {
        header("Location: account.php?error=" . urlencode("Order not found or already paid"));
        exit();
    }

    // Update order with latest contact details and pending status
    $stmt = $conn->prepare("UPDATE orders SET order_status = 'pending', user_phone = ?, user_city = ?, user_address = ? WHERE order_id = ? AND user_id = ?");
    $stmt->bind_param("sssii", $user_phone, $user_city, $user_address, $order_id, $user_id);
    $stmt->execute();
    $stmt->close();

    // Prepare payment info
    $mobile_no = NULL; $pin = NULL; $bank_name = NULL; $account_no = NULL; $reference = NULL;

    switch($payment_method) {
        case 'easypaisa':
            $mobile_no = trim($_POST['easypaisa_number'] ?? '');
            break;
        case 'jazzcash':
            $mobile_no = trim($_POST['jazzcash_number'] ?? '');
            break;
        case 'bank':
            $bank_name = trim($_POST['bank_name'] ?? '');
            $account_no = trim($_POST['bank_account'] ?? '');
            break;
        case 'cod':
            break;
    }

    // Replace old payment record for this order (idempotent behavior)
    $delStmt = $conn->prepare("DELETE FROM payments WHERE order_id = ?");
    $delStmt->bind_param("i", $order_id);
    $delStmt->execute();
    $delStmt->close();

    // Insert into payment table in data base
    $stmt2 = $conn->prepare("INSERT INTO payments (order_id, payment_method, mobile_no, pin, bank_name, account_no, reference, payment_date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt2->bind_param("issssss", $order_id, $payment_method, $mobile_no, $pin, $bank_name, $account_no, $reference);
    $stmt2->execute();
    $stmt2->close();

    header("Location: order_success.php?order_id=".$order_id);
    exit();
}
?>
