<?php
$page_title = "Dashboard - 2nd Phone Shop";
// This file is in the 'user' subdirectory, so paths to includes need to be adjusted.
include_once __DIR__ . '/../includes/header.php';

// Ensure user is logged in, otherwise redirect to login page
if (!isLoggedIn()) {
    header('Location: ../login/login.php?message=Please login to view your dashboard.');
    exit;
}

// Get user's first name from session to personalize the welcome message
$user_first_name = isset($_SESSION['user_first_name']) ? htmlspecialchars($_SESSION['user_first_name']) : 'User';

$user_id = $_SESSION['user_id'];
$stats = [
    'total_orders' => 0,
    'pending_orders' => 0,
    'total_spent' => 0
];

// Fetch user statistics
$sql = "SELECT 
    COUNT(*) as total_orders,
    SUM(CASE WHEN order_status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
    SUM(total_amount) as total_spent
    FROM orders 
    WHERE user_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats = $row;
        }
    }
    $stmt->close();
}

// Fetch recent orders
$recent_orders = [];
$sql = "SELECT id, date_ordered, total_amount, order_status as status 
        FROM orders 
        WHERE user_id = ? 
        ORDER BY date_ordered DESC 
        LIMIT 5";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $recent_orders[] = $row;
        }
    }
    $stmt->close();
}

// Placeholder for dashboard content sections (e.g., recent orders, profile summary)
$active_section = isset($_GET['page']) ? $_GET['page'] : 'overview';
?>

<div class="admin-page">
    <div class="d-flex flex-column flex-lg-row">
        <nav class="nav flex-column nav-pills bg-white p-3 mb-3 mb-lg-0 me-lg-3 rounded shadow-sm" style="width: 250px; height: 100%; position: sticky; top: 1rem;">
            <div class="d-flex flex-column h-100">
                <div class="flex-grow-1">
                    <a class="nav-link active" href="dashboard.php?page=overview" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <i class="bi bi-person-circle me-2"></i>Dashboard Overview
                    </a>
                    <a class="nav-link" href="orders.php" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <i class="bi bi-box-seam me-2"></i>My Orders
                    </a>
                    <a class="nav-link" href="profile.php" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <i class="bi bi-person-badge me-2"></i>My Profile
                    </a>
                    <a class="nav-link" href="profile.php?section=settings" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <i class="bi bi-gear me-2"></i>Account Settings
                    </a>
                </div>
                <div class="mt-auto">
                    <a class="nav-link text-danger" href="../logout.php" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </a>
                </div>
            </div>
        </nav>

        <div class="flex-grow-1">
            <h1 class="gradient-text mb-4">Welcome, <?php echo $user_first_name; ?>!</h1>
            <p class="lead mb-4">Here's an overview of your account activity.</p>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-box-seam text-primary fs-1"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="card-subtitle text-muted mb-1">Total Orders</h6>
                                    <h2 class="card-title mb-0"><?php echo number_format($stats['total_orders']); ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-clock-history text-warning fs-1"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="card-subtitle text-muted mb-1">Pending Orders</h6>
                                    <h2 class="card-title mb-0"><?php echo number_format($stats['pending_orders']); ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-currency-dollar text-success fs-1"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="card-subtitle text-muted mb-1">Total Spent</h6>
                                    <h2 class="card-title mb-0">₱<?php echo number_format($stats['total_spent'], 2); ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">Quick Actions</h5>
                    <div class="d-flex gap-2">
                        <a href="orders.php" class="btn btn-primary">
                            <i class="bi bi-box-seam me-2"></i>View All Orders
                        </a>
                        <a href="profile.php" class="btn btn-outline-primary">
                            <i class="bi bi-person me-2"></i>Update Profile
                        </a>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">Recent Orders</h5>
                    <?php if (empty($recent_orders)): ?>
                        <div class="alert alert-info mb-0">You haven't placed any orders yet.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th class="text-end">Total</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($order['id']); ?></td>
                                            <td><?php echo date("M d, Y h:i A", strtotime($order['date_ordered'])); ?></td>
                                            <td class="text-end">₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo strtolower(htmlspecialchars($order['status'])) === 'delivered' ? 'success' : (strtolower(htmlspecialchars($order['status'])) === 'shipped' ? 'info' : (strtolower(htmlspecialchars($order['status'])) === 'processing' ? 'warning' : 'secondary')); ?>">
                                                    <?php echo htmlspecialchars($order['status']); ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="order_detail.php?order_id=<?php echo htmlspecialchars($order['id']); ?>" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../includes/footer.php';
?> 