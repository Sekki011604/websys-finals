</div> <!-- End of main page content container -->

<?php if ($current_page === 'login.php' || $current_page === 'register.php'): ?>
    <footer class="bg-transparent text-muted text-center small py-3 mt-auto w-100">
        <p class="mb-0">&copy; <?php echo date("Y"); ?> 2nd Phone Shop. All Rights Reserved.</p>
    </footer>
<?php else: ?>
    <?php
    // Get contact information from settings
    $contact_email = getSetting('contact_email', '');
    $contact_phone = getSetting('contact_phone', '');
    $address = getSetting('address', '');

    // Get social media links from settings
    $facebook_url = getSetting('facebook_url', '');
    $instagram_url = getSetting('instagram_url', '');
    $twitter_url = getSetting('twitter_url', '');
    ?>
    <footer class="bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="gradient-text">About Us</h5>
                    <p><?php echo htmlspecialchars(getSetting('site_description', '')); ?></p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="gradient-text">Contact Info</h5>
                    <ul class="list-unstyled">
                        <?php if ($contact_email): ?>
                            <li><i class="bi bi-envelope me-2"></i> <?php echo htmlspecialchars($contact_email); ?></li>
                        <?php endif; ?>
                        <?php if ($contact_phone): ?>
                            <li><i class="bi bi-telephone me-2"></i> <?php echo htmlspecialchars($contact_phone); ?></li>
                        <?php endif; ?>
                        <?php if ($address): ?>
                            <li><i class="bi bi-geo-alt me-2"></i> <?php echo htmlspecialchars($address); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="gradient-text">Follow Us</h5>
                    <div class="social-links">
                        <?php if ($facebook_url): ?>
                            <a href="<?php echo htmlspecialchars($facebook_url); ?>" target="_blank" class="text-light me-3">
                                <i class="bi bi-facebook fs-4"></i>
                            </a>
                        <?php endif; ?>
                        <?php if ($instagram_url): ?>
                            <a href="<?php echo htmlspecialchars($instagram_url); ?>" target="_blank" class="text-light me-3">
                                <i class="bi bi-instagram fs-4"></i>
                            </a>
                        <?php endif; ?>
                        <?php if ($twitter_url): ?>
                            <a href="<?php echo htmlspecialchars($twitter_url); ?>" target="_blank" class="text-light">
                                <i class="bi bi-twitter fs-4"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(getSetting('site_name', '2nd Phone Shop')); ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="privacy.php" class="text-light me-3">Privacy Policy</a>
                    <a href="terms.php" class="text-light">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php 
    // Adjust jQuery path based on directory depth for pages that might still use it (like register.php)
    // Note: Bootstrap 5 JS components do not require jQuery.
    $jquery_path = 'asset/js/jquery-3.7.0.slim.min.js'; // Example path, adjust if you have local jQuery
    // For pages like register.php that used it directly in their script block:
    if ($current_page === 'register.php' || (isset($include_jquery) && $include_jquery)) {
      // echo '<script src="https://code.jquery.com/jquery-3.7.0.slim.min.js"></script>';
      // If you want to use a local copy:
      // echo '<script src="'. ((strpos($_SERVER['REQUEST_URI'], '/login/') !== false || strpos($_SERVER['REQUEST_URI'], '/user/') !== false) ? '../' : '') .'asset/js/jquery.min.js"></script>';
    }
?>
<!-- Add page-specific JS files here if needed -->

</body>
</html>
<?php
// Close the database connection if it was opened by the header
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?> 