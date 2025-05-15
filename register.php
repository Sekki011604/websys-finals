<?php 
$page_title = "Register - 2nd Phone Shop";
// Note: The header.php now includes session_start() and db_connection.php
include_once __DIR__ . '/includes/header.php';

// Declare variables for storing form data
$firstName = $middleName = $lastName = $email = $mobileNumber = $password = $confirmPassword = "";
$errorMessages = [];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Collect and sanitize form data (basic trim, consider more robust sanitization)
    $firstName = trim($_POST['first_name']);
    $middleName = trim($_POST['middle_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $mobileNumber = trim($_POST['mobile_number']);
    $password = $_POST['password']; // Password validation handles complexity, no trim needed before hashing
    $confirmPassword = $_POST['confirm_password'];

    // Validate First Name
    if (empty($firstName) || strlen($firstName) < 2 || !preg_match("/^[A-Za-z\s]+$/", $firstName)) {
        $errorMessages[] = "First Name is required, should be at least 2 characters, and contain only letters and spaces.";
    }

    // Validate Middle Name (optional)
    if (!empty($middleName) && (strlen($middleName) < 1 || !preg_match("/^[A-Za-z\s]*$/", $middleName))) {
        $errorMessages[] = "Middle Name should contain only letters and spaces.";
    }

    // Validate Last Name
    if (empty($lastName) || strlen($lastName) < 2 || !preg_match("/^[A-Za-z\s]+$/", $lastName)) {
        $errorMessages[] = "Last Name is required, should be at least 2 characters, and contain only letters and spaces.";
    }

    // Validate Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessages[] = "Please enter a valid email address.";
    } else {
        // Check if email already exists
        $check_email_sql = "SELECT email FROM user WHERE email = ?";
        if ($stmt = $conn->prepare($check_email_sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $errorMessages[] = "Email address already registered.";
            }
            $stmt->close();
        } else {
            $errorMessages[] = "Error checking email uniqueness: " . $conn->error;
            error_log("DB Error (check email): " . $conn->error);
        }
    }

    // Validate Mobile Number (Philippine format: 09xxxxxxxxx)
    if (!preg_match("/^09\d{9}$/", $mobileNumber)) {
        $errorMessages[] = "Mobile number must start with 09 and be exactly 11 digits.";
    } else {
        // Check if mobile number already exists
        $check_mobile_sql = "SELECT mobile_number FROM user WHERE mobile_number = ?";
        if ($stmt = $conn->prepare($check_mobile_sql)) {
            $stmt->bind_param("s", $mobileNumber);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $errorMessages[] = "Mobile number already registered.";
            }
            $stmt->close();
        } else {
            $errorMessages[] = "Error checking mobile number uniqueness: " . $conn->error;
            error_log("DB Error (check mobile): " . $conn->error);
        }
    }

    // Validate Password
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&_])[A-Za-z\d@$!%*#?&_]{8,}$/", $password)) {
        $errorMessages[] = "Password must be at least 8 characters, with 1 uppercase, 1 lowercase, 1 number, and 1 special character (e.g., @, $, !, %, *, #, ?, &, _).";
    }

    // Validate Confirm Password
    if ($password !== $confirmPassword) {
        $errorMessages[] = "Passwords do not match.";
    }

    // If no validation errors, proceed with registration
    if (empty($errorMessages)) {
        // Hash the password for storage
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL query for inserting user into the database
        // The 'role' column will use its default value ('customer') as defined in the schema
        // 'date_created' also uses its default CURRENT_TIMESTAMP
        $insert_user_sql = "INSERT INTO user (first_name, middle_name, last_name, email, mobile_number, password_hash) 
                              VALUES (?, ?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($insert_user_sql)) {
            $stmt->bind_param("ssssss", $firstName, $middleName, $lastName, $email, $mobileNumber, $hashedPassword);

            if ($stmt->execute()) {
                $new_user_id = $stmt->insert_id; // Get the ID of the newly registered user

                // Log the registration in audit trail
                $activity_type = 'USER_REGISTRATION';
                $description = "User '{$email}' registered successfully.";
                $ip_address = $_SERVER['REMOTE_ADDR'];
                $user_agent = $_SERVER['HTTP_USER_AGENT'];

                $log_sql = "INSERT INTO audit_trail (user_id, user_email_snapshot, activity_type, target_entity, target_id, description, ip_address, user_agent) 
                            VALUES (?, ?, ?, 'user', ?, ?, ?, ?)";
                
                if ($log_stmt = $conn->prepare($log_sql)) {
                    $log_stmt->bind_param("issssss", $new_user_id, $email, $activity_type, $new_user_id, $description, $ip_address, $user_agent);
                    if (!$log_stmt->execute()) {
                        error_log("Audit trail logging failed for user registration: " . $log_stmt->error);
                        // Don't let audit failure stop user registration flow, but log it.
                    }
                    $log_stmt->close();
                } else {
                     error_log("Audit trail statement preparation failed: " . $conn->error);
                }

                // Set a success message and redirect (or just redirect)
                $_SESSION['registration_success'] = "Registration successful! You can now log in.";
                header("Location: login/login.php"); // Assuming login.php will be in the same directory
                exit();
            } else {
                $errorMessages[] = "Error during registration. Please try again.";
                error_log("User registration failed: " . $stmt->error);
            }
            $stmt->close();
        } else {
            $errorMessages[] = "Error preparing for registration. Please try again.";
            error_log("User registration statement preparation failed: " . $conn->error);
        }
    }
}
?>

<div class="auth-center-wrapper">
    <div class="auth-container">
        <div class="auth-card">
            <h2>Create Your Account</h2>

            <?php if (!empty($errorMessages)): ?>
                <div class="alert alert-danger"><ul>
                    <?php foreach ($errorMessages as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul></div>
            <?php endif; ?>
            
            <?php 
            // Display success message from session if redirected from here after successful registration
            // This is more for a login page after successful registration, but can be adapted.
            if (isset($_SESSION['registration_success'])):
            ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($_SESSION['registration_success']); unset($_SESSION['registration_success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form id="registrationForm" action="register.php" method="POST" novalidate>
                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($firstName); ?>" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-3">
                    <label for="middle_name" class="form-label">Middle Name (Optional)</label>
                    <input type="text" class="form-control" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($middleName); ?>">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($lastName); ?>" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-3">
                    <label for="mobile_number" class="form-label">Mobile Number (09xxxxxxxxx)</label>
                    <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="<?php echo htmlspecialchars($mobileNumber); ?>" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div class="form-text">Must be at least 8 characters, with 1 uppercase, 1 lowercase, 1 number, and 1 special character.</div>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    <div class="invalid-feedback"></div>
                </div>
                <button type="submit" class="btn btn-primary auth-btn">Create Account</button>
            </form>
            <p class="text-center mt-3">Already have an account? <a href="login/login.php">Login here</a></p>
        </div>
    </div>
</div>

    <?php // Specific JS for register.php - jQuery is loaded via CDN in footer if $include_jquery is set or $current_page matches ?>
    <?php // Or, if jQuery is consistently needed and loaded from footer.php, this check might not be needed. Ensure jQuery is loaded before this script block. ?>
    <script src="https://code.jquery.com/jquery-3.7.0.slim.min.js"></script> 
    <script>
    $(document).ready(function() {
        $('#first_name, #middle_name, #last_name, #email, #mobile_number, #password, #confirm_password').on('input', function() {
            validateField($(this));
        });

        function validateField(field) {
            let error = '';
            let fieldId = field.attr('id');
            let fieldValue = field.val();

            // Clear previous error messages for the current field
            field.removeClass('is-invalid is-valid');
            field.siblings('.invalid-feedback').text(''); // Clear text from existing div
            // If you prefer to remove and re-add, use: field.next('.invalid-feedback').remove();

            if (fieldId === 'first_name' || fieldId === 'last_name') {
                if (!fieldValue) {
                    error = 'This field is required';
                } else if (fieldValue.length < 2) {
                    error = 'Must be at least 2 characters long';
                } else if (!/^[A-Za-z\s]+$/.test(fieldValue)) {
                    error = 'Can only contain letters and spaces';
                }
            } else if (fieldId === 'middle_name') {
                if (fieldValue && !/^[A-Za-z\s]*$/.test(fieldValue)) {
                    error = 'Can only contain letters and spaces';
                }
            } else if (fieldId === 'email') {
                if (!fieldValue) {
                    error = 'Email is required';
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(fieldValue)) {
                    error = 'Please enter a valid email address';
                }
            } else if (fieldId === 'mobile_number') {
                if (!fieldValue) {
                    error = 'Mobile number is required';
                } else if (!/^09\d{9}$/.test(fieldValue)) {
                    error = 'Mobile number must start with 09 and be exactly 11 digits';
                }
            } else if (fieldId === 'password') {
                if (!fieldValue) {
                    error = 'Password is required';
                } else if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&_])[A-Za-z\d@$!%*#?&_]{8,}$/.test(fieldValue)) {
                    error = 'Password must be at least 8 characters, with 1 uppercase, 1 lowercase, 1 number, and 1 special character';
                }
            } else if (fieldId === 'confirm_password') {
                if (!fieldValue) {
                    error = 'Please confirm your password';
                } else if (fieldValue !== $('#password').val()) {
                    error = 'Passwords do not match';
                }
            }

            let feedbackDiv = field.siblings('.invalid-feedback');
            if (!feedbackDiv.length) { // Should not happen if HTML structure is correct
                // field.after('<div class="invalid-feedback"></div>');
                // feedbackDiv = field.siblings('.invalid-feedback');
                 console.warn("No invalid-feedback div found for field: ", fieldId);
            }

            if (error) {
                field.addClass('is-invalid');
                feedbackDiv.text(error);
            } else if (fieldValue || fieldId === 'middle_name') { // Show valid only if field has value or is optional middle name
                field.addClass('is-valid');
            }
        }

        // Optional: You might want to validate all fields once on form submit 
        // before allowing the actual PHP submission, or rely solely on server-side for final check.
        $('#registrationForm').on('submit', function(e) {
            let formIsValid = true;
            $('#first_name, #last_name, #email, #mobile_number, #password, #confirm_password').each(function() {
                validateField($(this)); // Validate each field
                if ($(this).hasClass('is-invalid')) {
                    formIsValid = false;
                }
            });
            // Also validate optional middle_name if it has a value
            if ($('#middle_name').val()){
                 validateField($('#middle_name'));
                 if ($('#middle_name').hasClass('is-invalid')) {
                    formIsValid = false;
                }
            }

            if (!formIsValid) {
                e.preventDefault(); // Prevent submission if any field is invalid
                // Optionally, scroll to the first error or show a general message
                alert('Please correct the errors in the form.');
            }
            // If formIsValid is true, submission proceeds to PHP
        });
    });
    </script>

<?php include_once __DIR__ . '/includes/footer.php'; ?> 