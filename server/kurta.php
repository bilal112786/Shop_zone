<?php
// get_featured_product.php
include 'connection.php';

$stmt = $conn->prepare("SELECT * FROM products where product_category='kurta' ");
$stmt->execute();

$kurta = $stmt->get_result();//[]
?>
