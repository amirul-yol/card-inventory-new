<?php include 'views/includes/header.php';
include 'views/includes/sidebar.php'; ?>

<div class="content">
    <h1>Transactions for <?= htmlspecialchars($card['name']); ?>
        <a href="?path=card/depositCardForm&card_id=<?= $card['id']; ?>" class="btn">Deposit Card</a>
    </h1>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Deposit (In)</th>
                <th>Withdrawn (Out)</th>
                <th>Rejected </th>
                <th>Total Transaction</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?= $transaction['transaction_date']; ?></td>
                    <td><?= $transaction['quantity_in']; ?></td>
                    <td><?= $transaction['quantity_out']; ?></td>
                    <td><?= $transaction['reject_quantity']; ?></td>
                    <td><?= $transaction['quantity_in'] + $transaction['quantity_out'] + $transaction['reject_quantity']; ?></td>
                    <!-- <td>
                        <a href="?path=card/editTransactionForm&transaction_id=<?= $transaction['id']; ?>" class="btn">Edit</a>
                    </td> -->
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="index.php?path=card" class="btn btn-primary">Back to Card List</a>
</div>

<?php include 'views/includes/footer.php'; ?>
