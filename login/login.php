<?php
$page_title = "Login - 2nd Phone Shop";
// Note: The header.php now includes session_start() and db_connection.php
// The path to header.php needs to be adjusted because login.php is in a subfolder.
include_once __DIR__ . '/../includes/header.php';

// Variables (moved from original top, $email already declared in header scope potentially, ensure no conflict or use different var names if needed)
// For this page, we can redefine $email for form resubmission context if needed.
$login_email = ""; // Use a different name to avoid conflict if $email from header is used for something else
$errorMessages = []; // Re-initialize for page-specific errors, even if declared in header
$login_attempt_message = "";

// If user is already logged in (checked in header.php too, but good for clarity or specific logic here)
// if (isLoggedIn()) {
//     header("Location: ../index.php"); 
//     exit();
// }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_email = trim($_POST['email']);
    $password = $_POST['password'];
    $errorMessages = []; // Reset for current submission

    if (empty($login_email)) {
        $errorMessages[] = "Email is required.";
    } elseif (!filter_var($login_email, FILTER_VALIDATE_EMAIL)) {
        $errorMessages[] = "Invalid email format.";
    }

    if (empty($password)) {
        $errorMessages[] = "Password is required.";
    }

    if (empty($errorMessages)) {
        $sql = "SELECT id, first_name, last_name, email, password_hash, role, is_active FROM user WHERE email = ? LIMIT 1";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $login_email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                if (!$user['is_active']) {
                    $login_attempt_message = "Your account is inactive. Please contact support.";
                    $activity_type = 'LOGIN_ATTEMPT_INACTIVE_ACCOUNT';
                    $description = "Login attempt for inactive account '{$login_email}'."; 
                } elseif (password_verify($password, $user['password_hash'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_first_name'] = $user['first_name'];
                    $_SESSION['user_last_name'] = $user['last_name'];
                    $_SESSION['user_role'] = $user['role'];
                    $update_login_sql = "UPDATE user SET last_login_at = CURRENT_TIMESTAMP WHERE id = ?";
                    if ($update_stmt = $conn->prepare($update_login_sql)){
                        $update_stmt->bind_param("i", $user['id']);
                        $update_stmt->execute();
                        $update_stmt->close();
                    }
                    $activity_type = 'USER_LOGIN_SUCCESS';
                    $description = "User '{$user['email']}' logged in successfully.";
                    $ip_address = $_SERVER['REMOTE_ADDR'];
                    $user_agent = $_SERVER['HTTP_USER_AGENT'];
                    $log_sql = "INSERT INTO audit_trail (user_id, user_email_snapshot, activity_type, target_entity, target_id, description, ip_address, user_agent) VALUES (?, ?, ?, 'user', ?, ?, ?, ?)";
                    if ($log_stmt = $conn->prepare($log_sql)) {
                        $log_stmt->bind_param("issssss", $user['id'], $user['email'], $activity_type, $user['id'], $description, $ip_address, $user_agent);
                        if(!$log_stmt->execute()){ error_log("Audit trail logging failed for successful login: " . $log_stmt->error); }
                        $log_stmt->close();
                    } else { error_log("Audit trail statement preparation failed for login: " . $conn->error);}
                    header("Location: ../index.php"); 
                    exit();
                } else {
                    $login_attempt_message = "Invalid email or password.";
                    $activity_type = 'USER_LOGIN_FAILURE';
                    $description = "Failed login attempt for email '{$login_email}'. Reason: Incorrect password.";
                }
            } else {
                $login_attempt_message = "Invalid email or password.";
                $activity_type = 'USER_LOGIN_FAILURE';
                $description = "Failed login attempt for email '{$login_email}'. Reason: User not found.";
            }
            $stmt->close();
        } else {
            $login_attempt_message = "Login error. Please try again later.";
            error_log("Login statement preparation failed: " . $conn->error);
        }

        if (!empty($login_attempt_message) && isset($activity_type) && isset($description)) {
            $current_user_id_for_log = isset($user) && $user ? $user['id'] : null;
            $current_user_email_for_log = $login_email; 
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            $target_id_for_log = $current_user_id_for_log;
            $log_sql = "INSERT INTO audit_trail (user_id, user_email_snapshot, activity_type, target_entity, target_id, description, ip_address, user_agent) VALUES (?, ?, ?, 'user', ?, ?, ?, ?)";
            if ($log_stmt = $conn->prepare($log_sql)) {
                $log_stmt->bind_param("issssss", $current_user_id_for_log, $current_user_email_for_log, $activity_type, $target_id_for_log, $description, $ip_address, $user_agent);
                if(!$log_stmt->execute()){ error_log("Audit trail logging failed for {$activity_type}: " . $log_stmt->error);}
                $log_stmt->close();
            } else { error_log("Audit trail statement preparation failed for {$activity_type}: " . $conn->error);}
        }
    }
}
// The rest of the HTML form from login.php follows
?>

<div class="auth-center-wrapper">
    <div class="auth-container">
        <div class="auth-card">
            <h2>Login</h2>

            <?php if (!empty($login_attempt_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($login_attempt_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($errorMessages)): ?>
                <div class="alert alert-danger"><ul>
                    <?php foreach ($errorMessages as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul></div>
            <?php endif; ?>

            <?php 
            if (isset($_SESSION['registration_success'])):
            ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($_SESSION['registration_success']); unset($_SESSION['registration_success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form id="loginForm" action="login.php" method="POST" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control <?php if(!empty($login_email) && (in_array("Email is required.", $errorMessages) || in_array("Invalid email format.", $errorMessages))) echo 'is-invalid'; ?>" id="email" name="email" value="<?php echo htmlspecialchars($login_email); ?>" required>
                    <div class="invalid-feedback">
                        <?php 
                            if(in_array("Email is required.", $errorMessages)) echo "Email is required.";
                            elseif(in_array("Invalid email format.", $errorMessages)) echo "Invalid email format."; 
                        ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control <?php if(in_array("Password is required.", $errorMessages)) echo 'is-invalid'; ?>" id="password" name="password" required>
                     <div class="invalid-feedback">
                        <?php if(in_array("Password is required.", $errorMessages)) echo "Password is required."; ?>
                    </div>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="rememberMe" name="remember_me">
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary auth-btn">Login</button>
            </form>
            <p class="text-center mt-3">Don't have an account? <a href="../register.php">Register here</a></p>
            <p class="text-center mt-2"><a href="#">Forgot password?</a></p>
        </div>
    </div>
</div>

    <?php // Specific JS for login page (jQuery is loaded via CDN in footer if needed) ?>
    <script src="https://code.jquery.com/jquery-3.7.0.slim.min.js"></script> 
    <script>
        $(document).ready(function() {
            $("#loginForm").on('submit', function(e) {
                let emailField = $("#email");
                let passwordField = $("#password");
                let email = emailField.val().trim();
                let password = passwordField.val().trim();
                let errors = false;

                emailField.removeClass('is-invalid');
                passwordField.removeClass('is-invalid');
                emailField.siblings('.invalid-feedback').text('');
                passwordField.siblings('.invalid-feedback').text('');

                if (!email) {
                    emailField.addClass('is-invalid').siblings('.invalid-feedback').text('Email is required.');
                    errors = true;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    emailField.addClass('is-invalid').siblings('.invalid-feedback').text('Invalid email format.');
                    errors = true;
                }

                if (!password) {
                    passwordField.addClass('is-invalid').siblings('.invalid-feedback').text('Password is required.');
                    errors = true;
                }

                if (errors) {
                    e.preventDefault();
                }
            });
        });
    </script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?> 