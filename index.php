<?php 
$page_title = "Welcome - 2nd Phone Shop";
include_once __DIR__ . '/includes/header.php'; 

// Fetch homepage content
$hero_data = null;
$hero_result = $conn->query("SELECT * FROM homepage_hero WHERE id = 1 LIMIT 1");
if ($hero_result && $hero_result->num_rows > 0) {
    $hero_data = $hero_result->fetch_assoc();
}

$brands_data = [];
$brands_result = $conn->query("SELECT * FROM homepage_brands WHERE is_active = 1 ORDER BY id ASC");
if ($brands_result) {
    while ($row = $brands_result->fetch_assoc()) {
        $brands_data[] = $row;
    }
}

$reviews_data = [];
$reviews_result = $conn->query("SELECT * FROM homepage_reviews WHERE is_active = 1 ORDER BY rating DESC, id DESC LIMIT 3"); // Show top 3 reviews
if ($reviews_result) {
    while ($row = $reviews_result->fetch_assoc()) {
        $reviews_data[] = $row;
    }
}

// Get total user count
$user_count = 0;
$user_count_result = $conn->query("SELECT COUNT(*) as total FROM user WHERE is_active = 1");
if ($user_count_result && $user_count_result->num_rows > 0) {
    $user_count = $user_count_result->fetch_assoc()['total'];
}

// Get total review count
$review_count = 0;
$review_count_result = $conn->query("SELECT COUNT(*) as total FROM product_reviews WHERE is_approved = 1");
if ($review_count_result && $review_count_result->num_rows > 0) {
    $review_count = $review_count_result->fetch_assoc()['total'];
}
?>

<?php /* Landing Page for 2nd Phone Shop */ ?>
<?php /* The DOCTYPE, html, head, opening body, and nav are now in header.php */ ?>

<section class="container py-5">
  <div class="row align-items-center">
    <div class="col-lg-6">
      <?php if ($hero_data): ?>
        <h1 class="display-4 fw-bold gradient-text mb-3"><?php echo htmlspecialchars($hero_data['title']); ?></h1>
        <p class="lead mb-4"><?php echo htmlspecialchars($hero_data['subtitle']); ?></p>
        <div class="mb-4">
          <a href="<?php echo htmlspecialchars($hero_data['button_link']); ?>" class="btn btn-dark btn-lg me-3"><?php echo htmlspecialchars($hero_data['button_text']); ?></a>
          <a href="about.php" class="btn btn-link btn-lg">Learn more <i class="bi bi-arrow-right"></i></a>
        </div>
      <?php else: ?>
        <h1 class="display-4 fw-bold gradient-text mb-3">SMART <span style="color:#ff4e8e">WATCHES</span> FACILITATE YOUR <span style="color:#ff4e8e">EVERY ACTIVITY</span></h1>
        <p class="lead mb-4">Everyone needs a smartwatch that helps to accurately track all-day steps, calorie consumption, distance traveled and heart rate. Always facilitating your every daily activity.</p>
        <div class="mb-4">
          <a href="shop.php" class="btn btn-dark btn-lg me-3">Order Now!</a>
          <a href="about.php" class="btn btn-link btn-lg">Learn more <i class="bi bi-arrow-right"></i></a>
        </div>
      <?php endif; ?>
      <?php if (!empty($brands_data)): ?>
      <div class="brand-logos d-flex align-items-center mt-4">
        <?php foreach ($brands_data as $brand): ?>
          <img src="<?php echo htmlspecialchars($brand['logo_url']); ?>" alt="<?php echo htmlspecialchars($brand['alt_text']); ?>" title="<?php echo htmlspecialchars($brand['alt_text']); ?>">
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <div class="brand-logos d-flex align-items-center mt-4">
        <p>No brands available.</p>
      </div>
      <?php endif; ?>
    </div>
    <div class="col-lg-6 text-center">
      <div class="hero-img p-4">
        <?php if ($hero_data && !empty($hero_data['image_url'])): ?>
            <img src="<?php echo htmlspecialchars($hero_data['image_url']); ?>" alt="Featured Product" class="img-fluid" style="width: 100%; height: 500px; object-fit: cover; border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        <?php else: ?>
            <p>No featured image available.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<section class="container pb-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Featured Products</h2>
    <a href="shop.php" class="btn btn-outline-primary">View All Products <i class="bi bi-arrow-right"></i></a>
  </div>
  <div class="row g-4">
    <?php
      $featured_sql = "SELECT id, name, price, main_image_url, slug FROM products WHERE is_featured = TRUE AND is_active = TRUE LIMIT 4";
      $featured_result = $conn->query($featured_sql);
      if ($featured_result && $featured_result->num_rows > 0) {
        while($product = $featured_result->fetch_assoc()) {
          echo '<div class="col-md-3">';
          echo '  <div class="card product-card p-3 text-center h-100 position-relative">';
          echo '    <div class="product-image-wrapper">';
          echo '      <a href="product_detail.php?slug='.htmlspecialchars($product['slug']).'"><img src="'.htmlspecialchars($product['main_image_url'] ? $product['main_image_url'] : 'https://via.placeholder.com/150?text=No+Image').'" class="card-img-top mx-auto" style="width: 100%; height: 200px; object-fit: contain; padding: 1rem; background: #fff; border-radius: 12px;" alt="'.htmlspecialchars($product['name']).'"></a>';
          echo '      <div class="product-overlay">';
          echo '        <a href="cart.php?action=add&id='.htmlspecialchars($product['id']).'" class="btn btn-primary btn-sm"><i class="bi bi-cart-plus"></i> Add to Cart</a>';
          echo '        <a href="product_detail.php?slug='.htmlspecialchars($product['slug']).'" class="btn btn-outline-primary btn-sm mt-2"><i class="bi bi-eye"></i> Quick View</a>';
          echo '      </div>';
          echo '    </div>';
          echo '    <div class="card-body d-flex flex-column">';
          echo '      <h6 class="card-title mb-1"><a href="product_detail.php?slug='.htmlspecialchars($product['slug']).'" class="text-decoration-none text-dark">'.htmlspecialchars($product['name']).'</a></h6>';
          echo '      <p class="card-text text-muted mb-2 mt-auto">₱'.number_format($product['price'], 2).'</p>';
          echo '    </div>';
          echo '  </div>';
          echo '</div>';
        }
      } else {
        echo '<p class="text-center">No featured products available at the moment.</p>';
      }
    ?>
  </div>
</section>

<section class="container pb-5">
  <div class="row">
    <div class="col-md-4 offset-md-8">
      <div class="review-card">
        <div class="mb-2"><strong>REVIEWS</strong> <span class="text-secondary"><?php echo number_format($user_count); ?>+ users</span></div>
        <div class="mb-2 small"><?php echo number_format($review_count); ?>+ reviews from our satisfied customers. What our loyal customers say.</div>
        <?php if (!empty($reviews_data)): ?>
            <?php foreach ($reviews_data as $review): ?>
            <div class="d-flex align-items-center mb-2">
              <?php if (!empty($review['avatar_url'])): ?>
                <img src="<?php echo htmlspecialchars($review['avatar_url']); ?>" class="avatar me-2" alt="<?php echo htmlspecialchars($review['reviewer_name']); ?>">
              <?php else: ?>
                <div class="avatar me-2 bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%; font-size: 18px;">
                    <?php echo strtoupper(substr($review['reviewer_name'], 0, 1)); ?>
                </div>
              <?php endif; ?>
              <div>
                <div class="fw-bold"><?php echo htmlspecialchars($review['reviewer_name']); ?> 
                    <span class="text-warning">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <?php echo ($i < $review['rating']) ? '★' : '☆'; ?>
                        <?php endfor; ?>
                    </span>
                </div>
                <div class="small"><?php echo htmlspecialchars($review['review_text']); ?></div>
              </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
        <p>No reviews available.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php /* The closing body and html tags, and scripts are in footer.php */ ?>
<?php include_once __DIR__ . '/includes/footer.php'; ?> 