<?php
session_start();
include('server/connection.php');

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// ✅ Ensure user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// ✅ Logout handling
if (isset($_GET['logout'])) {
    $logoutToken = $_GET['token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $logoutToken)) {
        header("Location: account.php?error=" . urlencode("Invalid request token"));
        exit();
    }

    session_unset();
    session_destroy();

    header("Location: login.php?success=" . urlencode("Logged out successfully"));
    exit();
}

// ✅ Change Password
if (isset($_POST['change_password'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        header("Location: account.php?error=" . urlencode("Invalid request token"));
        exit();
    }

    $new_password     = $_POST['Password'];
    $confirm_password = $_POST['confirmPassword'];
    $user_email       = $_SESSION['user_email'];
    $user_id          = $_SESSION['user_id'];

    // 1. Check if passwords match
    if ($new_password !== $confirm_password) {
        header("Location: account.php?error=" . urlencode("Passwords do not match"));
        exit();
    }
    // 2. Check password length
    elseif (strlen($new_password) < 6) {
        header("Location: account.php?error=" . urlencode("Password must be at least 6 characters"));
        exit();
    }
    // 3. Update password
    else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET user_password = ? WHERE user_email = ?");
        if ($stmt === false) {
            header("Location: account.php?error=" . urlencode("Database error: " . $conn->error));
            exit();
        }

        $stmt->bind_param("ss", $hashed_password, $user_email);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: account.php?message=" . urlencode("Password changed successfully"));
            exit();
        } else {
            $stmt->close();
            header("Location: account.php?error=" . urlencode("Something went wrong, try again"));
            exit();
        }
    }
}

// ✅ Delete Order
if (isset($_POST['delete_order'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        header("Location: account.php?error=" . urlencode("Invalid request token"));
        exit();
    }

    $order_id = $_POST['order_id'];
    $user_id  = $_SESSION['user_id'];

    // Only delete order belonging to this user
    $stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $order_id, $user_id);

    if ($stmt->execute()) {
        header("Location: account.php?message=" . urlencode("Order deleted successfully"));
        exit();
    } else {
        header("Location: account.php?error=" . urlencode("Unable to delete order"));
        exit();
    }
}

// ✅ Get orders
if (isset($_SESSION['logged_in'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $orders = $stmt->get_result(); // []
}
?>

<?php include('layouts/header.php'); ?>

<!--- Account Section -->
<section class="login container my-5 py-5">
    <div class="row container mx-auto">
        <div class="text-center mt-3 pt-5 col-lg-6 col-md-12 col-sm-12">
            <p class="text-center" style="color: green">
                <?php if (isset($_GET['register_success'])) {
                    echo e($_GET['register_success']);
                } ?></p>
            <p class="text-center" style="color: green">
                <?php if (isset($_GET['login_success'])) {
                    echo e($_GET['login_success']);
                } ?></p>

            <h2 class="front-weight-bold">Account info</h2>
            <hr class="fancy-hr">
            <div class="account-info">
                <p><strong>Name:</strong> <?php if (isset($_SESSION['user_name'])) {
                                                echo e($_SESSION['user_name']);
                                            } ?></p>
                <p><strong>Email:</strong> <?php if (isset($_SESSION['user_email'])) {
                                                echo e($_SESSION['user_email']);
                                            } ?></p>
                <p><a href="#orders" id="orders-btn">Your Orders</a></p>
                <p><a href="account.php?logout=1&token=<?php echo urlencode($_SESSION['csrf_token']); ?>" id="logout-btn">Logout</a></p>
            </div>
        </div>

        <div class="col-lg-6 col-md-12 col-sm-12">
            <form id="account-form" method="post" action="account.php">
                <p class="text-center" style="color: red">
                    <?php if (isset($_GET['error'])) {
                        echo e($_GET['error']);
                    } ?></p>

                <p class="text-center" style="color: green">
                    <?php if (isset($_GET['message'])) {
                        echo e($_GET['message']);
                    } ?></p>
                <h3>Change Password</h3>
                <hr class="fancy-hr">
                <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" class="form-control" id="password" name="Password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" class="form-control" id="Account-password-confirm" name="confirmPassword" placeholder="Confirm Password" required>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" id="Change-pass-btn" name="change_password" value="Change Password" />
                </div>
            </form>
        </div>
    </div>
</section>

<!--- Orders -->
<section id="orders" class="orders container my-5 py-3">
    <div class="container mt-2 ">
        <h2 class="front-weight-bold text-center"> Your Orders</h2>
        <hr class="fancy-hr">
    </div>
    <table class="mt-5 pt-5">
        <tr>
            <th>Order ID</th>
            <th>Order Cost</th>
            <th>Order Status</th>
            <th>Order Date</th>
            <th>Action</th>
        </tr>

        <?php if ($orders && $orders->num_rows > 0) { ?>
            <?php while ($row = $orders->fetch_assoc()) { ?>
                <tr>
                    <td><span><?php echo e($row['order_id']); ?></span></td>
                    <td><span><?php echo e($row['order_cost']); ?></span></td>
                    <td><span><?php echo e($row['order_status']); ?></span></td>
                    <td><span><?php echo e($row['order_date']); ?></span></td>
                    <td>
                        <!-- Order Details -->
                        <form method="POST" action="order_details.php" style="display:inline-block;">
                            <input type="hidden" name="order_status" value="<?php echo e($row['order_status']); ?>">
                            <input type="hidden" name="order_id" value="<?php echo e($row['order_id']); ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">
                            <input class="btn btn-primary" name="order_details_btn" type="submit" value="Details">
                        </form>

                        <!-- Delete Order -->
                        <form method="POST" action="account.php" style="display:inline-block;"
                            onsubmit="return confirm('Are you sure you want to delete this order?');">
                            <input type="hidden" name="order_id" value="<?php echo e($row['order_id']); ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">
                            <input class="btn btn-danger" name="delete_order" type="submit" value="Delete">
                        </form>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="5" style="text-align:center;">You have no orders yet</td>
            </tr>
        <?php } ?>
    </table>
</section>

<?php include('layouts/footer.php'); ?>