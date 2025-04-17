<?php
// Include AuthController to check for admin role
require_once 'controllers/AuthController.php';
$authController = new AuthController();
$isAdmin = $authController->isAdmin();
$isBank = $authController->isBank();
?>
<div class="sidebar">
    <ul class="sidebar-menu">
        <li><a href="index.php?path=dashboard" class="<?= $path === 'dashboard' ? 'active' : '' ?>">Dashboard</a></li>
        <li><a href="index.php?path=card" class="<?= $path === 'card' ? 'active' : '' ?>">Card</a></li>
        <li><a href="index.php?path=report" class="<?= $path === 'report' ? 'active' : '' ?>">Report</a></li>
        <?php if (!$isBank): ?>
        <li><a href="index.php?path=bank" class="<?= $path === 'bank' ? 'active' : '' ?>">Bank</a></li>
        <?php endif; ?>
        <?php if ($isAdmin): ?>
        <li><a href="index.php?path=user" class="<?= $path === 'user' ? 'active' : '' ?>">User</a></li>
        <li><a href="index.php?path=role" class="<?= $path === 'role' ? 'active' : '' ?>">Role</a></li>
        <?php endif; ?>
        <li><a href="index.php?path=user_profile" class="<?= $path === 'user_profile' ? 'active' : '' ?>">User Profile</a></li>
    </ul>
    
    <div class="user-info">
        <?php if (isset($_SESSION['user_name'])): ?>
            <p>Logged in as: <?= htmlspecialchars($_SESSION['user_name']) ?></p>
            <a href="index.php?path=auth/logout" class="logout-btn">Logout</a>
        <?php endif; ?>
    </div>
    
    <style>
        .user-info {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #fff;
        }
        .user-info p {
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }
        .logout-btn {
            display: inline-block;
            padding: 0.3rem 0.7rem;
            background-color: rgba(231, 76, 60, 0.8);
            color: white;
            border-radius: 3px;
            text-decoration: none;
            font-size: 0.8rem;
            transition: background-color 0.3s;
        }
        .logout-btn:hover {
            background-color: rgba(231, 76, 60, 1);
        }
    </style>
</div>
