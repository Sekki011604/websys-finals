<?php
$page_title = "My Profile - 2nd Phone Shop";
include_once __DIR__ . '/../includes/header.php';

// Ensure user is logged in
if (!isLoggedIn()) {
    header('Location: ../login/login.php?message=Please login to view your profile.');
    exit;
}

$user_id = $_SESSION['user_id'];
$user = [];
$message = '';

// Fetch user data from the database
$sql = "SELECT * FROM user WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $user = $row;
        }
    } else {
        error_log("Error fetching user data: " . $conn->error);
        echo '<div class="alert alert-danger">An error occurred while fetching your profile. Please try again later.</div>';
    }
    $stmt->close();
} else {
    error_log("Error fetching user data: " . $conn->error);
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $mobile_number = trim($_POST['mobile_number']);
        $address = trim($_POST['address']);

        // Validate input
        if (empty($first_name) || empty($last_name) || empty($email)) {
            $message = '<div class="alert alert-danger">Please fill in all required fields.</div>';
        } else {
            // Update user data
            $sql = "UPDATE user SET first_name = ?, last_name = ?, email = ?, mobile_number = ?, address = ? WHERE id = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("sssssi", $first_name, $last_name, $email, $mobile_number, $address, $user_id);
                if ($stmt->execute()) {
                    $message = '<div class="alert alert-success">Profile updated successfully!</div>';
                    // Refresh user data
                    $user['first_name'] = $first_name;
                    $user['last_name'] = $last_name;
                    $user['email'] = $email;
                    $user['mobile_number'] = $mobile_number;
                    $user['address'] = $address;
                } else {
                    $message = '<div class="alert alert-danger">An error occurred while updating your profile. Please try again later.</div>';
                }
                $stmt->close();
            }
        }
    } elseif (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Validate input
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $message = '<div class="alert alert-danger">Please fill in all password fields.</div>';
        } elseif ($new_password !== $confirm_password) {
            $message = '<div class="alert alert-danger">New passwords do not match.</div>';
        } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&_])[A-Za-z\d@$!%*#?&_]{8,}$/", $new_password)) {
            $message = '<div class="alert alert-danger">Password must be at least 8 characters, with 1 uppercase, 1 lowercase, 1 number, and 1 special character.</div>';
        } else {
            // Verify current password
            $sql = "SELECT password_hash FROM user WHERE id = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $user_id);
                if ($stmt->execute()) {
                    $result = $stmt->get_result();
                    if ($row = $result->fetch_assoc()) {
                        if (password_verify($current_password, $row['password_hash'])) {
                            // Update password
                            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                            $update_sql = "UPDATE user SET password_hash = ? WHERE id = ?";
                            if ($update_stmt = $conn->prepare($update_sql)) {
                                $update_stmt->bind_param("si", $new_password_hash, $user_id);
                                if ($update_stmt->execute()) {
                                    $message = '<div class="alert alert-success">Password updated successfully!</div>';
                                } else {
                                    $message = '<div class="alert alert-danger">An error occurred while updating your password. Please try again later.</div>';
                                }
                                $update_stmt->close();
                            }
                        } else {
                            $message = '<div class="alert alert-danger">Current password is incorrect.</div>';
                        }
                    }
                }
                $stmt->close();
            }
        }
    }
}

// Determine which section to display
$active_section = isset($_GET['section']) ? $_GET['section'] : 'profile';
?>

<div class="admin-page">
    <div class="d-flex flex-column flex-lg-row">
        <nav class="nav flex-column nav-pills bg-white p-3 mb-3 mb-lg-0 me-lg-3 rounded shadow-sm" style="width: 250px; height: 100%; position: sticky; top: 1rem;">
            <div class="d-flex flex-column h-100">
                <div class="flex-grow-1">
                    <a class="nav-link" href="dashboard.php?page=overview" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <i class="bi bi-person-circle me-2"></i>Dashboard Overview
                    </a>
                    <a class="nav-link" href="orders.php" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <i class="bi bi-box-seam me-2"></i>My Orders
                    </a>
                    <a class="nav-link" href="addresses.php" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <i class="bi bi-geo-alt me-2"></i>Manage Addresses
                    </a>
                    <a class="nav-link <?php echo $active_section === 'profile' ? 'active' : ''; ?>" href="profile.php" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <i class="bi bi-person-badge me-2"></i>My Profile
                    </a>
                    <a class="nav-link <?php echo $active_section === 'settings' ? 'active' : ''; ?>" href="profile.php?section=settings" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <i class="bi bi-gear me-2"></i>Account Settings
                    </a>
                </div>
                <div class="mt-auto">
                    <a class="nav-link text-danger" href="../logout.php" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </a>
                </div>
            </div>
        </nav>

        <div class="flex-grow-1">
            <?php if ($active_section === 'settings'): ?>
                <h1 class="gradient-text mb-4">Account Settings</h1>
                <p class="lead mb-4">Manage your account security and preferences.</p>

                <?php echo $message; ?>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">Change Password</h5>
                        <form method="POST" action="">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    <div class="form-text">Must be at least 8 characters, with 1 uppercase, 1 lowercase, 1 number, and 1 special character.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="change_password" class="btn btn-primary">
                                        <i class="bi bi-key me-2"></i>Change Password
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <h1 class="gradient-text mb-4">My Profile</h1>
                <p class="lead mb-4">View and update your personal information.</p>

                <?php echo $message; ?>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form method="POST" action="">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="mobile_number" class="form-label">Mobile Number</label>
                                    <input type="tel" class="form-control" id="mobile_number" name="mobile_number" value="<?php echo htmlspecialchars($user['mobile_number'] ?? ''); ?>">
                                </div>
                                <div class="col-12">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="update_profile" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Update Profile
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../includes/footer.php';
?> 