<?php
// Check if card details are passed
if (!isset($card) || !is_array($card)) {
    die("Invalid card data.");
}

// Include header
include 'views/includes/header.php';
include 'views/includes/sidebar.php';

?>

<div class="content">
    <h1>Withdraw Card</h1>
    <h2>Card: <?= htmlspecialchars($card['name']); ?></h2>

    <form action="?path=report/processWithdraw" method="post">
        <!-- Hidden inputs for card_id and bank_id -->
        <input type="hidden" name="card_id" value="<?= htmlspecialchars($card['id']); ?>">
        <input type="hidden" name="bank_id" value="<?= htmlspecialchars($card['bank_id']); ?>">

        <!-- Input for quantity -->
        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" required min="1">

        <!-- Input for date -->
        <label for="date">Withdrawal Date:</label>
        <input type="date" name="date" id="date" value="<?= date('Y-m-d'); ?>" required>

        <!-- Dropdown for remarks -->
        <label for="remarks">Remarks:</label>
        <select name="remarks" id="remarks" required>
            <option value="Normal Withdraw">Normal Withdraw</option>
            <option value="Production">Production</option>
        </select>

        <!-- Submit button -->
        <button type="submit">Submit</button>
    </form>
</div>

<?php
// Include footer
include 'views/includes/footer.php';
?>
