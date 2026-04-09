<?php
session_start();

if (isset($_POST['checkout']) && !empty($_SESSION['cart'])) {
    //  Cart is not empty and checkout button pressed
    // let the user in (redirect to checkout page, etc.)
    // header("Location: checkout.php"); // uncomment if needed

} else {
    //  If cart is empty or checkout not clicked → redirect
    header("Location: index.php");
    exit();
}
?>



<?php include('layouts/header.php'); ?>
    <!--- Checkout -->
     <section class="login container my-5 py-5">
        <div class="container text-center mt-5 ">
            <h2 class="form-weight-bold">Check out</h2>
            <hr class="fancy-hr">
        </div>
        <div class="mx-auto container">
            <form id="checkout-form" method="post" action="server/place_order.php">
                
                <div class="form-group checkout-small-elements">
                    <label>Name</label>
                    <input type="name" class="form-control" id="Checkout-name" name="name" placeholder="Name" required>
                </div>  
                <div class="form-group checkout-small-elements ">
                    <label>Email</label>
                    <input type="email" class="form-control" id="checkout-email" name="email" placeholder="Enter your email" required>
                </div>  
                 <div class="form-group checkout-small-elements">
                    <label>Phone</label>
                    <input type="tel" class="form-control" id="checkout-phone" name="Phone" placeholder="Phone" required>
                </div>
                <div class="form-group checkout-small-elements">
                    <label>City</label>
                    <input type="text" class="form-control" id="checkout-city" name="city" placeholder="city" required>
                </div>
                <div class="form-group checkout-large-elements">
                    <label><Address></Address></label>
                    <input type="text" class="form-control" id="checkout-Address" name="address" placeholder="Address" required>
                </div>
                <div class="form-group checkout-small-container">
                     <p>Total amount : Pkr: <?php echo $_SESSION['total'];  ?></p>
                   
                    <input type="submit" class="checkout-btn" id="checkout-btn" name="place_order" value="Place Order"/>
                </div>
                
            </form>
        </div>
    </section>
                



     


<?php include('layouts/footer.php'); ?>
