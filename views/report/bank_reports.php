<?php
require_once 'models/ReportModel.php';

if (!isset($_GET['bank_id']) || !is_numeric($_GET['bank_id'])) {
    die("Bank ID is missing or invalid.");
}

$bankId = intval($_GET['bank_id']);
$reportModel = new ReportModel();

// Fetch bank details
$bank = $reportModel->getBankById($bankId);
if (!$bank) {
    die("Bank not found.");
}

// Fetch reports for the bank
$reports = $reportModel->getReportsByBank($bankId);
?>
<?php include 'views/includes/header.php'; ?>
<?php include 'views/includes/sidebar.php'; ?>


<div class="content">
    <h1>Bank Reports</h1>
    <h2>Reports for <?= htmlspecialchars($bank['name']); ?></h2>

    <!-- Withdraw Button -->
    <div class="action-buttons">
        <a href="index.php?path=report/withdrawCard&bank_id=<?= $bank['bank_id']; ?>" class="btn withdraw-btn">Withdraw Card</a>
    </div>

    <!-- Existing Reports Table -->
    <h3>Existing Reports</h3>
    <?php if (!empty($reports)): ?>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Details</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                    <tr>
                        <td><?= htmlspecialchars($report['report_date'] ?? 'N/A'); ?></td>
                        <td><?= htmlspecialchars($report['status'] ?? 'N/A'); ?></td>
                        <td><?= htmlspecialchars($report['details'] ?? 'No Details Available'); ?></td>

                        <td>
                            <a href="index.php?path=report/verify&report_id=<?= $report['id']; ?>&bank_id=<?= $bank['bank_id']; ?>" class="btn btn-action">Verify</a>
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
