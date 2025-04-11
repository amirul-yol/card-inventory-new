<?php include 'views/includes/header.php'; ?>
<?php include 'views/includes/sidebar.php'; ?>

<div class="content">
    <h1>Card Management 
        <a href="index.php?path=card/create" class="btn btn-primary add-card-btn">Add Card</a>
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