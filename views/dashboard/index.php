<?php 
// Include AuthController to check user role
require_once 'controllers/AuthController.php';
$authController = new AuthController();
$isBank = $authController->isBank();
$bankId = $isBank && isset($_SESSION['bank_id']) ? $_SESSION['bank_id'] : null;

include __DIR__ . '/../includes/header.php'; 
?>
<div class="content">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <main>
        <h1>Dashboard</h1>
        <div class="stats">
        <div class="dashboard-grid">
            <?php if ($isBank): ?>
                <!-- Reports Box -->
                <div class="dashboard-box clickable" onclick="window.location.href='index.php?path=report/bankReports&bank_id=<?= $bankId ?>'">
                    <h2>Total Reports</h2>
                    <p><?php echo $data['totalReports']; ?></p>
                </div>
                <!-- Total Cards Box -->
                <div class="dashboard-box clickable" onclick="window.location.href='index.php?path=card'">
                    <h2>Total Cards</h2>
                    <p><?php echo $data['totalCards']; ?></p>
                </div>
                <!-- Debit Cards Box -->
                <div class="dashboard-box clickable" onclick="window.location.href='index.php?path=card'">
                    <h2>Debit Cards</h2>
                    <p><?php echo $data['totalDebitCards']; ?></p>
                </div>
                <!-- Credit Cards Box -->
                <div class="dashboard-box clickable" onclick="window.location.href='index.php?path=card'">
                    <h2>Credit Cards</h2>
                    <p><?php echo $data['totalCreditCards']; ?></p>
                </div>
            <?php else: ?>
                <!-- Admin view boxes -->
                <div class="dashboard-box">
                    <h2>Total Reports</h2>
                    <p><?php echo $data['totalReports']; ?></p>
                </div>
                <div class="dashboard-box">
                    <h2>Total Cards</h2>
                    <p><?php echo $data['totalCards']; ?></p>
                </div>
                <div class="dashboard-box">
                    <h2>Total Banks</h2>
                    <p><?php echo $data['totalBanks']; ?></p>
                </div>
                <div class="dashboard-box">
                    <h2>Total Users</h2>
                    <p><?php echo $data['totalUsers']; ?></p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<style>
.clickable {
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

.clickable:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>
