<?php
$page_title = "Order Details - 2nd Phone Shop";
include_once __DIR__ . '/../includes/header.php';

// Ensure user is logged in
if (!isLoggedIn()) {
    header('Location: ../login/login.php?message=Please login to view order details.');
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id_from_get = isset($_GET['order_id']) ? $_GET['order_id'] : null;
$order = null;
$order_items = [];

if ($order_id_from_get) {
    // Fetch order details from the database for this user_id and order_id
    $sql_order = "SELECT o.*, sa.recipient_name AS shipping_recipient, sa.address_line1 AS shipping_line1, sa.city AS shipping_city, sa.state_province_region AS shipping_state, sa.postal_code AS shipping_postal, sa.country_code AS shipping_country, ba.recipient_name AS billing_recipient, ba.address_line1 AS billing_line1, ba.city AS billing_city, ba.state_province_region AS billing_state, ba.postal_code AS billing_postal, ba.country_code AS billing_country FROM orders o LEFT JOIN user_addresses sa ON o.shipping_address_id = sa.id LEFT JOIN user_addresses ba ON o.billing_address_id = ba.id WHERE o.id = ? AND o.user_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql_order);
    $stmt->bind_param('ii', $order_id_from_get, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $order = $result->fetch_assoc();
        // Fetch order items
        $sql_order_items = "SELECT oi.*, p.name as product_name, p.main_image_url FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?";
        $stmt_items = $conn->prepare($sql_order_items);
        $stmt_items->bind_param('i', $order_id_from_get);
        $stmt_items->execute();
        $result_items = $stmt_items->get_result();
        while ($row = $result_items->fetch_assoc()) {
            $order_items[] = $row;
        }
    }
}
?>

<div class="d-flex flex-column flex-lg-row">
    <nav class="nav flex-column nav-pills bg-light p-3 mb-3 mb-lg-0 me-lg-3 rounded" style="min-width: 220px;">
        <a class="nav-link" href="dashboard.php?page=overview"><i class="bi bi-person-circle me-2"></i>Dashboard Overview</a>
        <a class="nav-link active" href="orders.php"><i class="bi bi-box-seam me-2"></i>My Orders</a> <!-- Keep My Orders active -->
        <a class="nav-link" href="addresses.php"><i class="bi bi-geo-alt me-2"></i>Manage Addresses</a>
        <a class="nav-link" href="profile.php"><i class="bi bi-person-badge me-2"></i>My Profile</a>
        <a class="nav-link" href="profile.php?section=settings"><i class="bi bi-gear me-2"></i>Account Settings</a>
        <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
    </nav>

    <div class="flex-grow-1">
        <?php if ($order): ?>
            <h1>Order Details: <?php echo htmlspecialchars($order['id']); ?></h1>
            <p><a href="orders.php"><i class="bi bi-arrow-left"></i> Back to My Orders</a></p>

            <div class="card mb-4">
                <div class="card-header">Order Summary</div>
                <div class="card-body">
                    <p><strong>Order Date:</strong> <?php echo date("M d, Y h:i A", strtotime($order['date_ordered'])); ?></p>
                    <p><strong>Order Status:</strong> <span class="badge bg-info"><?php echo htmlspecialchars($order['order_status']); ?></span></p>
                    <p><strong>Total Amount:</strong> ₱<?php echo number_format($order['total_amount'], 2); ?></p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Items Ordered</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th colspan="2">Product</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td style="width: 60px;"><img src="<?php echo htmlspecialchars($item['main_image_url'] ?? 'https://via.placeholder.com/50?text=No+Image'); ?>" alt="" class="img-fluid rounded"></td>
                                        <td><?php echo htmlspecialchars($item['product_name_snapshot'] ?? $item['product_name']); ?></td>
                                        <td class="text-center"><?php echo $item['quantity']; ?></td>
                                        <td class="text-end">₱<?php echo number_format($item['price_at_purchase'], 2); ?></td>
                                        <td class="text-end">₱<?php echo number_format($item['subtotal'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">Shipping Address</div>
                        <div class="card-body">
                            <address>
                                <?php echo htmlspecialchars($order['shipping_recipient']); ?><br>
                                <?php echo htmlspecialchars($order['shipping_line1']); ?><br>
                                <?php echo htmlspecialchars($order['shipping_city']); ?>, <?php echo htmlspecialchars($order['shipping_state']); ?><br>
                                <?php echo htmlspecialchars($order['shipping_postal']); ?>, <?php echo htmlspecialchars($order['shipping_country']); ?>
                            </address>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">Payment Information</div>
                        <div class="card-body">
                            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($order_id_from_get): ?>
            <div class="alert alert-danger">Order not found or you do not have permission to view it.</div>
            <p><a href="orders.php"><i class="bi bi-arrow-left"></i> Back to My Orders</a></p>
        <?php else: ?>
            <div class="alert alert-warning">No order ID specified.</div>
            <p><a href="orders.php"><i class="bi bi-arrow-left"></i> Back to My Orders</a></p>
        <?php endif; ?>
    </div>
</div>

<?php
include_once __DIR__ . '/../includes/footer.php';
?> 