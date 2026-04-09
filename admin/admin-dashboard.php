<?php
session_start();
include '../server/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

$pageTitle = 'Dashboard';
$navActive = 'dashboard';

$userCount = 0;
$productCount = 0;
$orderCount = 0;
$revenueTotal = 0.0;

$r = $conn->query('SELECT COUNT(*) AS c FROM users');
if ($r && $row = $r->fetch_assoc()) {
    $userCount = (int) $row['c'];
}
$r = $conn->query('SELECT COUNT(*) AS c FROM products');
if ($r && $row = $r->fetch_assoc()) {
    $productCount = (int) $row['c'];
}
$r = $conn->query('SELECT COUNT(*) AS c FROM orders');
if ($r && $row = $r->fetch_assoc()) {
    $orderCount = (int) $row['c'];
}
$r = $conn->query('SELECT COALESCE(SUM(order_cost), 0) AS r FROM orders');
if ($r && $row = $r->fetch_assoc()) {
    $revenueTotal = (float) $row['r'];
}

$orderMonthsLabels = [];
$orderMonthsData = [];
$q = $conn->query("SELECT DATE_FORMAT(order_date, '%Y-%m') AS ym, COUNT(*) AS cnt
    FROM orders
    WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY ym ORDER BY ym ASC");
if ($q) {
    while ($row = $q->fetch_assoc()) {
        $orderMonthsLabels[] = $row['ym'];
        $orderMonthsData[] = (int) $row['cnt'];
    }
}

$statusLabels = [];
$statusData = [];
$q = $conn->query('SELECT order_status, COUNT(*) AS c FROM orders GROUP BY order_status ORDER BY c DESC');
if ($q) {
    while ($row = $q->fetch_assoc()) {
        $statusLabels[] = $row['order_status'];
        $statusData[] = (int) $row['c'];
    }
}

$catLabels = [];
$catData = [];
$q = $conn->query('SELECT product_category, COUNT(*) AS c FROM products GROUP BY product_category ORDER BY c DESC LIMIT 12');
if ($q) {
    while ($row = $q->fetch_assoc()) {
        $catLabels[] = $row['product_category'];
        $catData[] = (int) $row['c'];
    }
}

$jsonSafe = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
$overviewJson = json_encode([
    'labels' => ['Users', 'Products', 'Orders'],
    'data' => [$userCount, $productCount, $orderCount],
], $jsonSafe);
$monthsJson = json_encode(['labels' => $orderMonthsLabels, 'data' => $orderMonthsData], $jsonSafe);
$statusJson = json_encode(['labels' => $statusLabels, 'data' => $statusData], $jsonSafe);
$catJson = json_encode(['labels' => $catLabels, 'data' => $catData], $jsonSafe);

$chartPrimary = '#fb774b';
$chartMuted = ['#94a3b8', '#cbd5e1', '#64748b', '#f97316', '#ea580c', '#c2410c', '#fdba74', '#fed7aa'];

include 'includes/admin-header.php';
?>

<div class="admin-dashboard">
    <div class="dashboard-welcome">
        <h1 class="dashboard-heading">Welcome back, <?php echo htmlspecialchars((string) ($_SESSION['admin_name'] ?? 'Admin'), ENT_QUOTES, 'UTF-8'); ?></h1>
        <p class="dashboard-sub">Overview of users, products, and orders at a glance.</p>
    </div>

    <div class="stat-grid">
        <article class="stat-card stat-card-users">
            <div class="stat-card-label">Total users</div>
            <div class="stat-card-value"><?php echo number_format($userCount); ?></div>
        </article>
        <article class="stat-card stat-card-products">
            <div class="stat-card-label">Products</div>
            <div class="stat-card-value"><?php echo number_format($productCount); ?></div>
        </article>
        <article class="stat-card stat-card-orders">
            <div class="stat-card-label">Orders</div>
            <div class="stat-card-value"><?php echo number_format($orderCount); ?></div>
        </article>
        <article class="stat-card stat-card-revenue">
            <div class="stat-card-label">Order revenue</div>
            <div class="stat-card-value stat-card-value-sm">PKR <?php echo number_format($revenueTotal, 0); ?></div>
        </article>
    </div>

    <div class="chart-grid">
        <div class="chart-card">
            <h2 class="chart-card-title">Users, products &amp; orders</h2>
            <p class="chart-card-desc">Total counts compared side by side.</p>
            <div class="chart-canvas-wrap">
                <canvas id="chartOverview" height="220" aria-label="Bar chart of users products and orders"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <h2 class="chart-card-title">Orders by month</h2>
            <p class="chart-card-desc">Last 6 months (by order date).</p>
            <div class="chart-canvas-wrap">
                <canvas id="chartOrdersMonth" height="220" aria-label="Line chart of orders by month"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <h2 class="chart-card-title">Orders by status</h2>
            <p class="chart-card-desc">Share of each order status.</p>
            <div class="chart-canvas-wrap chart-canvas-wrap-doughnut">
                <canvas id="chartOrderStatus" height="260" aria-label="Doughnut chart of order status"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <h2 class="chart-card-title">Products by category</h2>
            <p class="chart-card-desc">Up to 12 categories by inventory count.</p>
            <div class="chart-canvas-wrap">
                <canvas id="chartCategories" height="260" aria-label="Bar chart of products by category"></canvas>
            </div>
        </div>
    </div>

    <div class="dashboard-quick-links">
        <a class="btn btn-secondary" href="admin-products.php">Manage products</a>
        <a class="btn btn-secondary" href="admin-users.php">Manage users</a>
        <a class="btn btn-secondary" href="admin-orders.php">View orders</a>
    </div>
</div>

<?php
$extraBodyEnd = <<<'HTML'
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
  var overview = OVERVIEW_PLACEHOLDER;
  var months = MONTHS_PLACEHOLDER;
  var status = STATUS_PLACEHOLDER;
  var categories = CATEGORIES_PLACEHOLDER;
  var primary = PRIMARY_PLACEHOLDER;
  var palette = PALETTE_PLACEHOLDER;

  Chart.defaults.font.family = '"Segoe UI", Tahoma, Arial, sans-serif';
  Chart.defaults.color = '#64748b';

  new Chart(document.getElementById('chartOverview'), {
    type: 'bar',
    data: {
      labels: overview.labels,
      datasets: [{
        label: 'Count',
        data: overview.data,
        backgroundColor: [palette[0], palette[1], primary],
        borderRadius: 8,
        maxBarThickness: 56
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, ticks: { precision: 0 } },
        x: { grid: { display: false } }
      }
    }
  });

  new Chart(document.getElementById('chartOrdersMonth'), {
    type: 'line',
    data: {
      labels: months.labels.length ? months.labels : ['No data'],
      datasets: [{
        label: 'Orders',
        data: months.labels.length ? months.data : [0],
        borderColor: primary,
        backgroundColor: 'rgba(251, 119, 75, 0.12)',
        fill: true,
        tension: 0.35,
        pointRadius: 4,
        pointBackgroundColor: primary
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, ticks: { precision: 0 } },
        x: { grid: { display: false } }
      }
    }
  });

  var statusColors = status.labels.map(function (_, i) {
    return palette[i % palette.length];
  });
  new Chart(document.getElementById('chartOrderStatus'), {
    type: 'doughnut',
    data: {
      labels: status.labels.length ? status.labels : ['No orders'],
      datasets: [{
        data: status.labels.length ? status.data : [1],
        backgroundColor: status.labels.length ? statusColors : ['#e2e8f0'],
        borderWidth: 0
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '62%',
      plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12 } }
      }
    }
  });

  new Chart(document.getElementById('chartCategories'), {
    type: 'bar',
    data: {
      labels: categories.labels.length ? categories.labels : ['No products'],
      datasets: [{
        label: 'Products',
        data: categories.labels.length ? categories.data : [0],
        backgroundColor: categories.labels.map(function (_, i) {
          return i === 0 ? primary : palette[(i + 2) % palette.length];
        }),
        borderRadius: 6,
        maxBarThickness: 28
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { beginAtZero: true, ticks: { precision: 0 } },
        y: { grid: { display: false } }
      }
    }
  });
})();
</script>
HTML;

$extraBodyEnd = str_replace(
    [
        'OVERVIEW_PLACEHOLDER',
        'MONTHS_PLACEHOLDER',
        'STATUS_PLACEHOLDER',
        'CATEGORIES_PLACEHOLDER',
        'PRIMARY_PLACEHOLDER',
        'PALETTE_PLACEHOLDER',
    ],
    [
        $overviewJson,
        $monthsJson,
        $statusJson,
        $catJson,
        json_encode($chartPrimary, $jsonSafe),
        json_encode($chartMuted, $jsonSafe),
    ],
    $extraBodyEnd
);

include 'includes/admin-footer.php';
