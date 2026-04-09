<?php
session_start();
include('server/connection.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

if (!isset($_GET['order_id'])) {
    die("Invalid Request");
}

$order_id = (int)$_GET['order_id'];
$user_id = (int)$_SESSION['user_id'];

// Get order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ? LIMIT 1");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
  die("Order not found");
}

// Get payment details
$stmt2 = $conn->prepare("SELECT * FROM payments WHERE order_id = ?");
$stmt2->bind_param("i", $order_id);
$stmt2->execute();
$payment = $stmt2->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Success</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
    .success-box { background: #fff; padding: 20px; border-radius: 8px; max-width: 600px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    h2 { color: green; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    table td { padding: 8px; border: 1px solid #ddd; }
    .btn { display: inline-block; margin-top: 15px; padding: 10px 20px; background: green; color: #fff; text-decoration: none; border-radius: 5px; }
  </style>
</head>
<body>
  <div class="success-box">
    <h2> Order Confirmed Successfully!</h2>
    <p>Thank you for shopping with us. Here are your details:</p>

    <h3>Order Details</h3>
    <table>
      <tr><td>Order ID</td><td><?= htmlspecialchars($order['order_id']) ?></td></tr>
      <tr><td>Amount</td><td>Rs <?= htmlspecialchars($order['order_cost']) ?></td></tr>
      <tr><td>Status</td><td><?= htmlspecialchars($order['order_status']) ?></td></tr>
      <tr><td>City</td><td><?= htmlspecialchars($order['user_city']) ?></td></tr>
      <tr><td>Address</td><td><?= htmlspecialchars($order['user_address']) ?></td></tr>
      <tr><td>Date</td><td><?= htmlspecialchars($order['order_date']) ?></td></tr>
    </table>

    <h3>Payment Details</h3>
    <table>
      <tr><td>Method</td><td><?= isset($payment['payment_method']) ? htmlspecialchars(ucfirst($payment['payment_method'])) : '-' ?></td></tr>
      <?php if (!empty($payment['mobile_no'])) { ?><tr><td>Mobile No</td><td><?= htmlspecialchars($payment['mobile_no']) ?></td></tr><?php } ?>
      <?php if (!empty($payment['bank_name'])) { ?><tr><td>Bank</td><td><?= htmlspecialchars($payment['bank_name']) ?></td></tr><?php } ?>
      <?php if (!empty($payment['account_no'])) { ?><tr><td>Account No</td><td><?= htmlspecialchars($payment['account_no']) ?></td></tr><?php } ?>
      <?php if (!empty($payment['reference'])) { ?><tr><td>Reference</td><td><?= htmlspecialchars($payment['reference']) ?></td></tr><?php } ?>
      <tr><td>Date</td><td><?= isset($payment['payment_date']) ? htmlspecialchars($payment['payment_date']) : '-' ?></td></tr>
    </table>

    <a href="index.php" class="btn">Continue Shopping</a>
  </div>
</body>
</html>
