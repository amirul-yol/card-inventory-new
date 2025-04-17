<?php
require_once 'models/ReportModel.php';
require_once 'models/CardModel.php';

// Get bank ID from the URL
if (!isset($_GET['bank_id']) || empty($_GET['bank_id']) || !is_numeric($_GET['bank_id'])) {
    die("Invalid or missing Bank ID.");
}

$bankId = intval($_GET['bank_id']);
$reportModel = new ReportModel();
$cardModel = new CardModel();

// Fetch bank details
$bank = $reportModel->getBankById($bankId);
if (!$bank) {
    die("Bank not found.");
}

// Get all cards and their withdrawal status for today
$cards = $cardModel->getCardsWithWithdrawalStatus($bankId);

// Check if ANY card is withdrawn (instead of ALL cards)
$anyWithdrawn = false;
foreach ($cards as $card) {
    if (!empty($card['withdrawn_today'])) { // withdrawn_today is a boolean field
        $anyWithdrawn = true;
        break;
    }
}

// Check if there's a report for the current date
$currentDate = date('Y-m-d');
$latestReport = $reportModel->getLatestReportByBankAndDate($bankId, $currentDate);

// Include header and sidebar
include 'views/includes/header.php';
include 'views/includes/sidebar.php';
?>

<div class="content">
    <h1>Withdraw Cards</h1>
    <h2>Bank: <?= htmlspecialchars($bank['name'], ENT_QUOTES, 'UTF-8'); ?></h2>

    <!-- Finish Withdraw Button - Enable if ANY card is withdrawn -->
    <button class="finish-withdraw-btn" 
            <?= ($anyWithdrawn && (!$latestReport || $latestReport['status'] === 'rejected')) ? 'onclick="finishWithdraw()"' : 'disabled'; ?>>
        Finish Withdraw
    </button>
    
    <!-- Tooltip for disabled button -->
    <?php if (!$anyWithdrawn): ?>
    <div class="tooltip-container">
        <span class="tooltip-text">Withdraw at least one card to enable this button</span>
    </div>
    <?php endif; ?>

    <script>
        function finishWithdraw() {
            const url = "index.php?path=report/submitWithdrawReport&bank_id=<?php echo $bankId; ?>";
            console.log("Navigating to:", url); // Debugging
            if (confirm("Are you sure you want to finish the withdrawal process?")) {
                fetch(url)
                    .then(response => {
                        if (response.ok) {
                            // Redirect to bank reports page after successful processing
                            window.location.href = "index.php?path=report/bankReports&bank_id=<?php echo $bankId; ?>";
                        } else {
                            alert("Failed to process the request. Please try again.");
                        }
                    })
                    .catch(error => console.error("Error:", error));
            }
        }

    </script>

    <!-- Cards Table -->
    <table class="card-table">
        <thead>
            <tr>
                <th>Card Name</th>
                <th>Quantity</th>
                <th>Chip Type</th>
                <th>Type</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cards as $card): ?>
                <?php
                $withdrawal = $reportModel->getCardWithdrawalDetails($card['id'], date('Y-m-d'));
                $quantity = htmlspecialchars($card['quantity'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                $chipType = htmlspecialchars($card['chip_type'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                $type = htmlspecialchars($card['type'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                ?>
                <tr>
                    <td><?= htmlspecialchars($card['name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= $quantity; ?></td>
                    <td><?= $chipType; ?></td>
                    <td><?= $type; ?></td>
                    <td><?= $withdrawal ? 'Withdrawn' : 'Not Withdrawn'; ?></td>
                    <td>
                        <?php if ($withdrawal): ?>
                            <?php if (!$latestReport || $latestReport['status'] === 'rejected'): ?>
                                <a href="index.php?path=report/editWithdrawalForm&card_id=<?= $card['id']; ?>&bank_id=<?= $bankId; ?>" 
                                class="btn edit-btn">Edit</a>
                            <?php else: ?>
                                <button class="btn edit-btn" disabled>Locked</button>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php if (!$latestReport || $latestReport['status'] === 'rejected'): ?>
                                <a href="index.php?path=report/withdrawCardForm&card_id=<?= $card['id']; ?>&bank_id=<?= $bankId; ?>" 
                                class="btn withdraw-btn">Withdraw</a>
                            <?php else: ?>
                                <button class="btn withdraw-btn" disabled>Locked</button>
                            <?php endif; ?>
                        <?php endif; ?>

                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
    .card-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    .card-table th, .card-table td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }
    .card-table th {
        background-color:rgb(3, 3, 3);
        font-weight: bold;
    }
    .finish-withdraw-btn {
        margin-bottom: 20px;
        padding: 10px 20px;
        font-size: 16px;
        background-color: <?= $anyWithdrawn ? '#28a745' : '#cccccc'; ?>;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: <?= $anyWithdrawn ? 'pointer' : 'not-allowed'; ?>;
    }
    .finish-withdraw-btn:disabled {
        background-color: #cccccc;
    }
    .btn {
        padding: 8px 12px;
        border-radius: 5px;
        text-decoration: none;
        color: #fff;
        font-size: 14px;
    }
    .withdraw-btn {
        background-color: #28a745;
    }
    .edit-btn {
        background-color: #007bff;
    }
    .btn:hover {
        opacity: 0.8;
    }
    .tooltip-container {
        position: relative;
        display: inline-block;
        margin-left: 10px;
    }
    .tooltip-text {
        color: #777;
        font-size: 14px;
        font-style: italic;
    }
</style>

<?php include 'views/includes/footer.php'; ?>
