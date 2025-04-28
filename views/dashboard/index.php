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

    <!-- Reports Section -->
    <div class="section-divider">
        <div class="section-title">Recent Reports</div>
        <div class="section-actions">
            <?php if ($authController->isLogisticsOfficer()): ?>
            <a href="#" id="withdrawCardBtn" class="see-all-link">Withdraw Card</a>
            <?php endif; ?>
            <a href="#" id="viewAllReportsBtn" class="see-all-link">See all reports</a>
        </div>
    </div>

    <div class="carousel-container">
        <button class="carousel-arrow prev-arrow reports-prev-arrow">
            <i class="fas fa-chevron-left"></i>
        </button>
        
        <div class="carousel-wrapper">
            <div class="reports-carousel">
                <?php 
                if (!empty($recentReports)):
                    foreach ($recentReports as $report):
                        // Get the bank data from the bank array
                        $bankLogo = isset($report['bank_logo']) ? $report['bank_logo'] : 'uploads/logos/default_bank.png';
                        $bankName = isset($report['bank_name']) ? $report['bank_name'] : 'Unknown Bank';
                        
                        // Map the status to the appropriate CSS class
                        $statusClass = '';
                        switch ($report['status']) {
                            case 'Pending':
                                $statusClass = 'status-pending';
                                break;
                            case 'Verified':
                                $statusClass = 'status-verified';
                                break;
                            case 'Rejected':
                                $statusClass = 'status-rejected';
                                break;
                            default:
                                $statusClass = 'status-pending';
                        }
                        
                        // Check if this report is pending and if current user is PO (for verify button)
                        $isPending = $report['status'] == 'Pending';
                        $isPO = $authController->isProductionOfficer();
                ?>
                    <div class="report-carousel-item" data-report-id="<?= $report['id'] ?>">
                        <div class="report-header">
                            <div class="report-bank-logo">
                                <img src="<?= $bankLogo ?>" alt="<?= $bankName ?>" class="report-logo">
                            </div>
                            <div class="report-bank-name"><?= $bankName ?></div>
                        </div>
                        <div class="report-date">
                            <i class="fas fa-calendar-alt"></i>
                            <?= $report['report_date'] ?>
                        </div>
                        <div class="report-status <?= $statusClass ?>">
                            <span class="status-indicator"></span>
                            <?= $report['status'] ?>
                        </div>
                        <div class="report-actions">
                            <button class="btn-view-report">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <?php if($isPending && $isPO): ?>
                            <button class="btn-verify-report">
                                <i class="fas fa-check-circle"></i> Verify
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php 
                    endforeach; 
                else: 
                ?>
                    <div class="no-reports-message">No reports available</div>
                <?php endif; ?>
            </div>
        </div>
        
        <button class="carousel-arrow next-arrow reports-next-arrow">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
</div>

<!-- Reports styling -->
<link rel="stylesheet" href="css/dashboard.css">

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

<!-- Modal for Report Details -->
<div id="reportDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>
                <i class="fas fa-file-alt"></i>
                Report Details
            </h2>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <div class="report-overview-header">
                <div class="report-bank-info">
                    <img src="uploads/logos/default_bank.png" alt="Bank Logo" class="bank-logo-lg">
                    <div class="bank-info-text">
                        <h3 id="reportBankName"></h3>
                        <p>Report ID: <span id="reportId"></span></p>
                    </div>
                </div>
                <div id="reportStatusBadge" class="report-status-badge"></div>
            </div>
            
            <div class="report-details-grid">
                <div class="report-detail-item">
                    <div class="detail-label">Date</div>
                    <div class="detail-value" id="reportDate"></div>
                </div>
                <div class="report-detail-item">
                    <div class="detail-label">Created By</div>
                    <div class="detail-value" id="reportCreator"></div>
                </div>
                <div class="report-detail-item">
                    <div class="detail-label">Verified By</div>
                    <div class="detail-value" id="reportVerifier"></div>
                </div>
            </div>
            
            <div class="report-content">
                <h3>Withdrawn Cards</h3>
                <table class="report-cards-table">
                    <thead>
                        <tr>
                            <th>Card Name</th>
                            <th>Quantity</th>
                            <th>Remarks</th>
                            <th>Rejected</th>
                        </tr>
                    </thead>
                    <tbody id="reportCardsTableBody">
                        <!-- Data will be loaded dynamically -->
                    </tbody>
                </table>
            </div>
            
            <div class="report-actions">
                <a href="#" id="generatePdfBtn" class="btn-action">
                    <i class="fas fa-file-pdf"></i> Generate PDF
                </a>
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
                <div class="form-grid">
                    <div class="form-column">
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
                    </div>
                    
                    <div class="form-column">
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
                    </div>
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

<!-- All Reports popup -->
<div id="allReportsPopup" class="popup">
    <div class="popup-content popup-lg">
        <div class="popup-header">
            <h3>All Reports</h3>
            <span class="close-popup">&times;</span>
        </div>
        <div class="popup-body">
            <?php if ($authController->isLogisticsOfficer()): ?>
            <div class="popup-actions">
                <button id="showWithdrawCardBtn" class="btn-primary">
                    <i class="fas fa-minus"></i> Withdraw Card
                </button>
            </div>
            <?php endif; ?>
            
            <div class="reports-filter">
                <div class="filter-group">
                    <label for="filter-bank">Filter by Bank:</label>
                    <select id="filter-bank" class="form-control">
                        <option value="">All Banks</option>
                        <?php foreach($allBanks as $bank): ?>
                            <option value="<?= $bank['bank_id']; ?>"><?= $bank['bank_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filter-status">Filter by Status:</label>
                    <select id="filter-status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="Pending">Pending</option>
                        <option value="Verified">Verified</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filter-date">Filter by Date:</label>
                    <input type="date" id="filter-date" class="form-control">
                </div>
            </div>
            
            <div class="reports-grid-container">
                <div class="reports-grid-items">
                    <?php 
                    // Fetch all available reports
                    $allReports = isset($recentReports) ? $recentReports : [];
                    if (!empty($allReports)): 
                        // Calculate total pages
                        $reportsPerPage = 8;
                        $totalReports = count($allReports);
                        $totalPages = ceil($totalReports / $reportsPerPage);
                        
                        foreach($allReports as $index => $report):
                            // Calculate which page this report belongs to
                            $page = floor($index / $reportsPerPage) + 1;
                            
                            // Get the bank data from the bank array
                            $bankLogo = isset($report['bank_logo']) ? $report['bank_logo'] : 'uploads/logos/default_bank.png';
                            $bankName = isset($report['bank_name']) ? $report['bank_name'] : 'Unknown Bank';
                            
                            // Map the status to the appropriate CSS class
                            $statusClass = '';
                            switch ($report['status']) {
                                case 'Pending':
                                    $statusClass = 'status-pending';
                                    break;
                                case 'Verified':
                                    $statusClass = 'status-verified';
                                    break;
                                case 'Rejected':
                                    $statusClass = 'status-rejected';
                                    break;
                                default:
                                    $statusClass = 'status-pending';
                            }
                            
                            // Check if this report is pending and if current user is PO (for verify button)
                            $isPending = $report['status'] == 'Pending';
                            $isPO = $authController->isProductionOfficer();
                    ?>
                        <div class="report-card" data-report-id="<?= $report['id'] ?>" data-page="<?= $page ?>" style="display: <?= $page === 1 ? 'flex' : 'none' ?>">
                            <div class="report-header">
                                <div class="report-bank-logo">
                                    <img src="<?= $bankLogo ?>" alt="<?= $bankName ?>" class="report-logo">
                                </div>
                                <div class="report-bank-name"><?= $bankName ?></div>
                            </div>
                            <div class="report-date">
                                <i class="fas fa-calendar-alt"></i>
                                <?= $report['report_date'] ?>
                            </div>
                            <div class="report-status <?= $statusClass ?>">
                                <span class="status-indicator"></span>
                                <?= $report['status'] ?>
                            </div>
                            <div class="report-actions">
                                <button class="btn-view-report">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <?php if($isPending && $isPO): ?>
                                <button class="btn-verify-report">
                                    <i class="fas fa-check-circle"></i> Verify
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php 
                        endforeach;
                    else: 
                    ?>
                        <div class="no-reports-message">No reports available</div>
                    <?php endif; ?>
                </div>
                
                <?php if(!empty($allReports) && $totalPages > 1): ?>
                <div class="pagination">
                    <button class="pagination-btn prev-page" disabled>
                        <i class="fas fa-chevron-left"></i> Previous
                    </button>
                    <div class="page-indicator">
                        Page <span id="currentReportPage">1</span> of <?= $totalPages ?>
                    </div>
                    <button class="pagination-btn next-page">
                        Next <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Withdraw Card popup -->
<div id="withdrawCardPopup" class="popup">
    <div class="popup-content">
        <div class="popup-header">
            <h3>Withdraw Card</h3>
            <span class="close-popup">&times;</span>
        </div>
        <div class="popup-body">
            <button id="backToReportsBtn" class="back-button">
                <i class="fas fa-arrow-left"></i> Back to reports
            </button>
            
            <form id="withdrawCardForm" class="form-container" method="post" action="#">
                <div class="form-group">
                    <label for="withdraw-bank">Bank:</label>
                    <select id="withdraw-bank" name="bank_id" required class="form-control">
                        <option value="">-- Select Bank --</option>
                        <?php foreach($allBanks as $bank): ?>
                            <option value="<?= $bank['bank_id']; ?>"><?= $bank['bank_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group" id="card-selection-container" style="display: none;">
                    <label for="withdraw-card">Card:</label>
                    <select id="withdraw-card" name="card_id" required class="form-control">
                        <option value="">-- Select Card --</option>
                        <!-- Cards will be populated dynamically based on bank selection -->
                    </select>
                </div>
                
                <div class="form-group" id="withdraw-details-container" style="display: none;">
                    <div class="form-group">
                        <label for="withdraw-quantity">Quantity:</label>
                        <input type="number" id="withdraw-quantity" name="quantity" min="1" required class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="withdraw-date">Withdrawal Date:</label>
                        <input type="date" id="withdraw-date" name="date" value="<?= date('Y-m-d'); ?>" required class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="withdraw-remarks">Remarks:</label>
                        <select id="withdraw-remarks" name="remarks" required class="form-control">
                            <option value="Normal Withdraw">Normal Withdraw</option>
                            <option value="Production">Production</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" id="cancelWithdrawCardBtn" class="btn-secondary">Cancel</button>
                    <button type="submit" id="submitWithdrawBtn" class="btn-primary" disabled>Withdraw</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Store card data for JavaScript access -->
<script>
    const banksWithCards = <?= json_encode($banksWithCards) ?>;
</script>

<!-- Notification element -->
<div id="notification" class="notification">
    <div class="notification-content">
        <div class="notification-icon">
            <i class="fas fa-check"></i>
        </div>
        <div class="notification-message" id="notification-message">
            Card added successfully
        </div>
        <div class="notification-close" id="notification-close">
            <i class="fas fa-times"></i>
        </div>
    </div>
    <div class="notification-progress" id="notification-progress"></div>
</div>

<!-- Edit Transaction popup -->
<div id="editTransactionPopup" class="popup">
    <div class="popup-content">
        <div class="popup-header">
            <h3>Edit Transaction</h3>
            <span class="close-popup">&times;</span>
        </div>
        <div class="popup-body">
            <form id="editTransactionForm" method="post" action="index.php?path=card/processEditTransaction">
                <input type="hidden" id="edit_transaction_id" name="transaction_id">
                <input type="hidden" id="edit_card_id" name="card_id">
                
                <div class="form-group">
                    <label for="edit_quantity">Quantity:</label>
                    <input type="number" id="edit_quantity" name="quantity" min="1" required class="form-control">
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-secondary" id="cancelEditTransactionBtn">Cancel</button>
                    <button type="submit" class="btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Include dashboard scripts only on the dashboard page
include __DIR__ . '/scripts.php';
?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
