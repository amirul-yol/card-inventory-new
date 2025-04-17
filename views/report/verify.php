<?php
require_once 'models/ReportModel.php';
require_once 'controllers/AuthController.php';

$bankId = isset($_GET['bank_id']) ? intval($_GET['bank_id']) : null;
$reportId = isset($_GET['report_id']) ? intval($_GET['report_id']) : null;

if (!$bankId || !$reportId) {
    die("Invalid or missing parameters.");
}

$reportModel = new ReportModel();
$transactions = $reportModel->getTransactionsForReport($reportId);

// Get the report to check its status
$report = $reportModel->getReportById($reportId);
$isVerified = ($report && $report['status'] === 'Verified');

// Check user role
$authController = new AuthController();
$isPO = $authController->isProductionOfficer();
$isLO = $authController->isLogisticsOfficer();

// Determine if the user can verify (only POs can verify and only if not already verified)
$canVerify = $isPO && !$isVerified;

include 'views/includes/header.php';
include 'views/includes/sidebar.php';
?>

<div class="content">
    <h1>Verify Withdrawal Report</h1>
    <form method="POST" action="index.php?path=report/verifyWithdrawReport">
        <input type="hidden" name="bank_id" value="<?php echo htmlspecialchars($bankId); ?>">
        <input type="hidden" name="report_id" value="<?php echo htmlspecialchars($reportId); ?>">

        <?php if ($isVerified): ?>
            <div class="alert alert-info">
                This report has already been verified and cannot be modified.
            </div>
        <?php elseif ($isLO): ?>
            <div class="alert alert-info">
                You can view this report, but only Production Officers can verify reports.
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Card Name</th>
                    <th>Quantity</th>
                    <th>Remarks</th>
                    <th>Reject Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($transactions)): ?>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?= htmlspecialchars($transaction['card_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($transaction['quantity']); ?></td>
                            <td><?= htmlspecialchars($transaction['remarks']); ?></td>
                            <td>
                                <?php 
                                    $rejectedAmount = $reportModel->getRejectedAmount($transaction['id']);
                                    echo htmlspecialchars($rejectedAmount ?? '0');
                                ?>
                            </td>
                            <td>
                                <?php if ($canVerify): ?>
                                    <a href="index.php?path=report/rejectCard&transaction_id=<?= $transaction['id']; ?>&bank_id=<?= $bankId ?>&report_id=<?= $reportId ?>" class="btn btn-danger">
                                        Reject
                                    </a>
                                <?php else: ?>
                                    <button type="button" class="btn btn-danger" disabled>Reject</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No transactions found for this report.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <button type="submit" name="verify_report" class="btn btn-primary" <?= $canVerify ? '' : 'disabled' ?>>
            <?php if ($isVerified): ?>
                Already Verified
            <?php elseif (!$isPO): ?>
                Only POs Can Verify
            <?php else: ?>
                Verify
            <?php endif; ?>
        </button>
    </form>
</div>

<style>
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }
    .alert-info {
        color: #31708f;
        background-color: #d9edf7;
        border-color: #bce8f1;
    }
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>

<?php include 'views/includes/footer.php'; ?>
