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

$cart_items = $_SESSION['cart'];
$cart_total = 0;
foreach ($cart_items as $item) {
    $cart_total += $item['price'] * $item['quantity'];
}

// Placeholder for checkout form processing logic
$checkout_error = "";
$checkout_success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process payment, save order, clear cart, etc.
    // This is a major piece of functionality to be built.
    $checkout_success = "Your order has been placed successfully! (This is a placeholder)";
    $_SESSION['cart'] = []; // Clear cart on successful mock order
    // header('Location: user/order_detail.php?order_id=XYZ'); // Redirect to an order confirmation page
    // For now, just show message on page
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
                    <p><!-- User address form or display will go here --></p>
                    <p><em>For now, we assume this is pre-filled or handled elsewhere.</em></p>
                    <hr>
                    <h4>Payment Method</h4>
                    <p><!-- Payment options will go here --></p>
                    <p><em>For now, we assume a simple "Place Order" button.</em></p>
                    <form action="checkout.php" method="POST" class="mt-4">
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