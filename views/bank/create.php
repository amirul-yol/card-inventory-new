<?php include 'views/includes/header.php'; ?>
<?php include 'views/includes/sidebar.php'; ?>


<div class="content">
    <h1>
        Add Bank
        <a href="index.php?path=bank" class="btn btn-primary add-card-btn">Back to Bank List</a>
    </h1>

    
        <form class="form-container" action="index.php?path=bank/store" method="POST" enctype="multipart/form-data" >
            <!-- Bank Name -->
            <div class="form-group">
                <label for="bank_name" >Bank Name:</label>
                <input type="text" id="name" name="name" placeholder="Enter bank name">
            </div>

            <!-- Bank Logo -->
            <div class="form-group">
                <label for="bank_logo">Bank Logo:</label>
                <input type="file" id="logo_url" name="logo_url" >
                <small>Optional: Upload a logo for the bank.</small>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary add-card-btn">Add Bank</button>
                <a href="index.php?path=bank" class="btn btn-secondary">Cancel</a>
            </div>
        </form>

</div>


<?php include 'views/includes/footer.php'; ?>

