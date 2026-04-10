
<?php
$nav_active = 'home';
require_once __DIR__ . '/server/product_image_helper.php';
include 'server/connection.php';

$show_all_kurtas_on_home = isset($_GET['all_kurtas']) && (string) $_GET['all_kurtas'] === '1';

$home_kurta_block1 = [];
$home_kurta_block2 = [];
$allKurta = [];

$category = 'kurta';
$sqlKurta = 'SELECT * FROM products WHERE product_category = ? ORDER BY product_id DESC';
if (!$show_all_kurtas_on_home) {
    $sqlKurta .= ' LIMIT 24';
}
$hkStmt = $conn->prepare($sqlKurta);
if ($hkStmt) {
    $hkStmt->bind_param('s', $category);
    $hkStmt->execute();
    $hkRes = $hkStmt->get_result();
    while ($row = $hkRes->fetch_assoc()) {
        $allKurta[] = $row;
    }
    $hkStmt->close();
    if (!$show_all_kurtas_on_home) {
        $home_kurta_block1 = array_slice($allKurta, 0, 12);
        $home_kurta_block2 = array_slice($allKurta, 12, 12);
    }
}

$home_kurta_all_url = 'index.php?all_kurtas=1#all-kurtas-home';
$home_kurta_preview_url = 'index.php';

include 'layouts/header.php';
?>
<style>
  .home-kurta-section .product img {
    width: 100%;
    max-width: 100%;
    height: 400px;
    object-fit: cover;
    border-radius: 8px;
  }
  .home-kurta-section h2 span {
    color: coral;
  }
  /* Kurta “shop all” CTA — inline so it always wins over Bootstrap link styles */
  section.home-kurta-section p.home-kurta-cta-row a.home-shop-all-kurtas-btn,
  section.home-kurta-section p.home-kurta-cta-row a.home-shop-all-kurtas-btn:visited {
    position: relative;
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    min-height: 3.35rem;
    padding: 0.85rem 0.85rem 0.85rem 1.85rem !important;
    margin-top: 0.25rem;
    font-size: 1rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.03em;
    color: #fff !important;
    text-decoration: none !important;
    border-radius: 999px !important;
    border: 2px solid rgba(255, 255, 255, 0.45) !important;
    background: linear-gradient(180deg, #ff9b78 0%, #fb774b 42%, #e85a32 100%) !important;
    box-shadow:
      0 5px 0 #b84322,
      0 10px 28px rgba(232, 90, 50, 0.42),
      inset 0 2px 0 rgba(255, 255, 255, 0.28) !important;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease, border-color 0.2s ease;
  }
  section.home-kurta-section p.home-kurta-cta-row a.home-shop-all-kurtas-btn::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: inherit;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.18) 0%, transparent 42%);
    pointer-events: none;
  }
  section.home-kurta-section p.home-kurta-cta-row a.home-shop-all-kurtas-btn:hover {
    color: #fff !important;
    text-decoration: none !important;
    transform: translateY(-3px);
    filter: brightness(1.06);
    border-color: rgba(255, 255, 255, 0.6) !important;
    box-shadow:
      0 7px 0 #9a3819,
      0 16px 36px rgba(232, 90, 50, 0.48),
      inset 0 2px 0 rgba(255, 255, 255, 0.32) !important;
  }
  section.home-kurta-section p.home-kurta-cta-row a.home-shop-all-kurtas-btn:active {
    transform: translateY(1px);
    box-shadow:
      0 2px 0 #b84322,
      0 6px 16px rgba(232, 90, 50, 0.35),
      inset 0 2px 0 rgba(255, 255, 255, 0.2) !important;
  }
  section.home-kurta-section p.home-kurta-cta-row a.home-shop-all-kurtas-btn:focus-visible {
    outline: 3px solid #fb774b;
    outline-offset: 4px;
  }
  section.home-kurta-section p.home-kurta-cta-row a.home-shop-all-kurtas-btn .home-shop-all-kurtas-btn__label {
    position: relative;
    z-index: 1;
  }
  section.home-kurta-section p.home-kurta-cta-row a.home-shop-all-kurtas-btn .home-shop-all-kurtas-btn__icon-wrap {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.35rem;
    height: 2.35rem;
    border-radius: 50%;
    font-size: 0.8rem;
    color: #fff !important;
    background: rgba(0, 0, 0, 0.22) !important;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.15);
    transition: transform 0.25s ease, background 0.25s ease;
  }
  section.home-kurta-section p.home-kurta-cta-row a.home-shop-all-kurtas-btn:hover .home-shop-all-kurtas-btn__icon-wrap {
    transform: translateX(4px);
    background: rgba(0, 0, 0, 0.32) !important;
  }
  section.home-kurta-section p.home-kurta-cta-row a.home-shop-all-kurtas-btn--secondary,
  section.home-kurta-section p.home-kurta-cta-row a.home-shop-all-kurtas-btn--secondary:visited {
    background: #fff !important;
    color: #fb774b !important;
    border-color: #fb774b !important;
    box-shadow:
      0 4px 0 #d9d9d9,
      0 8px 20px rgba(0, 0, 0, 0.08),
      inset 0 1px 0 rgba(255, 255, 255, 1) !important;
  }
  section.home-kurta-section p.home-kurta-cta-row a.home-shop-all-kurtas-btn--secondary .home-shop-all-kurtas-btn__icon-wrap {
    color: #fff !important;
    background: #fb774b !important;
  }
  section.home-kurta-section p.home-kurta-cta-row a.home-shop-all-kurtas-btn--secondary:hover {
    color: #fff !important;
    background: #fb774b !important;
    border-color: rgba(255, 255, 255, 0.45) !important;
    filter: none;
    box-shadow:
      0 5px 0 #b84322,
      0 10px 28px rgba(232, 90, 50, 0.42),
      inset 0 2px 0 rgba(255, 255, 255, 0.28) !important;
  }
  section.home-kurta-section p.home-kurta-cta-row a.home-shop-all-kurtas-btn--secondary:hover .home-shop-all-kurtas-btn__icon-wrap {
    background: rgba(0, 0, 0, 0.22) !important;
    color: #fff !important;
  }
  /* Mid banner embedded in “all kurtas” grid: full-bleed like standalone #banner */
  .home-kurta-section .home-mid-banner-in-kurta-flow {
    width: 100vw;
    max-width: 100vw;
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    box-sizing: border-box;
  }
</style>

<!--- Home Section -->
<section id="home" >
<div  class="container">
<h5 >NEW ARRIVAL</h5>
<h1><span> Best Prices </span>This Season</h1>
<p>Shopping Zone offers the best products at the most affordable prices.</p>

</div>
</section>
<!--- brands (full-width strip like banner edge-to-edge) -->
<section id="brands" class="container-fluid home-brands-wide px-3 px-md-4 px-xl-5 py-3">
<div class="row g-3 g-lg-4 align-items-center justify-content-center">
    <div class="col-6 col-md-3 text-center home-brand-cell">
      <img class="img-fluid home-brand-img" src="assets/images/OIP (3).jpg" alt="">
    </div>
    <div class="col-6 col-md-3 text-center home-brand-cell">
      <img class="img-fluid home-brand-img" src="assets/images/the-new-yorker1866.jpg" alt="">
    </div>
    <div class="col-6 col-md-3 text-center home-brand-cell">
      <img class="img-fluid home-brand-img" src="assets/images/OIP (4).jpg" alt="">
    </div>
    <div class="col-6 col-md-3 text-center home-brand-cell">
      <img class="img-fluid home-brand-img" src="assets/images/Coach-logo-1080x451.jpg" alt="">
    </div>
</div>
</section>

<?php if ($show_all_kurtas_on_home && count($allKurta) > 0):
    $kurta_all_split = (int) ceil(count($allKurta) / 2);
    $kurta_all_first = array_slice($allKurta, 0, $kurta_all_split);
    $kurta_all_second = array_slice($allKurta, $kurta_all_split);
    ?>
<section id="all-kurtas-home" class="home-kurta-section container-fluid px-3 px-md-4 px-xl-5 my-5 py-4" aria-label="All kurta designs">
  <h2 class="mb-4">All <span>kurta</span> designs</h2>
  <div class="row g-3 g-lg-4">
    <?php foreach ($kurta_all_first as $product): ?>
    <div onclick="window.location.href='shop.php?home_pick=<?php echo (int) $product['product_id']; ?>'"
         class="product product--home text-center col-12 col-sm-6 col-md-4 col-lg-3 mb-0 d-flex">
      <div class="home-product-card-inner w-100 d-flex flex-column h-100">
        <div class="home-product-img-wrap">
          <img src="./<?php echo htmlspecialchars(product_image_url($product['product_image']), ENT_QUOTES, 'UTF-8'); ?>" alt="" width="400" height="500">
        </div>
        <div class="home-product-body flex-grow-1 d-flex flex-column text-center">
          <div class="p-price home-product-stars mb-2">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
          </div>
          <h5 class="p-name"><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
          <h4 class="p-price mb-2">PKR <?php echo htmlspecialchars((string) $product['product_price'], ENT_QUOTES, 'UTF-8'); ?></h4>
          <a href="shop.php?home_pick=<?php echo (int) $product['product_id']; ?>" class="btn buy-btn mt-auto align-self-center">Select Design</a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <!--- midbanner (center of home kurta grid when showing all) -->
  <section id="banner" class="home-mid-banner-in-kurta-flow my-5 py-5">
  <div class="container">
  <h4> MId SEASON'S SALE</h4>
  <h1>Autumn Collection <br> UP to 30% OFF Up Comming </h1>
  </div>
  </section>
  <?php if (count($kurta_all_second) > 0): ?>
  <div class="row g-3 g-lg-4">
    <?php foreach ($kurta_all_second as $product): ?>
    <div onclick="window.location.href='shop.php?home_pick=<?php echo (int) $product['product_id']; ?>'"
         class="product product--home text-center col-12 col-sm-6 col-md-4 col-lg-3 mb-0 d-flex">
      <div class="home-product-card-inner w-100 d-flex flex-column h-100">
        <div class="home-product-img-wrap">
          <img src="./<?php echo htmlspecialchars(product_image_url($product['product_image']), ENT_QUOTES, 'UTF-8'); ?>" alt="" width="400" height="500">
        </div>
        <div class="home-product-body flex-grow-1 d-flex flex-column text-center">
          <div class="p-price home-product-stars mb-2">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
          </div>
          <h5 class="p-name"><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
          <h4 class="p-price mb-2">PKR <?php echo htmlspecialchars((string) $product['product_price'], ENT_QUOTES, 'UTF-8'); ?></h4>
          <a href="shop.php?home_pick=<?php echo (int) $product['product_id']; ?>" class="btn buy-btn mt-auto align-self-center">Select Design</a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
  <p class="text-center mt-4 mb-0 home-kurta-cta-row">
    <a href="<?php echo htmlspecialchars($home_kurta_preview_url, ENT_QUOTES, 'UTF-8'); ?>" class="home-shop-all-kurtas-btn home-shop-all-kurtas-btn--secondary">
      <span class="home-shop-all-kurtas-btn__label">Show fewer on home</span>
      <span class="home-shop-all-kurtas-btn__icon-wrap" aria-hidden="true"><i class="fas fa-arrow-left"></i></span>
    </a>
  </p>
</section>
<?php endif; ?>

<?php if (!$show_all_kurtas_on_home && count($home_kurta_block1) > 0): ?>
<section class="home-kurta-section container-fluid px-3 px-md-4 px-xl-5 my-5 py-4" aria-label="Kurta collection">
  <h2 class="mb-4"><span>Kurta</span> Designs</h2>
  <div class="row g-3 g-lg-4">
    <?php foreach ($home_kurta_block1 as $product): ?>
    <div onclick="window.location.href='shop.php?home_pick=<?php echo (int) $product['product_id']; ?>'"
         class="product product--home text-center col-12 col-sm-6 col-md-4 col-lg-3 mb-0 d-flex">
      <div class="home-product-card-inner w-100 d-flex flex-column h-100">
        <div class="home-product-img-wrap">
          <img src="./<?php echo htmlspecialchars(product_image_url($product['product_image']), ENT_QUOTES, 'UTF-8'); ?>" alt="" width="400" height="500">
        </div>
        <div class="home-product-body flex-grow-1 d-flex flex-column text-center">
          <div class="p-price home-product-stars mb-2">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
          </div>
          <h5 class="p-name"><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
          <h4 class="p-price mb-2">PKR <?php echo htmlspecialchars((string) $product['product_price'], ENT_QUOTES, 'UTF-8'); ?></h4>
          <a href="shop.php?home_pick=<?php echo (int) $product['product_id']; ?>" class="btn buy-btn mt-auto align-self-center">Select Design</a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php if (count($home_kurta_block2) === 0): ?>
  <p class="text-center mt-4 mb-0 home-kurta-cta-row">
    <a href="<?php echo htmlspecialchars($home_kurta_all_url, ENT_QUOTES, 'UTF-8'); ?>" class="home-shop-all-kurtas-btn">
      <span class="home-shop-all-kurtas-btn__label">Show all kurta Designs</span>
      <span class="home-shop-all-kurtas-btn__icon-wrap" aria-hidden="true"><i class="fas fa-arrow-right"></i></span>
    </a>
  </p>
  <?php endif; ?>
</section>
<?php endif; ?>

<?php if (!$show_all_kurtas_on_home): ?>
<!--- midbanner (between kurta blocks on normal home) -->
<section id="banner" class="my-5 py-5">
<div class="container">
<h4> MId SEASON'S SALE</h4>
<h1>Autumn Collection <br> UP to 30% OFF Up Comming </h1>

</div>

</section>
<?php endif; ?>

<?php if (!$show_all_kurtas_on_home && count($home_kurta_block2) > 0): ?>
<section class="home-kurta-section container-fluid px-3 px-md-4 px-xl-5 my-5 py-4" aria-label="More kurta picks">
  <h2 class="mb-4">More <span>kurta</span> picks</h2>
  <div class="row g-3 g-lg-4">
    <?php foreach ($home_kurta_block2 as $product): ?>
    <div onclick="window.location.href='shop.php?home_pick=<?php echo (int) $product['product_id']; ?>'"
         class="product product--home text-center col-12 col-sm-6 col-md-4 col-lg-3 mb-0 d-flex">
      <div class="home-product-card-inner w-100 d-flex flex-column h-100">
        <div class="home-product-img-wrap">
          <img src="./<?php echo htmlspecialchars(product_image_url($product['product_image']), ENT_QUOTES, 'UTF-8'); ?>" alt="" width="400" height="500">
        </div>
        <div class="home-product-body flex-grow-1 d-flex flex-column text-center">
          <div class="p-price home-product-stars mb-2">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
          </div>
          <h5 class="p-name"><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
          <h4 class="p-price mb-2">PKR <?php echo htmlspecialchars((string) $product['product_price'], ENT_QUOTES, 'UTF-8'); ?></h4>
          <a href="shop.php?home_pick=<?php echo (int) $product['product_id']; ?>" class="btn buy-btn mt-auto align-self-center">Select Design</a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <p class="text-center mt-4 mb-0 home-kurta-cta-row">
    <a href="<?php echo htmlspecialchars($home_kurta_all_url, ENT_QUOTES, 'UTF-8'); ?>" class="home-shop-all-kurtas-btn">
      <span class="home-shop-all-kurtas-btn__label">Show all kurta Designs</span>
      <span class="home-shop-all-kurtas-btn__icon-wrap" aria-hidden="true"><i class="fas fa-arrow-right"></i></span>
    </a>
  </p>
</section>
<?php endif; ?>



<?php include('layouts/footer.php');

?>
