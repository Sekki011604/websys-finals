<?php
$page_title = "Site Settings - Admin Panel";
include_once __DIR__ . '/../includes/header.php';

// Admin access check
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login/login.php?message=Access Denied');
    exit;
}

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $success = true;
    foreach ($_POST['settings'] as $key => $value) {
        $stmt = $conn->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
        $stmt->bind_param('ss', $value, $key);
        if (!$stmt->execute()) {
            $success = false;
            $error_message = "Error updating settings: " . $conn->error;
        }
        $stmt->close();
    }
    if ($success) {
        $success_message = "Settings updated successfully!";
    }
}

// Get all settings grouped by category
$settings = [];
$result = $conn->query("SELECT * FROM site_settings ORDER BY setting_group, id");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_group']][] = $row;
    }
}

// Get current active tab
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
?>

<div class="admin-page">
<div class="d-flex flex-column flex-lg-row">
        <?php include_once __DIR__ . '/includes/admin_nav.php'; ?>

    <div class="flex-grow-1">
            <h1 class="gradient-text mb-4"><?php echo htmlspecialchars($page_title); ?></h1>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="">
                        <!-- Settings Tabs -->
                        <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php echo $active_tab === 'general' ? 'active' : ''; ?>" 
                                        id="general-tab" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#general" 
                                        type="button" 
                                        role="tab">
                                    <i class="bi bi-gear me-2"></i>General
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php echo $active_tab === 'social' ? 'active' : ''; ?>" 
                                        id="social-tab" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#social" 
                                        type="button" 
                                        role="tab">
                                    <i class="bi bi-share me-2"></i>Social Media
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php echo $active_tab === 'shop' ? 'active' : ''; ?>" 
                                        id="shop-tab" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#shop" 
                                        type="button" 
                                        role="tab">
                                    <i class="bi bi-shop me-2"></i>Shop
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php echo $active_tab === 'seo' ? 'active' : ''; ?>" 
                                        id="seo-tab" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#seo" 
                                        type="button" 
                                        role="tab">
                                    <i class="bi bi-search me-2"></i>SEO
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php echo $active_tab === 'maintenance' ? 'active' : ''; ?>" 
                                        id="maintenance-tab" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#maintenance" 
                                        type="button" 
                                        role="tab">
                                    <i class="bi bi-tools me-2"></i>Maintenance
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="settingsTabContent">
                            <!-- General Settings -->
                            <div class="tab-pane fade <?php echo $active_tab === 'general' ? 'show active' : ''; ?>" 
                                 id="general" 
                                 role="tabpanel">
                                <div class="row g-4">
                                    <?php foreach ($settings['general'] ?? [] as $setting): ?>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                    <?php echo htmlspecialchars($setting['setting_label']); ?>
                                                </label>
                                                <?php if ($setting['setting_type'] === 'textarea'): ?>
                                                    <textarea class="form-control" 
                                                              id="<?php echo $setting['setting_key']; ?>" 
                                                              name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                              rows="3"><?php echo htmlspecialchars($setting['setting_value']); ?></textarea>
                                                <?php else: ?>
                                                    <input type="<?php echo $setting['setting_type']; ?>" 
                                                           class="form-control" 
                                                           id="<?php echo $setting['setting_key']; ?>" 
                                                           name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                           value="<?php echo htmlspecialchars($setting['setting_value']); ?>">
                                                <?php endif; ?>
                                                <?php if ($setting['setting_description']): ?>
                                                    <div class="form-text"><?php echo htmlspecialchars($setting['setting_description']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Social Media Settings -->
                            <div class="tab-pane fade <?php echo $active_tab === 'social' ? 'show active' : ''; ?>" 
                                 id="social" 
                                 role="tabpanel">
                                <div class="row g-4">
                                    <?php foreach ($settings['social'] ?? [] as $setting): ?>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                    <?php echo htmlspecialchars($setting['setting_label']); ?>
                                                </label>
                                                <input type="<?php echo $setting['setting_type']; ?>" 
                                                       class="form-control" 
                                                       id="<?php echo $setting['setting_key']; ?>" 
                                                       name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                       value="<?php echo htmlspecialchars($setting['setting_value']); ?>">
                                                <?php if ($setting['setting_description']): ?>
                                                    <div class="form-text"><?php echo htmlspecialchars($setting['setting_description']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Shop Settings -->
                            <div class="tab-pane fade <?php echo $active_tab === 'shop' ? 'show active' : ''; ?>" 
                                 id="shop" 
                                 role="tabpanel">
                                <div class="row g-4">
                                    <?php foreach ($settings['shop'] ?? [] as $setting): ?>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                    <?php echo htmlspecialchars($setting['setting_label']); ?>
                                                </label>
                                                <input type="<?php echo $setting['setting_type']; ?>" 
                                                       class="form-control" 
                                                       id="<?php echo $setting['setting_key']; ?>" 
                                                       name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                       value="<?php echo htmlspecialchars($setting['setting_value']); ?>">
                                                <?php if ($setting['setting_description']): ?>
                                                    <div class="form-text"><?php echo htmlspecialchars($setting['setting_description']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- SEO Settings -->
                            <div class="tab-pane fade <?php echo $active_tab === 'seo' ? 'show active' : ''; ?>" 
                                 id="seo" 
                                 role="tabpanel">
                                <div class="row g-4">
                                    <?php foreach ($settings['seo'] ?? [] as $setting): ?>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                    <?php echo htmlspecialchars($setting['setting_label']); ?>
                                                </label>
                                                <?php if ($setting['setting_type'] === 'textarea'): ?>
                                                    <textarea class="form-control" 
                                                              id="<?php echo $setting['setting_key']; ?>" 
                                                              name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                              rows="3"><?php echo htmlspecialchars($setting['setting_value']); ?></textarea>
                                                <?php else: ?>
                                                    <input type="<?php echo $setting['setting_type']; ?>" 
                                                           class="form-control" 
                                                           id="<?php echo $setting['setting_key']; ?>" 
                                                           name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                           value="<?php echo htmlspecialchars($setting['setting_value']); ?>">
                                                <?php endif; ?>
                                                <?php if ($setting['setting_description']): ?>
                                                    <div class="form-text"><?php echo htmlspecialchars($setting['setting_description']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Maintenance Settings -->
                            <div class="tab-pane fade <?php echo $active_tab === 'maintenance' ? 'show active' : ''; ?>" 
                                 id="maintenance" 
                                 role="tabpanel">
                                <div class="row g-4">
                                    <?php foreach ($settings['maintenance'] ?? [] as $setting): ?>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                    <?php echo htmlspecialchars($setting['setting_label']); ?>
                                                </label>
                                                <?php if ($setting['setting_type'] === 'boolean'): ?>
                                                    <select class="form-select" 
                                                            id="<?php echo $setting['setting_key']; ?>" 
                                                            name="settings[<?php echo $setting['setting_key']; ?>]">
                                                        <option value="1" <?php echo $setting['setting_value'] ? 'selected' : ''; ?>>Enabled</option>
                                                        <option value="0" <?php echo !$setting['setting_value'] ? 'selected' : ''; ?>>Disabled</option>
                                                    </select>
                                                <?php elseif ($setting['setting_type'] === 'textarea'): ?>
                                                    <textarea class="form-control" 
                                                              id="<?php echo $setting['setting_key']; ?>" 
                                                              name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                              rows="3"><?php echo htmlspecialchars($setting['setting_value']); ?></textarea>
                                                <?php else: ?>
                                                    <input type="<?php echo $setting['setting_type']; ?>" 
                                                           class="form-control" 
                                                           id="<?php echo $setting['setting_key']; ?>" 
                                                           name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                           value="<?php echo htmlspecialchars($setting['setting_value']); ?>">
                                                <?php endif; ?>
                                                <?php if ($setting['setting_description']): ?>
                                                    <div class="form-text"><?php echo htmlspecialchars($setting['setting_description']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" name="save_settings" class="btn btn-primary">
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