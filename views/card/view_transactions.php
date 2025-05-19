<?php include 'views/includes/header.php';
include 'views/includes/sidebar.php'; ?>

<div class="content">
    <h1>Transactions for <?= htmlspecialchars($card['name']); ?></h1>
    <!-- <a href="?path=card/depositCardForm&card_id=<?= $card['id']; ?>" class="btn">Deposit Card</a> -->
    <table>
        <thead>
            <tr>
                <!-- <th>ID</th> -->
                <th>Quantity</th>
                <th>Date</th>
                <th>Remarks</th>
                <!-- <th>Action</th> -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <!-- <td><?= $transaction['id']; ?></td> -->
                    <td><?= $transaction['quantity']; ?></td>
                    <td><?= $transaction['transaction_date']; ?></td>
                    <td><?= $transaction['remarks']; ?></td>
                    <!-- <td>
                        <a href="?path=card/editTransactionForm&transaction_id=<?= $transaction['id']; ?>" class="btn">Edit</a>
                    </td> -->
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'views/includes/footer.php'; ?>
