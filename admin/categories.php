<?php
$page_title = "Manage Categories - Admin Panel";
include_once __DIR__ . '/../includes/header.php';

// Admin access check
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login/login.php?message=Access Denied');
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$category_id = isset($_GET['id']) ? intval($_GET['id']) : null;

$feedback_message = '';
$error_message = '';

// Handle form submissions for add/edit/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_category'])) {
        $cat_name = trim($_POST['category_name']);
        $cat_slug = trim($_POST['category_slug']);
        $cat_desc = trim($_POST['category_description']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        if (empty($cat_name)) {
            $error_message = "Category name cannot be empty.";
        } else {
            if ($action === 'add') {
                $stmt = $conn->prepare("INSERT INTO categories (name, slug, description, is_active) VALUES (?, ?, ?, ?)");
                $stmt->bind_param('sssi', $cat_name, $cat_slug, $cat_desc, $is_active);
                if ($stmt->execute()) {
                    $feedback_message = "Category added successfully.";
                    $action = 'list';
                } else {
                    $error_message = "Failed to add category.";
                }
                $stmt->close();
            } else if ($action === 'edit' && $category_id) {
                $stmt = $conn->prepare("UPDATE categories SET name=?, slug=?, description=?, is_active=? WHERE id=?");
                $stmt->bind_param('sssii', $cat_name, $cat_slug, $cat_desc, $is_active, $category_id);
                if ($stmt->execute()) {
                    $feedback_message = "Category updated successfully.";
                    $action = 'list';
                } else {
                    $error_message = "Failed to update category.";
                }
                $stmt->close();
            }
        }
    } elseif (isset($_POST['delete_category']) && $category_id) {
        $stmt = $conn->prepare("DELETE FROM categories WHERE id=?");
        $stmt->bind_param('i', $category_id);
        if ($stmt->execute()) {
            $feedback_message = "Category deleted successfully.";
            $action = 'list';
        } else {
            $error_message = "Failed to delete category.";
        }
        $stmt->close();
    }
}

$current_category = null;
if (($action === 'edit' || $action === 'delete') && $category_id) {
    $res = $conn->query("SELECT * FROM categories WHERE id=$category_id");
    if ($res && $res->num_rows) {
        $current_category = $res->fetch_assoc();
    } else {
        $error_message = "Category not found for ID: $category_id";
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
                            <h2 class="gradient-text mb-0">Category List</h2>
                            <a href="categories.php?action=add" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Add New Category
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Active</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $res = $conn->query("SELECT * FROM categories ORDER BY id DESC");
                                    if ($res && $res->num_rows) {
                                        while ($cat = $res->fetch_assoc()) {
                                    ?>
                                        <tr>
                                            <td><?php echo $cat['id']; ?></td>
                                            <td><?php echo htmlspecialchars($cat['name']); ?></td>
                                            <td><?php echo htmlspecialchars($cat['slug']); ?></td>
                                            <td><?php echo $cat['is_active'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle-fill text-danger"></i>'; ?></td>
                                            <td class="text-center">
                                                <a href="categories.php?action=edit&id=<?php echo $cat['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i></a>
                                                <a href="categories.php?action=delete&id=<?php echo $cat['id']; ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php
                                        }
                                    } else {
                                    ?>
                                        <tr><td colspan="5" class="text-center">No categories found.</td></tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php elseif ($action === 'add' || ($action === 'edit' && $current_category)): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="gradient-text mb-4"><?php echo $action === 'add' ? 'Add New Category' : 'Edit Category: ' . htmlspecialchars($current_category['name']); ?></h2>
                        <form action="categories.php<?php echo $action === 'edit' ? '?action=edit&id=' . $category_id : '?action=add'; ?>" method="POST">
                            <div class="mb-3">
                                <label for="category_name" class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="category_name" name="category_name" value="<?php echo htmlspecialchars($current_category['name'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="category_slug" class="form-label">Slug (URL)</label>
                                <input type="text" class="form-control" id="category_slug" name="category_slug" value="<?php echo htmlspecialchars($current_category['slug'] ?? ''); ?>">
                                <div class="form-text">Leave blank to auto-generate from name.</div>
                            </div>
                            <div class="mb-3">
                                <label for="category_description" class="form-label">Description</label>
                                <textarea class="form-control" id="category_description" name="category_description" rows="3"><?php echo htmlspecialchars($current_category['description'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" <?php if (isset($current_category['is_active']) && $current_category['is_active']) echo 'checked'; elseif($action === 'add') echo 'checked'; ?>>
                                <label class="form-check-label" for="is_active">Active (Visible on site)</label>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" name="save_category" class="btn btn-primary">
                                    <i class="bi bi-save me-2"></i>Save Category
                                </button>
                                <a href="categories.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php elseif ($action === 'delete' && $current_category): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="gradient-text mb-4">Delete Category: <?php echo htmlspecialchars($current_category['name']); ?></h2>
                        <div class="alert alert-danger">Are you sure you want to delete this category? This may affect products associated with it.</div>
                        <form action="categories.php?action=delete&id=<?php echo $category_id; ?>" method="POST">
                            <div class="d-flex gap-2">
                                <button type="submit" name="delete_category" class="btn btn-danger">
                                    <i class="bi bi-trash me-2"></i>Confirm Delete
                                </button>
                                <a href="categories.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">Invalid action or category not found. <a href="categories.php">Return to category list.</a></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../includes/footer.php';
?> 