<?php 
require_once 'controllers/AuthController.php';
// Create Auth Controller instance to check user roles
$authController = new AuthController();
$isLO = $authController->isLogisticsOfficer();
$isBank = $authController->isBank();
$bankId = $isBank && isset($_SESSION['bank_id']) ? $_SESSION['bank_id'] : null;
include 'views/includes/header.php'; 
?>
<?php include 'views/includes/sidebar.php'; ?>

<style>
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .filter-info {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #e9f7fe;
        padding: 8px 15px;
        border-radius: 4px;
        border-left: 4px solid #2196F3;
    }
    
    .filter-options {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .filter-options span {
        color: #555;
        font-weight: 500;
    }
    
    .btn-outline {
        background: transparent;
        border: 1px solid #2196F3;
        color: #2196F3;
        padding: 5px 12px;
        border-radius: 4px;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .btn-outline:hover {
        background: #e9f7fe;
    }
    
    .clear-filter {
        color: #f44336;
        text-decoration: none;
        font-weight: bold;
        font-size: 1.1em;
        line-height: 1;
    }
    
    .clear-filter:hover {
        text-decoration: none;
        color: #d32f2f;
    }
    
    .add-card-btn.disabled {
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
    <div class="card-header">
        <h1>Card Management</h1>
        <?php if (isset($this->activeFilter)): ?>
            <div class="filter-info">
                <span>Showing: <?= htmlspecialchars($this->activeFilter) ?></span>
                <a href="index.php?path=card" class="clear-filter">× Clear filter</a>
            </div>
        <?php else: ?>
            <div class="filter-options">
                <?php if (!$authController->isBank()): ?>
                    <a href="index.php?path=card/create" class="btn btn-primary add-card-btn">Add Card</a>                
                <?php endif; ?>
                <span>Filter by type: </span>
                <a href="index.php?path=card&type=CREDIT CARD" class="btn btn-outline">Credit Cards</a>
                <a href="index.php?path=card&type=DEBIT CARD" class="btn btn-outline">Debit Cards</a>
            </div>
        <?php endif; ?>
    </div>
    <div class="bank-list">
        <?php foreach ($banks as $currentBankId => $bank): ?>
            <!-- Bank Card -->
            <div class="bank-card" data-bank-id="<?= $currentBankId ?>">
                <div class="bank-header">
                    <div class="bank-info">
                        <img src="<?= $bank['bank_logo'] ?>" alt="<?= $bank['bank_name'] ?>" class="bank-logo">
                        <h2><?= $bank['bank_name'] ?></h2>
                    </div>
                    <span class="expand-icon">▼</span>
                </div>
            </div>
            <!-- Card Table -->
            <div class="bank-details" id="bank-<?= $currentBankId ?>">
                <?php if (!empty($bank['cards'])): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Card Name</th>
                                <th>Payment Scheme</th>
                                <th>Chip Type</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>Expiration Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bank['cards'] as $card): ?>
                                <tr>
                                    <td><?= $card['card_name'] ?></td>
                                    <td><?= $card['association'] ?></td>
                                    <td><?= $card['chip_type'] ?></td>
                                    <td><?= $card['card_type'] ?></td>
                                    <td><?= number_format($card['card_quantity'], 0, '.', ',') ?></td>
                                    <td><?= $card['expired_at'] ?></td>
                                    <td>
                                        <a href="index.php?path=card/details&id=<?= $card['card_id']; ?>" class="btn-icon">
                                            <i class="fa fa-search"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="index.php?path=card/viewTransactions&card_id=<?= $card['card_id']; ?>" class="btn-icon">
                                            <i class="fa fa-file-text"></i> <!-- FontAwesome icon for a report -->
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No cards available for this bank.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Add auto-expand script for bank users -->
<?php if ($isBank && $bankId): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-expand the bank's card section for bank users
        const bankDetails = document.getElementById('bank-<?= $bankId ?>');
        const bankCard = document.querySelector(`.bank-card[data-bank-id="<?= $bankId ?>"]`);
        
        if (bankDetails && bankCard) {
            // Display the details
            bankDetails.style.display = 'block';
            
            // Update the icon if needed
            const icon = bankCard.querySelector('.expand-icon');
            if (icon) {
                icon.classList.add('open');
            }
        }
    });
</script>
<?php endif; ?>

<?php include 'views/includes/footer.php'; ?>