<?php
session_start();
include('server/connection.php');

if (isset($_POST['register'])) {
    $name             = trim($_POST['name']);
    $email            = trim($_POST['email']);
    $password         = $_POST['Password'];        // Must match your form field
    $confirm_password = $_POST['ConfirmPassword']; // Must match your form field

    // 1. Check if passwords match
    if ($password !== $confirm_password) {
        header("Location: register.php?error=" . urlencode("Passwords do not match"));
        exit();
    } elseif (strlen($password) < 6) {
        header("Location: register.php?error=" . urlencode("Password must be at least 6 characters"));
        exit();
    }

    // 2. Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 3. Check if email already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows() > 0) {
        $stmt->close();
        header("Location: register.php?error=" . urlencode("Email already registered"));
        exit();
    }
    $stmt->close();

    // 4. Insert new user
    $stmt = $conn->prepare("INSERT INTO users (user_name, user_email, user_password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashed_password);

    if ($stmt->execute()) {
        $new_user_id = $conn->insert_id; // 

        // Auto login
        $_SESSION['user_id']    = $new_user_id;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name']  = $name;
        $_SESSION['logged_in']  = true;

        $stmt->close();
        header("Location: account.php?register_success=" . urlencode("Registration successful"));
        exit();
    } else {
        $stmt->close();
        header("Location: register.php?error=" . urlencode("Something went wrong, try again"));
        exit();
    }
}
?>

<?php include('layouts/header.php'); ?>

    <!--- register Section -->
    <section class="login container my-5 py-5">
        <div class="container text-center mt-5 ">
            <h2 class="front-weight-bold">register</h2>
            <hr class="fancy-hr">
        </div>
        <div class="mx-auto container">
           <form id="register-form" method="post" action="register.php">
    <p style="color: red;"><?php if(isset($_GET['error'])){ echo $_GET['error'];}?></p>

    <div class="form-group">
        <label>Name</label>
        <input type="text" class="form-control" id="register-name" name="name" placeholder="Name" required>
    </div>  

    <div class="form-group">
        <label>Email</label>
        <input type="email" class="form-control" id="register-email" name="email" placeholder="Enter your email" required>
    </div>  

    <div class="form-group">
        <label>Password</label>
        <input type="password" class="form-control" id="register-password" name="Password" placeholder="Password" required>
    </div>

    <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" class="form-control" id="register-confirm-password" name="ConfirmPassword" placeholder="Confirm password" required>
    </div>

    <div class="form-group">
        <input type="submit" class="btn" id="register-btn" name="register" value="Register"/>
    </div>

    <div class="form-group">
        <p>Do you have an account? <a href="login.php" id="login-url">Login</a></p>
    </div>
</form>

        </div>
    </section>
<?php include('layouts/footer.php'); ?>