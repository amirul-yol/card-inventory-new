<?php include 'views/includes/header.php'; ?>
<?php include 'views/includes/sidebar.php'; ?>

<div class="content">
    <h1>Card Details</h1>
    <?php if ($card): ?>
        <table class="table">
            <!-- <tr>
                <th>ID</th>
                <td><?= htmlspecialchars($card['id']); ?></td>
            </tr> -->
            <tr>
                <th>Name</th>
                <td><?= htmlspecialchars($card['name']); ?></td>
            </tr>
            <!-- <tr>
                <th>Bank ID</th>
                <td><?= htmlspecialchars($card['bank_id']); ?></td>
            </tr> -->
            <tr>
                <th>Payment Scheme</th>
                <td><?= htmlspecialchars($card['association']); ?></td>
            </tr>
            <tr>
                <th>Chip Type</th>
                <td><?= htmlspecialchars($card['chip_type']); ?></td>
            </tr>
            <tr>
                <th>Type</th>
                <td><?= htmlspecialchars($card['type']); ?></td>
            </tr>
            <tr>
                <th>Expiration Date</th>
                <td><?= htmlspecialchars($card['expired_at']); ?></td>
            </tr>
            <tr>
                <th>Quantity</th>
                <td><?= number_format(htmlspecialchars($card['quantity']), 0, '.', ','); ?></td>
            </tr>
            <!-- <tr>
                <th>Created At</th>
                <td><?= htmlspecialchars($card['created_at']); ?></td>
            </tr>
            <tr>
                <th>Updated At</th>
                <td><?= htmlspecialchars($card['updated_at']); ?></td>
            </tr> -->
        </table>
    <?php else: ?>
        <p>No details found for this card.</p>
    <?php endif; ?>
    <a href="index.php?path=card" class="btn btn-primary">Back to Card List</a>
</div>
<?php include 'views/includes/footer.php'; ?>
