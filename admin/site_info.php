<?php
$page_title = "Edit Site Info - 2nd Phone Shop";
include_once __DIR__ . '/../includes/header.php';

// Ensure user is logged in AND is an admin
if (!isLoggedIn()) {
    header('Location: ../login/login.php?message=Please login to access the admin panel.');
    exit;
}
if (!isAdmin()) {
    header('Location: ../user/dashboard.php?message=Access Denied: You do not have admin privileges.');
    exit;
}

// Fetch current mission and vision
$site_info = $conn->query("SELECT mission, vision FROM site_info WHERE id=1")->fetch_assoc();
$mission = $site_info ? $site_info['mission'] : '';
$vision = $site_info ? $site_info['vision'] : '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_mission = $_POST['mission'];
    $new_vision = $_POST['vision'];
    $stmt = $conn->prepare("UPDATE site_info SET mission=?, vision=? WHERE id=1");
    $stmt->bind_param("ss", $new_mission, $new_vision);
    $stmt->execute();
    header("Location: site_info.php?success=1");
    exit;
}
?>

<div class="admin-page">
    <div class="d-flex flex-column flex-lg-row">
        <?php include_once __DIR__ . '/includes/admin_nav.php'; ?>

        <div class="flex-grow-1">
            <h1 class="gradient-text mb-4"><?php echo htmlspecialchars($page_title); ?></h1>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-2"></i>Site info updated successfully!
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="post">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="mission" class="form-label">Mission</label>
                                    <textarea name="mission" id="mission" class="form-control" rows="6"><?php echo htmlspecialchars($mission); ?></textarea>
                                    <div class="form-text">Your company's mission statement should reflect your core purpose and values.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="vision" class="form-label">Vision</label>
                                    <textarea name="vision" id="vision" class="form-control" rows="6"><?php echo htmlspecialchars($vision); ?></textarea>
                                    <div class="form-text">Your company's vision statement should describe your long-term goals and aspirations.</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../includes/footer.php';
?> 