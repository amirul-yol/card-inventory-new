<?php 
// Include AuthController to check user role
require_once 'controllers/AuthController.php';
$authController = new AuthController();
$isBank = $authController->isBank();

include __DIR__ . '/../includes/header.php'; 
?>
<div class="content">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <main>
        <h1>Dashboard</h1>
        <div class="stats">
        <div class="dashboard-grid">
        <div class="dashboard-box">
            <h2>Total Reports: </h2>
            <p> <?php echo $data['totalReports']; ?></p>
        </div>
        <div class="dashboard-box">
            <h2>Total Cards</h2>
            <p><?php echo $data['totalCards']; ?></p>
        </div>
        <?php if (!$isBank): ?>
        <div class="dashboard-box">
            <h2>Total Banks</h2>
            <p><?php echo $data['totalBanks']; ?></p>
        </div>
        <div class="dashboard-box">
            <h2>Total Users</h2>
            <p><?php echo $data['totalUsers']; ?></p>
        </div>
        <?php endif; ?>
    </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
