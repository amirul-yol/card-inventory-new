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
    <div class="report-header">
        <?php if ($isBank): ?>
            <h1>Withdrawal Report</h1>
        <?php else: ?>
            <h1>Verify Withdrawal Report</h1>
        <?php endif; ?>
        <div class="report-date">
            <strong>Report Date:</strong> <?= htmlspecialchars($report['report_date'] ?? 'N/A'); ?>
        </div>
    </div>

    <form method="POST" action="index.php?path=report/verifyWithdrawReport">
        <input type="hidden" name="bank_id" value="<?php echo htmlspecialchars($bankId); ?>">
        <input type="hidden" name="report_id" value="<?php echo htmlspecialchars($reportId); ?>">

        <!-- <?php if ($isVerified): ?>
            <div class="alert alert-info">
                This report has already been verified and cannot be modified.
            </div>
        <?php elseif ($isLO): ?>
            <div class="alert alert-info">
                You can view this report, but only Production Officers can verify reports.
            </div>
        <?php endif; ?> -->

        <table>
            <thead>
                <tr>
                    <th>Card Name</th>
                    <th>Withdraw Quantity</th>
                    <th>Reject Quantity</th>
                    <th>Total Withdrawal</th>
                    <th>Card Balance</th>
                    <?php if (!($authController->isBank() && isset($_SESSION['bank_id']))): ?>
                        <th>Action</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Initialize total variables
                $totalWithdrawQuantity = 0;
                $totalRejectQuantity = 0;
                $totalTotalWithdrawal = 0;
                $totalCardBalance = 0;

                if (!empty($transactions)): ?>
                    <?php foreach ($transactions as $transaction): 
                        // Calculate reject quantity and total withdrawal for this transaction
                        $rejectedDetails = $reportModel->getRejectedDetails($transaction['id']); // Fetch detailed rejected info
                        $rejectedAmount = array_sum($rejectedDetails);
                        $totalWithdrawal = $transaction['transaction_quantity'] + $rejectedAmount;

                        // Add to totals
                        $totalWithdrawQuantity += $transaction['transaction_quantity'];
                        $totalRejectQuantity += $rejectedAmount;
                        $totalTotalWithdrawal += $totalWithdrawal;
                        $totalCardBalance += $transaction['card_balance'];

                        // Prepare reject details for hover tooltip
                        $rejectTooltip = htmlspecialchars(json_encode($rejectedDetails), ENT_QUOTES, 'UTF-8');
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($transaction['card_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($transaction['transaction_quantity']); ?></td>
                        <td class="reject-quantity" data-details="<?= htmlspecialchars(json_encode($rejectedDetails), ENT_QUOTES, 'UTF-8'); ?>">
                            <?= htmlspecialchars($rejectedAmount); ?>
                        </td>

                        <td><?= htmlspecialchars($totalWithdrawal); ?></td>
                        <td><?= htmlspecialchars(number_format($transaction['card_balance'])); ?></td>
                        <?php if (!($authController->isBank() && isset($_SESSION['bank_id']))): ?>
                            <td>
                                <?php if ($canVerify): ?>
                                    <a href="index.php?path=report/rejectCard&transaction_id=<?= $transaction['id']; ?>&bank_id=<?= $bankId ?>&report_id=<?= $reportId ?>" class="btn btn-danger">
                                        Reject
                                    </a>
                                <?php else: ?>
                                    <button type="button" class="btn btn-danger" disabled>Reject</button>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No transactions found for this report.</td>
                    </tr>
                <?php endif; ?>
            </tbody>

            <tfoot >
                <tr style="background-color:rgb(88, 166, 218); color:#f2f2f2; font-weight: bold;">
                    <td>Total</td>
                    <td><?= htmlspecialchars($totalWithdrawQuantity); ?></td>
                    <td><?= htmlspecialchars($totalRejectQuantity); ?></td>
                    <td><?= htmlspecialchars($totalTotalWithdrawal); ?></td>
                    
                </tr>
            </tfoot>
        </table>


        <?php if (!($authController->isBank() && isset($_SESSION['bank_id']))): ?>
        <button type="submit" name="verify_report" class="btn btn-primary" <?= $canVerify ? '' : 'disabled' ?>>
            <?php if ($isVerified): ?>
                Already Verified
            <?php elseif (!$isPO): ?>
                Only POs Can Verify
            <?php else: ?>
                Verify
            <?php endif; ?>
        </button>
        <?php endif; ?>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rows = document.querySelectorAll('.reject-quantity');
        const messageBox = document.createElement('div');
        messageBox.style.position = 'absolute';
        messageBox.style.padding = '10px';
        messageBox.style.backgroundColor = '#333';
        messageBox.style.color = '#fff';
        messageBox.style.borderRadius = '5px';
        messageBox.style.boxShadow = '0 2px 5px rgba(0, 0, 0, 0.3)';
        messageBox.style.display = 'none';
        messageBox.style.zIndex = '1000';
        document.body.appendChild(messageBox);

        rows.forEach(row => {
            row.addEventListener('mouseover', function (e) {
                const details = JSON.parse(this.dataset.details);
                messageBox.innerHTML = `
                    <strong>Quality:</strong> ${details.quality || 0}<br>
                    <strong>System Error:</strong> ${details.system || 0}
                `;
                messageBox.style.left = `${e.pageX + 10}px`;
                messageBox.style.top = `${e.pageY + 10}px`;
                messageBox.style.display = 'block';
            });

            row.addEventListener('mousemove', function (e) {
                messageBox.style.left = `${e.pageX + 10}px`;
                messageBox.style.top = `${e.pageY + 10}px`;
            });

            row.addEventListener('mouseout', function () {
                messageBox.style.display = 'none';
            });
        });
    });
</script>

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
    .report-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .report-date {
        background-color: #f8f9fa;
        padding: 8px 15px;
        border-radius: 4px;
        border-left: 4px solid #007bff;
        font-size: 16px;
    }

    td[title] {
        position: relative;
        cursor: help;
    }

    td[title]:hover::after {
        content: attr(title);
        position: absolute;
        left: 50%;
        bottom: 120%;
        transform: translateX(-50%);
        background-color: #333;
        color: #fff;
        padding: 5px 10px;
        border-radius: 5px;
        white-space: pre; /* Preserves line breaks in tooltip */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        z-index: 10;
        display: block;
    }

    td[title]:hover::before {
        content: '';
        position: absolute;
        left: 50%;
        bottom: 110%;
        transform: translateX(-50%);
        border: 5px solid transparent;
        border-top-color: #333;
        z-index: 10;
        display: block;
    }

    .reject-quantity {
            position: relative;
            cursor: pointer;
            color: #3498db;
        }

    .reject-quantity:hover {
        text-decoration: underline;
    }

</style>

<?php include 'views/includes/footer.php'; ?>
