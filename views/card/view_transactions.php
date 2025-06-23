<?php include 'views/includes/header.php';
include 'views/includes/sidebar.php'; ?>

<div class="content">
    <h1>
        Transactions for <?= htmlspecialchars($card['name']); ?>
        <div class="action-buttons">
            <?php if (!$isBank): ?>
                <a href="?path=card/depositCardForm&card_id=<?= $card['id']; ?>" class="btn">Deposit Card</a>
            <?php endif; ?>
        </div>
    </h1>

    <div style="float: right; margin-bottom: 10px;">
        <label for="transaction_month_filter">Filter by Month:</label>
        <select id="transaction_month_filter">
            <option value="">All Months</option>
            <?php
            // Generate dropdown options for all months
            for ($m = 1; $m <= 12; $m++) {
                $monthName = date("F", mktime(0, 0, 0, $m, 1));
                echo "<option value=\"$m\">$monthName</option>";
            }
            ?>
        </select>
        <button id="applyTransactionFilter" class="btn btn-filter">Apply</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Quantity In</th>
                <th>Quantity Out</th>
                <th>Rejected</th>
                <th>Total Transactions</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody id="transactionTableBody">
            <?php foreach ($transactions as $transaction): ?>
                <?php 
                    $reject_total = $transaction['reject_quality'] + $transaction['reject_system'];
                    $row_total = $transaction['quantity_in'] + $transaction['quantity_out'] + $reject_total;
                ?>
                <tr data-month="<?= date('n', strtotime($transaction['transaction_date'])); ?>">
                    <td><?= $transaction['transaction_date']; ?></td>
                    <td><?= $transaction['quantity_in']; ?></td>
                    <td><?= $transaction['quantity_out']; ?></td>
                    <td><?= $reject_total; ?></td>
                    <td><?= $row_total; ?></td>
                    <td><?= $transaction['transaction_type']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="index.php?path=card" class="btn btn-primary">Back to Card List</a>
</div>

<script>
    document.getElementById('applyTransactionFilter').addEventListener('click', function () {
        const selectedMonth = document.getElementById('transaction_month_filter').value;
        const rows = document.querySelectorAll('#transactionTableBody tr');

        rows.forEach(row => {
            const rowMonth = row.getAttribute('data-month');
            if (selectedMonth === "" || rowMonth === selectedMonth) {
                row.style.display = ""; // Show the row
            } else {
                row.style.display = "none"; // Hide the row
            }
        });
    });
</script>


<?php include 'views/includes/footer.php'; ?>
