<?php
include 'server/connection.php';
require_once __DIR__ . '/server/product_image_helper.php';

if (!isset($_GET['product_id']) || !is_numeric($_GET['product_id'])) {
    header('Location: index.php');
    exit;
}

$product_id = (int) $_GET['product_id'];
$stmt = $conn->prepare('SELECT * FROM products WHERE product_id = ?');
$stmt->bind_param('i', $product_id);
$stmt->execute();
$product = $stmt->get_result();
$row = $product->fetch_assoc();
$stmt->close();

if (!$row) {
    header('Location: index.php');
    exit;
}

$pair_home = null;
if (isset($_GET['home_pick']) && is_numeric($_GET['home_pick'])) {
    $home_pick_id = (int) $_GET['home_pick'];
    if ($home_pick_id > 0 && $home_pick_id !== $product_id) {
        $st2 = $conn->prepare('SELECT * FROM products WHERE product_id = ?');
        $st2->bind_param('i', $home_pick_id);
        $st2->execute();
        $pair_home = $st2->get_result()->fetch_assoc();
        $st2->close();
    }
}

$main_price = (float) $row['product_price'];
$home_price = $pair_home ? (float) $pair_home['product_price'] : 0.0;
$combined_price = $main_price + $home_price;

$shop_cat = 'shop';
$stmt_shop = $conn->prepare(
    'SELECT product_id, product_name, product_price, product_image FROM products WHERE LOWER(TRIM(COALESCE(product_category, \'\'))) = ? AND product_id != ? ORDER BY product_id DESC LIMIT 4'
);
$stmt_shop->bind_param('si', $shop_cat, $product_id);
$stmt_shop->execute();
$res_shop = $stmt_shop->get_result();
$shop_variation_list = [];
while ($r = $res_shop->fetch_assoc()) {
    $shop_variation_list[] = $r;
}
$stmt_shop->close();

$design_main_file = '';
$design_thumb_files = [null, null, null, null];
if ($pair_home) {
    $dkeys = ['product_image', 'product_image2', 'product_image3', 'product_image4'];
    $first_nonempty = '';
    foreach ($dkeys as $i => $k) {
        $v = trim((string) ($pair_home[$k] ?? ''));
        $ok = $v !== '' && basename($v) !== '';
        if ($ok && $first_nonempty === '') {
            $first_nonempty = $v;
        }
        $design_thumb_files[$i] = $ok ? $v : null;
    }
    foreach ($design_thumb_files as $i => $f) {
        if ($f === null && $first_nonempty !== '') {
            $design_thumb_files[$i] = $first_nonempty;
        }
    }
    $dm = trim((string) ($pair_home['product_image'] ?? ''));
    $design_main_file = ($dm !== '' && basename($dm) !== '') ? $dm : $first_nonempty;
}

$home_pick_query = ($pair_home && !empty($pair_home['product_id']))
    ? '&home_pick=' . (int) $pair_home['product_id']
    : '';

?>




<?php include('layouts/header.php'); ?>

<style>
  .small-img-group img {
    width: 100%;              /* full width inside product card */
    height: 180px;            /* fixed height */
    object-fit: cover;        /* crop the image but keep proportions */
    border-radius: 8px;       /* optional: smooth corners */
    
  }
  .design-from-home-section .design-home-thumbs img {
    cursor: pointer;
  }
  .shop-selection-wide .shop-selection-thumb {
    width: 100%;
    height: clamp(220px, 28vw, 360px);
    object-fit: cover;
    border-radius: 10px;
    display: block;
  }
  @media (max-width: 767.98px) {
    .shop-selection-wide .shop-selection-thumb {
      height: clamp(180px, 45vw, 280px);
    }
  }
  .variation-under-home h4 {
    font-size: 1.25rem;
    font-weight: 600;
  }
  .variation-under-home .variation-shop-equal-img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 8px;
    display: block;
  }
  @media (min-width: 992px) {
    .variation-under-home .variation-shop-equal-img {
      height: 200px;
    }
  }
  .variation-under-home .variation-shop-link {
    display: block;
    height: 100%;
  }
</style>
  <!--- single product: paired flow = design gallery + details side by side; shop gallery below; else classic layout -->
  <section class="container single-product my-5 <?php echo $pair_home ? 'pt-4' : 'pt-5'; ?>">
    <?php if ($pair_home): ?>
    <div class="row mt-2 align-items-start design-from-home-section">
      <div class="col-lg-5 col-md-6 col-sm-12">
        <?php if ($design_main_file !== ''): ?>
        <img class="img-fluid w-100 pb-1" src="./<?php echo htmlspecialchars(product_image_url($design_main_file), ENT_QUOTES, 'UTF-8'); ?>" id="designMainImg" alt="<?php echo htmlspecialchars($pair_home['product_name'], ENT_QUOTES, 'UTF-8'); ?>">
        <div class="small-img-group design-home-thumbs mt-3">
          <?php foreach ($design_thumb_files as $tf): ?>
          <div class="small-img-col">
            <?php if ($tf !== null && $tf !== ''): ?>
            <img src="./<?php echo htmlspecialchars(product_image_url($tf), ENT_QUOTES, 'UTF-8'); ?>" width="100%" class="design-small-img" alt="">
            <?php else: ?>
            <div class="design-thumb-placeholder bg-light border rounded" style="height:180px;width:100%;"></div>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="text-muted">No images for this design.</p>
        <?php endif; ?>

        <?php if (!empty($shop_variation_list)): ?>
        <div class="variation-under-home w-100 mt-4">
          <h4 class="text-center mb-3">Variation</h4>
          <div class="row g-3">
            <?php foreach ($shop_variation_list as $sv): ?>
            <div class="col-6 col-md-3">
              <a class="variation-shop-link text-decoration-none" href="<?php echo 'single_product.php?product_id=' . (int) $sv['product_id'] . $home_pick_query; ?>" title="<?php echo htmlspecialchars($sv['product_name'], ENT_QUOTES, 'UTF-8'); ?>">
                <img class="variation-shop-equal-img" src="./<?php echo htmlspecialchars(product_image_url($sv['product_image']), ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($sv['product_name'], ENT_QUOTES, 'UTF-8'); ?>">
              </a>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

      </div>
      <div class="col-lg-6 col-md-6 col-sm-12 mt-4 mt-md-0">
        <h6>Shop item</h6>
        <h3 class="py-4"><?php echo htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
        <div class="border rounded p-3 mb-3 bg-light">
          <p class="mb-2 small text-muted mb-0">Your selected design (Home)</p>
          <p class="mb-1"><?php echo htmlspecialchars($pair_home['product_name'], ENT_QUOTES, 'UTF-8'); ?> — <strong>PKR <?php echo number_format($home_price, 2); ?></strong></p>
          <hr class="my-2">
          <p class="mb-2 small text-muted mb-0">Your selected item (Shop)</p>
          <p class="mb-1"><?php echo htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8'); ?> — <strong>PKR <?php echo number_format($main_price, 2); ?></strong></p>
        </div>
        <h2 class="text-danger">Combined total: PKR <?php echo number_format($combined_price, 2); ?></h2>
        <form method="POST" action="cart.php" >
          <input type="hidden" name="product_id" value="<?php echo (int) $row['product_id']; ?>">
          <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($row['product_image'], ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="product_price" value="<?php echo htmlspecialchars((string) $row['product_price'], ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="pair_home_product_id" value="<?php echo (int) $pair_home['product_id']; ?>">
          <input type="hidden" name="pair_home_product_image" value="<?php echo htmlspecialchars($pair_home['product_image'], ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="pair_home_product_name" value="<?php echo htmlspecialchars($pair_home['product_name'], ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="pair_home_product_price" value="<?php echo htmlspecialchars((string) $pair_home['product_price'], ENT_QUOTES, 'UTF-8'); ?>">
          <button class="buy-btn" type="submit" name="add_to_cart" >Add to Cart</button>
        </form>
        <h4 class="mt-5 mb-3">Product details</h4>
        <span><?php echo htmlspecialchars($row['product_description'], ENT_QUOTES, 'UTF-8'); ?></span>
      </div>
    </div>
    <div class="row mt-4 pt-2">
      <div class="col-12 shop-selection-wide">
        <hr class="my-4">
        <p class="small text-muted mb-1">Color Variation</p>
        <h5 class="mb-3"><?php echo htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
        <div class="row g-3">
          <div class="col-6 col-lg-3">
            <img src="./<?php echo htmlspecialchars(product_image_url($row['product_image']), ENT_QUOTES, 'UTF-8'); ?>" class="shop-selection-thumb" alt="">
          </div>
          <div class="col-6 col-lg-3">
            <img src="./<?php echo htmlspecialchars(product_image_url($row['product_image2']), ENT_QUOTES, 'UTF-8'); ?>" class="shop-selection-thumb" alt="">
          </div>
          <div class="col-6 col-lg-3">
            <img src="./<?php echo htmlspecialchars(product_image_url($row['product_image3']), ENT_QUOTES, 'UTF-8'); ?>" class="shop-selection-thumb" alt="">
          </div>
          <div class="col-6 col-lg-3">
            <img src="./<?php echo htmlspecialchars(product_image_url($row['product_image4']), ENT_QUOTES, 'UTF-8'); ?>" class="shop-selection-thumb" alt="">
          </div>
        </div>
      </div>
    </div>
    <?php else: ?>
    <div class="row mt-5">
      <div class="col-lg-5 col-md-6 col-sm-12">
        <img class="img-fluid w-100 pb-1 " src="./<?php echo htmlspecialchars(product_image_url($row['product_image']), ENT_QUOTES, 'UTF-8'); ?>" id="mainimg">
        <div class="small-img-group">
          <div class="small-img-col">
            <img src="./<?php echo htmlspecialchars(product_image_url($row['product_image']), ENT_QUOTES, 'UTF-8'); ?>" width="100%" class="small-img">
          </div>
          <div class="small-img-col">
            <img src="./<?php echo htmlspecialchars(product_image_url($row['product_image2']), ENT_QUOTES, 'UTF-8'); ?>" width="100%" class="small-img">
          </div>
          <div class="small-img-col">
            <img src="./<?php echo htmlspecialchars(product_image_url($row['product_image3']), ENT_QUOTES, 'UTF-8'); ?>" width="100%" class="small-img">
          </div>
          <div class="small-img-col">
            <img src="./<?php echo htmlspecialchars(product_image_url($row['product_image4']), ENT_QUOTES, 'UTF-8'); ?>" width="100%" class="small-img">
          </div>
        </div>
      </div>
      <div class="col-lg-6 col-md-12 col-12">
        <h6>Men/Shoes</h6>
        <h3 class="py-4"><?php echo htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
        <h2>Pkr: <?php echo htmlspecialchars((string) $row['product_price'], ENT_QUOTES, 'UTF-8'); ?></h2>
        <form method="POST" action="cart.php" >
          <input type="hidden" name="product_id" value="<?php echo (int) $row['product_id']; ?>">
          <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($row['product_image'], ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="product_price" value="<?php echo htmlspecialchars((string) $row['product_price'], ENT_QUOTES, 'UTF-8'); ?>">
          <button class="buy-btn" type="submit" name="add_to_cart" >Add to Cart</button>
        </form>
        <h4 class="mt-5 mb-5">Product details</h4>
        <span><?php echo htmlspecialchars($row['product_description'], ENT_QUOTES, 'UTF-8'); ?></span>
      </div>
    </div>
    <?php endif; ?>
  </section>

  <?php if (!$pair_home): ?>
  <?php include __DIR__ . '/layouts/color-variation-section.php'; ?>
  <?php endif; ?>



 
     <script>
  window.addEventListener('DOMContentLoaded', function () {
    var mainimg = document.getElementById("mainimg");
    var smallimg = document.getElementsByClassName("small-img");
    if (mainimg && smallimg.length > 0) {
      for (let i = 0; i < smallimg.length; i++) {
        smallimg[i].onclick = function () {
          mainimg.src = this.src;
        };
      }
    }
    var designMain = document.getElementById("designMainImg");
    var designSmall = document.getElementsByClassName("design-small-img");
    if (designMain && designSmall.length > 0) {
      for (let j = 0; j < designSmall.length; j++) {
        designSmall[j].onclick = function () {
          designMain.src = this.src;
        };
      }
    }
  });
</script>
   <?php include('layouts/footer.php');

?>


