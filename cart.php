<?php
$page_title = "Shopping Cart - 2nd Phone Shop";
include_once __DIR__ . '/includes/header.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart_items = [];
$cart_total = 0;

// Function to sync session cart with database
function syncCartWithDatabase($user_id = null) {
    global $conn;
    $session_id = session_id();
    
    // If user is logged in, transfer session cart to database
    if ($user_id) {
        // First, get all items from session cart
        foreach ($_SESSION['cart'] as $product_id => $item) {
            // Check if item already exists in database for this user
            $check_sql = "SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?";
            if ($check_stmt = $conn->prepare($check_sql)) {
                $check_stmt->bind_param('ii', $user_id, $product_id);
                $check_stmt->execute();
                $result = $check_stmt->get_result();
                
                if ($result->num_rows > 0) {
                    // Update existing item with the session quantity
                    $row = $result->fetch_assoc();
                    $update_sql = "UPDATE cart_items SET quantity = ? WHERE id = ?";
                    if ($update_stmt = $conn->prepare($update_sql)) {
                        $quantity = $item['quantity'];
                        $cart_item_id = $row['id'];
                        $update_stmt->bind_param('ii', $quantity, $cart_item_id);
                        $update_stmt->execute();
                        $update_stmt->close();
                    }
                } else {
                    // Insert new item
                    $insert_sql = "INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)";
                    if ($insert_stmt = $conn->prepare($insert_sql)) {
                        $quantity = $item['quantity'];
                        $insert_stmt->bind_param('iii', $user_id, $product_id, $quantity);
                        $insert_stmt->execute();
                        $insert_stmt->close();
                    }
                }
                $check_stmt->close();
            }
        }
    } else {
        // For guest users, store in database with session_id
        foreach ($_SESSION['cart'] as $product_id => $item) {
            // Check if item already exists in database for this session
            $check_sql = "SELECT id, quantity FROM cart_items WHERE session_id = ? AND product_id = ?";
            if ($check_stmt = $conn->prepare($check_sql)) {
                $check_stmt->bind_param('si', $session_id, $product_id);
                $check_stmt->execute();
                $result = $check_stmt->get_result();
                
                if ($result->num_rows > 0) {
                    // Update existing item with the session quantity
                    $row = $result->fetch_assoc();
                    $update_sql = "UPDATE cart_items SET quantity = ? WHERE id = ?";
                    if ($update_stmt = $conn->prepare($update_sql)) {
                        $quantity = $item['quantity'];
                        $cart_item_id = $row['id'];
                        $update_stmt->bind_param('ii', $quantity, $cart_item_id);
                        $update_stmt->execute();
                        $update_stmt->close();
                    }
                } else {
                    // Insert new item
                    $insert_sql = "INSERT INTO cart_items (session_id, product_id, quantity) VALUES (?, ?, ?)";
                    if ($insert_stmt = $conn->prepare($insert_sql)) {
                        $quantity = $item['quantity'];
                        $insert_stmt->bind_param('sii', $session_id, $product_id, $quantity);
                        $insert_stmt->execute();
                        $insert_stmt->close();
                    }
                }
                $check_stmt->close();
            }
        }
    }
    
    // Clear session cart after syncing
    $_SESSION['cart'] = [];
    
    // Fetch cart items from database
    $fetch_sql = isLoggedIn() 
        ? "SELECT ci.*, p.name, p.price, p.main_image_url FROM cart_items ci JOIN products p ON ci.product_id = p.id WHERE ci.user_id = ?"
        : "SELECT ci.*, p.name, p.price, p.main_image_url FROM cart_items ci JOIN products p ON ci.product_id = p.id WHERE ci.session_id = ?";
    
    if ($fetch_stmt = $conn->prepare($fetch_sql)) {
        $param = isLoggedIn() ? $_SESSION['user_id'] : $session_id;
        $fetch_stmt->bind_param(isLoggedIn() ? 'i' : 's', $param);
        $fetch_stmt->execute();
        $result = $fetch_stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $_SESSION['cart'][$row['product_id']] = [
                'id' => $row['product_id'],
                'name' => $row['name'],
                'price' => $row['price'],
                'quantity' => $row['quantity'],
                'image' => $row['main_image_url']
            ];
        }
        $fetch_stmt->close();
    }
}

// Sync cart with database if user is logged in
if (isLoggedIn()) {
    syncCartWithDatabase($_SESSION['user_id']);
} else {
    syncCartWithDatabase();
}

// Handle add to cart action
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 1;
    
    // Fetch product details from the database
    $sql = "SELECT id, name, price, main_image_url FROM products WHERE id = ? AND is_active = TRUE LIMIT 1";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $product = $result->fetch_assoc();
            
            // Update session cart
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'id' => $product_id,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity,
                    'image' => $product['main_image_url']
                ];
            }
            
            // Sync with database
            if (isLoggedIn()) {
                syncCartWithDatabase($_SESSION['user_id']);
            } else {
                syncCartWithDatabase();
            }
            
            // Redirect to prevent re-adding on refresh
            header('Location: cart.php');
            exit;
        }
        $stmt->close();
    }
}

// Handle update quantity action
if (isset($_POST['action']) && $_POST['action'] == 'update' && isset($_POST['id'])) {
    $product_id = intval($_POST['id']);
    $quantity = intval($_POST['quantity']);
    
    if (isset($_SESSION['cart'][$product_id])) {
        if ($quantity > 0) {
            // Update session cart
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            
            // Update database
            if (isLoggedIn()) {
                $update_sql = "UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?";
                if ($update_stmt = $conn->prepare($update_sql)) {
                    $user_id = $_SESSION['user_id'];
                    $update_stmt->bind_param('iii', $quantity, $user_id, $product_id);
                    $update_stmt->execute();
                    $update_stmt->close();
                }
            } else {
                $update_sql = "UPDATE cart_items SET quantity = ? WHERE session_id = ? AND product_id = ?";
                if ($update_stmt = $conn->prepare($update_sql)) {
                    $session = session_id();
                    $update_stmt->bind_param('isi', $quantity, $session, $product_id);
                    $update_stmt->execute();
                    $update_stmt->close();
                }
            }
        } else {
            // Remove item if quantity is 0 or negative
            unset($_SESSION['cart'][$product_id]);
            
            // Remove from database
            if (isLoggedIn()) {
                $delete_sql = "DELETE FROM cart_items WHERE user_id = ? AND product_id = ?";
                if ($delete_stmt = $conn->prepare($delete_sql)) {
                    $user_id = $_SESSION['user_id'];
                    $delete_stmt->bind_param('ii', $user_id, $product_id);
                    $delete_stmt->execute();
                    $delete_stmt->close();
                }
            } else {
                $delete_sql = "DELETE FROM cart_items WHERE session_id = ? AND product_id = ?";
                if ($delete_stmt = $conn->prepare($delete_sql)) {
                    $session = session_id();
                    $delete_stmt->bind_param('si', $session, $product_id);
                    $delete_stmt->execute();
                    $delete_stmt->close();
                }
            }
        }
    }
    header('Location: cart.php');
    exit;
}

// Handle remove item action
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    
    // Remove from session cart
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        
        // Remove from database
        if (isLoggedIn()) {
            $delete_sql = "DELETE FROM cart_items WHERE user_id = ? AND product_id = ?";
            if ($delete_stmt = $conn->prepare($delete_sql)) {
                $user_id = $_SESSION['user_id'];
                $delete_stmt->bind_param('ii', $user_id, $product_id);
                $delete_stmt->execute();
                $delete_stmt->close();
            }
        } else {
            $delete_sql = "DELETE FROM cart_items WHERE session_id = ? AND product_id = ?";
            if ($delete_stmt = $conn->prepare($delete_sql)) {
                $session = session_id();
                $delete_stmt->bind_param('si', $session, $product_id);
                $delete_stmt->execute();
                $delete_stmt->close();
            }
        }
        
        // Set success message
        $_SESSION['success_message'] = "Item removed from cart successfully.";
    } else {
        // Set error message if item not found
        $_SESSION['error_message'] = "Item not found in cart.";
    }
    
    // Redirect back to cart page
    header('Location: cart.php');
    exit;
}

// Calculate cart total
$cart_items = $_SESSION['cart'];
foreach ($cart_items as $item) {
    $cart_total += $item['price'] * $item['quantity'];
}
?>

<style>
.cart-table th, .cart-table td {
    vertical-align: middle !important;
}
.cart-table tbody tr {
    border-bottom: 1px solid #f0f0f0;
}
.cart-table img {
    max-height: 60px;
    border-radius: 8px;
}
.cart-table .form-control-sm {
    width: 60px;
    display: inline-block;
    margin-right: 8px;
}
.cart-table .save-btn {
    background: #a259e6;
    border: none;
    color: #fff;
    border-radius: 8px;
    padding: 6px 16px;
    font-weight: 500;
    transition: background 0.2s;
}
.cart-table .save-btn:hover {
    background: #7c3aed;
}
.cart-table .delete-btn {
    background: #ff4d4f;
    border: none;
    color: #fff;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    transition: background 0.2s;
}
.cart-table .delete-btn:hover {
    background: #d9363e;
}
.cart-table .form-check-input {
    width: 1.3em;
    height: 1.3em;
    margin-top: 0;
}
.selected-total-row {
    background: #f8f9fa;
    font-size: 1.1rem;
    font-weight: bold;
}
@media (max-width: 768px) {
    .cart-table th, .cart-table td {
        font-size: 0.95rem;
        padding: 0.5rem;
    }
    .cart-table img {
        max-height: 40px;
    }
}
</style>

<div class="admin-page">
    <div class="container py-5">
        <h1 class="gradient-text mb-4">Shopping Cart</h1>
        <p class="lead mb-4">Review and manage your shopping cart items.</p>

        <?php if (empty($cart_items)): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 text-center">
                    <i class="bi bi-cart-x display-1 text-muted mb-3"></i>
                    <h5 class="card-title">Your cart is empty</h5>
                    <p class="card-text text-muted">Looks like you haven't added any items to your cart yet.</p>
                    <a href="shop.php" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-2"></i>Continue Shopping
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <form action="checkout.php" method="GET" id="checkoutForm">
                            <table class="table align-middle mb-0 cart-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th scope="col" class="text-center">Select</th>
                                        <th scope="col" colspan="2">Product</th>
                                        <th scope="col" class="text-center">Price</th>
                                        <th scope="col" class="text-center">Quantity</th>
                                        <th scope="col" class="text-end">Total</th>
                                        <th scope="col" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_items as $item): ?>
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" name="selected_items[]" value="<?php echo $item['id']; ?>" class="form-check-input item-checkbox">
                                            </td>
                                            <td style="width: 80px;">
                                                <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                     class="img-fluid rounded" 
                                                     style="max-height: 80px; object-fit: contain;">
                                            </td>
                                            <td>
                                                <h6 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                            </td>
                                            <td class="text-center">₱<?php echo number_format($item['price'], 2); ?></td>
                                            <td class="text-center" style="min-width: 150px;">
                                                <form action="cart.php" method="POST" class="d-flex align-items-center gap-2 mb-0">
                                                    <input type="hidden" name="action" value="update">
                                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                                           class="form-control form-control-sm" min="1">
                                                    <button type="submit" class="save-btn">
                                                        <i class="bi bi-save me-1"></i>Save
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="text-end fw-bold item-total">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                            <td class="text-center">
                                                <a href="cart.php?action=remove&id=<?php echo $item['id']; ?>" 
                                                   class="delete-btn" 
                                                   onclick="return confirm('Are you sure you want to remove this item?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="bg-light selected-total-row">
                                    <tr>
                                        <td colspan="5" class="text-end">Selected Total:</td>
                                        <td class="text-end h5 mb-0" id="selectedTotal">₱0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </form>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="shop.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Continue Shopping
                </a>
                <button type="submit" form="checkoutForm" class="btn btn-primary auth-btn" id="checkoutBtn" disabled>
                    <i class="bi bi-bag-check me-2"></i>Proceed to Checkout
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const checkoutBtn = document.getElementById('checkoutBtn');
    const selectedTotal = document.getElementById('selectedTotal');
    const cartItems = <?php echo json_encode($cart_items); ?>;

    function updateSelectedTotal() {
        let total = 0;
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const itemId = checkbox.value;
                const item = cartItems[itemId];
                if (item) {
                    total += item.price * item.quantity;
                }
            }
        });
        selectedTotal.textContent = '₱' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        checkoutBtn.disabled = total === 0;
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedTotal);
    });
});
</script>

<?php
include_once __DIR__ . '/includes/footer.php';
?> 