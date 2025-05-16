<?php
$page_title = "My Orders - 2nd Phone Shop";
include_once __DIR__ . '/../includes/header.php';

// Ensure user is logged in
if (!isLoggedIn()) {
    header('Location: ../login/login.php?message=Please login to view your orders.');
    exit;
}

$user_id = $_SESSION['user_id'];
$orders = [];

// Fetch orders for the current user from the database
$sql = "SELECT id, date_ordered, total_amount, order_status as status FROM orders WHERE user_id = ? ORDER BY date_ordered DESC";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            // Fetch product names for this order
            $order_id = $row['id'];
            $product_names = [];
            $sql_items = "SELECT product_name_snapshot FROM order_items WHERE order_id = ?";
            if ($stmt_items = $conn->prepare($sql_items)) {
                $stmt_items->bind_param("i", $order_id);
                $stmt_items->execute();
                $result_items = $stmt_items->get_result();
                while ($item = $result_items->fetch_assoc()) {
                    $product_names[] = $item['product_name_snapshot'];
                }
                $stmt_items->close();
            }
            $row['product_names'] = $product_names;
            $orders[] = $row;
        }
    } else {
        // Handle error - though for a placeholder, we might not show db errors directly
        error_log("Error fetching orders: " . $conn->error);
        echo '<div class="alert alert-danger">An error occurred while fetching your orders. Please try again later.</div>';
    }
    $stmt->close();
} else {
    // Handle error - though for a placeholder, we might not show db errors directly
    error_log("Error fetching orders: " . $conn->error);
}
?>

<div class="admin-page">
    <div class="d-flex flex-column flex-lg-row">
        <nav class="nav flex-column nav-pills bg-white p-3 mb-3 mb-lg-0 me-lg-3 rounded shadow-sm" style="width: 250px; height: 100%; position: sticky; top: 1rem;">
            <div class="d-flex flex-column h-100">
                <div class="flex-grow-1">
                    <a class="nav-link" href="dashboard.php?page=overview" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <i class="bi bi-person-circle me-2"></i>Dashboard Overview
                    </a>
                    <a class="nav-link active" href="orders.php" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <i class="bi bi-box-seam me-2"></i>My Orders
                    </a>
                    <a class="nav-link" href="addresses.php" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <i class="bi bi-geo-alt me-2"></i>Manage Addresses
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
            <h1 class="gradient-text mb-4">My Orders</h1>
            <p class="lead mb-4">View and manage your order history.</p>

            <?php if (empty($orders)): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="alert alert-info mb-0">You have not placed any orders yet.</div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Date</th>
                                        <th class="text-end">Total</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>
                                                <?php echo htmlspecialchars(implode(', ', $order['product_names'])); ?>
                                            </td>
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
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../includes/footer.php';
?> 