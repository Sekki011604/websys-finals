<?php
$page_title = "Manage Orders - Admin Panel";
include_once __DIR__ . '/../includes/header.php';

// Admin access check
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login/login.php?message=Access Denied');
    exit;
}

$filter_status = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$feedback_message = "";
$error_message = "";

// Order statuses from DB schema
$order_statuses = ['pending_payment', 'processing', 'shipped', 'delivered', 'cancelled', 'completed'];
$payment_statuses = ['pending', 'paid', 'failed', 'refunded', 'partially_refunded'];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order_status'])) {
    $order_id_to_update = intval($_POST['order_id']);
    $new_status = $_POST['new_status'];
    if (in_array($new_status, $order_statuses)) {
        $stmt = $conn->prepare("UPDATE orders SET order_status=? WHERE id=?");
        $stmt->bind_param('si', $new_status, $order_id_to_update);
        if ($stmt->execute()) {
            $feedback_message = "Order status updated.";
        } else {
            $error_message = "Failed to update order status.";
        }
        $stmt->close();
    } else {
        $error_message = "Invalid status.";
    }
}

// Fetch orders from DB
$where = '';
if ($filter_status !== 'all' && in_array($filter_status, $order_statuses)) {
    $where = "WHERE o.order_status = '" . $conn->real_escape_string($filter_status) . "'";
}
$sql = "SELECT o.*, u.email as user_email FROM orders o LEFT JOIN user u ON o.user_id = u.id $where ORDER BY o.date_ordered DESC";
$res = $conn->query($sql);
$orders = [];
if ($res && $res->num_rows) {
    while ($row = $res->fetch_assoc()) {
        $orders[] = $row;
    }
}
?>

<div class="admin-page">
    <div class="d-flex flex-column flex-lg-row">
        <?php include_once __DIR__ . '/includes/admin_nav.php'; ?>

        <div class="flex-grow-1">
            <h1 class="gradient-text mb-4"><?php echo htmlspecialchars($page_title); ?></h1>

            <?php if ($feedback_message): ?>
                <div class="alert alert-success"><?php echo $feedback_message; ?></div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="gradient-text mb-0">Order List</h2>
                        <div class="btn-group">
                            <a href="orders.php?filter=all" class="btn btn-sm <?php echo ($filter_status === 'all') ? 'btn-primary' : 'btn-outline-secondary'; ?>">All</a>
                            <?php foreach ($order_statuses as $status_option): ?>
                                <a href="orders.php?filter=<?php echo htmlspecialchars($status_option); ?>" class="btn btn-sm <?php echo ($filter_status === $status_option) ? 'btn-primary' : 'btn-outline-secondary'; ?>"><?php echo ucwords(str_replace('_', ' ', $status_option)); ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer Email</th>
                                    <th>Date</th>
                                    <th class="text-end">Total</th>
                                    <th>Status</th>
                                    <th>Payment Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($orders)): ?>
                                    <tr><td colspan="7" class="text-center">No orders found for this filter.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                                            <td><?php echo htmlspecialchars($order['user_email']); ?></td>
                                            <td><?php echo date("M d, Y h:i A", strtotime($order['date_ordered'])); ?></td>
                                            <td class="text-end">$<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td>
                                                <form action="orders.php?filter=<?php echo $filter_status; ?>" method="POST" class="d-inline-flex align-items-center">
                                                    <input type="hidden" name="order_id" value="<?php echo intval($order['id']); ?>">
                                                    <select name="new_status" class="form-select form-select-sm" style="min-width: 120px;">
                                                        <?php foreach ($order_statuses as $status_val): ?>
                                                            <option value="<?php echo htmlspecialchars($status_val); ?>" <?php if ($order['order_status'] === $status_val) echo 'selected'; ?>><?php echo ucwords(str_replace('_', ' ', $status_val)); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <button type="submit" name="update_order_status" class="btn btn-sm btn-outline-primary ms-2"><i class="bi bi-check-lg"></i></button>
                                                </form>
                                            </td>
                                            <td><span class="badge bg-<?php echo strtolower($order['payment_status']) === 'paid' ? 'success' : (strtolower($order['payment_status']) === 'pending' ? 'warning' : (strtolower($order['payment_status']) === 'refunded' ? 'secondary' : 'info')); ?>"><?php echo ucwords(str_replace('_', ' ', $order['payment_status'])); ?></span></td>
                                            <td class="text-center">
                                                <a href="../user/order_detail.php?order_id=<?php echo htmlspecialchars($order['id']); ?>" class="btn btn-sm btn-outline-info" title="View Details"><i class="bi bi-eye"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../includes/footer.php';
?> 