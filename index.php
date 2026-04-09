
<?php
require_once __DIR__ . '/server/product_image_helper.php';
include 'layouts/header.php';
?>
<style>
  .product img {
    width: 300%;              /* full width inside product card */
    height: 300px;            /* fixed height */
    object-fit: cover;        /* crop the image but keep proportions */
    border-radius: 8px;       /* optional: smooth corners */
    
}
</style>

<!--- Home Section -->
<section id="home" >
<div  class="container">
<h5 >NEW ARRIVAL</h5>
<h1><span> Best Prices </span>This Season</h1>
<p>Shopping Zone offers the best products at the most affordable prices.</p>

</div>
</section>
<!--- brands -->
<section id="brands" class="container">
<div class="row">
<img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/images/OIP (3).jpg">
<img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/images/the-new-yorker1866.jpg">
    <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/images/OIP (4).jpg">
    
    <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/images/Coach-logo-1080x451.jpg">
</div>
</section>
<!--- new --> 
<section id="new" class="w-100">
<div class="row p-0 m-0">
<!--- one -->
<div class="one col-lg-4 col-md-12 col-sm-12 p-0">
<img class="img-fluid" src="assets/images/sho3.webp">
<div class="detail">
    <h2>Extreamely Awesome Shoes</h2>
    <button class="text-uppercase">Shop Now</button>
</div>
</div>
<!--- two -->
<div class="one col-lg-4 col-md-12 col-sm-12 p-0">
<img class="img-fluid" src="assets/images/bag1.jpeg">
<div class="detail">
    <h2> Awesome Jacket</h2>
    <button class="text-uppercase">Shop Now</button>
</div>
</div>
<!--- three-->
<div class="one col-lg-4 col-md-12 col-sm-12 p-0">
<img class="img-fluid" src="assets/images/watch1.jpg">
<div class="detail">
    <h2>50% OFF Watches</h2>
    <button class="text-uppercase">Shop Now</button>
</div>
</div>
</div>
</section>
<!--- Featured -->
<section id="featured" class="my-5 pb5">
<div class="container text-center mt-5 py-5" >
<h3>Our Featured</h3>
<hr class="fancy-hr">
<p>Here you can check out our featured product</p>
</div>

<div class="row mx-auto container-fluid">
<?php include('server/get_featured_products.php'); ?>
<?php while($row= $featured_products->fetch_assoc()) { ?>
<div class="product text-center col-lg-3 col-md-4 col-sm-12">
<img class="img-fluid mb-3" src="./<?php echo htmlspecialchars(product_image_url($row['product_image']), ENT_QUOTES, 'UTF-8'); ?>">
<div class="p-price">
    <i class="fas fa-star"></i>
    <i class="fas fa-star"></i> 
    <i class="fas fa-star"></i>
    <i class="fas fa-star"></i> 
        <i class="fas fa-star"></i>

</div>

<h5 class="p-name"><?php echo $row['product_name']; ?></h5>
<h4 class="p-price">Pkr: <?php echo $row['product_price']; ?></h4>
<a href="<?php echo "single_product.php?product_id=". $row['product_id']; ?>"><button class="buy-btn">Buy now</button></a>

</div>

<?php } ?>


</div> 

</section>
<!--- midbanner -->
<section id="banner" class="my-5 py-5">
<div class="container">
<h4> MId SEASON'S SALE</h4>
<h1>Autumn Collection <br> UP to 30% OFF Up Comming </h1>

</div>

</section>
<!--- clothes <button class="buy-btn">Buy now</button>-->
<section id="clothes" class="my-5 ">
<div class="container text-center mt-5 py-5" >
<h3>Dresses & Coats</h3>
<hr class="fancy-hr">
<p>Here you can check out our amazing clothes</p>
</div>

<div class="row mx-auto container-fluid">
<?php include('server/get_dresses.php'); ?>
<?php while($row= $coats_products->fetch_assoc()) { ?>
<div class="product text-center col-lg-3 col-md-4 col-sm-12">
<img class="img-fluid mb-3" src="./<?php echo htmlspecialchars(product_image_url($row['product_image']), ENT_QUOTES, 'UTF-8'); ?>">
<div class="p-price">
    <i class="fas fa-star"  ></i>
    <i class="fas fa-star"></i> 
    <i class="fas fa-star"></i>
    <i class="fas fa-star"></i> 
        <i class="fas fa-star"></i>

</div>

<h5 class="p-name"><?php echo $row['product_name']; ?></h5>
<h4 class="p-price">Pkr: <?php echo $row['product_price']; ?></h4>
<a href="<?php echo "single_product.php?product_id=". $row['product_id']; ?>"><button class="buy-btn">Buy now</button>
</a>


</div>

<?php } ?>

</div>
</section>
<!--- watches -->
<section id="watches" class="my-5 ">
<div class="container text-center mt-5 py-5" >
<h3>Best Watches</h3>
<hr class="fancy-hr">
<p> check out our unique Watches </p>
</div>

<div class="row mx-auto container-fluid">
    <?php include('server/get_watches.php'); ?>
    <?php while($row= $watches->fetch_assoc()) { ?>

<div class="product text-center col-lg-3 col-md-4 col-sm-12">
<img class="img-fluid mb-3" src="./<?php echo htmlspecialchars(product_image_url($row['product_image']), ENT_QUOTES, 'UTF-8'); ?>">
<div class="p-price">
    <i class="fas fa-star"  ></i>
    <i class="fas fa-star"></i> 
    <i class="fas fa-star"></i>
    <i class="fas fa-star"></i> 
        <i class="fas fa-star"></i>

</div>

<h5 class="p-name"><?php echo $row['product_name']; ?></h5>
<h4 class="p-price">Pkr: <?php echo $row['product_price']; ?></h4>
<a href="<?php echo "single_product.php?product_id=". $row['product_id']; ?>"><button class="buy-btn">Buy now</button>
</a>
</div>

<?php } ?>

</div>


</section>
<!---shoes-->

<section id="shoes" class="my-5 ">
<div class="container text-center mt-5 py-5" >
<h3>Shoes</h3>
<hr class="fancy-hr">
<p>Here you can check out our amazing Shoes</p>
</div>

<div class="row mx-auto container-fluid">
     <?php include('server/get_shoes.php'); ?>
    <?php while($row= $shoes->fetch_assoc()) { ?>
<div class="product text-center col-lg-3 col-md-4 col-sm-12">
<img class="img-fluid mb-3" src="./<?php echo htmlspecialchars(product_image_url($row['product_image']), ENT_QUOTES, 'UTF-8'); ?>">
<div class="p-price">
    <i class="fas fa-star"  ></i>
    <i class="fas fa-star"></i> 
    <i class="fas fa-star"></i>
    <i class="fas fa-star"></i> 
        <i class="fas fa-star"></i>

</div>

<h5 class="p-name"><?php echo $row['product_name']; ?></h5>
<h4 class="p-price">Pkr:<?php echo $row['product_price']; ?></h4>
<a href="<?php echo "single_product.php?product_id=". $row['product_id']; ?>"><button class="buy-btn">Buy now</button>
</a>

</div>

<?php } ?>

</div>
</section>

<?php include('layouts/footer.php');

?>