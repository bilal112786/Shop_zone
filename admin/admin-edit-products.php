<?php
session_start();
include '../server/connection.php';
require_once __DIR__ . '/../server/product_image_helper.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

$error = '';
$returnHome = isset($_GET['return']) && $_GET['return'] === 'home';
$returnShop = isset($_GET['return']) && $_GET['return'] === 'shop';

// Get product details
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $stmt = $conn->prepare('SELECT * FROM products WHERE product_id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();
} else {
    $product = null;
}

// Update product
if (isset($_POST['update_product'])) {
    $id    = (int) $_POST['product_id'];
    $name  = trim((string) ($_POST['product_name'] ?? ''));
    $price = (float) ($_POST['product_price'] ?? -1);
    $categoryRaw = trim((string) ($_POST['product_category'] ?? ''));
    if ($categoryRaw === '__other__') {
        $category = strtolower(trim((string) ($_POST['product_category_other'] ?? '')));
        $category = preg_replace('/[^a-z0-9_-]+/', '-', $category);
        $category = trim($category, '-');
    } else {
        $category = strtolower($categoryRaw);
    }
    $description = trim((string) ($_POST['product_description'] ?? ''));
    $returnHomePost = !empty($_POST['return_home']);
    $returnShopPost = !empty($_POST['return_shop']);

    if ($price < 0) {
        $error = 'Price cannot be negative.';
    } elseif ($name === '' || $category === '' || $categoryRaw === '' || $description === '') {
        $error = 'Name, category, description, and price are required.';
    } else {
        if (!$product || (int) ($product['product_id'] ?? 0) !== $id) {
            $reloadStmt = $conn->prepare('SELECT * FROM products WHERE product_id=?');
            $reloadStmt->bind_param('i', $id);
            $reloadStmt->execute();
            $product = $reloadStmt->get_result()->fetch_assoc();
            $reloadStmt->close();
        }
        if (!$product) {
            $error = 'Product not found.';
        }
    }

    if ($error === '' && isset($product['product_id']) && (int) $product['product_id'] === $id) {
        $upload_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $img1 = (string) ($product['product_image'] ?? '');
        $img2 = (string) ($product['product_image2'] ?? '');
        $img3 = (string) ($product['product_image3'] ?? '');
        $img4 = (string) ($product['product_image4'] ?? '');
        if ($img2 === '') {
            $img2 = $img1;
        }
        if ($img3 === '') {
            $img3 = $img1;
        }
        if ($img4 === '') {
            $img4 = $img1;
        }

        $anyImageChange = false;

        if (isset($_FILES['product_image']['name']) && $_FILES['product_image']['name'] !== '' && (int) ($_FILES['product_image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $ext = pathinfo((string) $_FILES['product_image']['name'], PATHINFO_EXTENSION);
            $newMain = time() . '_' . bin2hex(random_bytes(4)) . '_main.' . $ext;
            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_dir . $newMain)) {
                $img1 = $newMain;
                $anyImageChange = true;
            }
        }

        $saveExtraEdit = static function (string $field, string $dir, string $current) use (&$anyImageChange): string {
            if (!isset($_FILES[$field]['name']) || $_FILES[$field]['name'] === '' || (int) ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                return $current;
            }
            $ext = pathinfo((string) $_FILES[$field]['name'], PATHINFO_EXTENSION);
            $nm = time() . '_' . bin2hex(random_bytes(4)) . '_' . preg_replace('/[^a-z0-9]/i', '', $field) . '.' . $ext;
            if (move_uploaded_file($_FILES[$field]['tmp_name'], $dir . $nm)) {
                $anyImageChange = true;
                return $nm;
            }
            return $current;
        };

        $img2 = $saveExtraEdit('product_image2', $upload_dir, $img2);
        $img3 = $saveExtraEdit('product_image3', $upload_dir, $img3);
        $img4 = $saveExtraEdit('product_image4', $upload_dir, $img4);

        if ($anyImageChange) {
            $stmt = $conn->prepare('UPDATE products SET product_name=?, product_price=?, product_category=?, product_description=?, product_image=?, product_image2=?, product_image3=?, product_image4=? WHERE product_id=?');
            $stmt->bind_param('sdssssssi', $name, $price, $category, $description, $img1, $img2, $img3, $img4, $id);
        } else {
            $stmt = $conn->prepare('UPDATE products SET product_name=?, product_price=?, product_category=?, product_description=? WHERE product_id=?');
            $stmt->bind_param('sdsi', $name, $price, $category, $description, $id);
        }

        if ($stmt && $stmt->execute()) {
            $stmt->close();
            if ($returnHomePost) {
                $loc = 'admin-home-products.php?success=Product updated successfully';
            } elseif ($returnShopPost) {
                $loc = 'admin-shop-products.php?success=Product updated successfully';
            } else {
                $loc = 'admin-products.php?success=Product updated successfully';
            }
            header('Location: ' . $loc);
            exit;
        }
        $error = 'Error updating product.';
    }
}

if (!$product) {
    header('Location: admin-products.php?error=Product not found');
    exit;
}

$curCat = strtolower((string) ($product['product_category'] ?? ''));
$knownCats = ['kurta', 'shop', 'shoes', 'dresses'];
$selKnown = in_array($curCat, $knownCats, true) ? $curCat : '__other__';
$otherCatValue = $selKnown === '__other__' ? $curCat : '';

$pageTitle = 'Edit product';
$navActive = $returnHome ? 'page-home' : ($returnShop ? 'page-shop' : 'products');
include 'includes/admin-header.php';
?>

    <div class="admin-page admin-page-flush">
    <div class="admin-card">
        <h1>Edit product</h1>
        <?php if ($returnHome): ?>
        <p class="admin-hint">Changes apply to the storefront home page when category stays <strong>kurta</strong>.</p>
        <?php endif; ?>
        <?php if ($returnShop): ?>
        <p class="admin-hint">Keep category <strong>shop</strong> for this item to stay on the storefront shop page.</p>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <p class="notice error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?php echo (int) $product['product_id']; ?>">
            <?php if ($returnHome): ?>
            <input type="hidden" name="return_home" value="1">
            <?php endif; ?>
            <?php if ($returnShop): ?>
            <input type="hidden" name="return_shop" value="1">
            <?php endif; ?>

            <label>Product name</label>
            <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>

            <label>Category</label>
            <select name="product_category" id="product_category" required>
                <option value="kurta" <?php echo $selKnown === 'kurta' ? 'selected' : ''; ?>>Kurta — home page</option>
                <option value="shop" <?php echo $selKnown === 'shop' ? 'selected' : ''; ?>>Shop — shop page</option>
                <option value="shoes" <?php echo $selKnown === 'shoes' ? 'selected' : ''; ?>>Shoes</option>
                <option value="dresses" <?php echo $selKnown === 'dresses' ? 'selected' : ''; ?>>Dresses</option>
                <option value="__other__" <?php echo $selKnown === '__other__' ? 'selected' : ''; ?>>Other…</option>
            </select>
            <div id="product_category_other_wrap" class="<?php echo $selKnown === '__other__' ? '' : 'admin-field-hidden'; ?>">
                <label for="product_category_other">Custom category (slug)</label>
                <input type="text" name="product_category_other" id="product_category_other" value="<?php echo htmlspecialchars($otherCatValue); ?>" placeholder="e.g. watches" pattern="[a-z0-9]([a-z0-9_-]*[a-z0-9])?" title="Letters, numbers, hyphen or underscore">
            </div>

            <label>Description</label>
            <textarea name="product_description" rows="4" required><?php echo htmlspecialchars((string) ($product['product_description'] ?? '')); ?></textarea>

            <label>Price (PKR)</label>
            <input type="number" step="0.01" min="0" name="product_price" value="<?php echo htmlspecialchars((string) $product['product_price']); ?>" required>

            <label>Main image</label>
            <img class="thumb" src="<?php echo htmlspecialchars(product_image_url_from_admin($product['product_image']), ENT_QUOTES, 'UTF-8'); ?>" width="120" alt="">
            <label class="admin-label-follow">Replace main image (optional)</label>
            <input type="file" name="product_image" accept="image/*">

            <?php
            $g2 = (string) ($product['product_image2'] ?? '');
            $g3 = (string) ($product['product_image3'] ?? '');
            $g4 = (string) ($product['product_image4'] ?? '');
            ?>
            <label>Gallery image 2</label>
            <?php if ($g2 !== ''): ?>
            <img class="thumb" src="<?php echo htmlspecialchars(product_image_url_from_admin($g2), ENT_QUOTES, 'UTF-8'); ?>" width="100" alt="">
            <?php endif; ?>
            <label class="admin-label-follow">Upload / replace (optional)</label>
            <input type="file" name="product_image2" accept="image/*">

            <label>Gallery image 3</label>
            <?php if ($g3 !== ''): ?>
            <img class="thumb" src="<?php echo htmlspecialchars(product_image_url_from_admin($g3), ENT_QUOTES, 'UTF-8'); ?>" width="100" alt="">
            <?php endif; ?>
            <label class="admin-label-follow">Upload / replace (optional)</label>
            <input type="file" name="product_image3" accept="image/*">

            <label>Gallery image 4</label>
            <?php if ($g4 !== ''): ?>
            <img class="thumb" src="<?php echo htmlspecialchars(product_image_url_from_admin($g4), ENT_QUOTES, 'UTF-8'); ?>" width="100" alt="">
            <?php endif; ?>
            <label class="admin-label-follow">Upload / replace (optional)</label>
            <input type="file" name="product_image4" accept="image/*">

            <p class="admin-hint">Single product page uses all four slots in the thumbnail row. Leave optional fields empty to keep current files.</p>

            <input type="submit" name="update_product" value="Update product" class="btn btn-success">
        </form>
        <a href="<?php echo htmlspecialchars(admin_asset_url($returnHome ? 'admin-home-products.php' : ($returnShop ? 'admin-shop-products.php' : 'admin-products.php')), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-secondary"><?php echo $returnHome ? 'Back to home products' : ($returnShop ? 'Back to shop products' : 'Back to products'); ?></a>
    </div>
    </div>
<script>
(function () {
  var sel = document.getElementById('product_category');
  var wrap = document.getElementById('product_category_other_wrap');
  var other = document.getElementById('product_category_other');
  if (!sel || !wrap) return;
  function toggle() {
    var on = sel.value === '__other__';
    wrap.classList.toggle('admin-field-hidden', !on);
    if (other) other.required = on;
  }
  sel.addEventListener('change', toggle);
  toggle();
})();
</script>
<?php include 'includes/admin-footer.php'; ?>
