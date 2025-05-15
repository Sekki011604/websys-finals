<?php
$page_title = "Homepage Settings - Admin Panel";
include_once __DIR__ . '/../includes/header.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login/login.php?message=Access Denied');
    exit;
}

// --- Handle Form Submissions ---
$hero_message = $brand_message = $review_message = '';

// Save Hero Section (with file upload)
if (isset($_POST['hero_title'])) {
    $title = trim($_POST['hero_title']);
    $subtitle = trim($_POST['hero_subtitle']);
    $button_text = trim($_POST['hero_button_text']);
    $button_link = trim($_POST['hero_button_link']);
    $image_url = '';
    // Handle file upload
    if (isset($_FILES['hero_image_file']) && $_FILES['hero_image_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['hero_image_file']['tmp_name'];
        $file_name = basename($_FILES['hero_image_file']['name']);
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp','svg'];
        if (in_array($ext, $allowed)) {
            $new_name = 'hero_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            $target_dir = __DIR__ . '/../asset/images/upload/';
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $target_file = $target_dir . $new_name;
            if (move_uploaded_file($file_tmp, $target_file)) {
                $image_url = 'asset/images/upload/' . $new_name;
            }
        }
    } else {
        // If no new file uploaded, keep the old image
        $image_url = $hero['image_url'] ?? '';
    }
    // Upsert (insert or update)
    $stmt = $conn->prepare("REPLACE INTO homepage_hero (id, title, subtitle, button_text, button_link, image_url) VALUES (1,?,?,?,?,?)");
    $stmt->bind_param('sssss', $title, $subtitle, $button_text, $button_link, $image_url);
    if ($stmt->execute()) {
        $hero_message = '<div class="alert alert-success">Hero section updated.</div>';
    } else {
        $hero_message = '<div class="alert alert-danger">Failed to update hero section.</div>';
    }
    $stmt->close();
}

// Add Brand Logo (with file upload)
if (isset($_POST['brand_logo_url']) || isset($_FILES['brand_logo_file'])) {
    $logo_url = trim($_POST['brand_logo_url'] ?? '');
    $alt_text = trim($_POST['brand_alt_text'] ?? '');
    $upload_path = '';
    if (isset($_FILES['brand_logo_file']) && $_FILES['brand_logo_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['brand_logo_file']['tmp_name'];
        $file_name = basename($_FILES['brand_logo_file']['name']);
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp','svg'];
        if (in_array($ext, $allowed)) {
            $new_name = 'brand_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            $target_dir = __DIR__ . '/../asset/images/upload/';
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $target_file = $target_dir . $new_name;
            if (move_uploaded_file($file_tmp, $target_file)) {
                $upload_path = 'asset/images/upload/' . $new_name;
            }
        }
    }
    $final_logo = $upload_path ?: $logo_url;
    if ($final_logo) {
        $stmt = $conn->prepare("INSERT INTO homepage_brands (logo_url, alt_text) VALUES (?, ?)");
        $stmt->bind_param('ss', $final_logo, $alt_text);
        if ($stmt->execute()) {
            $brand_message = '<div class="alert alert-success">Brand logo added.</div>';
        } else {
            $brand_message = '<div class="alert alert-danger">Failed to add brand logo.</div>';
        }
        $stmt->close();
    }
}
// Delete Brand Logo
if (isset($_GET['delete_brand'])) {
    $id = intval($_GET['delete_brand']);
    $conn->query("DELETE FROM homepage_brands WHERE id=$id");
    header('Location: homepage_settings.php');
    exit;
}

// Add Review
if (isset($_POST['reviewer_name'])) {
    $name = trim($_POST['reviewer_name']);
    $avatar = trim($_POST['reviewer_avatar_url']);
    $text = trim($_POST['review_text']);
    $rating = intval($_POST['review_rating']);
    if ($name && $text && $rating >= 1 && $rating <= 5) {
        $stmt = $conn->prepare("INSERT INTO homepage_reviews (reviewer_name, avatar_url, review_text, rating) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('sssi', $name, $avatar, $text, $rating);
        if ($stmt->execute()) {
            $review_message = '<div class="alert alert-success">Review added.</div>';
        } else {
            $review_message = '<div class="alert alert-danger">Failed to add review.</div>';
        }
        $stmt->close();
    }
}
// Delete Review
if (isset($_GET['delete_review'])) {
    $id = intval($_GET['delete_review']);
    $conn->query("DELETE FROM homepage_reviews WHERE id=$id");
    header('Location: homepage_settings.php');
    exit;
}

// Handle Edit Brand (with file upload)
if (isset($_POST['edit_brand_id'])) {
    $edit_id = intval($_POST['edit_brand_id']);
    $edit_logo_url = trim($_POST['edit_brand_logo_url'] ?? '');
    $edit_alt_text = trim($_POST['edit_brand_alt_text'] ?? '');
    $upload_path = '';
    if (isset($_FILES['edit_brand_logo_file']) && $_FILES['edit_brand_logo_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['edit_brand_logo_file']['tmp_name'];
        $file_name = basename($_FILES['edit_brand_logo_file']['name']);
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp','svg'];
        if (in_array($ext, $allowed)) {
            $new_name = 'brand_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            $target_dir = __DIR__ . '/../asset/images/upload/';
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $target_file = $target_dir . $new_name;
            if (move_uploaded_file($file_tmp, $target_file)) {
                $upload_path = 'asset/images/upload/' . $new_name;
            }
        }
    }
    $final_logo = $upload_path ?: $edit_logo_url;
    $stmt = $conn->prepare("UPDATE homepage_brands SET logo_url=?, alt_text=? WHERE id=?");
    $stmt->bind_param('ssi', $final_logo, $edit_alt_text, $edit_id);
    if ($stmt->execute()) {
        $brand_message = '<div class="alert alert-success">Brand updated.</div>';
    } else {
        $brand_message = '<div class="alert alert-danger">Failed to update brand.</div>';
    }
    $stmt->close();
}
// Handle Edit Review
if (isset($_POST['edit_review_id'])) {
    $edit_id = intval($_POST['edit_review_id']);
    $edit_name = trim($_POST['edit_reviewer_name']);
    $edit_avatar = trim($_POST['edit_reviewer_avatar_url']);
    $edit_text = trim($_POST['edit_review_text']);
    $edit_rating = intval($_POST['edit_review_rating']);
    $stmt = $conn->prepare("UPDATE homepage_reviews SET reviewer_name=?, avatar_url=?, review_text=?, rating=? WHERE id=?");
    $stmt->bind_param('sssii', $edit_name, $edit_avatar, $edit_text, $edit_rating, $edit_id);
    if ($stmt->execute()) {
        $review_message = '<div class="alert alert-success">Review updated.</div>';
    } else {
        $review_message = '<div class="alert alert-danger">Failed to update review.</div>';
    }
    $stmt->close();
}

// --- Fetch Current Data ---
// Hero
$hero = ["title"=>"","subtitle"=>"","button_text"=>"","button_link"=>"","image_url"=>""];
$res = $conn->query("SELECT * FROM homepage_hero WHERE id=1");
if ($row = $res->fetch_assoc()) $hero = $row;
// Brands
$brands = [];
$res = $conn->query("SELECT * FROM homepage_brands ORDER BY id DESC");
while ($row = $res->fetch_assoc()) $brands[] = $row;
// Reviews
$reviews = [];
$res = $conn->query("SELECT * FROM homepage_reviews ORDER BY id DESC");
while ($row = $res->fetch_assoc()) $reviews[] = $row;

// Get edit mode IDs from GET
$edit_brand_id = isset($_GET['edit_brand']) ? intval($_GET['edit_brand']) : 0;
$edit_review_id = isset($_GET['edit_review']) ? intval($_GET['edit_review']) : 0;
?>

<div class="admin-page">
    <div class="d-flex flex-column flex-lg-row">
        <?php include_once __DIR__ . '/includes/admin_nav.php'; ?>

        <div class="flex-grow-1">
            <h1 class="gradient-text mb-4"><?php echo htmlspecialchars($page_title); ?></h1>

            <?php echo $hero_message; ?>
            <?php echo $brand_message; ?>
            <?php echo $review_message; ?>

            <!-- Hero Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="gradient-text mb-4">Hero Section</h2>
                    <form method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" class="form-control" name="hero_title" value="<?php echo htmlspecialchars($hero['title']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Subtitle</label>
                                    <input type="text" class="form-control" name="hero_subtitle" value="<?php echo htmlspecialchars($hero['subtitle']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Button Text</label>
                                    <input type="text" class="form-control" name="hero_button_text" value="<?php echo htmlspecialchars($hero['button_text']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Button Link</label>
                                    <input type="text" class="form-control" name="hero_button_link" value="<?php echo htmlspecialchars($hero['button_link']); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Hero Image</label>
                                    <input type="file" class="form-control" name="hero_image_file" accept="image/*">
                                    <?php if (!empty($hero['image_url'])): ?>
                                        <div class="mt-2">
                                            <img src="../<?php echo htmlspecialchars($hero['image_url']); ?>" alt="Hero Image" class="img-thumbnail" style="max-width:220px;max-height:120px;">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Save Hero Section
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Brand Logos Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="gradient-text mb-4">Brand Logos</h2>
                    <form method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Brand Logo Image</label>
                                    <input type="file" class="form-control" name="brand_logo_file" accept="image/*">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Or Brand Logo URL</label>
                                    <input type="text" class="form-control" name="brand_logo_url" value="">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Alt Text</label>
                                    <input type="text" class="form-control" name="brand_alt_text" value="">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Add Brand Logo
                            </button>
                        </div>
                    </form>

                    <div class="mt-4">
                        <h3 class="gradient-text mb-3">Existing Brands</h3>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Logo</th>
                                        <th>Alt Text</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($brands as $b): ?>
                                        <tr>
                                            <td>
                                                <img src="<?php echo htmlspecialchars($b['logo_url']); ?>" 
                                                     alt="<?php echo htmlspecialchars($b['alt_text']); ?>" 
                                                     style="height:32px;max-width:80px;object-fit:contain;">
                                            </td>
                                            <td><?php echo htmlspecialchars($b['alt_text']); ?></td>
                                            <td class="text-center">
                                                <a href="?edit_brand=<?php echo $b['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <a href="?delete_brand=<?php echo $b['id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Delete this brand?');">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="gradient-text mb-4">Customer Reviews</h2>
                    <form method="post">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Reviewer Name</label>
                                    <input type="text" class="form-control" name="reviewer_name" value="">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Avatar URL</label>
                                    <input type="text" class="form-control" name="reviewer_avatar_url" value="">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Rating (1-5)</label>
                                    <input type="number" class="form-control" name="review_rating" min="1" max="5" value="5">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Review Text</label>
                                    <textarea class="form-control" name="review_text" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Add Review
                            </button>
                        </div>
                    </form>

                    <div class="mt-4">
                        <h3 class="gradient-text mb-3">Existing Reviews</h3>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Reviewer</th>
                                        <th>Rating</th>
                                        <th>Review</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reviews as $r): ?>
                                        <tr>
                                            <td>
                                                <?php if ($r['avatar_url']): ?>
                                                    <img src="<?php echo htmlspecialchars($r['avatar_url']); ?>" 
                                                         alt="<?php echo htmlspecialchars($r['reviewer_name']); ?>" 
                                                         class="rounded-circle me-2" 
                                                         style="width:32px;height:32px;object-fit:cover;">
                                                <?php endif; ?>
                                                <?php echo htmlspecialchars($r['reviewer_name']); ?>
                                            </td>
                                            <td>
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="bi bi-star<?php echo $i <= $r['rating'] ? '-fill text-warning' : ''; ?>"></i>
                                                <?php endfor; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($r['review_text']); ?></td>
                                            <td class="text-center">
                                                <a href="?edit_review=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <a href="?delete_review=<?php echo $r['id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Delete this review?');">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
