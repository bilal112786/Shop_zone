<?php
require_once __DIR__ . '/server/product_image_helper.php';
include 'layouts/header.php';

// ---------- CART ACTIONS ----------
if (isset($_POST['add_to_cart'])) {     
    $product_id    = $_POST['product_id'];    
    $product_name  = $_POST['product_name'];    
    $product_price = $_POST['product_price'];    
    $product_image = $_POST['product_image'];    
    $product_quantity = 1;    

    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {        
        $product_array_ids = array_column($_SESSION['cart'], 'product_id');         
        if (!in_array($product_id, $product_array_ids)) {            
            $_SESSION['cart'][$product_id] = [                
                'product_id'       => $product_id,                
                'product_name'     => $product_name,                
                'product_price'    => $product_price,                
                'product_image'    => $product_image,                
                'product_quantity' => $product_quantity            
            ];        
        } else {            
            echo "<script>alert('Product is already in the cart')</script>";        
        }     
    } else {        
        $_SESSION['cart'][$product_id] = [            
            'product_id'       => $product_id,            
            'product_name'     => $product_name,            
            'product_price'    => $product_price,            
            'product_image'    => $product_image,            
            'product_quantity' => $product_quantity        
        ];    
    }     
    calculateTotalCart();  

} elseif (isset($_POST['remove_product'])) {     
    $product_id = $_POST['product_id'];     
    if (isset($_SESSION['cart'][$product_id])) {        
        unset($_SESSION['cart'][$product_id]);    
    }     
    calculateTotalCart();  

    if (isset($_POST['ajax'])) {
        renderCartTable();
        exit;
    }

} elseif (isset($_POST['edit_quantity'])) {     
    $product_id       = $_POST['product_id'];    
    $product_quantity = max(1, intval($_POST['product_quantity'])); // ✅ Never below 1
    if (isset($_SESSION['cart'][$product_id])) {        
        $_SESSION['cart'][$product_id]['product_quantity'] = $product_quantity;    
    }     
    calculateTotalCart();  

    if (isset($_POST['ajax'])) {
        renderCartTable();
        exit;
    }
}  

// ---------- FUNCTIONS ----------
function calculateTotalCart() {    
    $total_price   = 0;   
    $total_quantity = 0;  
    if (!empty($_SESSION['cart'])) {        
        foreach ($_SESSION['cart'] as $product) {            
            $total_price  += ($product['product_price'] * $product['product_quantity']);    
            $total_quantity += $product['product_quantity'];    
        }    
    }
    $_SESSION['total']    = $total_price; 
    $_SESSION['quantity'] = $total_quantity;
    if($total_quantity == 0){
        unset($_SESSION['cart']);
    }
}

function renderCartTable() {
    if (!empty($_SESSION['cart'])) { ?>
        <table class="cart mt-5 pt-5">             
            <tr>                 
                <th>Product</th>                 
                <th>Quantity</th>                 
                <th>Total</th>             
            </tr>            
            <?php foreach ($_SESSION['cart'] as $value): ?>     
            <tr>         
                <td>             
                    <div class="product-info">                 
                        <img src="<?php echo htmlspecialchars(product_image_url($value['product_image']), ENT_QUOTES, 'UTF-8'); ?>" width="80">                 
                        <div>                     
                            <p><?php echo htmlspecialchars($value['product_name']); ?></p>                     
                            <small><span>Pkr:</span><?php echo number_format($value['product_price'], 2); ?></small>                     
                            <br>                     
                            <button class="remove-btn" data-id="<?php echo $value['product_id']; ?>"> Remove</button>                
                        </div>             
                    </div>         
                </td>          
                <td>                         
                    <input type="number" 
                           class="qty-input" 
                           data-id="<?php echo $value['product_id']; ?>" 
                           value="<?php echo $value['product_quantity']; ?>" 
                           min="1"/>             
                </td>          
                <td>             
                    <span>Pkr: </span>             
                    <span class="product-price" 
                          data-unitprice="<?php echo $value['product_price']; ?>">                 
                        <?php echo number_format($value['product_price'] * $value['product_quantity'], 2); ?>             
                    </span>         
                </td>     
            </tr> 
            <?php endforeach; ?> 
        </table>         

        <div class="cart-total">             
            <table>                 
                <tr>                     
                    <td>Total</td>                     
                    <td><span>Pkr: </span><span id="cart-total"><?php echo $_SESSION['total'] ?? 0 ?></span></td>             
                </tr>
            </table>         
        </div>       

        <div class="checkout-container">     
            <form method="POST" action="checkout.php">         
                <input type="submit" class="checkout-btn" value="checkout" name="checkout">     
            </form> 
        </div>  
    <?php } else {
        echo "<p style='text-align:center;'>Your cart is empty</p>";
    }
}
?>

<!-- ---------- CART STYLES ---------- -->
<style>
.cart .remove-btn { cursor:pointer; color:red; background:none; border:none; font-size:14px; }
.cart .qty-input { width:70px; text-align:center; padding:5px; border:1px solid #ccc; border-radius:4px; }
.cart .checkout-btn{ background:#fb774b; color:#fff; padding:10px 20px; border:none; border-radius:5px; cursor:pointer; }
.cart .checkout-btn:hover{ background:#e05f35; }
</style>

<!-- ---------- CART HTML ---------- -->
<section class="cart container my-5 py-5">         
    <div class="container mt-5 ">              
        <h2 class="fonts-weight-bold"> Your Cart</h2>              
        <hr class="fancy-hr">         
    </div>         

    <div id="cart-container">
        <?php renderCartTable(); ?>
    </div>
</section>

<!-- jQuery AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// ✅ Live Quantity Change with Instant Price Update
$(document).on("input", ".qty-input", function(){
    let product_id = $(this).data("id");
    let product_quantity = parseInt($(this).val());

    if(isNaN(product_quantity) || product_quantity < 1){ 
        product_quantity = 1; 
        $(this).val(1);
    }

    // ---- Get the current row ----
    let row = $(this).closest("tr");

    // ---- Update only this row’s total ----
    let unitPrice = parseFloat(row.find(".product-price").data("unitprice"));
    let newRowTotal = (unitPrice * product_quantity).toFixed(2);
    row.find(".product-price").text(newRowTotal);

    // ---- Recalculate Grand Total ----
    let total = 0;
    $(".qty-input").each(function(){
        let qty = parseInt($(this).val());
        let unitPrice = parseFloat($(this).closest("tr").find(".product-price").data("unitprice"));
        total += qty * unitPrice;
    });
    $("#cart-total").text(total.toFixed(2));

    // ---- Update backend (keep session correct) ----
    $.post("cart.php", {
        product_id: product_id,
        product_quantity: product_quantity,
        edit_quantity: true,
        ajax: true
    });
});

// ✅ Remove product instantly
$(document).on("click", ".remove-btn", function(){
    let product_id = $(this).data("id");
    $.post("cart.php", {
        product_id: product_id,
        remove_product: true,
        ajax: true
    }, function(response){
        $("#cart-container").html(response);
    });
});
</script>

<?php include('layouts/footer.php'); ?>
