<?php
session_start();
include('server/connection.php');

// Redirect logged-in user away from login.php
if (isset($_SESSION['logged_in']) && basename($_SERVER['PHP_SELF']) == "login.php") {
    header("Location: account.php");
    exit();
}

if (isset($_POST['login_btn'])) {
    $email    = trim($_POST['email']);
    $password = $_POST['Password']; // must match your form field name!

    //  Select user by email only
    $stmt = $conn->prepare("SELECT user_id, user_name, user_email, user_password 
                            FROM users 
                            WHERE user_email = ? 
                            LIMIT 1");

    if ($stmt === false) {
        die("MySQL prepare error: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $user_name, $user_email, $hashed_password);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id']    = $user_id;
            $_SESSION['user_name']  = $user_name;
            $_SESSION['user_email'] = $user_email;
            $_SESSION['logged_in']  = true;

            $stmt->close();
            header("Location: account.php?login_success=" . urlencode("Login successful"));
            exit();
        } else {
            $stmt->close();
            header("Location: login.php?error=" . urlencode("Invalid email or password"));
            exit();
        }
    } else {
        $stmt->close();
        header("Location: login.php?error=" . urlencode("Invalid email or password"));
        exit();
    }
}
?>


<?php include('layouts/header.php'); ?>
    <!--- Login Section -->
    <section class="login container my-5 py-5">
        <div class="container text-center mt-5">
            <h2 class="font-weight-bold">Login</h2>
            <hr class="fancy-hr">
        </div>
        <div class="mx-auto container">
            <form id="login-form" action="login.php" method="POST">
                <p style="color: red" class="text-center">
                    <?php if(isset($_GET['error'])){ echo $_GET['error']; } ?>
                </p>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" id="login-email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" id="login-password" name="Password" placeholder="Enter your Password" required>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn" name="login_btn" id="login-btn" value="Login"/>
                </div>
                <div class="form-group">
                    <p>Don't have an account? <a href="register.php" class="btn" id="register-url">Register here</a></p>
                </div>
            </form>
        </div>
    </section>




<?php include('layouts/footer.php'); ?>