<?php
$page_title = "About Us - 2nd Phone Shop";
include_once __DIR__ . '/includes/header.php';

// Fetch mission and vision from database
$mission = '';
$vision = '';
$sql = "SELECT mission, vision FROM site_info LIMIT 1";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $mission = $row['mission'];
    $vision = $row['vision'];
}
?>

<div class="admin-page">
    <div class="container py-5">
        <h1 class="gradient-text mb-4">About Us</h1>
        <p class="lead mb-5">Learn more about our mission, vision, and commitment to providing quality second-hand phones.</p>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <i class="bi bi-bullseye display-4 text-primary me-3"></i>
                            <h3 class="card-title mb-0">Our Mission</h3>
                        </div>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($mission)); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <i class="bi bi-eye display-4 text-primary me-3"></i>
                            <h3 class="card-title mb-0">Our Vision</h3>
                        </div>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($vision)); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-shield-check display-4 text-primary mb-3"></i>
                        <h4 class="card-title">Quality Assurance</h4>
                        <p class="card-text">Every phone undergoes rigorous testing and quality checks to ensure you receive a reliable device.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-currency-dollar display-4 text-primary mb-3"></i>
                        <h4 class="card-title">Best Value</h4>
                        <p class="card-text">Get premium smartphones at affordable prices without compromising on quality.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-headset display-4 text-primary mb-3"></i>
                        <h4 class="card-title">Customer Support</h4>
                        <p class="card-text">Our dedicated team is always ready to assist you with any questions or concerns.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-5">
            <div class="card-body p-4">
                <h3 class="card-title mb-4">Why Choose Us?</h3>
                <div class="row g-4">
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <strong>Quality Tested Devices</strong>
                                <p class="text-muted mb-0">All phones are thoroughly tested and certified.</p>
                            </li>
                            <li class="mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <strong>Warranty Included</strong>
                                <p class="text-muted mb-0">Enjoy peace of mind with our warranty coverage.</p>
                            </li>
                            <li class="mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <strong>Secure Transactions</strong>
                                <p class="text-muted mb-0">Safe and secure payment processing.</p>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <strong>Fast Shipping</strong>
                                <p class="text-muted mb-0">Quick delivery to your doorstep.</p>
                            </li>
                            <li class="mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <strong>Easy Returns</strong>
                                <p class="text-muted mb-0">Hassle-free return policy.</p>
                            </li>
                            <li class="mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <strong>Expert Support</strong>
                                <p class="text-muted mb-0">Professional assistance when you need it.</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/includes/footer.php';
?> 