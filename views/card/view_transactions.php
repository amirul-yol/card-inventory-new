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
                <th>Quantity In</th>
                <th>Quantity Out</th>
                <th>Rejected</th>
                <th>Total Transactions</th>
                <!-- <th>Remarks</th> -->
                <!-- <th>Action</th> -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction): ?>
                <?php 
                    $reject_total = $transaction['reject_quality'] + $transaction['reject_system'];
                    $row_total = $transaction['quantity_in'] + $transaction['quantity_out'] + $reject_total;
                ?>
                <tr>
                    <td><?= $transaction['transaction_date']; ?></td>
                    <td><?= $transaction['quantity_in']; ?></td>
                    <td><?= $transaction['quantity_out']; ?></td>
                    <td><?= $reject_total; ?></td>
                    <td><?= $row_total; ?></td>

                    <!-- <td><?= $transaction['remarks']; ?></td> -->
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
