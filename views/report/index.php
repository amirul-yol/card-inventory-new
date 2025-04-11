<?php include 'views/includes/header.php'; ?>
<?php include 'views/includes/sidebar.php'; ?>

<div class="content">
    <h1>Reports</h1>
    <h2>Select a Bank</h2>

    <div class="bank-cards">
        <?php foreach ($banks as $bank): ?>
            <div class="card">
                <!-- Check for a valid logo URL -->
                <?php if (!empty($bank['logo_url'])): ?>
                    <img src="<?= htmlspecialchars($bank['logo_url']); ?>" alt="<?= htmlspecialchars($bank['name']); ?>" class="card-logo">
                <?php else: ?>
                    <img src="assets/images/default-logo.png" alt="<?= htmlspecialchars($bank['name']); ?>" class="card-logo">
                <?php endif; ?>

                <h3 class="card-title"><?= htmlspecialchars($bank['name']); ?></h3>
                <a href="index.php?path=report/bankReports&bank_id=<?= $bank['bank_id']; ?>" class="btn">View Reports</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .bank-cards {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
    }
    .card {
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        width: 220px;
        background-color: #f9f9f9;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .card:hover {
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
    .card-logo {
        max-width: 100%;
        height: auto;
        margin-bottom: 15px;
        border-radius: 8px;
    }
    .card-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .btn {
        display: inline-block;
        padding: 10px 16px;
        background-color: #007bff;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        font-size: 14px;
        font-weight: bold;
    }
    .btn:hover {
        background-color: #0056b3;
    }
</style>

<?php include 'views/includes/footer.php'; ?>
