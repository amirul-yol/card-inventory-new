<?php include 'views/includes/header.php';
include 'views/includes/sidebar.php'; ?>

<div class="content">
    <h1>Deposit Card</h1>
    <h2>Card: <?= htmlspecialchars($card['name']); ?></h2>

    <form action="index.php?path=card/processDepositCard" method="post">
        <input type="hidden" name="card_id" value="<?= $card['id']; ?>">
        <input type="hidden" name="bank_id" value="<?= $card['bank_id']; ?>">
        
        <label for="quantity">Deposit Quantity:</label>
        <input type="number" name="quantity" id="quantity" min="1" required>

        <button type="submit" class="btn btn-submit">Submit</button>
    </form>
</div>


<?php include 'views/includes/footer.php'; ?>