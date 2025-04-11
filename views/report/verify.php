<?php
require_once 'models/ReportModel.php';

$bankId = isset($_GET['bank_id']) ? intval($_GET['bank_id']) : null;
$reportId = isset($_GET['report_id']) ? intval($_GET['report_id']) : null;

if (!$bankId || !$reportId) {
    die("Invalid or missing parameters.");
}

$reportModel = new ReportModel();
$transactions = $reportModel->getTransactionsForReport($reportId);

include 'views/includes/header.php';
include 'views/includes/sidebar.php';
?>

<div class="content">
    <h1>Verify Withdrawal Report</h1>
    <form method="POST" action="index.php?path=report/verifyWithdrawReport">
        <input type="hidden" name="bank_id" value="<?php echo htmlspecialchars($bankId); ?>">
        <input type="hidden" name="report_id" value="<?php echo htmlspecialchars($reportId); ?>">

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
                                <a href="index.php?path=report/rejectCard&transaction_id=<?= $transaction['id']; ?>&bank_id=<?= $bankId ?>&report_id=<?= $reportId ?>" class="btn btn-danger">
                                    Reject
                                </a>
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

        <button type="submit" name="verify_report" class="btn btn-primary">Verify</button>
    </form>
</div>

<?php include 'views/includes/footer.php'; ?>
