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
  <!-- Custom Modern Styles -->
  <link rel="stylesheet" href="css/modern-styles.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php?path=dashboard">Card Inventory</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= $path === 'dashboard' ? 'active' : '' ?>" href="index.php?path=dashboard">Dashboard</a>
        </li>
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
        <li class="nav-item">
          <a class="nav-link <?= $path === 'role' ? 'active' : '' ?>" href="index.php?path=role">Role</a>
        </li>
        <?php endif; ?>
        <li class="nav-item">
          <a class="nav-link <?= $path === 'user_profile' ? 'active' : '' ?>" href="index.php?path=user_profile">User Profile</a>
        </li>
      </ul>
      <div class="d-flex align-items-center">
        <?php if (isset($_SESSION['user_name'])): ?>
        <span class="navbar-text me-3">Logged in as: <?= htmlspecialchars($_SESSION['user_name']) ?></span>
        <a class="btn btn-outline-danger" href="index.php?path=auth/logout">Logout</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<!-- Page content starts here -->

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
