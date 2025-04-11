<?php include 'views/includes/header.php'; ?>
<?php include 'views/includes/sidebar.php'; ?>

<div class="content">
    <h1>
        Bank Management
        <a href="index.php?path=bank/create" class="btn btn-primary add-card-btn" >Add Bank</a>
    </h1>

    <div class="bank-list">
        <?php foreach ($banks as $bank): ?>
            <div class="bank-card" data-bank-id="<?= $bank['bank_id']; ?>">
                <div class="bank-header">
                    <div class="bank-info">
                        <img src="<?= $bank['bank_logo']; ?>" alt="<?= $bank['bank_name']; ?>" class="bank-logo">
                        <div>
                            <h2><?= $bank['bank_name']; ?></h2>
                            <p>Total Cards: <strong><?= $bank['card_count']; ?></strong></p>
                        </div>
                    </div>
                    <span class="expand-icon">▼</span>
                </div>
            </div>
            <div id="bank-<?= $bank['bank_id']; ?>" class="bank-details" style="display: none;">
                <p><strong>Bank ID:</strong> <?= $bank['bank_id']; ?></p>
                <p><strong>Bank Name:</strong> <?= $bank['bank_name']; ?></p>
                <p><strong>Total Cards:</strong> <?= $bank['card_count']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'views/includes/footer.php'; ?>
