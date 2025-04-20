<?php 
// Include AuthController to check user role
require_once 'controllers/AuthController.php';
$authController = new AuthController();
$isBank = $authController->isBank();
$bankId = $isBank && isset($_SESSION['bank_id']) ? $_SESSION['bank_id'] : null;

// We'll need bank data from BankController
require_once 'controllers/BankController.php';
$bankController = new BankController();
$allBanks = $bankController->getAllBanks();

// We'll need card data from CardController
require_once 'controllers/CardController.php';
$cardController = new CardController();
$banksWithCards = $cardController->getBanksWithCards();

include __DIR__ . '/../includes/header.php'; 
?>

<div class="container">
    <div class="stats-grid">
        <div class="stat-card <?= $isBank ? 'clickable' : ''; ?>" <?= $isBank ? 'onclick="window.location.href=\'index.php?path=report/bankReports&bank_id=' . $bankId . '\'"' : ''; ?>>
            <div class="stat-box-layout">
                <div class="stat-icon-box">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-title">Total Reports</div>
                    <div class="stat-value"><?php echo $data['totalReports']; ?></div>
                </div>
            </div>
        </div>
        
        <div class="stat-card <?= $isBank ? 'clickable' : ''; ?>" <?= $isBank ? 'onclick="window.location.href=\'index.php?path=card\'"' : ''; ?>>
            <div class="stat-box-layout">
                <div class="stat-icon-box">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-title">Total Cards</div>
                    <div class="stat-value"><?php echo $data['totalCards']; ?></div>
                </div>
            </div>
        </div>
        
        <?php if (!$isBank): ?>
        <div class="stat-card">
            <div class="stat-box-layout">
                <div class="stat-icon-box">
                    <i class="fas fa-university"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-title">Total Banks</div>
                    <div class="stat-value"><?php echo $data['totalBanks']; ?></div>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-box-layout">
                <div class="stat-icon-box">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-title">Total Users</div>
                    <div class="stat-value"><?php echo $data['totalUsers']; ?></div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php if (!$isBank): ?>
    <!-- Bank Cards Carousel Section -->
    <div class="section-divider">
        <div class="section-title">Banks</div>
        <div class="section-actions">
            <?php if (!$isBank && ($authController->isAdmin() || $authController->isLogisticsOfficer())): ?>
            <a href="#" id="addCardBtn" class="see-all-link">Add Card</a>
            <?php endif; ?>
            <a href="#" id="viewAllBanksBtn" class="see-all-link">See all banks</a>
        </div>
    </div>
    
    <div class="carousel-container">
        <button class="carousel-arrow prev-arrow">
            <i class="fas fa-chevron-left"></i>
        </button>
        
        <div class="carousel-wrapper">
            <div class="bank-carousel">
                <?php if(!empty($allBanks)): ?>
                    <?php foreach($allBanks as $bank): ?>
                        <div class="bank-card-item" data-bank-id="<?= $bank['bank_id']; ?>">
                            <div class="bank-logo-container">
                                <img src="<?= $bank['bank_logo']; ?>" alt="<?= $bank['bank_name']; ?>" class="bank-card-logo">
                            </div>
                            <div class="bank-card-info">
                                <div class="bank-card-title"><?= $bank['bank_name']; ?></div>
                                <div class="bank-card-stats">
                                    <span class="card-count">
                                        <i class="fas fa-credit-card"></i>
                                        <?= $bank['card_count']; ?> card<?= $bank['card_count'] != 1 ? 's' : ''; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-banks-message">No banks available</div>
                <?php endif; ?>
            </div>
        </div>
        
        <button class="carousel-arrow next-arrow">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
    <?php endif; ?>
</div>

<!-- Modal for Card Details -->
<div id="cardDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalBankName"></h2>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <!-- Main cards table view -->
            <div id="cardsTableContainer" class="modal-view">
                <!-- Card info will be loaded here -->
            </div>
            
            <!-- Card details view (initially hidden) -->
            <div id="cardDetailContainer" class="modal-view" style="display: none;">
                <div class="card-detail-header">
                    <button id="backToTableBtn" class="back-button">
                        <i class="fas fa-arrow-left"></i> Back to cards
                    </button>
                </div>
                <div class="card-detail-content">
                    <!-- Card details will be loaded here -->
                </div>
            </div>
            
            <!-- Card transactions view (initially hidden) -->
            <div id="cardTransactionsContainer" class="modal-view" style="display: none;">
                <div class="card-detail-header">
                    <button id="backToTableFromTransBtn" class="back-button">
                        <i class="fas fa-arrow-left"></i> Back to cards
                    </button>
                </div>
                <div class="transactions-content">
                    <!-- Transaction history will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Deposit Card form popup -->
<div id="depositCardPopup" class="popup">
    <div class="popup-content">
        <div class="popup-header">
            <h3>Deposit Card</h3>
            <span class="close-popup">&times;</span>
        </div>
        <div class="popup-body">
            <form id="depositCardForm" method="post" action="index.php?path=card/processDepositCard">
                <input type="hidden" id="deposit_card_id" name="card_id">
                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" min="1" required class="form-control">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" id="cancelDepositBtn">Cancel</button>
                    <button type="submit" class="btn-primary">Deposit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Card form popup -->
<div id="addCardPopup" class="popup">
    <div class="popup-content">
        <div class="popup-header">
            <h3>Add New Card</h3>
            <span class="close-popup">&times;</span>
        </div>
        <div class="popup-body">
            <form id="addCardForm" method="post" action="index.php?path=card/store">
                <div class="form-group">
                    <label for="name">Card Name:</label>
                    <input type="text" id="name" name="name" required class="form-control">
                </div>

                <div class="form-group">
                    <label for="bank_id">Bank:</label>
                    <select id="bank_id" name="bank_id" required class="form-control">
                        <option value="">-- Select Bank --</option>
                        <?php 
                        // Get all banks from the array we already have loaded
                        foreach ($allBanks as $bank): 
                        ?>
                            <option value="<?= $bank['bank_id'] ?>"><?= $bank['bank_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="association">Association:</label>
                    <input type="text" id="association" name="association" required class="form-control">
                </div>

                <div class="form-group">
                    <label for="chip_type">Chip Type:</label>
                    <input type="text" id="chip_type" name="chip_type" required class="form-control">
                </div>

                <div class="form-group">
                    <label for="type">Type:</label>
                    <input type="text" id="type" name="type" required class="form-control">
                </div>

                <div class="form-group">
                    <label for="expired_at">Expiration Date:</label>
                    <input type="date" id="expired_at" name="expired_at" required class="form-control">
                </div>

                <div class="form-group">
                    <label for="card_quantity">Quantity:</label>
                    <input type="number" id="card_quantity" name="quantity" min="1" required class="form-control">
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" id="cancelAddCardBtn">Cancel</button>
                    <button type="submit" class="btn-primary">Add Card</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- All Banks popup -->
<div id="allBanksPopup" class="popup">
    <div class="popup-content popup-lg">
        <div class="popup-header">
            <h3>All Banks</h3>
            <span class="close-popup">&times;</span>
        </div>
        <div class="popup-body">
            <?php if (!$isBank && $authController->isAdmin()): ?>
            <div class="popup-actions">
                <button id="showAddBankBtn" class="btn-primary">
                    <i class="fas fa-plus"></i> Add Bank
                </button>
            </div>
            <?php endif; ?>
            
            <div class="banks-grid">
                <?php if(!empty($allBanks)): ?>
                    <?php 
                    // Configuration for pagination
                    $banksPerPage = 8; // 2 rows of 4 banks
                    $totalBanks = count($allBanks);
                    $totalPages = ceil($totalBanks / $banksPerPage);
                    ?>
                    
                    <div class="banks-grid-items">
                        <?php foreach($allBanks as $index => $bank): ?>
                            <div class="bank-grid-item" data-page="<?= floor($index / $banksPerPage) + 1 ?>">
                                <div class="bank-logo-container">
                                    <img src="<?= $bank['bank_logo']; ?>" alt="<?= $bank['bank_name']; ?>" class="bank-card-logo">
                                </div>
                                <div class="bank-card-info">
                                    <div class="bank-card-title"><?= $bank['bank_name']; ?></div>
                                    <div class="bank-card-stats">
                                        <span class="card-count">
                                            <i class="fas fa-credit-card"></i>
                                            <?= $bank['card_count']; ?> card<?= $bank['card_count'] != 1 ? 's' : ''; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if($totalPages > 1): ?>
                    <div class="pagination">
                        <button class="pagination-btn prev-page" disabled>
                            <i class="fas fa-chevron-left"></i> Previous
                        </button>
                        <div class="page-indicator">
                            Page <span id="currentPage">1</span> of <?= $totalPages ?>
                        </div>
                        <button class="pagination-btn next-page">
                            Next <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="no-banks-message">No banks available</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Bank popup -->
<div id="addBankPopup" class="popup">
    <div class="popup-content">
        <div class="popup-header">
            <h3>Add Bank</h3>
            <span class="close-popup">&times;</span>
        </div>
        <div class="popup-body">
            <button id="backToBanksBtn" class="back-button">
                <i class="fas fa-arrow-left"></i> Back to banks
            </button>
            
            <form class="form-container" action="index.php?path=bank/store" method="POST" enctype="multipart/form-data">
                <!-- Bank Name -->
                <div class="form-group">
                    <label for="name">Bank Name:</label>
                    <input type="text" id="name" name="name" placeholder="Enter bank name" required class="form-control">
                </div>

                <!-- Bank Logo -->
                <div class="form-group">
                    <label for="logo_url">Bank Logo:</label>
                    <input type="file" id="logo_url" name="logo_url" class="form-control">
                    <small>Optional: Upload a logo for the bank.</small>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" id="cancelAddBankBtn" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Add Bank</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Store card data for JavaScript access -->
<script>
    const banksWithCards = <?= json_encode($banksWithCards) ?>;
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
