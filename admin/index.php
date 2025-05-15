<?php
$page_title = "Admin Dashboard - 2nd Phone Shop";
// This file is in the 'admin' subdirectory, so paths to includes need to be adjusted.
include_once __DIR__ . '/../includes/header.php';

// Ensure user is logged in AND is an admin
if (!isLoggedIn()) {
    header('Location: ../login/login.php?message=Please login to access the admin panel.');
    exit;
}
if (!isAdmin()) {
    // Optional: Redirect to a generic "access denied" page or user dashboard
    header('Location: ../user/dashboard.php?message=Access Denied: You do not have admin privileges.');
    // Or simply: echo "<p>Access Denied. You do not have admin privileges.</p>"; include_once __DIR__ . '/../includes/footer.php';
    exit;
}

$admin_first_name = isset($_SESSION['user_first_name']) ? htmlspecialchars($_SESSION['user_first_name']) : 'Admin';

// Fetch basic stats for the dashboard
$total_users = $conn->query("SELECT COUNT(*) as count FROM user")->fetch_assoc()['count'] ?? 0;
$total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'] ?? 0;
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'] ?? 0;
$pending_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE order_status = 'Processing'")->fetch_assoc()['count'] ?? 0;
?>

<div class="admin-page">
    <div class="d-flex flex-column flex-lg-row">
        <?php include_once __DIR__ . '/includes/admin_nav.php'; ?>

        <div class="flex-grow-1">
            <h1 class="gradient-text mb-4">Admin Dashboard</h1>
            <p class="lead mb-4">Welcome back, <?php echo $admin_first_name; ?>! Here's what's happening in your shop.</p>

            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-people-fill fs-1 gradient-text"></i>
                            </div>
                            <h5 class="card-title mb-2">Total Users</h5>
                            <p class="card-text display-6 mb-0"><?php echo number_format($total_users); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-box-seam fs-1 gradient-text"></i>
                            </div>
                            <h5 class="card-title mb-2">Total Products</h5>
                            <p class="card-text display-6 mb-0"><?php echo number_format($total_products); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-receipt fs-1 gradient-text"></i>
                            </div>
                            <h5 class="card-title mb-2">Total Orders</h5>
                            <p class="card-text display-6 mb-0"><?php echo number_format($total_orders); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-hourglass-split fs-1 gradient-text"></i>
                            </div>
                            <h5 class="card-title mb-2">Pending Orders</h5>
                            <p class="card-text display-6 mb-0"><?php echo number_format($pending_orders); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="gradient-text mb-4">Quick Actions</h2>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="products.php?action=add" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Add New Product
                        </a>
                        <a href="orders.php?filter=pending" class="btn btn-outline-primary">
                            <i class="bi bi-eye me-2"></i>View Pending Orders
                        </a>
                        <a href="users.php" class="btn btn-outline-primary">
                            <i class="bi bi-people me-2"></i>Manage Users
                        </a>
                        <a href="settings.php" class="btn btn-outline-primary">
                            <i class="bi bi-gear me-2"></i>Site Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../includes/footer.php';
?> 