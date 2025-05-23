<?php
// views/components/BankCard.php
// Expects: $bankLogoUrl, $bankName, $cardsValue
?>
<div class="card h-100 shadow-sm">
  <img src="<?= htmlspecialchars($bankLogoUrl) ?>" class="card-img-top p-3 bank-card-logo-img" alt="<?= htmlspecialchars($bankName) ?> Logo">
  <div class="card-body text-center d-flex flex-column justify-content-center">
    <h5 class="card-title"><?= htmlspecialchars($bankName) ?></h5>
    <p class="card-text">
      <small class="text-muted">Total Cards: <?= htmlspecialchars($cardsValue) ?></small>
    </p>
  </div>
</div>
