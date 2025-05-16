<?php
include_once __DIR__ . '/../includes/header.php';

if (!isLoggedIn()) {
    header('Location: ../login/login.php?message=Please login to manage your addresses.');
    exit;
}

$user_id = $_SESSION['user_id'];
$address_error = '';
$address_success = '';

// Handle new address submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_address'])) {
    $type = $_POST['address_type'];
    $recipient = $_POST['recipient_name'];
    $line1 = $_POST['address_line1'];
    $city = $_POST['city'];
    $state = $_POST['state_province_region'];
    $postal = $_POST['postal_code'];
    $country = $_POST['country_code'];
    $is_default = isset($_POST['is_default']) ? 1 : 0;

    if (!$recipient || !$line1 || !$city || !$state || !$postal || !$country) {
        $address_error = 'Please fill in all required fields.';
    } else {
        if ($is_default) {
            // Unset other defaults of this type
            $sql = "UPDATE user_addresses SET is_default = 0 WHERE user_id = ? AND address_type = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('is', $user_id, $type);
            $stmt->execute();
        }
        $sql = "INSERT INTO user_addresses (user_id, address_type, is_default, recipient_name, address_line1, city, state_province_region, postal_code, country_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('isissssss', $user_id, $type, $is_default, $recipient, $line1, $city, $state, $postal, $country);
        if ($stmt->execute()) {
            $address_success = 'Address added successfully!';
        } else {
            $address_error = 'Failed to add address.';
        }
    }
}

// Handle set as default
if (isset($_GET['set_default']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $type = $_GET['type'] === 'billing' ? 'billing' : 'shipping';
    // Unset other defaults
    $sql = "UPDATE user_addresses SET is_default = 0 WHERE user_id = ? AND address_type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('is', $user_id, $type);
    $stmt->execute();
    // Set this as default
    $sql = "UPDATE user_addresses SET is_default = 1 WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $id, $user_id);
    $stmt->execute();
    header('Location: addresses.php');
    exit;
}

// Fetch addresses
$sql = "SELECT * FROM user_addresses WHERE user_id = ? ORDER BY address_type, is_default DESC, id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$addresses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

?>
<div class="container py-5">
    <h1 class="mb-4">Manage Addresses</h1>
    <?php if ($address_error): ?>
        <div class="alert alert-danger"><?php echo $address_error; ?></div>
    <?php endif; ?>
    <?php if ($address_success): ?>
        <div class="alert alert-success"><?php echo $address_success; ?></div>
    <?php endif; ?>
    <div class="row">
        <div class="col-md-6">
            <h4>Add New Address</h4>
            <form method="POST" class="mb-4">
                <div class="mb-2">
                    <label>Type</label>
                    <select name="address_type" class="form-control" required>
                        <option value="shipping">Shipping</option>
                        <option value="billing">Billing</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label>Recipient Name</label>
                    <input type="text" name="recipient_name" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Address Line 1</label>
                    <input type="text" name="address_line1" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>City</label>
                    <input type="text" name="city" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>State/Province/Region</label>
                    <input type="text" name="state_province_region" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Postal Code</label>
                    <input type="text" name="postal_code" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Country Code</label>
                    <input type="text" name="country_code" class="form-control" value="PHL" required>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_default" id="is_default">
                    <label class="form-check-label" for="is_default">Set as default</label>
                </div>
                <button type="submit" name="add_address" class="btn btn-primary">Add Address</button>
            </form>
        </div>
        <div class="col-md-6">
            <h4>Your Addresses</h4>
            <?php if (empty($addresses)): ?>
                <div class="alert alert-info">No addresses found.</div>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($addresses as $addr): ?>
                        <li class="list-group-item">
                            <strong><?php echo ucfirst($addr['address_type']); ?></strong>
                            <?php if ($addr['is_default']): ?>
                                <span class="badge bg-success ms-2">Default</span>
                            <?php else: ?>
                                <a href="addresses.php?set_default=1&id=<?php echo $addr['id']; ?>&type=<?php echo $addr['address_type']; ?>" class="btn btn-sm btn-outline-primary ms-2">Set as Default</a>
                            <?php endif; ?><br>
                            <?php echo htmlspecialchars($addr['recipient_name']); ?><br>
                            <?php echo htmlspecialchars($addr['address_line1']); ?><br>
                            <?php echo htmlspecialchars($addr['city']); ?>, <?php echo htmlspecialchars($addr['state_province_region']); ?><br>
                            <?php echo htmlspecialchars($addr['postal_code']); ?>, <?php echo htmlspecialchars($addr['country_code']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include_once __DIR__ . '/../includes/footer.php'; ?> 