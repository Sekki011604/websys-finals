<?php
$page_title = "Manage Products - Admin Panel";
include_once __DIR__ . '/../includes/header.php';

// Admin access check
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login/login.php?message=Access Denied');
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$product_id = isset($_GET['id']) ? intval($_GET['id']) : null;

$feedback_message = '';
$error_message = '';

// Fetch categories from DB
$categories = [];
$res = $conn->query("SELECT id, name FROM categories WHERE is_active = 1 ORDER BY name");
while ($row = $res->fetch_assoc()) {
    $categories[$row['id']] = $row['name'];
}

// Handle form submissions for add/edit/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_product'])) {
        $name = trim($_POST['product_name']);
        $slug = trim($_POST['product_slug']);
        if (empty($slug)) {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
        }
        $description = trim($_POST['product_description']);
        $category_id = intval($_POST['product_category_id']);
        $price = floatval($_POST['product_price']);
        $stock_quantity = intval($_POST['product_stock_quantity']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $main_image_url = '';
        // Handle image upload
        if (isset($_FILES['product_main_image']) && $_FILES['product_main_image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['product_main_image']['tmp_name'];
            $file_name = basename($_FILES['product_main_image']['name']);
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','webp','svg'];
            if (in_array($ext, $allowed)) {
                $new_name = 'product_' . time() . '_' . rand(1000,9999) . '.' . $ext;
                $target_dir = __DIR__ . '/../asset/images/upload/';
                if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                $target_file = $target_dir . $new_name;
                if (move_uploaded_file($file_tmp, $target_file)) {
                    $main_image_url = 'asset/images/upload/' . $new_name;
                }
            }
        } else if ($action === 'edit' && $product_id) {
            // Keep old image if not uploading new
            $q = $conn->query("SELECT main_image_url FROM products WHERE id=$product_id");
            $main_image_url = $q && $q->num_rows ? $q->fetch_assoc()['main_image_url'] : '';
        }
        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO products (name, slug, description, category_id, price, stock_quantity, is_active, is_featured, main_image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('sssiddiss', $name, $slug, $description, $category_id, $price, $stock_quantity, $is_active, $is_featured, $main_image_url);
            if ($stmt->execute()) {
                $feedback_message = "Product added successfully.";
                $action = 'list';
            } else {
                $error_message = "Failed to add product.";
            }
            $stmt->close();
        } else if ($action === 'edit' && $product_id) {
            $stmt = $conn->prepare("UPDATE products SET name=?, slug=?, description=?, category_id=?, price=?, stock_quantity=?, is_active=?, is_featured=?, main_image_url=? WHERE id=?");
            $stmt->bind_param('sssiddissi', $name, $slug, $description, $category_id, $price, $stock_quantity, $is_active, $is_featured, $main_image_url, $product_id);
            if ($stmt->execute()) {
                $feedback_message = "Product updated successfully.";
                $action = 'list';
            } else {
                $error_message = "Failed to update product.";
            }
            $stmt->close();
        }
    } elseif (isset($_POST['delete_product']) && $product_id) {
        $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
        $stmt->bind_param('i', $product_id);
        if ($stmt->execute()) {
            $feedback_message = "Product deleted successfully.";
            $action = 'list';
        } else {
            $error_message = "Failed to delete product.";
        }
        $stmt->close();
    }
}

$current_product = null;
if (($action === 'edit' || $action === 'delete') && $product_id) {
    $res = $conn->query("SELECT * FROM products WHERE id=$product_id");
    if ($res && $res->num_rows) {
        $current_product = $res->fetch_assoc();
    } else {
        $error_message = "Product not found for ID: $product_id";
        $action = 'list';
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

            <?php if ($action === 'list'): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="gradient-text mb-0">Product List</h2>
                            <a href="products.php?action=add" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Add New Product
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-center">Stock</th>
                                        <th class="text-center">Active</th>
                                        <th class="text-center">Featured</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $res = $conn->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
                                    while ($p = $res && $res->num_rows ? $res->fetch_assoc() : null) {
                                    ?>
                                        <tr>
                                            <td><?php echo $p['id']; ?></td>
                                            <td><?php echo htmlspecialchars($p['name']); ?></td>
                                            <td><?php echo htmlspecialchars($p['category_name'] ?? 'N/A'); ?></td>
                                            <td class="text-end">₱<?php echo number_format($p['price'], 2); ?></td>
                                            <td class="text-center"><?php echo $p['stock_quantity']; ?></td>
                                            <td class="text-center"><?php echo $p['is_active'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle-fill text-danger"></i>'; ?></td>
                                            <td class="text-center"><?php echo $p['is_featured'] ? '<i class="bi bi-star-fill text-warning"></i>' : '<i class="bi bi-star"></i>'; ?></td>
                                            <td class="text-center">
                                                <a href="products.php?action=edit&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i></a>
                                                <a href="products.php?action=delete&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php elseif ($action === 'add' || ($action === 'edit' && $current_product)): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="gradient-text mb-4"><?php echo $action === 'add' ? 'Add New Product' : 'Edit Product: ' . htmlspecialchars($current_product['name']); ?></h2>
                        <form action="products.php<?php echo $action === 'edit' ? '?action=edit&id=' . $product_id : '?action=add'; ?>" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="product_name" class="form-label">Product Name</label>
                                        <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo htmlspecialchars($current_product['name'] ?? ''); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="product_slug" class="form-label">Slug (URL)</label>
                                        <input type="text" class="form-control" id="product_slug" name="product_slug" value="<?php echo htmlspecialchars($current_product['slug'] ?? ''); ?>">
                                        <div class="form-text">Leave blank to auto-generate from name.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="product_description" class="form-label">Description</label>
                                        <textarea class="form-control" id="product_description" name="product_description" rows="5"><?php echo htmlspecialchars($current_product['description'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="product_main_image" class="form-label">Main Image</label>
                                        <input type="file" class="form-control" id="product_main_image" name="product_main_image">
                                        <?php if (isset($current_product['main_image_url']) && $current_product['main_image_url']): ?>
                                            <img src="../<?php echo htmlspecialchars($current_product['main_image_url']); ?>" alt="Main image preview" class="img-thumbnail mt-2" style="max-height: 100px;">
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="product_category" class="form-label">Category</label>
                                        <select class="form-select" id="product_category" name="product_category_id" required>
                                            <option value="">Select Category</option>
                                            <?php foreach ($categories as $cat_id => $cat_name): ?>
                                                <option value="<?php echo $cat_id; ?>" <?php if (isset($current_product['category_id']) && $current_product['category_id'] == $cat_id) echo 'selected'; ?>><?php echo htmlspecialchars($cat_name); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="product_price" class="form-label">Price</label>
                                        <input type="number" step="0.01" class="form-control" id="product_price" name="product_price" value="<?php echo htmlspecialchars($current_product['price'] ?? '0.00'); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="product_stock" class="form-label">Stock Quantity</label>
                                        <input type="number" class="form-control" id="product_stock" name="product_stock_quantity" value="<?php echo htmlspecialchars($current_product['stock_quantity'] ?? '0'); ?>" required>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" <?php if (isset($current_product['is_active']) && $current_product['is_active']) echo 'checked'; elseif($action === 'add') echo 'checked'; ?>>
                                        <label class="form-check-label" for="is_active">Active (Visible on site)</label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" value="1" id="is_featured" name="is_featured" <?php if (isset($current_product['is_featured']) && $current_product['is_featured']) echo 'checked'; ?>>
                                        <label class="form-check-label" for="is_featured">Featured (Show on homepage)</label>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex gap-2">
                                <button type="submit" name="save_product" class="btn btn-primary">
                                    <i class="bi bi-save me-2"></i>Save Product
                                </button>
                                <a href="products.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php elseif ($action === 'delete' && $current_product): ?>
                <h2>Delete Product: <?php echo htmlspecialchars($current_product['name']); ?></h2>
                <div class="alert alert-danger">Are you sure you want to delete this product? This action cannot be undone.</div>
                <form action="products.php?action=delete&id=<?php echo $product_id; ?>" method="POST">
                    <button type="submit" name="delete_product" class="btn btn-danger"><i class="bi bi-trash"></i> Confirm Delete</button>
                    <a href="products.php" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Cancel</a>
                </form>
            <?php else: ?>
                <div class="alert alert-warning">Invalid action or product not found. <a href="products.php">Return to product list.</a></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../includes/footer.php';
?> 