<?php
session_start();
require_once __DIR__ . '/includes/paths.php';
include '../server/connection.php';

if(isset($_POST['register'])){
    $name = trim($_POST['admin_name']);
    $email = trim($_POST['admin_email']);
    $password = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);

    // Check if email exists
    $stmt = $conn->prepare("SELECT * FROM admins WHERE admin_email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $error = "Email already exists";
    } else {
        $stmt = $conn->prepare("INSERT INTO admins (admin_name, admin_email, admin_password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);

        if($stmt->execute()){
            $success = "Admin registered successfully. <a href='admin-login.php'>Login now</a>";
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Register</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars(admin_asset_url('admin-style.css?v=3'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body>
    <div class="login-container">
        <h2>Admin Register</h2>

        <?php if(isset($error)) echo "<p class='notice error'>".htmlspecialchars($error)."</p>"; ?>
        <?php if(isset($success)) echo "<p class='notice success'>".$success."</p>"; ?>

        <form method="POST" action="">
            <input type="text" name="admin_name" placeholder="Name" required>
            <input type="email" name="admin_email" placeholder="Email" required>
            <input type="password" name="admin_password" placeholder="Password" required>
            <input type="submit" name="register" value="Register">
        </form>

        <p class="muted">Already have an account? <a href="admin-login.php">Login</a></p>
    </div>
</body>
</html>
