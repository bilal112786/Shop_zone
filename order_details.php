<?php
session_start();

// not paid
// shipped
// delieverd



include 'server/connection.php';
require_once __DIR__ . '/server/product_image_helper.php';

if (isset($_POST['order_details_btn']) && isset($_POST['order_id'])) {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        header("Location: account.php?error=" . urlencode("Invalid request token"));
        exit();
    }

    $order_id = $_POST['order_id'];
    $user_id = $_SESSION['user_id'];

    $stmtOrder = $conn->prepare("SELECT order_status FROM orders WHERE order_id = ? AND user_id = ? LIMIT 1");
    $stmtOrder->bind_param("ii", $order_id, $user_id);
    $stmtOrder->execute();
    $orderRow = $stmtOrder->get_result()->fetch_assoc();

    if (!$orderRow) {
        header("Location: account.php?error=" . urlencode("Order not found"));
        exit();
    }

    $order_status = $orderRow['order_status'];

    $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    $order_details = $stmt->get_result();
    $order_total_price = calculateTotalOrderPrice($order_details);
    if ($order_details instanceof mysqli_result) {
        $order_details->data_seek(0);
    }

} else {
    header("Location: account.php");
    exit();
}
 function calculateTotalOrderPrice($order_details) {    
    $total = 0;    
    
    foreach($order_details as $row) { 
         $product_price = $row['product_price'];
         $product_quantity= $row['product_quantity'];
         $total   += ($product_price * $product_quantity);  
        

    }

    

   return $total;
}




?>

<?php include('layouts/header.php'); ?>
<!--- Orders detail -->
<section id="orders" class="orders container my-5 py-3">
    <div class="container mt-5 ">
        <h2 class="font-weight-bold text-center">Order Details</h2>
        <hr class="fancy-hr">
    </div>
    <table class="mt-5 pt-5 mx-auto">
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
        </tr>

        <?php if ($order_details && $order_details->num_rows > 0) { ?>
            <?php foreach ($order_details as $row) { ?>
                <tr>
                    <td>
                        <div class="product-info">
                            <img src="<?php echo htmlspecialchars(product_image_url($row['product_image']), ENT_QUOTES, 'UTF-8'); ?>"/> 
                            <div>
                                <p class="mt-3 "><?php echo htmlspecialchars($row['product_name']); ?></p>
                            </div>
                        </div>
                    </td>
                    <td><span><?php echo number_format($row['product_price'], 2); ?></span></td>
                    <td><span><?php echo $row['product_quantity']; ?></span></td>
                </tr>
                  
            <?php } ?>
    

        <?php } else { ?>
            <tr>
                <td colspan="3" style="text-align:center;">No items found for this order</td>
            </tr>
        <?php } ?>
    </table>
       <?php
        if ($order_status == 'not paid'){  ?> 
            <form style="float: right;" method="GET" action="payment.php">
    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>"/>
    <input type="hidden" name="order_total" value="<?php echo $order_total_price; ?>"/>
    <input type="submit" class="btn btn-primary" value="Pay Now">
</form>

        <?php }?>


        
      


</section>























<?php include('layouts/footer.php');

?>