<?php include 'views/includes/header.php';
include 'views/includes/sidebar.php'; ?>

<div class="content">
    <h1>Edit Transaction</h1>
    <form action="index.php?path=card/processEditTransaction" method="post">
        <input type="hidden" name="transaction_id" value="<?= $transaction['id']; ?>">
        <input type="hidden" name="card_id" value="<?= $transaction['card_id']; ?>">

        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" value="<?= htmlspecialchars($transaction['quantity']); ?>" required>

        <button type="submit" class="btn btn-submit">Update</button>
    </form>
</div>


<?php include 'views/includes/footer.php'; ?>