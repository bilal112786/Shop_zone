


<?php
// get_featured_product.php
include 'connection.php';

$stmt = $conn->prepare('SELECT * FROM products ORDER BY product_id DESC LIMIT 4');
$stmt->execute();

$featured_products = $stmt->get_result();
?>
