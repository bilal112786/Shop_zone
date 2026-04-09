<?php
session_start();
include '../server/connection.php';
require_once __DIR__ . '/../server/product_image_helper.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

$error = ""; // 🔹 to store error messages

// Get product details
if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Update product
if(isset($_POST['update_product'])){
    $id    = intval($_POST['product_id']);
    $name  = trim($_POST['product_name']);
    $price = floatval($_POST['product_price']);

    // Prevent negative price
    if ($price < 0) {
        $error = " Price cannot be negative!";
    } else {
        // If image updated
        if (isset($_FILES['product_image']['name']) && $_FILES['product_image']['name'] !== '') {
            $ext = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
            $image = time() . '_' . rand(1000, 9999) . '.' . $ext;

            $tmp_name = $_FILES['product_image']['tmp_name'];
            $upload_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            if (move_uploaded_file($tmp_name, $upload_dir . $image)) {
                $stmt = $conn->prepare('UPDATE products SET product_name=?, product_price=?, product_image=?, product_image2=?, product_image3=?, product_image4=? WHERE product_id=?');
                $stmt->bind_param('sdssssi', $name, $price, $image, $image, $image, $image, $id);
            } else {
                $stmt = $conn->prepare('UPDATE products SET product_name=?, product_price=? WHERE product_id=?');
                $stmt->bind_param('sdi', $name, $price, $id);
            }
        } else {
            $stmt = $conn->prepare("UPDATE products SET product_name=?, product_price=? WHERE product_id=?");
            $stmt->bind_param("sdi", $name, $price, $id);
        }

        if($stmt->execute()){
            header("Location: admin-products.php?success=Product updated successfully");
            exit;
        } else {
            $error = "Error updating product.";
        }
    }
}

$pageTitle = 'Edit product';
$navActive = 'products';
include 'includes/admin-header.php';
?>

    <div class="admin-page admin-page-flush">
    <div class="admin-card">
        <h1>Edit Product</h1>

        <!--  Show error if any -->
        <?php if (!empty($error)): ?>
            <p class="notice error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
            
            <label>Product Name</label>
            <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
            
            <label>Product Price (PKR)</label>
            <input type="number" step="0.01" min="0" name="product_price" value="<?php echo $product['product_price']; ?>" required>
            
            <label>Current Image</label>
            <img class="thumb" src="<?php echo htmlspecialchars(product_image_url_from_admin($product['product_image']), ENT_QUOTES, 'UTF-8'); ?>" width="120"><br>
            
            <label>Upload New Image</label>
            <input type="file" name="product_image">
            
            <input type="submit" name="update_product" value="Update Product" class="btn btn-success">
        </form>
        <a href="admin-products.php" class="btn btn-secondary">Back to Products</a>
    </div>
    </div>
<?php include 'includes/admin-footer.php'; ?>
