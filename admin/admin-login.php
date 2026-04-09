<?php
session_start();
require_once __DIR__ . '/includes/paths.php';
include '../server/connection.php';

// Redirect if already logged in
if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true){
    header("Location: admin-dashboard.php");
    exit;
}

$error = '';

if(isset($_POST['login'])){
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if(!empty($email) && !empty($password)){
        $stmt = $conn->prepare("SELECT * FROM admins WHERE admin_email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if($admin && password_verify($password, $admin['admin_password'])){
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_name'] = $admin['admin_name'];
            $_SESSION['admin_id'] = $admin['admin_id'];
            header("Location: admin-dashboard.php");
            exit;
        } else {
            $error = "Invalid email or password";
        }
    } else {
        $error = "Please enter email and password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars(admin_asset_url('admin-style.css?v=3'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php if(!empty($error)) echo "<p class='notice error'>".htmlspecialchars($error)."</p>"; ?>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" name="login" value="Login">
        </form>
        <p class="muted">Don't have an account? <a href="admin-register.php">Register</a></p>
    </div>
</body>
</html>
