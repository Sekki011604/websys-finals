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
    // In a real app, fetch order details from the database for this user_id and order_id
    // $sql_order = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
    // $sql_order_items = "SELECT oi.*, p.name as product_name, p.main_image_url FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?";

    // Mock order data for now based on ID from orders.php mock data
    if ($order_id_from_get === 'ORD12345') {
        $order = [
            'id' => 'ORD12345',
            'order_date' => '2023-10-26 10:30:00',
            'total_amount' => 349.98,
            'status' => 'Shipped',
            'shipping_address' => "123 Main St, Anytown, USA 12345",
            'billing_address' => "123 Main St, Anytown, USA 12345",
            'payment_method' => "Credit Card ending in XXXX"
        ];
        $order_items = [
            ['product_name' => 'Awesome Smartphone X', 'quantity' => 1, 'price_at_purchase' => 299.99, 'subtotal' => 299.99, 'main_image_url' => 'https://via.placeholder.com/50?text=PhoneX'],
            ['product_name' => 'Phone Case', 'quantity' => 1, 'price_at_purchase' => 49.99, 'subtotal' => 49.99, 'main_image_url' => 'https://via.placeholder.com/50?text=Case']
        ];
        $page_title = "Order " . htmlspecialchars($order_id_from_get) . " - 2nd Phone Shop";
    } elseif ($order_id_from_get === 'ORD12300') {
        $order = [
            'id' => 'ORD12300',
            'order_date' => '2023-10-15 14:12:00',
            'total_amount' => 499.99,
            'status' => 'Delivered',
            'shipping_address' => "456 Oak Ave, Anytown, USA 12345",
            'billing_address' => "456 Oak Ave, Anytown, USA 12345",
            'payment_method' => "PayPal"
        ];
        $order_items = [
            ['product_name' => 'Super Tablet Z', 'quantity' => 1, 'price_at_purchase' => 499.99, 'subtotal' => 499.99, 'main_image_url' => 'https://via.placeholder.com/50?text=TabletZ']
        ];
        $page_title = "Order " . htmlspecialchars($order_id_from_get) . " - 2nd Phone Shop";
    }
}
?>

<div class="d-flex flex-column flex-lg-row">
    <nav class="nav flex-column nav-pills bg-light p-3 mb-3 mb-lg-0 me-lg-3 rounded" style="min-width: 220px;">
        <a class="nav-link" href="dashboard.php?page=overview"><i class="bi bi-person-circle me-2"></i>Dashboard Overview</a>
        <a class="nav-link active" href="orders.php"><i class="bi bi-box-seam me-2"></i>My Orders</a> <!-- Keep My Orders active -->
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
                    <p><strong>Order Date:</strong> <?php echo date("M d, Y h:i A", strtotime($order['order_date'])); ?></p>
                    <p><strong>Order Status:</strong> <span class="badge bg-info"><?php echo htmlspecialchars($order['status']); ?></span></p>
                    <p><strong>Total Amount:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
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
                                        <td style="width: 60px;"><img src="<?php echo htmlspecialchars($item['main_image_url']); ?>" alt="" class="img-fluid rounded"></td>
                                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                        <td class="text-center"><?php echo $item['quantity']; ?></td>
                                        <td class="text-end">$<?php echo number_format($item['price_at_purchase'], 2); ?></td>
                                        <td class="text-end">$<?php echo number_format($item['subtotal'], 2); ?></td>
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
                                <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?>
                            </address>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">Payment Information</div>
                        <div class="card-body">
                            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                            <?php // Add more payment details if available/necessary ?>
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