<?php
// Get the current page name and query parameters
$current_page = basename($_SERVER['PHP_SELF']);
$current_action = isset($_GET['action']) ? $_GET['action'] : '';
$current_filter = isset($_GET['filter']) ? $_GET['filter'] : '';

// Function to check if a link should be active
function isActive($page) {
    return basename($_SERVER['PHP_SELF']) === $page;
}
?>
<nav class="nav flex-column nav-pills bg-white p-3 mb-3 mb-lg-0 me-lg-3 rounded shadow-sm" style="width: 250px; height: 100%; position: sticky; top: 1rem;">
    <div class="d-flex flex-column h-100">
        <div class="flex-grow-1">
            <a class="nav-link <?php echo isActive('index.php') ? 'active' : ''; ?>" href="index.php" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
            <a class="nav-link <?php echo isActive('products.php') ? 'active' : ''; ?>" href="products.php" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                <i class="bi bi-box-seam me-2"></i>Manage Products
            </a>
            <a class="nav-link <?php echo isActive('categories.php') ? 'active' : ''; ?>" href="categories.php" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                <i class="bi bi-tags me-2"></i>Manage Categories
            </a>
            <a class="nav-link <?php echo isActive('orders.php') ? 'active' : ''; ?>" href="orders.php" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                <i class="bi bi-receipt me-2"></i>Manage Orders
            </a>
            <a class="nav-link <?php echo isActive('users.php') ? 'active' : ''; ?>" href="users.php" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                <i class="bi bi-people me-2"></i>Manage Users
            </a>
            <a class="nav-link <?php echo isActive('homepage_settings.php') ? 'active' : ''; ?>" href="homepage_settings.php" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                <i class="bi bi-house me-2"></i>Homepage Settings
            </a>
            <a class="nav-link <?php echo isActive('site_info.php') ? 'active' : ''; ?>" href="site_info.php" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                <i class="bi bi-info-circle me-2"></i>Mission & Vision
            </a>
            <a class="nav-link <?php echo isActive('settings.php') ? 'active' : ''; ?>" href="settings.php" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                <i class="bi bi-gear me-2"></i>Site Settings
            </a>
        </div>
        <div class="mt-auto">
            <a class="nav-link text-danger" href="../logout.php" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                <i class="bi bi-box-arrow-right me-2"></i>Logout
            </a>
        </div>
    </div>
</nav> 