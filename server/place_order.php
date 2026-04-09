<?php
session_start();
include('connection.php');

if (isset($_POST['place_order'])) {

    // ✅ Ensure cart exists
    if (empty($_SESSION['cart'])) {
        header("Location: ../cart.php?error=" . urlencode("Your cart is empty."));
        exit();
    }

    // ✅ Ensure user logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php?error=" . urlencode("Please log in/register  to place an order."));
        exit();
    }

    // ✅ Calculate total (if not already set)
    if (!isset($_SESSION['total']) || $_SESSION['total'] <= 0) {
        $_SESSION['total'] = 0;
        foreach ($_SESSION['cart'] as $product) {
            $_SESSION['total'] += $product['product_price'] * $product['product_quantity'];
        }
    }

    // User info
    $phone   = $_POST['Phone'];
    $city    = $_POST['city'];
    $address = $_POST['address'];

    $order_cost   = $_SESSION['total'];
    $order_status = 'not paid';
    $user_id      = $_SESSION['user_id']; // ✅ Use session user_id, not hardcoded
    $order_date   = date('Y-m-d H:i:s');

    // ✅ Insert into orders
    $stmt = $conn->prepare(
        "INSERT INTO orders (order_cost, order_status, user_id, user_phone, user_city, user_address, order_date)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    if (!$stmt) {
        header("Location: ../checkout.php?error=" . urlencode("Database error: " . $conn->error));
        exit();
    }

    /**
     * bind_param type string explanation:
     * i = integer
     * s = string
     * d = double
     */
    $stmt->bind_param(
        "isissss", 
        $order_cost,    // i → int/float (total cost)
        $order_status,  // s → string
        $user_id,       // i → int
        $phone,         // s → string (better store as VARCHAR, not INT, since phone numbers can have +, 0 prefix, etc.)
        $city,          // s → string
        $address,       // s → string
        $order_date     // s → string
    );

    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;
        // header('location:../index.php');
        // exit();
    } else {
        header("Location: ../checkout.php?error=" . urlencode("Error inserting order: " . $stmt->error));
        exit();
    }
    $stmt->close();

    // ✅ Insert order items
    foreach ($_SESSION['cart'] as $product) {
        $stmt1 = $conn->prepare(
            "INSERT INTO order_items 
            (order_id, product_id, product_name, product_image, product_price, product_quantity, user_id, order_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );

        if (!$stmt1) {
            header("Location: ../checkout.php?error=" . urlencode("DB error: " . $conn->error));
            exit();
        }

        $stmt1->bind_param(
            "iissiiis",
            $order_id,                  // i
            $product['product_id'],     // i
            $product['product_name'],   // s
            $product['product_image'],  // s
            $product['product_price'],  // i (if decimal, use d instead)
            $product['product_quantity'], // i
            $user_id,                   // i
            $order_date                 // s
        );

        if (!$stmt1->execute()) {
            header("Location: ../checkout.php?error=" . urlencode("Error inserting order item: " . $stmt1->error));
            exit();
        }
        $stmt1->close();
    }

    // ✅ Clear cart after successful order
    unset($_SESSION['cart'], $_SESSION['total']);
    $conn->close();

    // ✅ Redirect to payment page with order details
    header("Location: ../payment.php?success=1&order_id=$order_id&order_total=$order_cost");
    exit();
}
?>

