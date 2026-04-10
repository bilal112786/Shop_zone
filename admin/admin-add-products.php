<?php
session_start();
include '../server/connection.php';
require_once __DIR__ . '/../server/product_image_helper.php';
require_once __DIR__ . '/includes/paths.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

$homeFlow = isset($_GET['for']) && $_GET['for'] === 'home';
$shopFlow = isset($_GET['for']) && $_GET['for'] === 'shop';

$message = '';
$isSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['product_name'] ?? ''));
    $description = trim((string) ($_POST['product_description'] ?? ''));
    $price = (float) ($_POST['product_price'] ?? 0);
    $redirectHome = !empty($_POST['redirect_after_add']) && $_POST['redirect_after_add'] === 'home';
    $redirectShop = !empty($_POST['redirect_after_add']) && $_POST['redirect_after_add'] === 'shop';

    if (!empty($_POST['home_flow'])) {
        $category = 'kurta';
    } elseif (!empty($_POST['shop_flow'])) {
        $category = 'shop';
    } else {
        $categoryRaw = trim((string) ($_POST['product_category'] ?? ''));
        if ($categoryRaw === '__other__') {
            $category = strtolower(trim((string) ($_POST['product_category_other'] ?? '')));
            $category = preg_replace('/[^a-z0-9_-]+/', '-', $category);
            $category = trim($category, '-');
        } else {
            $category = strtolower($categoryRaw);
        }
    }

    if ($price <= 0) {
        $message = 'Price must be greater than 0.';
    } elseif ($name === '' || $category === '' || $description === '') {
        $message = 'All fields are required. If you chose “Other” category, enter a slug.';
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
            $img1 = $imageName;
            $extraUploaded = [$targetFile];

            $saveExtra = static function (string $field, string $targetDir, string $fallback) use (&$extraUploaded): string {
                if (!isset($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                    return $fallback;
                }
                $nm = time() . '_' . bin2hex(random_bytes(4)) . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename((string) $_FILES[$field]['name']));
                $path = $targetDir . $nm;
                if (move_uploaded_file($_FILES[$field]['tmp_name'], $path)) {
                    $extraUploaded[] = $path;
                    return $nm;
                }
                return $fallback;
            };

            $img2 = $saveExtra('product_image2', $targetDir, $img1);
            $img3 = $saveExtra('product_image3', $targetDir, $img1);
            $img4 = $saveExtra('product_image4', $targetDir, $img1);

            $offer = 0;
            $color = '';
            $stmt = $conn->prepare('INSERT INTO products (product_name, product_category, product_price, product_description, product_image, product_image2, product_image3, product_image4, product_special_offer, product_color) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param(
                'ssdsssssis',
                $name,
                $category,
                $price,
                $description,
                $img1,
                $img2,
                $img3,
                $img4,
                $offer,
                $color
            );

            if ($stmt->execute()) {
                $stmt->close();
                if ($redirectHome) {
                    header('Location: admin-home-products.php?success=Product added — it will show on the home page.');
                    exit;
                }
                if ($redirectShop) {
                    header('Location: admin-shop-products.php?success=' . rawurlencode('Product added with category shop — it will show on the shop page.'));
                    exit;
                }
                $message = 'Product added successfully.';
                $isSuccess = true;
            } else {
                $message = 'Database error: ' . $stmt->error;
                $stmt->close();
                foreach ($extraUploaded as $path) {
                    @unlink($path);
                }
            }
        } else {
            $message = 'Failed to upload image.';
        }
    }
}

$pageTitle = $homeFlow ? 'Add home product' : ($shopFlow ? 'Add shop product' : 'Add product');
$navActive = $homeFlow ? 'page-home' : ($shopFlow ? 'page-shop' : 'products');
include 'includes/admin-header.php';
?>

  <div class="admin-page admin-page-flush">
  <div class="admin-card">
    <h1><?php echo $homeFlow ? 'Add home page product' : ($shopFlow ? 'Add shop page product' : 'Add product'); ?></h1>

    <?php if ($message !== '') { ?>
      <p class="notice <?php echo $isSuccess ? 'success' : 'error'; ?>">
        <?php echo htmlspecialchars($message); ?>
        <?php if ($isSuccess && !$homeFlow && !$shopFlow): ?>
          — <a href="<?php echo htmlspecialchars(storefront_url('index.php'), ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">Open home page</a>
          <?php if (isset($category) && $category === 'kurta'): ?>
            <span class="admin-hint-inline">(Kurta products appear in the home grids.)</span>
          <?php endif; ?>
          <?php if (isset($category) && $category === 'shop'): ?>
            — <a href="<?php echo htmlspecialchars(storefront_url('shop.php'), ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">Open shop page</a>
          <?php endif; ?>
        <?php endif; ?>
      </p>
    <?php } ?>

    <form method="POST" action="" enctype="multipart/form-data">
      <?php if ($homeFlow): ?>
      <input type="hidden" name="home_flow" value="1">
      <input type="hidden" name="redirect_after_add" value="home">
      <p class="admin-hint">This product will be saved as category <strong>kurta</strong> and listed on your storefront home page.</p>
      <?php elseif ($shopFlow): ?>
      <input type="hidden" name="shop_flow" value="1">
      <input type="hidden" name="redirect_after_add" value="shop">
      <p class="admin-hint">This product will be saved as category <strong>shop</strong> and listed on <code>shop.php</code>.</p>
      <?php endif; ?>

      <label>Product name</label>
      <input type="text" name="product_name" required>

      <?php if ($homeFlow): ?>
      <label>Category</label>
      <input type="text" value="Kurta (home page)" readonly class="admin-input-readonly">
      <?php elseif ($shopFlow): ?>
      <label>Category</label>
      <input type="text" value="shop (shop page)" readonly class="admin-input-readonly">
      <?php else: ?>
      <label>Category</label>
      <select name="product_category" id="product_category" required>
        <option value="kurta" selected>Kurta — storefront home</option>
        <option value="shop">Shop — storefront shop page</option>
        <option value="shoes">Shoes</option>
        <option value="dresses">Dresses</option>
        <option value="__other__">Other…</option>
      </select>
      <p class="admin-hint">Choose <strong>Kurta</strong> or <strong>Shop</strong> for home or shop listing.</p>
      <div id="product_category_other_wrap" class="admin-field-hidden">
        <label for="product_category_other">Custom category (slug)</label>
        <input type="text" name="product_category_other" id="product_category_other" placeholder="e.g. watches" pattern="[a-z0-9]([a-z0-9_-]*[a-z0-9])?" title="Letters, numbers, hyphen or underscore">
      </div>
      <?php endif; ?>

      <label>Price (PKR)</label>
      <input type="number" step="0.01" name="product_price" required>

      <label>Description</label>
      <textarea name="product_description" rows="4" required></textarea>

      <label>Main image</label>
      <input type="file" name="product_image" accept="image/*" required>

      <label>Extra image 2 <span class="admin-hint-inline">(optional)</span></label>
      <input type="file" name="product_image2" accept="image/*">

      <label>Extra image 3 <span class="admin-hint-inline">(optional)</span></label>
      <input type="file" name="product_image3" accept="image/*">

      <label>Extra image 4 <span class="admin-hint-inline">(optional)</span></label>
      <input type="file" name="product_image4" accept="image/*">

      <p class="admin-hint">Up to <strong>4</strong> images total for the product gallery. Leave extras empty to reuse the main image.</p>

      <button type="submit">Add product</button>
    </form>
    <div class="top-actions">
        <a href="<?php echo htmlspecialchars(admin_asset_url($homeFlow ? 'admin-home-products.php' : ($shopFlow ? 'admin-shop-products.php' : 'admin-products.php')), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-secondary"><?php echo $homeFlow ? 'Back to home products' : ($shopFlow ? 'Back to shop products' : 'Back to products'); ?></a>
    </div>
  </div>
  </div>
<?php if (!$homeFlow && !$shopFlow): ?>
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
<?php endif; ?>
<?php include 'includes/admin-footer.php'; ?>
