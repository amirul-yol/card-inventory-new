<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Inventory</title>
    <link rel="stylesheet" href="css/modern.css"> <!-- Include our new modern CSS file -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php
    // Get current path for highlighting active nav link
    $current_path = $_GET['path'] ?? 'dashboard';
    
    // Include AuthController to check user role
    require_once 'controllers/AuthController.php';
    $authController = new AuthController();
    $isAdmin = $authController->isAdmin();
    $isBank = $authController->isBank();
    
    // Only show the navbar if user is logged in
    if (isset($_SESSION['user_id'])):
    ?>
    <nav class="top-navbar">
        <a href="index.php?path=dashboard" class="logo">
            <i class="fas fa-credit-card"></i>
            <span>Card Inventory</span>
        </a>
        
        <div class="nav-links">
            <a href="index.php?path=dashboard" class="nav-link <?= $current_path === 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="index.php?path=card" class="nav-link <?= $current_path === 'card' ? 'active' : '' ?>">
                <i class="fas fa-credit-card"></i>
                <span>Cards</span>
            </a>
            <a href="index.php?path=report" class="nav-link <?= $current_path === 'report' ? 'active' : '' ?>">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
            <?php if (!$isBank): ?>
            <a href="index.php?path=bank" class="nav-link <?= $current_path === 'bank' ? 'active' : '' ?>">
                <i class="fas fa-university"></i>
                <span>Banks</span>
            </a>
            <?php endif; ?>
            <?php if ($isAdmin): ?>
            <a href="index.php?path=user" class="nav-link <?= $current_path === 'user' ? 'active' : '' ?>">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
            <a href="index.php?path=role" class="nav-link <?= $current_path === 'role' ? 'active' : '' ?>">
                <i class="fas fa-user-tag"></i>
                <span>Roles</span>
            </a>
            <?php endif; ?>
        </div>
        
        <div class="user-dropdown">
            <button class="user-menu">
                <i class="fas fa-user-circle"></i>
                <span><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="dropdown-content">
                <a href="index.php?path=user_profile">
                    <i class="fas fa-id-card"></i> My Profile
                </a>
                <a href="index.php?path=auth/logout" class="logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>
    <?php endif; ?>
