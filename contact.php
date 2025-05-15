<?php
$page_title = "Contact Us - 2nd Phone Shop";
include_once __DIR__ . '/includes/header.php';

// Fetch contact information from site_settings
$contact_info = [
    'address' => getSetting('address', ''),
    'phone' => getSetting('contact_phone', ''),
    'email' => getSetting('contact_email', ''),
    'hours' => getSetting('business_hours', '')
];

// Handle form submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message_text = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($subject) || empty($message_text)) {
        $message = 'Please fill in all required fields.';
        $message_type = 'danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $message_type = 'danger';
    } else {
        // Insert message into database
        $sql = "INSERT INTO contact_submissions (name, email, subject, message, submission_ip) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $stmt->bind_param("sssss", $name, $email, $subject, $message_text, $ip_address);
        
        if ($stmt->execute()) {
            $message = 'Thank you for your message. We will get back to you soon!';
            $message_type = 'success';
        } else {
            $message = 'Sorry, there was an error sending your message. Please try again later.';
            $message_type = 'danger';
        }
    }
}
?>

<div class="admin-page">
    <div class="container py-5">
        <h1 class="gradient-text mb-4">Contact Us</h1>
        <p class="lead mb-4">Get in touch with us for any inquiries or support.</p>

        <div class="row g-4">
            <!-- Contact Information -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">Contact Information</h5>
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-geo-alt text-primary me-3 fs-4"></i>
                            <div>
                                <h6 class="mb-1">Address</h6>
                                <p class="mb-0 text-muted"><?php echo htmlspecialchars(getSetting('address')); ?></p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-telephone text-primary me-3 fs-4"></i>
                            <div>
                                <h6 class="mb-1">Phone</h6>
                                <p class="mb-0 text-muted"><?php echo htmlspecialchars(getSetting('contact_phone')); ?></p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-envelope text-primary me-3 fs-4"></i>
                            <div>
                                <h6 class="mb-1">Email</h6>
                                <p class="mb-0 text-muted"><?php echo htmlspecialchars(getSetting('contact_email')); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">Location</h5>
                        <div class="ratio ratio-4x3">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3932.300392081657!2d118.73303031147444!3d9.740602277402445!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33b56300713f4579%3A0x153e38320b143b4f!2sJust%20in%20case%20PLWN%20shop!5e0!3m2!1sen!2sph!4v1747298083009!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Business Hours Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <i class="bi bi-clock text-primary me-3 fs-4"></i>
                            <h5 class="card-title mb-0">Business Hours</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <tbody>
                                    <?php
                                    $hours = json_decode(getSetting('business_hours'), true);
                                    if ($hours) {
                                        foreach ($hours as $day => $time) {
                                            echo "<tr>";
                                            echo "<td class='text-muted' style='width: 150px;'><strong>" . htmlspecialchars($day) . "</strong></td>";
                                            echo "<td class='text-muted'>" . htmlspecialchars($time) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='2' class='text-muted'>Business hours not set</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?> 