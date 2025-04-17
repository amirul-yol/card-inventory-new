<?php 
require_once 'controllers/AuthController.php';
// Create Auth Controller instance to check user roles
$authController = new AuthController();
$isLO = $authController->isLogisticsOfficer();
include 'views/includes/header.php'; 
?>
<?php include 'views/includes/sidebar.php'; ?>

<style>
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
    <h1>Card Management 
        <?php if ($isLO): ?>
            <a href="index.php?path=card/create" class="btn btn-primary add-card-btn">Add Card</a>
        <?php else: ?>
            <div class="tooltip">
                <a class="btn btn-primary add-card-btn disabled">Add Card</a>
                <span class="tooltip-text">Only Logistics Officers can add cards</span>
            </div>
        <?php endif; ?>
    </h1>
    <div class="bank-list">
        <?php foreach ($banks as $bankId => $bank): ?>
            <!-- Bank Card -->
            <div class="bank-card" data-bank-id="<?= $bankId ?>">
                <div class="bank-header">
                    <div class="bank-info">
                        <img src="<?= $bank['bank_logo'] ?>" alt="<?= $bank['bank_name'] ?>" class="bank-logo">
                        <h2><?= $bank['bank_name'] ?></h2>
                    </div>
                    <span class="expand-icon">▼</span>
                </div>
            </div>
            <!-- Card Table -->
            <div class="bank-details" id="bank-<?= $bankId ?>">
                <?php if (!empty($bank['cards'])): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Card Name</th>
                                <th>Association</th>
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
                                    <td><?= $card['card_quantity'] ?></td>
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

<?php include 'views/includes/footer.php'; ?>