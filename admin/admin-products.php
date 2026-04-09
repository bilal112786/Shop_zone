<?php
session_start();
include '../server/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

require_once __DIR__ . '/includes/paths.php';
require_once __DIR__ . '/../server/product_image_helper.php';

$perPage = 12;
$page = max(1, (int) ($_GET['page'] ?? 1));
$search = trim((string) ($_GET['q'] ?? ''));

$total = 0;
$like = '';
if ($search !== '') {
    $like = '%' . $search . '%';
    $stmt = $conn->prepare('SELECT COUNT(*) AS c FROM products WHERE (product_name LIKE ? OR product_category LIKE ? OR CAST(product_id AS CHAR) = ?)');
    $stmt->bind_param('sss', $like, $like, $search);
    $stmt->execute();
    $total = (int) $stmt->get_result()->fetch_assoc()['c'];
    $stmt->close();
} else {
    $r = $conn->query('SELECT COUNT(*) AS c FROM products');
    if ($r) {
        $total = (int) $r->fetch_assoc()['c'];
    }
}

$totalPages = max(1, (int) ceil($total / $perPage));
if ($page > $totalPages) {
    $page = $totalPages;
}
$offset = ($page - 1) * $perPage;

$result = null;
if ($search !== '') {
    $stmt = $conn->prepare('SELECT * FROM products WHERE (product_name LIKE ? OR product_category LIKE ? OR CAST(product_id AS CHAR) = ?) ORDER BY product_id DESC LIMIT ? OFFSET ?');
    $stmt->bind_param('sssii', $like, $like, $search, $perPage, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $stmt = $conn->prepare('SELECT * FROM products ORDER BY product_id DESC LIMIT ? OFFSET ?');
    $stmt->bind_param('ii', $perPage, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
}

$productsQuery = static function (array $extra = []) use ($search) {
    $q = $extra;
    if ($search !== '') {
        $q['q'] = $search;
    }

    return 'admin-products.php' . ($q !== [] ? '?' . http_build_query($q) : '');
};

$pageTitle = 'Products';
$navActive = 'products';
include 'includes/admin-header.php';
?>

  <div class="admin-page admin-page-flush">
    <div class="admin-card admin-card-products">
    <div class="title-row">
      <h1>Products</h1>
      <div class="top-actions">
        <a href="admin-add-products.php" class="btn btn-success">Add Product</a>
      </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <p class="notice success"><?php echo htmlspecialchars($_GET['success']); ?></p>
    <?php endif; ?>

    <div class="products-toolbar">
      <form class="products-search-form" method="get" action="admin-products.php">
        <input type="search" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name, category, or ID…" class="products-search-input" autocomplete="off">
        <button type="submit" class="btn btn-primary btn-sm">Search</button>
        <?php if ($search !== ''): ?>
          <a href="admin-products.php" class="btn btn-secondary btn-sm">Clear</a>
        <?php endif; ?>
      </form>
      <div class="products-meta">
        <span class="products-count"><?php echo number_format($total); ?> product<?php echo $total !== 1 ? 's' : ''; ?></span>
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
            <th class="col-cat">Category</th>
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
                    <td class="col-cat"><?php echo htmlspecialchars($row['product_category']); ?></td>
                    <td class="col-price"><?php echo number_format((float) $row['product_price'], 2); ?></td>
                    <td class="col-img">
                        <?php if (!empty($row['product_image'])): ?>
                            <img class="product-thumb" src="<?php echo htmlspecialchars(product_image_url_from_admin($row['product_image']), ENT_QUOTES, 'UTF-8'); ?>" alt="" width="48" height="48" loading="lazy">
                        <?php else: ?>
                            <span class="product-thumb-empty">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="actions col-actions">
                        <a class="btn btn-success btn-sm" href="<?php echo htmlspecialchars(admin_asset_url('admin-edit-products.php?id=' . (int) $row['product_id']), ENT_QUOTES, 'UTF-8'); ?>">Edit</a>
                        <a class="btn btn-danger btn-sm" href="<?php echo htmlspecialchars(admin_asset_url('admin-delete-products.php?id=' . (int) $row['product_id']), ENT_QUOTES, 'UTF-8'); ?>" onclick="return confirm('Delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="products-empty"><?php echo $search !== '' ? 'No products match your search.' : 'No products found.'; ?></td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <nav class="products-pagination" aria-label="Product pages">
      <?php if ($page > 1): ?>
        <a class="btn btn-secondary btn-sm" href="<?php echo htmlspecialchars($productsQuery(['page' => $page - 1]), ENT_QUOTES, 'UTF-8'); ?>">← Previous</a>
      <?php endif; ?>
      <span class="pagination-pages">
        <?php
        $window = 2;
        $start = max(1, $page - $window);
        $end = min($totalPages, $page + $window);
        if ($start > 1) {
            echo '<a class="pagination-num" href="' . htmlspecialchars($productsQuery(['page' => 1]), ENT_QUOTES, 'UTF-8') . '">1</a>';
            if ($start > 2) {
                echo '<span class="pagination-ellipsis">…</span>';
            }
        }
        for ($i = $start; $i <= $end; $i++) {
            if ($i === $page) {
                echo '<span class="pagination-num is-current" aria-current="page">' . $i . '</span>';
            } else {
                echo '<a class="pagination-num" href="' . htmlspecialchars($productsQuery(['page' => $i]), ENT_QUOTES, 'UTF-8') . '">' . $i . '</a>';
            }
        }
        if ($end < $totalPages) {
            if ($end < $totalPages - 1) {
                echo '<span class="pagination-ellipsis">…</span>';
            }
            echo '<a class="pagination-num" href="' . htmlspecialchars($productsQuery(['page' => $totalPages]), ENT_QUOTES, 'UTF-8') . '">' . $totalPages . '</a>';
        }
        ?>
      </span>
      <?php if ($page < $totalPages): ?>
        <a class="btn btn-secondary btn-sm" href="<?php echo htmlspecialchars($productsQuery(['page' => $page + 1]), ENT_QUOTES, 'UTF-8'); ?>">Next →</a>
      <?php endif; ?>
    </nav>
    <?php endif; ?>

    </div>
  </div>
<?php include 'includes/admin-footer.php'; ?>
