<?php
include 'server/connection.php';
require_once __DIR__ . '/server/product_image_helper.php';

$category = 'kurta';
$sliderMax = 15000;

$rangeStmt = $conn->prepare('SELECT COALESCE(MIN(product_price), 0) AS mn FROM products WHERE product_category = ?');
$rangeStmt->bind_param('s', $category);
$rangeStmt->execute();
$rangeRow = $rangeStmt->get_result()->fetch_assoc();
$rangeStmt->close();

$priceMinDb = (float) $rangeRow['mn'];
$sliderMin = max(0, (int) floor($priceMinDb / 100) * 100);
if ($sliderMin >= $sliderMax) {
    $sliderMin = 0;
}

$hasPriceFilter = isset($_GET['max_price']) && $_GET['max_price'] !== '' && is_numeric($_GET['max_price']);
$priceCap = $hasPriceFilter
    ? min($sliderMax, max(1, (float) $_GET['max_price']))
    : 99999999.99;

$sliderCurrent = $hasPriceFilter
    ? min(max((int) round($priceCap), $sliderMin), $sliderMax)
    : $sliderMax;

$page_no = isset($_GET['page_no']) && $_GET['page_no'] !== '' ? max(1, (int) $_GET['page_no']) : 1;

$total_records_per_page = 16;

$stmt1 = $conn->prepare('SELECT COUNT(*) AS c FROM products WHERE product_category = ? AND product_price <= ?');
$stmt1->bind_param('sd', $category, $priceCap);
$stmt1->execute();
$total_records = (int) $stmt1->get_result()->fetch_assoc()['c'];
$stmt1->close();

$total_no_of_pages = max(1, (int) ceil($total_records / $total_records_per_page));
$page_no = min($page_no, $total_no_of_pages);
$page_no = max(1, $page_no);
$offset = ($page_no - 1) * $total_records_per_page;
$previous_page = $page_no - 1;
$next_page = $page_no + 1;

$stmt2 = $conn->prepare('SELECT * FROM products WHERE product_category = ? AND product_price <= ? ORDER BY product_id DESC LIMIT ? OFFSET ?');
$stmt2->bind_param('sdii', $category, $priceCap, $total_records_per_page, $offset);
$stmt2->execute();
$products = $stmt2->get_result();
$stmt2->close();

$shopQuery = static function (array $params) {
    return 'shop.php?' . http_build_query($params);
};

$paginationBase = ['max_price' => $hasPriceFilter ? (string) (int) $sliderCurrent : null];
$paginationBase = array_filter($paginationBase, static function ($v) {
    return $v !== null && $v !== '';
});
?>

<?php include 'layouts/header.php'; ?>

<style>
  .product img {
    width: 100%;
    max-width: 100%;
    height: 300px;
    object-fit: cover;
    border-radius: 8px;
  }

  .shop-filter-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
    padding: 22px 20px;
    position: sticky;
    top: 100px;
  }

  .shop-filter-card h3 {
    font-size: 1.05rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 6px;
  }

  .shop-filter-card .shop-filter-lead {
    font-size: 13px;
    color: #6b7280;
    margin-bottom: 20px;
    line-height: 1.45;
  }

  .shop-filter-label {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #374151;
    margin-bottom: 10px;
    display: block;
  }

  .shop-price-display {
    font-size: 1.35rem;
    font-weight: 800;
    color: #fb774b;
    margin-bottom: 6px;
    font-variant-numeric: tabular-nums;
  }

  .shop-price-display small {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
    display: block;
    margin-top: 4px;
  }

  .shop-range-wrap {
    margin: 14px 0 8px;
  }

  .shop-range {
    width: 100%;
    height: 8px;
    border-radius: 999px;
    -webkit-appearance: none;
    appearance: none;
    background: linear-gradient(to right, #fed7aa 0%, #fb774b 100%);
    outline: none;
  }

  .shop-range::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #fff;
    border: 3px solid #fb774b;
    box-shadow: 0 2px 8px rgba(251, 119, 75, 0.45);
    cursor: pointer;
  }

  .shop-range::-moz-range-thumb {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #fff;
    border: 3px solid #fb774b;
    cursor: pointer;
  }

  .shop-range-labels {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: #9ca3af;
    margin-top: 6px;
  }

  .shop-price-input-row {
    display: flex;
    gap: 10px;
    align-items: center;
    margin-top: 14px;
  }

  .shop-price-input-row label {
    font-size: 13px;
    color: #4b5563;
    white-space: nowrap;
  }

  .shop-price-input {
    flex: 1;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 10px 12px;
    font-size: 14px;
    font-weight: 600;
    max-width: 140px;
  }

  .shop-filter-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 18px;
  }

  .shop-btn-apply {
    background: #fb774b;
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 12px 16px;
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.2s;
  }

  .shop-btn-apply:hover {
    background: #ea580c;
  }

  .shop-btn-reset {
    text-align: center;
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
    text-decoration: none;
    padding: 8px;
  }

  .shop-btn-reset:hover {
    color: #fb774b;
  }

  .shop-results-banner {
    background: #fff7f3;
    border: 1px solid #fed7aa;
    border-radius: 12px;
    padding: 12px 16px;
    margin-bottom: 18px;
    font-size: 14px;
    color: #9a3412;
  }

  .shop-empty {
    text-align: center;
    padding: 48px 20px;
    color: #6b7280;
    background: #f9fafb;
    border-radius: 12px;
    border: 1px dashed #e5e7eb;
  }
</style>

<hr class="fancy-hr">
<div class="container my-5 py-5">
  <div class="row">
    <hr class="fancy-hr">

    <div class="col-lg-3 col-md-4 col-sm-12 mb-4">
      <div class="shop-filter-card">
        <h3>Filter</h3>
        <p class="shop-filter-lead">Kurta collection — choose max budget from <strong>PKR <?php echo number_format($sliderMin); ?></strong> to <strong>PKR <?php echo number_format($sliderMax); ?></strong>. Apply to see products at or below that price.</p>

        <form method="get" action="shop.php" id="shop-filter-form">
          <span class="shop-filter-label">Max price (PKR)</span>
          <div class="shop-price-display">
            <span id="shop-price-live"><?php echo number_format($sliderCurrent); ?></span>
            <small id="shop-filter-hint"><?php echo $hasPriceFilter ? 'Products ≤ this price' : 'Range up to PKR 15,000 — Apply to filter, or clear to see all prices'; ?></small>
          </div>

          <div class="shop-range-wrap">
            <input type="range"
                   class="shop-range"
                   id="shop-max-price-range"
                   name="max_price"
                   min="<?php echo (int) $sliderMin; ?>"
                   max="<?php echo (int) $sliderMax; ?>"
                   step="100"
                   value="<?php echo (int) $sliderCurrent; ?>"
                   aria-valuemin="<?php echo (int) $sliderMin; ?>"
                   aria-valuemax="<?php echo (int) $sliderMax; ?>"
                   aria-valuenow="<?php echo (int) $sliderCurrent; ?>">
            <div class="shop-range-labels">
              <span><?php echo number_format($sliderMin); ?></span>
              <span><?php echo number_format($sliderMax); ?></span>
            </div>
          </div>

          <div class="shop-price-input-row">
            <label for="shop-max-price-input">Exact max</label>
            <input type="number"
                   class="shop-price-input"
                   id="shop-max-price-input"
                   min="<?php echo (int) $sliderMin; ?>"
                   max="<?php echo (int) $sliderMax; ?>"
                   step="1"
                   value="<?php echo (int) $sliderCurrent; ?>"
                   inputmode="numeric"
                   aria-label="Maximum price in PKR">
          </div>

          <div class="shop-filter-actions">
            <button type="submit" class="shop-btn-apply">Apply filter</button>
            <?php if ($hasPriceFilter): ?>
              <a class="shop-btn-reset" href="shop.php">Clear filter · show all prices</a>
            <?php endif; ?>
          </div>
        </form>
      </div>
    </div>

    <div class="col-lg-9 col-md-8 col-sm-12">
      <section id="featured">
        <h2><span>Kurta shop</span></h2>

        <div class="shop-results-banner">
          <?php if ($hasPriceFilter): ?>
            Showing <strong><?php echo number_format($total_records); ?></strong> kurta<?php echo $total_records !== 1 ? 's' : ''; ?> with price up to <strong>PKR <?php echo number_format($sliderCurrent); ?></strong>
            · Page <strong><?php echo $page_no; ?></strong> of <strong><?php echo $total_no_of_pages; ?></strong>
          <?php else: ?>
            Showing <strong><?php echo number_format($total_records); ?></strong> kurta<?php echo $total_records !== 1 ? 's' : ''; ?> (all prices)
            · Page <strong><?php echo $page_no; ?></strong> of <strong><?php echo $total_no_of_pages; ?></strong>
          <?php endif; ?>
        </div>

        <div class="row mx-auto container">
          <?php if ($products->num_rows === 0): ?>
            <div class="col-12 shop-empty">
              <p class="mb-2"><strong>No products in this range.</strong></p>
              <p class="mb-0">Try a higher amount (up to PKR <?php echo number_format($sliderMax); ?>) or <a href="shop.php">clear the filter</a> for all prices.</p>
            </div>
          <?php else: ?>
            <?php while ($product = $products->fetch_assoc()) { ?>
            <div onclick="window.location.href='single_product.php?product_id=<?php echo (int) $product['product_id']; ?>'"
                 class="product text-center col-lg-3 col-md-4 col-sm-12 mb-4">

              <img class="img-fluid mb-3" src="./<?php echo htmlspecialchars(product_image_url($product['product_image']), ENT_QUOTES, 'UTF-8'); ?>" alt="">

              <div class="p-price">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
              </div>

              <h5 class="p-name"><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
              <h4 class="p-price">PKR <?php echo htmlspecialchars((string) $product['product_price'], ENT_QUOTES, 'UTF-8'); ?></h4>
              <a href="single_product.php?product_id=<?php echo (int) $product['product_id']; ?>" class="btn buy-btn">Buy now</a>
            </div>
            <?php } ?>
          <?php endif; ?>
        </div>

        <?php if ($total_no_of_pages > 1): ?>
        <nav class="mt-5" aria-label="Shop pagination">
          <ul class="pagination justify-content-center flex-wrap">
              <li class="page-item <?php echo $page_no <= 1 ? 'disabled' : ''; ?>">
                  <a class="page-link" href="<?php echo $page_no <= 1 ? '#' : htmlspecialchars($shopQuery(array_merge($paginationBase, ['page_no' => $previous_page])), ENT_QUOTES, 'UTF-8'); ?>">Previous</a>
              </li>

              <?php
              $win = 2;
              $start = max(1, $page_no - $win);
              $end = min($total_no_of_pages, $page_no + $win);
              if ($start > 1) {
                  echo '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($shopQuery(array_merge($paginationBase, ['page_no' => 1])), ENT_QUOTES, 'UTF-8') . '">1</a></li>';
                  if ($start > 2) {
                      echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                  }
              }
              for ($i = $start; $i <= $end; $i++) {
                  $active = $i === $page_no ? ' active' : '';
                  echo '<li class="page-item' . $active . '"><a class="page-link" href="' . htmlspecialchars($shopQuery(array_merge($paginationBase, ['page_no' => $i])), ENT_QUOTES, 'UTF-8') . '">' . $i . '</a></li>';
              }
              if ($end < $total_no_of_pages) {
                  if ($end < $total_no_of_pages - 1) {
                      echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                  }
                  echo '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($shopQuery(array_merge($paginationBase, ['page_no' => $total_no_of_pages])), ENT_QUOTES, 'UTF-8') . '">' . $total_no_of_pages . '</a></li>';
              }
              ?>

              <li class="page-item <?php echo $page_no >= $total_no_of_pages ? 'disabled' : ''; ?>">
                  <a class="page-link" href="<?php echo $page_no >= $total_no_of_pages ? '#' : htmlspecialchars($shopQuery(array_merge($paginationBase, ['page_no' => $next_page])), ENT_QUOTES, 'UTF-8'); ?>">Next</a>
              </li>
          </ul>
        </nav>
        <?php endif; ?>
      </section>
    </div>
  </div>
</div>

<script>
(function () {
  var range = document.getElementById('shop-max-price-range');
  var input = document.getElementById('shop-max-price-input');
  var live = document.getElementById('shop-price-live');
  var hint = document.getElementById('shop-filter-hint');
  if (!range || !input || !live) return;

  function format(n) {
    return Number(n).toLocaleString('en-PK');
  }

  function syncFromRange() {
    var v = parseInt(range.value, 10);
    input.value = v;
    live.textContent = format(v);
    var maxR = parseInt(range.max, 10);
    hint.textContent = (v >= maxR)
      ? 'Maximum for search: PKR ' + format(maxR)
      : 'Products at or below this price';
    range.setAttribute('aria-valuenow', v);
  }

  function syncFromInput() {
    var min = parseInt(range.min, 10);
    var max = parseInt(range.max, 10);
    var v = parseInt(input.value, 10);
    if (isNaN(v)) v = min;
    v = Math.min(max, Math.max(min, v));
    input.value = v;
    range.value = v;
    syncFromRange();
  }

  range.addEventListener('input', syncFromRange);
  input.addEventListener('change', syncFromInput);
  input.addEventListener('blur', syncFromInput);

  document.getElementById('shop-filter-form').addEventListener('submit', function () {
    syncFromInput();
  });
})();
</script>

<?php include 'layouts/footer.php'; ?>
