<?php 
$page_title = "Shop - 2nd Phone Shop";
include_once __DIR__ . '/includes/header.php'; 
?>

<div class="admin-page">
    <div class="container-fluid px-4 py-5">
        <h1 class="gradient-text mb-4">Shop</h1>
        <p class="lead mb-4">Browse our collection of quality pre-owned smartphones.</p>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">All Categories</option>
                                    <?php
                                    // Fetch categories from database
                                    $cat_sql = "SELECT c.id, c.name, c.slug, COUNT(p.id) as product_count 
                                               FROM categories c 
                                               LEFT JOIN products p ON c.id = p.category_id AND p.is_active = TRUE 
                                               WHERE c.is_active = TRUE 
                                               GROUP BY c.id 
                                               ORDER BY c.name ASC";
                                    $cat_result = $conn->query($cat_sql);
                                    if ($cat_result && $cat_result->num_rows > 0) {
                                        while($category = $cat_result->fetch_assoc()) {
                                            echo '<option value="'.htmlspecialchars($category['id']).'" '.($category['id'] == (isset($_GET['category']) ? $_GET['category'] : '') ? 'selected' : '').'>'.htmlspecialchars($category['name']).'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="sort" class="form-label">Sort By</label>
                                <select class="form-select" id="sort" name="sort">
                                    <option value="newest" <?php echo isset($_GET['sort']) && $_GET['sort'] == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                                    <option value="price_low" <?php echo isset($_GET['sort']) && $_GET['sort'] == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                                    <option value="price_high" <?php echo isset($_GET['sort']) && $_GET['sort'] == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" placeholder="Search products...">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search me-2"></i>Apply Filters
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row g-4">
            <?php
            // Fetch products from database with filters
            $products_sql = "SELECT p.id, p.name, p.price, p.main_image_url, p.slug 
                             FROM products p 
                             WHERE p.is_active = TRUE";

            // Apply category filter
            if (isset($_GET['category']) && !empty($_GET['category'])) {
                $category_id = intval($_GET['category']);
                $products_sql .= " AND p.category_id = ?";
            }

            // Apply search filter
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search = '%' . $conn->real_escape_string($_GET['search']) . '%';
                $products_sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            }

            // Apply sorting
            $products_sql .= " ORDER BY ";
            if (isset($_GET['sort'])) {
                switch ($_GET['sort']) {
                    case 'price_low':
                        $products_sql .= "p.price ASC";
                        break;
                    case 'price_high':
                        $products_sql .= "p.price DESC";
                        break;
                    case 'newest':
                    default:
                        $products_sql .= "p.id DESC";
                        break;
                }
            } else {
                $products_sql .= "p.id DESC";
            }

            // Prepare and execute the query
            $stmt = $conn->prepare($products_sql);

            // Bind parameters if needed
            if (isset($_GET['category']) && !empty($_GET['category'])) {
                if (isset($_GET['search']) && !empty($_GET['search'])) {
                    $stmt->bind_param('iss', $category_id, $search, $search);
                } else {
                    $stmt->bind_param('i', $category_id);
                }
            } elseif (isset($_GET['search']) && !empty($_GET['search'])) {
                $stmt->bind_param('ss', $search, $search);
            }

            $stmt->execute();
            $products_result = $stmt->get_result();

            if ($products_result && $products_result->num_rows > 0) {
                while($product = $products_result->fetch_assoc()) {
                    echo '<div class="col-6 col-md-4 col-lg-3">';
                    echo '  <div class="card product-card p-3 text-center h-100">';
                    echo '    <a href="product_detail.php?slug='.htmlspecialchars($product['slug']).'"><img src="'.htmlspecialchars($product['main_image_url'] ? $product['main_image_url'] : 'https://via.placeholder.com/150?text=No+Image').'" class="card-img-top mx-auto" style="max-height:200px; object-fit:contain;" alt="'.htmlspecialchars($product['name']).'"></a>';
                    echo '    <div class="card-body d-flex flex-column">';
                    echo '      <h6 class="card-title mb-1"><a href="product_detail.php?slug='.htmlspecialchars($product['slug']).'" class="text-decoration-none text-dark">'.htmlspecialchars($product['name']).'</a></h6>';
                    echo '      <p class="card-text text-muted mb-2 mt-auto">₱'.number_format($product['price'], 2).'</p>';
                    echo '      <a href="#" data-id="'.htmlspecialchars($product['id']).'" class="btn btn-primary btn-sm auth-btn mt-2 add-to-cart-btn">Add to Cart</a>';
                    echo '    </div>';
                    echo '  </div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col-12"><p class="text-center">No products found.</p></div>';
            }
            ?>
        </div>
    </div>
</div>

<div id="cart-message" style="position:fixed;top:30px;right:30px;z-index:9999;display:none;" class="alert"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.add-to-cart-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var productId = this.getAttribute('data-id');
            fetch('add_to_cart.php?id=' + encodeURIComponent(productId))
                .then(response => response.json())
                .then(data => {
                    var msgDiv = document.getElementById('cart-message');
                    msgDiv.textContent = data.message;
                    msgDiv.className = 'alert ' + (data.success ? 'alert-success' : 'alert-danger');
                    msgDiv.style.display = 'block';
                    setTimeout(function() {
                        msgDiv.style.display = 'none';
                    }, 2000);
                });
        });
    });
});
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?> 