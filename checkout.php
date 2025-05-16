<?php
$page_title = "Checkout - 2nd Phone Shop";
include_once __DIR__ . '/includes/header.php';

// Redirect to login if user is not logged in
if (!isLoggedIn()) {
    $_SESSION['redirect_to_checkout'] = true; // Optional: to redirect back after login
    header('Location: login/login.php?message=Please login to proceed to checkout.');
    exit;
}

// Ensure cart is not empty
if (empty($_SESSION['cart'])) {
    header('Location: cart.php?message=Your cart is empty.');
    exit;
}

// Get selected items from GET or POST
$selected_items = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['selected_items'])) {
    $selected_items = $_GET['selected_items'];
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_items'])) {
    $selected_items = $_POST['selected_items'];
}
if (empty($selected_items)) {
    header('Location: cart.php?message=Please select items to checkout.');
    exit;
}

// Filter cart items to only include selected items
$cart_items = array_filter($_SESSION['cart'], function($item) use ($selected_items) {
    return in_array($item['id'], $selected_items);
});

$cart_total = 0;
foreach ($cart_items as $item) {
    $cart_total += $item['price'] * $item['quantity'];
}

// Placeholder for checkout form processing logic
$checkout_error = "";
$checkout_success = "";
$selected_payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
$payment_status = ($selected_payment_method === 'Pick Up') ? 'paid' : 'pending';

// Fetch default shipping address
$shipping_address = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM user_addresses WHERE user_id = ? AND address_type = 'shipping' AND is_default = 1 LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $shipping_address = $result->fetch_assoc();
}

// Only place order if 'place_order' is set in POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    try {
        // Start transaction
        $conn->begin_transaction();

        // Get user's default shipping and billing addresses
        $user_id = $_SESSION['user_id'];
        $shipping_address_sql = "SELECT id FROM user_addresses WHERE user_id = ? AND address_type = 'shipping' AND is_default = 1 LIMIT 1";
        $billing_address_sql = "SELECT id FROM user_addresses WHERE user_id = ? AND address_type = 'billing' AND is_default = 1 LIMIT 1";
        
        $stmt = $conn->prepare($shipping_address_sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $shipping_address = $stmt->get_result()->fetch_assoc();
        
        $stmt = $conn->prepare($billing_address_sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $billing_address = $stmt->get_result()->fetch_assoc();

        if (!$shipping_address || !$billing_address) {
            throw new Exception("Please add shipping and billing addresses before placing an order.");
        }

        // Generate unique order number
        $order_number = 'ORD-' . strtoupper(uniqid());

        $shipping_id = $shipping_address['id'];
        $billing_id = $billing_address['id'];
        // Insert order
        $order_sql = "INSERT INTO orders (user_id, order_number, shipping_address_id, billing_address_id, 
                    subtotal_amount, total_amount, order_status, payment_method, payment_status) 
                    VALUES (?, ?, ?, ?, ?, ?, 'processing', ?, ?)";
        
        $stmt = $conn->prepare($order_sql);
        $stmt->bind_param("isiiidss", 
            $user_id,
            $order_number,
            $shipping_id,
            $billing_id,
            $cart_total,
            $cart_total,
            $selected_payment_method,
            $payment_status
        );
        $stmt->execute();
        $order_id = $conn->insert_id;

        // Insert order items and update product stock
        foreach ($cart_items as $item) {
            $product_id = $item['id'];
            $product_name = $item['name'];
            $product_sku = $item['sku'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            $subtotal = $item['price'] * $item['quantity'];
            // Insert order item
            $order_item_sql = "INSERT INTO order_items (order_id, product_id, product_name_snapshot, 
                             product_sku_snapshot, quantity, price_at_purchase, subtotal) 
                             VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($order_item_sql);
            $stmt->bind_param("iissidi", 
                $order_id,
                $product_id,
                $product_name,
                $product_sku,
                $quantity,
                $price,
                $subtotal
            );
            $stmt->execute();

            // Update product stock
            $update_stock_sql = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?";
            $stmt = $conn->prepare($update_stock_sql);
            $stmt->bind_param("ii", $quantity, $product_id);
            $stmt->execute();

            // Remove the ordered items from cart (session)
            unset($_SESSION['cart'][$product_id]);

            // Remove the ordered items from cart_items table
            if (isLoggedIn()) {
                $delete_cart_sql = "DELETE FROM cart_items WHERE user_id = ? AND product_id = ?";
                $stmt = $conn->prepare($delete_cart_sql);
                $stmt->bind_param("ii", $user_id, $product_id);
                $stmt->execute();
            } else {
                $session_id = session_id();
                $delete_cart_sql = "DELETE FROM cart_items WHERE session_id = ? AND product_id = ?";
                $stmt = $conn->prepare($delete_cart_sql);
                $stmt->bind_param("si", $session_id, $product_id);
                $stmt->execute();
            }
        }

        // Commit transaction
        $conn->commit();

        // Redirect to orders page
        header('Location: user/orders.php?success=1');
        exit;

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $checkout_error = $e->getMessage();
    }
}
?>

<div class="py-5">
    <h1 class="text-center mb-4"><?php echo htmlspecialchars($page_title); ?></h1>
    <div class="container">
        <?php if (!empty($checkout_error)): ?>
            <div class="alert alert-danger"><?php echo $checkout_error; ?></div>
        <?php endif; ?>
        <?php if (!empty($checkout_success)): ?>
            <div class="alert alert-success"><?php echo $checkout_success; ?></div>
            <p class="text-center"><a href="shop.php" class="btn btn-primary"><i class="bi bi-arrow-left"></i> Continue Shopping</a></p>
        <?php else: ?>
            <div class="row">
                <div class="col-md-7">
                    <h4>Shipping Information</h4>
                    <?php if ($shipping_address): ?>
                        <div class="mb-2 border rounded p-3 bg-light">
                            <strong><?php echo htmlspecialchars($shipping_address['recipient_name']); ?></strong><br>
                            <?php echo htmlspecialchars($shipping_address['address_line1']); ?><br>
                            <?php echo htmlspecialchars($shipping_address['city']); ?>, <?php echo htmlspecialchars($shipping_address['state_province_region']); ?><br>
                            <?php echo htmlspecialchars($shipping_address['postal_code']); ?>, <?php echo htmlspecialchars($shipping_address['country_code']); ?><br>
                            <a href="user/addresses.php" class="btn btn-sm btn-outline-primary mt-2">Edit</a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">No default shipping address found. <a href="user/addresses.php" class="btn btn-sm btn-primary ms-2">Add Address</a></div>
                    <?php endif; ?>
                    <hr>
                    <h4>Payment Method</h4>
                    <form action="checkout.php" method="POST" class="mt-4">
                        <?php foreach ($selected_items as $id): ?>
                            <input type="hidden" name="selected_items[]" value="<?php echo htmlspecialchars($id); ?>">
                        <?php endforeach; ?>
                        <input type="hidden" name="place_order" value="1">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="pickup" value="Pick Up" required <?php if($selected_payment_method==="Pick Up") echo 'checked'; ?>>
                                <label class="form-check-label" for="pickup">Pick Up</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="Cash on Delivery" required <?php if($selected_payment_method==="Cash on Delivery") echo 'checked'; ?>>
                                <label class="form-check-label" for="cod">Cash on Delivery (COD)</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg auth-btn"><i class="bi bi-shield-check"></i> Place Order</button>
                    </form>
                </div>
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title"><i class="bi bi-cart3"></i> Order Summary</h4>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($cart_items as $item): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)
                                        <span>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                    </li>
                                <?php endforeach; ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                                    Total
                                    <span>₱<?php echo number_format($cart_total, 2); ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
include_once __DIR__ . '/includes/footer.php';
?> 