<?php
session_start();
include('server/connection.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get order details safely
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$order_total = 0;
$user_id = $_SESSION['user_id'];

// Validate order from DB
if ($order_id > 0) {
    $stmt = $conn->prepare("SELECT order_cost, order_status FROM orders WHERE order_id = ? AND user_id = ? LIMIT 1");
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result && isset($result['order_cost']) && $result['order_status'] === 'not paid') {
        $order_total = (float)$result['order_cost'];
    }
}
?>

<?php include('layouts/header.php'); ?>

<section class="login container my-5 py-5">
  <div class="container text-center mt-5">
    <h2 class="form-weight-bold">Payment</h2>
    <hr class="fancy-hr">
  </div>

  <div class="checkout-container">
    <?php if ($order_id > 0 && $order_total > 0): ?>
      <p>Total payment: Pkr:<strong><?php echo number_format($order_total, 2); ?></strong></p>

      <form action="order_confirm.php" method="POST">
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

        <div class="container">
          <h2>Checkout</h2>

          <label>Phone Number</label>
          <input type="text" name="user_phone" required>

          <label>City</label>
          <input type="text" name="user_city" required>

          <label>Address</label>
          <input type="text" name="user_address" required>

          <label>Payment Method</label>
          <select name="payment_method" id="payment_method" required>
            <option value="">Select Payment Method</option>
            <option value="easypaisa">Easypaisa</option>
            <option value="jazzcash">JazzCash</option>
            <option value="bank">Bank Transfer</option>
            <option value="cod">Cash on Delivery</option>
          </select>

          <!-- Easypaisa Fields -->
          <div id="easypaisa" class="payment-option">
              <img src="./assets/images/Easypaisa-Icon-Vector.jpg" alt="Easypaisa">Easypaisa
              <input type="text" name="easypaisa_number" placeholder="Mobile Number">
              <input type="text" name="easypaisa_pin" placeholder="PIN">
          </div>

          <!-- JazzCash Fields -->
          <div id="jazzcash" class="payment-option">
              <img src="./assets/images/images (1).png" alt="JazzCash">JazzCash
              <input type="text" name="jazzcash_number" placeholder="Mobile Number">
              <input type="text" name="jazzcash_pin" placeholder="PIN">
          </div>

          <!-- Bank Transfer Fields -->
          <div id="bank" class="payment-option">
              <img src="./assets/images/bank-logo-icon-illustration-vector.jpg" alt="Bank">Bank Transfer
              <input type="text" name="bank_name" placeholder="Bank Name">
              <input type="text" name="bank_account" placeholder="Account Number">
              <input type="text" name="bank_pin" placeholder="PIN">
          </div>

          <button type="submit">Confirm Order</button>
        </div>
      </form>
    <?php else: ?>
      <p class="text-center text-danger">Invalid order details.</p>
    <?php endif; ?>
  </div>
</section>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f5f5f5;
}
.checkout-container {
    max-width: 500px;
    margin: 30px auto;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
h2 { text-align:center; margin-bottom:20px; color:#333; }
label { display:block; margin-top:15px; font-weight: bold; color:#555; }
input[type=text], input[type=number], select {
    width: 100%;
    padding: 10px;
    margin-top:5px;
    border: 1px solid #ccc;
    border-radius: 5px;
}
.payment-option {
    display: none;
    margin-top: 10px;
    padding: 10px;
    border:1px solid #ddd;
    border-radius:5px;
    background:#f9f9f9;
}
.payment-option img {
    width: 40px;
    vertical-align: middle;
    margin-right: 10px;
}
button {
    margin-top:20px;
    width:100%;
    padding:12px;
    background:#fb774b;
    color:white;
    font-size:16px;
    border:none;
    border-radius:5px;
    cursor:pointer;
}
button:hover { background:#e0663c; }
</style>

<script>
const paymentSelect = document.getElementById('payment_method');
const options = ['easypaisa', 'jazzcash', 'bank'];

paymentSelect.addEventListener('change', function() {
    options.forEach(opt => {
        document.getElementById(opt).style.display = 'none';
    });
    const selected = this.value;
    if(options.includes(selected)){
        document.getElementById(selected).style.display = 'block';
    }
});
</script>

<?php include('layouts/footer.php'); ?>
