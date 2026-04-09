
<?php
  session_start();

?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Home</title>

<link rel="stylesheet" href="assets/css/style.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<link rel="stylesheet" href="bootstrap-5.0.2-dist/css/bootstrap.min.css">


<style>
  /* nav bar cart value show  */

.cart-quantity{
    background-color: #fb774b;
    color: white;
    padding: 2px 5px ;
    border-radius: 50%;
    margin: -3px ;
    font-size: 1rem;
}</style>


<!-- FontAwesome CDN -->
<link rel="stylesheet" href="fontawesome-free-6.7.2-web/css/all.min.css">
</head>
<body>

<!--- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white py-4 fixed-top">
<div class="container">
<img class="logo" src="assets/images/safe-shopping-logo-design-template-trusted-choice-shopping-cart-logo-icon-design-vector.jpg" alt="Logo" width="100">
<h2 class="brands">Shopping Zone</h2>
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">

<span class="navbar-toggler-icon"></span>
</button>
<div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
<ul class="navbar-nav">
    <li class="nav-item">
        <a class="nav-link" href="index.php">Home</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="shop.php">Shop</a>
    </li>
   
    <li class="nav-item">
        <a class="nav-link" href="contact.php">Contact Us</a>
    </li>
    <li class="nav-item d-flex">
        <a class="nav-link" href="cart.php"><i class="fa-solid fa-cart-shopping">
            <?php if(isset($_SESSION['quantity']) && $_SESSION['quantity'] !=0 ) {?>
                <span class="cart-quantity"><?php echo $_SESSION['quantity']; ?></span>
            <?php } ?>
        </i>

        </a>
        <a class="nav-link" href="account.php"><i class="fa-solid fa-user"></i></a>
    </li>
</ul>
</div>
</div>
</nav>