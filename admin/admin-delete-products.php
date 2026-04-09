<?php
session_start();
include '../server/connection.php';
require_once __DIR__ . '/../server/product_image_helper.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];

    $stmt = $conn->prepare('SELECT product_image FROM products WHERE product_id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($product) {
        $stmt = $conn->prepare('DELETE FROM products WHERE product_id=?');
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            product_image_unlink_all($product['product_image']);
            header('Location: admin-products.php?success=Product deleted successfully');
            exit;
        }
        header('Location: admin-products.php?error=Failed to delete product');
        exit;
    }
    header('Location: admin-products.php?error=Product not found');
    exit;
}

header('Location: admin-products.php?error=Invalid product ID');
exit;
