<?php
require_once 'controllers/AuthController.php';
$authController = new AuthController();
$isAdmin = $authController->isAdmin();
$isBank = $authController->isBank();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Card Inventory</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="css/modern-styles.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php?path=dashboard">
      <img src="public/logo.png" alt="Logo" height="30" class="me-2">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= $path === 'card' ? 'active' : '' ?>" href="index.php?path=card">Card</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $path === 'report' ? 'active' : '' ?>" href="index.php?path=report">Report</a>
        </li>
        <?php if (! $isBank): ?>
        <li class="nav-item">
          <a class="nav-link <?= $path === 'bank' ? 'active' : '' ?>" href="index.php?path=bank">Bank</a>
        </li>
        <?php endif; ?>
        <?php if ($isAdmin): ?>
        <li class="nav-item">
          <a class="nav-link <?= $path === 'user' ? 'active' : '' ?>" href="index.php?path=user">User</a>
        </li>
        <?php endif; ?>
      </ul>
      <div class="d-flex align-items-center ms-auto">
        <span id="currentTime" class="navbar-text me-3"></span>
        <div class="dropdown">
          <?php if (isset($_SESSION['user_name'])): ?>
          <a class="nav-link dropdown-toggle text-dark" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <?= htmlspecialchars($_SESSION['user_name']) ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="index.php?path=user_profile">My Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="index.php?path=auth/logout">Logout</a></li>
          </ul>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</nav>

<!-- Page content starts here -->
