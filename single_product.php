<?php
include 'server/connection.php';
require_once __DIR__ . '/server/product_image_helper.php';

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();

    $product = $stmt->get_result();
} else {
    header('location: index.php');
    
}






?>




<?php include('layouts/header.php'); ?>

<style>
  .small-img-group img {
    width: 100%;              /* full width inside product card */
    height: 180px;            /* fixed height */
    object-fit: cover;        /* crop the image but keep proportions */
    border-radius: 8px;       /* optional: smooth corners */
    
}
</style>
  <!--- single product details -->
  <section class="container single-product my-5 pt-5">
    <div class="row mt-5">
        <?php while($row = $product->fetch_assoc()){ ?> 

         <div class="col-lg-5 col-md-6 col-sm-12">
                    <img class="img-fluid w-100 pb-1 " src="./<?php echo htmlspecialchars(product_image_url($row['product_image']), ENT_QUOTES, 'UTF-8'); ?>" id="mainimg">
            <div class="small-img-group"> 
                <div class="small-img-col">
                    <img src="./<?php echo htmlspecialchars(product_image_url($row['product_image']), ENT_QUOTES, 'UTF-8'); ?>" width="100%" class="small-img">
                </div>
                <div class="small-img-col">
                    <img src="./<?php echo htmlspecialchars(product_image_url($row['product_image2']), ENT_QUOTES, 'UTF-8'); ?>" width="100%" class="small-img">
                </div>
                <div class="small-img-col">
                    <img src="./<?php echo htmlspecialchars(product_image_url($row['product_image3']), ENT_QUOTES, 'UTF-8'); ?>" width="100%" class="small-img">
            </div>
             <div class="small-img-col">
                    <img src="./<?php echo htmlspecialchars(product_image_url($row['product_image4']), ENT_QUOTES, 'UTF-8'); ?>" width="100%" class="small-img">
            </div>
         </div>
          
         </div>
         

         <div class=" col-lg-6 col-md-12 col-12">
            <h6>Men/Shoes</h6>
            <h3 class="py-4"><?php echo $row['product_name']; ?></h3>
            <h2>Pkr:<?php echo $row['product_price']; ?></h2>
        <form method="POST" action="cart.php" >
             <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
            <input type="hidden" name="product_image" value="<?php echo $row['product_image']; ?>">
            <input type="hidden" name="product_name" value="<?php echo $row['product_name']; ?>">
            <input type="hidden" name="product_price" value="<?php echo $row['product_price']; ?>">
            
             <button class="buy-btn" type="submit" name="add_to_cart" >Add to Cart</button>
         </form>
           
            <h4 class="mt-5 mb-5">Prouct details</h4>
            <span> <?php echo $row['product_description']; ?>
            </span>
            </div>
            
           
            <?php } ?>
         
        </div>     
  </section>

    <!--- related product -->

     <section id="featured" class="my-5 pb5">
        <div class="container text-center mt-5 py-5" >
            <h3>Our Featured</h3>
            <hr>
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



 
     <script>
  window.addEventListener('DOMContentLoaded', function () {
    var mainimg = document.getElementById("mainimg");
    var smallimg = document.getElementsByClassName("small-img");

    if (mainimg && smallimg.length > 0) {
      for (let i = 0; i < smallimg.length; i++) {
        smallimg[i].onclick = function () {
          mainimg.src = smallimg[i].src;
        };
      }
    }
  });
</script>
   <?php include('layouts/footer.php');

?>


