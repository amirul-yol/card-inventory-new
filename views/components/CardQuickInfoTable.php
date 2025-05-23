<?php
// views/components/CardQuickInfoTable.php
// Expects an array $cardsData to be set, containing the card information for the table.
// Example structure for each item in $cardsData:
// [
//   'card_id' => 101,
//   'card_name' => 'Aeon Gold Visa',
//   'association' => 'Visa',
//   'chip_type' => 'EMV',
//   'card_type' => 'Credit Card',
//   'card_quantity' => 5000,
//   'expired_at' => '2025-12-31',
// ]
?>
<div class="mt-xl"> <!-- Margin top extra large -->
  <h2 class="mb-md">Card Quick Info</h2>
  <div class="table-responsive"> <!-- Ensures table responsiveness on smaller screens -->
    <table class="table table-striped table-hover table-bordered">
      <thead class="table-light"> <!-- Light background for header -->
        <tr>
          <th>Card Name</th>
          <th>Payment Scheme</th>
          <th>Chip Type</th>
          <th>Type</th>
          <th class="text-end">Quantity</th> <!-- Right-align quantity -->
          <th>Expiration Date</th>
          <th class="text-center">Actions</th> <!-- Center-align actions -->
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($cardsData)): ?>
          <?php foreach ($cardsData as $card): ?>
            <tr>
              <td><?= htmlspecialchars($card['card_name']) ?></td>
              <td><?= htmlspecialchars($card['association']) ?></td>
              <td><?= htmlspecialchars($card['chip_type']) ?></td>
              <td><?= htmlspecialchars($card['card_type']) ?></td>
              <td class="text-end"><?= number_format($card['card_quantity']) ?></td>
              <td><?= htmlspecialchars(date('d M Y', strtotime($card['expired_at']))) ?></td>
              <td class="text-center">
                <a href="index.php?path=card/details&id=<?= $card['card_id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="View Details">
                  <i class="fa fa-search"></i>
                </a>
                <a href="index.php?path=card/viewTransactions&card_id=<?= $card['card_id']; ?>" class="btn btn-sm btn-outline-secondary" title="View Transactions">
                  <i class="fa fa-file-text"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="text-center">No cards available for this bank.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
