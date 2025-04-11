<?php include 'views/includes/header.php'; ?>
<?php include 'views/includes/sidebar.php'; ?>

<div class="content">
    <h1>Add a New Card
        <a href="index.php?path=card" class="btn btn-primary add-card-btn">Back to Card List</a>
    </h1>
    
    <form action="index.php?path=card/store" method="POST" class="form-container">
        <div class="form-group">
            <label for="name">Card Name:</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="bank_id">Bank:</label>
            <select id="bank_id" name="bank_id" required>
                <option value="">-- Select Bank --</option>
                <?php foreach ($banks as $bank): ?>
                    <option value="<?= $bank['id'] ?>"><?= $bank['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="association">Association:</label>
            <input type="text" id="association" name="association" required>
        </div>

        <div class="form-group">
            <label for="chip_type">Chip Type:</label>
            <input type="text" id="chip_type" name="chip_type" required>
        </div>

        <div class="form-group">
            <label for="type">Type:</label>
            <input type="text" id="type" name="type" required>
        </div>

        <div class="form-group">
            <label for="expired_at">Expiration Date:</label>
            <input type="date" id="expired_at" name="expired_at" required>
        </div>

        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" min="1" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Add Card</button>
            <a href="index.php?path=card" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include 'views/includes/footer.php'; ?>
