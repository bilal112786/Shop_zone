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
            $from = $_GET['from'] ?? '';
            if ($from === 'home') {
                $back = 'admin-home-products.php?success=Product deleted successfully';
            } elseif ($from === 'shop') {
                $back = 'admin-shop-products.php?success=Product deleted successfully';
            } else {
                $back = 'admin-products.php?success=Product deleted successfully';
            }
            header('Location: ' . $back);
            exit;
        }
        $from = $_GET['from'] ?? '';
        $errBack = $from === 'home' ? 'admin-home-products.php' : ($from === 'shop' ? 'admin-shop-products.php' : 'admin-products.php');
        header('Location: ' . $errBack . '?error=Failed to delete product');
        exit;
    }
    $from = $_GET['from'] ?? '';
    $nfBack = $from === 'home' ? 'admin-home-products.php' : ($from === 'shop' ? 'admin-shop-products.php' : 'admin-products.php');
    header('Location: ' . $nfBack . '?error=Product not found');
    exit;
}

$from = $_GET['from'] ?? '';
$invBack = $from === 'home' ? 'admin-home-products.php' : ($from === 'shop' ? 'admin-shop-products.php' : 'admin-products.php');
header('Location: ' . $invBack . '?error=Invalid product ID');
exit;
