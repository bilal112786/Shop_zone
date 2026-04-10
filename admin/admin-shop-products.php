<?php
/**
 * Manage products on the storefront shop page (category = shop).
 */
session_start();
include '../server/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

require_once __DIR__ . '/includes/paths.php';
require_once __DIR__ . '/../server/product_image_helper.php';

$shopCategory = 'shop';
$perPage = 24;
$page = max(1, (int) ($_GET['page'] ?? 1));
$search = trim((string) ($_GET['q'] ?? ''));

$total = 0;
$like = '';
if ($search !== '') {
    $like = '%' . $search . '%';
    $stmt = $conn->prepare(
        'SELECT COUNT(*) AS c FROM products WHERE LOWER(TRIM(product_category)) = ? AND (product_name LIKE ? OR CAST(product_id AS CHAR) = ?)'
    );
    $stmt->bind_param('sss', $shopCategory, $like, $search);
    $stmt->execute();
    $total = (int) $stmt->get_result()->fetch_assoc()['c'];
    $stmt->close();
} else {
    $stmt = $conn->prepare('SELECT COUNT(*) AS c FROM products WHERE LOWER(TRIM(product_category)) = ?');
    $stmt->bind_param('s', $shopCategory);
    $stmt->execute();
    $total = (int) $stmt->get_result()->fetch_assoc()['c'];
    $stmt->close();
}

$totalPages = max(1, (int) ceil($total / $perPage));
if ($page > $totalPages) {
    $page = $totalPages;
}
$offset = ($page - 1) * $perPage;

$result = null;
if ($search !== '') {
    $stmt = $conn->prepare(
        'SELECT * FROM products WHERE LOWER(TRIM(product_category)) = ? AND (product_name LIKE ? OR CAST(product_id AS CHAR) = ?) ORDER BY product_id DESC LIMIT ? OFFSET ?'
    );
    $stmt->bind_param('sssii', $shopCategory, $like, $search, $perPage, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $stmt = $conn->prepare(
        'SELECT * FROM products WHERE LOWER(TRIM(product_category)) = ? ORDER BY product_id DESC LIMIT ? OFFSET ?'
    );
    $stmt->bind_param('sii', $shopCategory, $perPage, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
}

$shopQuery = static function (array $extra = []) use ($search) {
    $q = $extra;
    if ($search !== '') {
        $q['q'] = $search;
    }
    return 'admin-shop-products.php' . ($q !== [] ? '?' . http_build_query($q) : '');
};

$pageTitle = 'Shop page products';
$navActive = 'page-shop';
include 'includes/admin-header.php';
?>

<div class="admin-page admin-page-flush">
  <div class="admin-card admin-card-products">
    <div class="title-row">
      <h1>Shop page products</h1>
      <div class="top-actions">
        <a href="<?php echo htmlspecialchars(admin_asset_url('admin-add-products.php?for=shop'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-success">Add shop product</a>
        <a href="<?php echo htmlspecialchars(storefront_url('shop.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-secondary" target="_blank" rel="noopener">View live shop</a>
      </div>
    </div>

    <p class="admin-hint">Products here use category <strong>shop</strong> — the same list shown on your storefront <code>shop.php</code> page.</p>

    <?php if (isset($_GET['success'])): ?>
      <p class="notice success"><?php echo htmlspecialchars((string) $_GET['success']); ?></p>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
      <p class="notice error"><?php echo htmlspecialchars((string) $_GET['error']); ?></p>
    <?php endif; ?>

    <div class="products-toolbar">
      <form class="products-search-form" method="get" action="admin-shop-products.php">
        <input type="search" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search shop products by name or ID…" class="products-search-input" autocomplete="off">
        <button type="submit" class="btn btn-primary btn-sm">Search</button>
        <?php if ($search !== ''): ?>
          <a href="admin-shop-products.php" class="btn btn-secondary btn-sm">Clear</a>
        <?php endif; ?>
      </form>
      <div class="products-meta">
        <span class="products-count"><?php echo number_format($total); ?> shop product<?php echo $total !== 1 ? 's' : ''; ?></span>
        <?php if ($totalPages > 1): ?>
          <span class="products-page-hint">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
        <?php endif; ?>
      </div>
    </div>

    <div class="products-table-scroll">
      <table class="products-table">
        <thead>
          <tr>
            <th class="col-id">ID</th>
            <th class="col-name">Name</th>
            <th class="col-price">Price (PKR)</th>
            <th class="col-img">Image</th>
            <th class="col-actions">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td class="col-id"><?php echo (int) $row['product_id']; ?></td>
                <td class="col-name td-left"><?php echo htmlspecialchars($row['product_name']); ?></td>
                <td class="col-price"><?php echo number_format((float) $row['product_price'], 2); ?></td>
                <td class="col-img">
                  <?php if (!empty($row['product_image'])): ?>
                    <img class="product-thumb" src="<?php echo htmlspecialchars(product_image_url_from_admin($row['product_image']), ENT_QUOTES, 'UTF-8'); ?>" alt="" width="48" height="48" loading="lazy">
                  <?php else: ?>
                    <span class="product-thumb-empty">—</span>
                  <?php endif; ?>
                </td>
                <td class="actions col-actions">
                  <a class="btn btn-success btn-sm" href="<?php echo htmlspecialchars(admin_asset_url('admin-edit-products.php?id=' . (int) $row['product_id'] . '&return=shop'), ENT_QUOTES, 'UTF-8'); ?>">Edit</a>
                  <a class="btn btn-danger btn-sm" href="<?php echo htmlspecialchars(admin_asset_url('admin-delete-products.php?id=' . (int) $row['product_id'] . '&from=shop'), ENT_QUOTES, 'UTF-8'); ?>" onclick="return confirm('Delete this product from the shop?')">Delete</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="products-empty"><?php echo $search !== '' ? 'No shop products match your search.' : 'No shop products yet. Add one — category will be saved as <strong>shop</strong>.'; ?></td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <nav class="products-pagination" aria-label="Shop product pages">
      <?php if ($page > 1): ?>
        <a class="btn btn-secondary btn-sm" href="<?php echo htmlspecialchars($shopQuery(['page' => $page - 1]), ENT_QUOTES, 'UTF-8'); ?>">← Previous</a>
      <?php endif; ?>
      <span class="pagination-pages">
        <?php
        $window = 2;
        $start = max(1, $page - $window);
        $end = min($totalPages, $page + $window);
        if ($start > 1) {
            echo '<a class="pagination-num" href="' . htmlspecialchars($shopQuery(['page' => 1]), ENT_QUOTES, 'UTF-8') . '">1</a>';
            if ($start > 2) {
                echo '<span class="pagination-ellipsis">…</span>';
            }
        }
        for ($i = $start; $i <= $end; $i++) {
            if ($i === $page) {
                echo '<span class="pagination-num is-current" aria-current="page">' . $i . '</span>';
            } else {
                echo '<a class="pagination-num" href="' . htmlspecialchars($shopQuery(['page' => $i]), ENT_QUOTES, 'UTF-8') . '">' . $i . '</a>';
            }
        }
        if ($end < $totalPages) {
            if ($end < $totalPages - 1) {
                echo '<span class="pagination-ellipsis">…</span>';
            }
            echo '<a class="pagination-num" href="' . htmlspecialchars($shopQuery(['page' => $totalPages]), ENT_QUOTES, 'UTF-8') . '">' . $totalPages . '</a>';
        }
        ?>
      </span>
      <?php if ($page < $totalPages): ?>
        <a class="btn btn-secondary btn-sm" href="<?php echo htmlspecialchars($shopQuery(['page' => $page + 1]), ENT_QUOTES, 'UTF-8'); ?>">Next →</a>
      <?php endif; ?>
    </nav>
    <?php endif; ?>

  </div>
</div>
<?php include 'includes/admin-footer.php'; ?>
