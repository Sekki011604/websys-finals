<?php
session_start();
include_once __DIR__ . '/includes/connection/db_conn.inc.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'No product ID provided.']);
    exit;
}

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
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
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
        echo json_encode(['success' => true, 'message' => 'Added to cart!']);
        exit;
    }
    $stmt->close();
}
echo json_encode(['success' => false, 'message' => 'Product not found.']); 