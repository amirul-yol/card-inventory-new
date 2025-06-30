<?php
require_once 'models/ReportModel.php';

$transactionId = isset($_GET['transaction_id']) ? intval($_GET['transaction_id']) : null;
$bankId = isset($_GET['bank_id']) ? intval($_GET['bank_id']) : null;
$reportId = isset($_GET['report_id']) ? intval($_GET['report_id']) : null;

if (!$transactionId || !$bankId || !$reportId) {
    die("Invalid or missing parameters.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_rejection'])) {
    $rejectedAmount = intval($_POST['rejected_amount']);
    $rejectionReason = $_POST['reason'];

    if ($rejectedAmount <= 0) {
        die("Rejected amount must be greater than zero.");
    }

    $reportModel = new ReportModel();
    $reportModel->addRejection($transactionId, $rejectedAmount, $rejectionReason);

    // Redirect back to the verification page
    header("Location: index.php?action=report/verifyWithdrawReport&bank_id=$bankId&report_id=$reportId");
    exit();
}

include 'views/includes/header.php';
include 'views/includes/sidebar.php';
?>

<div class="content">
    <h1>Reject Card</h1>
    <form action="index.php?path=report/rejectCard" method="POST">
        <input type="hidden" name="transaction_id" value="<?= $transactionId ?>">
        <input type="hidden" name="bank_id" value="<?= $bankId ?>">
        <input type="hidden" name="report_id" value="<?= $reportId ?>">
        
        <label for="rejected_amount">Rejected Amount:</label>
        <input type="number" id="rejected_amount" name="rejected_amount" required>

        <label for="reason">Reason:</label>
        <select id="reason" name="reason" required>
            <option value="System Error">System Error</option>
            <option value="Quality">Quality</option>
        </select>

        <button type="submit" name="submit_rejection" class="btn">Submit</button>
    </form>
</div>

<?php include 'views/includes/footer.php'; ?>
