<?php
// views/components/BankCard.php
// Expects: $bankLogoUrl, $bankName, $cardsValue
?>
<div class="card h-100 shadow-sm">
  <img src="<?= htmlspecialchars($bankLogoUrl) ?>" class="card-img-top p-3" alt="<?= htmlspecialchars($bankName) ?> Logo" style="max-height: 80px; object-fit: contain; margin-left: auto; margin-right: auto;">
  <div class="card-body text-center d-flex flex-column justify-content-center">
    <h5 class="card-title"><?= htmlspecialchars($bankName) ?></h5>
    <p class="card-text">
      <small class="text-muted">Total Cards: <?= htmlspecialchars($cardsValue) ?></small>
    </p>
  </div>
</div>
