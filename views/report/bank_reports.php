<?php
require_once 'models/ReportModel.php';
require_once 'controllers/AuthController.php';

if (!isset($_GET['bank_id']) || !is_numeric($_GET['bank_id'])) {
    die("Bank ID is missing or invalid.");
}

$bankId = intval($_GET['bank_id']);
$reportModel = new ReportModel();

// Create Auth Controller instance to check user roles
$authController = new AuthController();
$isLO = $authController->isLogisticsOfficer();
$isPO = $authController->isProductionOfficer();

// Fetch bank details
$bank = $reportModel->getBankById($bankId);
if (!$bank) {
    die("Bank not found.");
}

// Fetch reports for the bank
$reports = $reportModel->getReportsByBank($bankId);

// Check if a report exists for today's date
$todayDate = date('Y-m-d');
$reportExistsForToday = false;

foreach ($reports as $report) {
    if ($report['report_date'] === $todayDate) {
        $reportExistsForToday = true;
        break;
    }
}
?>
<?php include 'views/includes/header.php'; ?>
<?php include 'views/includes/sidebar.php'; ?>

<style>
    .withdraw-btn.disabled, .btn-action.disabled {
        opacity: 0.6;
        cursor: not-allowed;
        pointer-events: none;
    }
    
    .tooltip {
        position: relative;
        display: inline-block;
    }
    
    .tooltip .tooltip-text {
        visibility: hidden;
        width: 220px;
        background-color: #555;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        margin-left: -110px;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .tooltip .tooltip-text::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #555 transparent transparent transparent;
    }
    
    .tooltip:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }
</style>

<div class="content">
    <h1>Bank Reports</h1>
    <h2>Reports for <?= htmlspecialchars($bank['name']); ?></h2>

    <!-- Withdraw Button -->
    <div class="action-buttons">
        <?php if ($isLO && !$reportExistsForToday): ?>
            <a href="index.php?path=report/withdrawCard&bank_id=<?= $bank['bank_id']; ?>" class="btn withdraw-btn">Withdraw Card</a>
        <?php else: ?>
            <div class="tooltip">
                <a class="btn withdraw-btn disabled">Withdraw Card</a>
                <span class="tooltip-text">
                    <?php if ($reportExistsForToday): ?>
                        A withdrawal report already exists for today
                    <?php else: ?>
                        Only Logistics Officers can withdraw cards
                    <?php endif; ?>
                </span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Existing Reports Table -->
    <h3>Existing Reports</h3>
    <?php if (!empty($reports)): ?>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Status</th>
                    <!-- <th>Details</th> -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                    <tr>
                        <td><?= htmlspecialchars($report['report_date'] ?? 'N/A'); ?></td>
                        <td><?= htmlspecialchars($report['status'] ?? 'N/A'); ?></td>
                        <!-- <td><?= htmlspecialchars($report['details'] ?? 'No Details Available'); ?></td> -->

                        <td>
                            <?php if ($isPO): ?>
                                <a href="index.php?path=report/verify&report_id=<?= $report['id']; ?>&bank_id=<?= $bank['bank_id']; ?>" class="btn btn-action">
                                    <?= $report['status'] === 'Verified' ? 'View' : 'Verify' ?>
                                </a>
                            <?php else: ?>
                                <div class="tooltip">
                                    <a class="btn btn-action disabled">Verify</a>
                                    <span class="tooltip-text">Only Production Officers can verify reports</span>
                                </div>
                            <?php endif; ?>
                            <a href="index.php?path=report/download&report_id=<?= $report['id']; ?>" class="btn btn-action">Generate Report</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No reports available for this bank.</p>
    <?php endif; ?>
</div>

<?php include 'views/includes/footer.php'; ?>
