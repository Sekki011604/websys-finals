<?php
$page_title = "Manage Users - Admin Panel";
include_once __DIR__ . '/../includes/header.php';

// Admin access check
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login/login.php?message=Access Denied');
    exit;
}

// Fetch users from DB
$users = [];
$res = $conn->query("SELECT id, first_name, last_name, email, role, is_active, date_created FROM user ORDER BY id DESC");
if ($res && $res->num_rows) {
    while ($row = $res->fetch_assoc()) {
        $users[] = $row;
    }
}
?>

<div class="admin-page">
    <div class="d-flex flex-column flex-lg-row">
        <?php include_once __DIR__ . '/includes/admin_nav.php'; ?>

        <div class="flex-grow-1">
            <h1 class="gradient-text mb-4"><?php echo htmlspecialchars($page_title); ?></h1>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="gradient-text mb-0">User List</h2>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Date Created</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                    <tr><td colspan="7" class="text-center">No users found.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo $user['id']; ?></td>
                                            <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td><span class="badge bg-<?php echo $user['role'] === 'admin' ? 'primary' : 'secondary'; ?>"><?php echo ucfirst($user['role']); ?></span></td>
                                            <td><?php echo $user['is_active'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle-fill text-danger"></i>'; ?></td>
                                            <td><?php echo date("M d, Y", strtotime($user['date_created'])); ?></td>
                                            <td class="text-center">
                                                <a href="users.php?action=edit&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i></a>
                                                <a href="users.php?action=delete&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../includes/footer.php';
?> 