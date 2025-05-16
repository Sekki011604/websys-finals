<?php 
session_start(); // Start session on all pages
include_once __DIR__ . '/connection/db_conn.inc.php'; // Adjusted path for includes folder
include_once __DIR__ . '/functions.php'; // Include utility functions

// Helper function to check if a user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Helper function to check if the logged-in user is an admin
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

$current_page = basename($_SERVER['PHP_SELF']);
$page_title = getSetting('site_name', '2nd Phone Shop'); // Default title from settings

// You can set specific titles for pages in each page file before including the header
// For example, in login.php, before include header: $page_title = "Login - 2nd Phone Shop";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars(getSetting('meta_description', '')); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars(getSetting('meta_keywords', '')); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <?php 
        $css_path = 'asset/css/styles.css';
        if (strpos($_SERVER['REQUEST_URI'], '/login/') !== false || strpos($_SERVER['REQUEST_URI'], '/user/') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
            $css_path = '../asset/css/styles.css';
        }
    ?>
    <link rel="stylesheet" href="<?php echo $css_path; ?>">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="<?php if ($current_page === 'login.php' || $current_page === 'register.php') echo 'auth-page-body'; ?>">

<?php if ($current_page === 'login.php' || $current_page === 'register.php'): ?>
    <nav class="navbar navbar-light bg-white py-3 shadow-sm w-100">
        <a class="navbar-brand fw-bold ms-4" href="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) ? '../index.php' : 'index.php'; ?>">just<span class="gradient-text"> in </span>case</a>
    </nav>
<?php else: ?>
    <nav class="navbar navbar-expand-lg navbar-light bg-white py-3 shadow-sm sticky-top">
      <div class="container">
        <a class="navbar-brand fw-bold" href="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) ? '../index.php' : 'index.php'; ?>">just<span class="gradient-text"> in </span>case</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
            <?php 
                $home_link = (strpos($_SERVER['REQUEST_URI'], '/login/') !== false || strpos($_SERVER['REQUEST_URI'], '/user/') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) ? '../index.php' : 'index.php';
                $about_link = (strpos($_SERVER['REQUEST_URI'], '/login/') !== false || strpos($_SERVER['REQUEST_URI'], '/user/') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) ? '../about.php' : 'about.php';
                $shop_link = (strpos($_SERVER['REQUEST_URI'], '/login/') !== false || strpos($_SERVER['REQUEST_URI'], '/user/') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) ? '../shop.php' : 'shop.php';
                $contact_link = (strpos($_SERVER['REQUEST_URI'], '/login/') !== false || strpos($_SERVER['REQUEST_URI'], '/user/') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) ? '../contact.php' : 'contact.php';
                $cart_link = (strpos($_SERVER['REQUEST_URI'], '/login/') !== false || strpos($_SERVER['REQUEST_URI'], '/user/') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) ? '../cart.php' : 'cart.php';
                $login_link = (strpos($_SERVER['REQUEST_URI'], '/login/') !== false) ? 'login.php' : ( (strpos($_SERVER['REQUEST_URI'], '/user/') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) ? '../login/login.php' : 'login/login.php');
                $register_link = (strpos($_SERVER['REQUEST_URI'], '/login/') !== false) ? '../register.php' : ( (strpos($_SERVER['REQUEST_URI'], '/user/') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) ? '../register.php' : 'register.php');
                $dashboard_link = (strpos($_SERVER['REQUEST_URI'], '/user/') !== false) ? 'dashboard.php' : ( (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) ? '../user/dashboard.php' : 'user/dashboard.php');
                $admin_dashboard_link = '/websys-finals/admin/index.php';
                $logout_link = (strpos($_SERVER['REQUEST_URI'], '/login/') !== false || strpos($_SERVER['REQUEST_URI'], '/user/') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) ? '../logout.php' : 'logout.php';
                $profile_settings_link = (strpos($_SERVER['REQUEST_URI'], '/user/') !== false) ? 'profile.php?section=settings' : 'user/profile.php?section=settings';
            ?>
            <li class="nav-item"><a class="nav-link <?php if($current_page === 'index.php') echo 'active fw-bold text-primary rounded-pill px-3'; ?>" href="<?php echo $home_link; ?>" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Home</a></li>
            <li class="nav-item"><a class="nav-link <?php if($current_page === 'about.php') echo 'active fw-bold text-primary rounded-pill px-3'; ?>" href="<?php echo $about_link; ?>" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">About</a></li>
            <li class="nav-item"><a class="nav-link <?php if($current_page === 'shop.php') echo 'active fw-bold text-primary rounded-pill px-3'; ?>" href="<?php echo $shop_link; ?>" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Shop</a></li>
            <li class="nav-item"><a class="nav-link <?php if($current_page === 'contact.php') echo 'active fw-bold text-primary rounded-pill px-3'; ?>" href="<?php echo $contact_link; ?>" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Contact</a></li>
            <li class="nav-item"><a class="nav-link <?php if($current_page === 'cart.php') echo 'active fw-bold text-primary rounded-pill px-3'; ?>" href="<?php echo $cart_link; ?>" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><i class="bi bi-cart"></i> Cart</a></li>
            <?php if (isLoggedIn()): ?>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUserLink" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['user_first_name']); ?>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="navbarDropdownUserLink">
                    <li><a class="dropdown-item" href="<?php echo $dashboard_link; ?>"><i class="bi bi-speedometer2 me-2"></i>My Dashboard</a></li>
                    <li><a class="dropdown-item" href="<?php echo $profile_settings_link; ?>"><i class="bi bi-gear me-2"></i>Account Settings</a></li>
                    <?php if (isAdmin()): ?>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?php echo $admin_dashboard_link; ?>"><i class="bi bi-shield-lock me-2"></i>Admin Panel</a></li>
                    <?php endif; ?>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?php echo $logout_link; ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                  </ul>
                </li>
            <?php else: ?>
                <li class="nav-item ms-lg-2">
                  <a class="btn btn-outline-primary btn-sm" href="<?php echo $login_link; ?>">Login</a>
                </li>
                <li class="nav-item ms-lg-2">
                  <a class="btn btn-primary btn-sm" href="<?php echo $register_link; ?>">Register</a>
                </li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </nav>
<?php endif; ?>

<!-- Start of main page content -->
<div class="container mt-4 mb-4 flex-grow-1"> 