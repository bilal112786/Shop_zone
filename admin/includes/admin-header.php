<?php
require_once __DIR__ . '/paths.php';

if (!isset($pageTitle)) {
    $pageTitle = 'Admin';
}
if (!isset($navActive)) {
    $navActive = '';
}
$pagesNavOpen = in_array($navActive, ['page-home', 'page-shop'], true);
$adminName = isset($_SESSION['admin_name']) ? htmlspecialchars((string) $_SESSION['admin_name'], ENT_QUOTES, 'UTF-8') : 'Admin';
$adminCss = htmlspecialchars(admin_asset_url('admin-style.css?v=7'), ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?> · Admin</title>
    <link rel="stylesheet" href="<?php echo $adminCss; ?>">
</head>
<body class="admin-app">
    <aside class="admin-sidebar" aria-label="Admin navigation">
        <div class="admin-brand">
            <span class="admin-brand-mark">◆</span>
            <div>
                <div class="admin-brand-title">Store Admin</div>
                <div class="admin-brand-sub">Control panel</div>
            </div>
        </div>
        <nav class="admin-side-nav">
            <a class="admin-nav-link<?php echo $navActive === 'dashboard' ? ' is-active' : ''; ?>" href="<?php echo htmlspecialchars(admin_asset_url('admin-dashboard.php'), ENT_QUOTES, 'UTF-8'); ?>">
                <span class="admin-nav-icon" aria-hidden="true">▣</span> Dashboard
            </a>
            <a class="admin-nav-link<?php echo $navActive === 'products' ? ' is-active' : ''; ?>" href="<?php echo htmlspecialchars(admin_asset_url('admin-products.php'), ENT_QUOTES, 'UTF-8'); ?>">
                <span class="admin-nav-icon" aria-hidden="true">◫</span> Products
            </a>
            <a class="admin-nav-link<?php echo $navActive === 'users' ? ' is-active' : ''; ?>" href="<?php echo htmlspecialchars(admin_asset_url('admin-users.php'), ENT_QUOTES, 'UTF-8'); ?>">
                <span class="admin-nav-icon" aria-hidden="true">◎</span> Users
            </a>
            <a class="admin-nav-link<?php echo $navActive === 'orders' ? ' is-active' : ''; ?>" href="<?php echo htmlspecialchars(admin_asset_url('admin-orders.php'), ENT_QUOTES, 'UTF-8'); ?>">
                <span class="admin-nav-icon" aria-hidden="true">☰</span> Orders
            </a>
            <details class="admin-nav-dropdown"<?php echo $pagesNavOpen ? ' open' : ''; ?>>
                <summary class="admin-nav-summary<?php echo $pagesNavOpen ? ' is-active' : ''; ?>">
                    <span class="admin-nav-icon" aria-hidden="true">▤</span> Pages
                    <span class="admin-nav-chevron" aria-hidden="true"></span>
                </summary>
                <div class="admin-nav-submenu">
                    <a class="admin-nav-link admin-nav-sublink<?php echo $navActive === 'page-home' ? ' is-active' : ''; ?>" href="<?php echo htmlspecialchars(admin_asset_url('admin-home-products.php'), ENT_QUOTES, 'UTF-8'); ?>">
                        Home products
                    </a>
                    <a class="admin-nav-link admin-nav-sublink<?php echo $navActive === 'page-shop' ? ' is-active' : ''; ?>" href="<?php echo htmlspecialchars(admin_asset_url('admin-shop-products.php'), ENT_QUOTES, 'UTF-8'); ?>">
                        Shop products
                    </a>
                    <a class="admin-nav-link admin-nav-sublink" href="<?php echo htmlspecialchars(storefront_url('index.php'), ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">
                        View live home
                    </a>
                    <a class="admin-nav-link admin-nav-sublink" href="<?php echo htmlspecialchars(storefront_url('shop.php'), ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">
                        View live shop
                    </a>
                </div>
            </details>
        </nav>
        <div class="admin-sidebar-foot">
            <a class="admin-nav-link admin-nav-logout" href="<?php echo htmlspecialchars(admin_asset_url('logout.php'), ENT_QUOTES, 'UTF-8'); ?>">
                <span class="admin-nav-icon" aria-hidden="true">⎋</span> Log out
            </a>
        </div>
    </aside>
    <div class="admin-main-wrap">
        <header class="admin-topbar">
            <div class="admin-topbar-left">
                <span class="admin-topbar-label"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="admin-topbar-right">
                <span class="admin-user-pill" title="Signed in"><?php echo $adminName; ?></span>
            </div>
        </header>
        <main class="admin-main">
