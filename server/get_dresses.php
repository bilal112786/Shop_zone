


<?php
// get_featured_product.php
include 'connection.php';

$stmt = $conn->prepare("SELECT * FROM products where product_category='dresses' LIMIT 4");
$stmt->execute();

$coats_products = $stmt->get_result();//[]
?>
