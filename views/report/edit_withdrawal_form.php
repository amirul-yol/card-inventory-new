<?php
require_once 'models/ReportModel.php';

// Check if card_id and bank_id are present in the URL
if (!isset($_GET['card_id'], $_GET['bank_id']) || !is_numeric($_GET['card_id']) || !is_numeric($_GET['bank_id'])) {
    die("Invalid or missing parameters.");
}

$cardId = intval($_GET['card_id']);
$bankId = intval($_GET['bank_id']);

// Initialize model
$reportModel = new ReportModel();

// Check if withdrawal data is passed
if (!isset($withdrawal) || !is_array($withdrawal)) {
    die("Invalid withdrawal data.");
}

// Define default values to prevent undefined keys
$cardName = $withdrawal['card_name'] ?? 'Unknown Card';
$quantity = $withdrawal['quantity'] ?? '';
$date = $withdrawal['transaction_date'] ?? date('Y-m-d'); // Default to today's date
$remarks = $withdrawal['remarks'] ?? '';
$withdrawalId = $withdrawal['id'] ?? '';
$bankId = $withdrawal['bank_id'] ?? '';


// Include header
include 'views/includes/header.php';
include 'views/includes/sidebar.php';

?>

<div class="content">
    <h1>Edit Withdrawal</h1>
    <h2>Card: <?= htmlspecialchars($cardName); ?></h2>

    <form action="?path=report/processWithdrawEdit" method="post">
    <input type="hidden" name="withdrawal_id" value="<?= htmlspecialchars($withdrawal['id'] ?? ''); ?>">
    <input type="hidden" name="bank_id" value="<?= htmlspecialchars($withdrawal['bank_id'] ?? ''); ?>">

    <label for="quantity">Quantity:</label>
    <input type="number" name="quantity" id="quantity" value="<?= htmlspecialchars($withdrawal['quantity'] ?? ''); ?>" required min="1">

    <label for="date">Withdrawal Date:</label>
    <input type="date" name="date" id="date" value="<?= htmlspecialchars($withdrawal['transaction_date'] ?? ''); ?>" required>

    <button type="submit">Update</button>
</form>

</div>

<style>
    .content {
        padding: 20px;
    }
    .form-group {
        margin-bottom: 15px;
    }
    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    .form-control {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .btn {
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        background-color: #007bff;
        color: white;
        font-size: 16px;
        cursor: pointer;
    }
    .btn:hover {
        background-color: #0056b3;
    }
</style>

<?php
// Include footer
include 'views/includes/footer.php';
?>
