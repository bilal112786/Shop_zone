<?php
session_start();
include '../server/connection.php';
require_once __DIR__ . '/../server/product_image_helper.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

$message = '';
$isSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['product_name']);
    $category = trim($_POST['product_category']);
    $price = (float) $_POST['product_price'];
    $description = trim($_POST['product_description']);

    if ($price <= 0) {
        $message = 'Price must be greater than 0.';
    } elseif ($name === '' || $category === '' || $description === '') {
        $message = 'All fields are required.';
    } elseif (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] !== UPLOAD_ERR_OK) {
        $message = 'Please select a valid image.';
    } else {
        $targetDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $imageName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['product_image']['name']));
        $targetFile = $targetDir . $imageName;

        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetFile)) {
            $offer = 0;
            $color = '';
            $stmt = $conn->prepare('INSERT INTO products (product_name, product_category, product_price, product_description, product_image, product_image2, product_image3, product_image4, product_special_offer, product_color) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param(
                'ssdsssssis',
                $name,
                $category,
                $price,
                $description,
                $imageName,
                $imageName,
                $imageName,
                $imageName,
                $offer,
                $color
            );

            if ($stmt->execute()) {
                $message = 'Product added successfully.';
                $isSuccess = true;
            } else {
                $message = 'Database error: ' . $stmt->error;
                @unlink($targetFile);
            }
            $stmt->close();
        } else {
            $message = 'Failed to upload image.';
        }
    }
}

$pageTitle = 'Add product';
$navActive = 'products';
include 'includes/admin-header.php';
?>

  <div class="admin-page admin-page-flush">
  <div class="admin-card">
    <h1>Add Product</h1>

    <?php if ($message !== '') { ?>
      <p class="notice <?php echo $isSuccess ? 'success' : 'error'; ?>"><?php echo htmlspecialchars($message); ?></p>
    <?php } ?>

    <form method="POST" action="" enctype="multipart/form-data">
      <label>Product Name:</label>
      <input type="text" name="product_name" required>

      <label>Category:</label>
      <input type="text" name="product_category" required placeholder="e.g. kurta, shoes, dresses (must match shop filters)">

      <label>Price:</label>
      <input type="number" step="0.01" name="product_price" required>

      <label>Description:</label>
      <textarea name="product_description" rows="4" required></textarea>

      <label>Image:</label>
      <input type="file" name="product_image" accept="image/*" required>

      <button type="submit">Add Product</button>
    </form>
    <div class="top-actions">
        <a href="admin-products.php" class="btn btn-secondary">Back to Products</a>
    </div>
  </div>
  </div>
<?php include 'includes/admin-footer.php'; ?>
