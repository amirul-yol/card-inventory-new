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
            <h2 id="modalReportTitle">Report Details</h2>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <!-- Report overview tab (initially visible) -->
            <div id="reportOverviewContainer" class="modal-view">
                <div class="report-overview-header">
                    <div class="report-bank-info">
                        <img src="uploads/logos/default_bank.png" alt="Bank Logo" class="bank-logo-lg">
                        <div class="bank-info-text">
                            <h3 id="reportBankName">Demo Bank</h3>
                            <p>Report ID: <span id="reportId">#12345</span></p>
                        </div>
                    </div>
                    <div class="report-status-badge" id="reportStatusBadge">
                        Pending
                    </div>
                </div>
                
                <div class="report-details-grid">
                    <div class="report-detail-item">
                        <div class="detail-label">Date</div>
                        <div class="detail-value" id="reportDate">2023-05-15</div>
                    </div>
                    <div class="report-detail-item">
                        <div class="detail-label">Created By</div>
                        <div class="detail-value" id="reportCreator">John Doe</div>
                    </div>
                    <div class="report-detail-item">
                        <div class="detail-label">Verified By</div>
                        <div class="detail-value" id="reportVerifier">Not verified yet</div>
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
                            <!-- Sample data, will be replaced dynamically -->
                            <tr>
                                <td>Visa Classic</td>
                                <td>100</td>
                                <td>Normal Withdraw</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>Mastercard Gold</td>
                                <td>50</td>
                                <td>Production</td>
                                <td>5</td>
                            </tr>
                            <tr>
                                <td>American Express Platinum</td>
                                <td>25</td>
                                <td>Normal Withdraw</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>Discover Cashback</td>
                                <td>75</td>
                                <td>Production</td>
                                <td>10</td>
                            </tr>
                            <tr>
                                <td>Visa Signature</td>
                                <td>60</td>
                                <td>Normal Withdraw</td>
                                <td>0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="report-actions" id="reportActionButtons">
                    <button id="generateReportBtn" class="btn-action">
                        <i class="fas fa-file-pdf"></i> Generate PDF
                    </button>
                    <button id="switchToVerifyViewBtn" class="btn-action btn-verify">
                        <i class="fas fa-check-circle"></i> Verify Report
                    </button>
                </div>
            </div>
            
            <!-- Report verification view (initially hidden) -->
            <div id="reportVerificationContainer" class="modal-view" style="display: none;">
                <div class="verification-header">
                    <button id="backToReportOverviewBtn" class="back-button">
                        <i class="fas fa-arrow-left"></i> Back to overview
                    </button>
                    <h3>Verify Withdrawal Report</h3>
                </div>
                
                <form id="verifyReportForm" class="verify-form">
                    <input type="hidden" id="verify-report-id" name="report_id" value="">
                    <input type="hidden" id="verify-bank-id" name="bank_id" value="">
                    
                    <div class="alert alert-info">
                        Please review the withdrawn cards and mark any rejected cards before verifying.
                    </div>
                    
                    <table class="verify-cards-table">
                        <thead>
                            <tr>
                                <th>Card Name</th>
                                <th>Quantity</th>
                                <th>Remarks</th>
                                <th>Reject Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="verifyCardsTableBody">
                            <!-- Sample data, will be replaced dynamically -->
                            <tr data-transaction-id="1">
                                <td>Visa Classic</td>
                                <td>100</td>
                                <td>Normal Withdraw</td>
                                <td>
                                    <span class="rejected-amount">0</span>
                                </td>
                                <td>
                                    <button type="button" class="btn-reject" data-transaction-id="1">
                                        Reject
                                    </button>
                                </td>
                            </tr>
                            <tr data-transaction-id="2">
                                <td>Mastercard Gold</td>
                                <td>50</td>
                                <td>Production</td>
                                <td>
                                    <span class="rejected-amount">0</span>
                                </td>
                                <td>
                                    <button type="button" class="btn-reject" data-transaction-id="2">
                                        Reject
                                    </button>
                                </td>
                            </tr>
                            <tr data-transaction-id="3">
                                <td>American Express Platinum</td>
                                <td>25</td>
                                <td>Normal Withdraw</td>
                                <td>
                                    <span class="rejected-amount">0</span>
                                </td>
                                <td>
                                    <button type="button" class="btn-reject" data-transaction-id="3">
                                        Reject
                                    </button>
                                </td>
                            </tr>
                            <tr data-transaction-id="4">
                                <td>Discover Cashback</td>
                                <td>75</td>
                                <td>Production</td>
                                <td>
                                    <span class="rejected-amount">0</span>
                                </td>
                                <td>
                                    <button type="button" class="btn-reject" data-transaction-id="4">
                                        Reject
                                    </button>
                                </td>
                            </tr>
                            <tr data-transaction-id="5">
                                <td>Visa Signature</td>
                                <td>60</td>
                                <td>Normal Withdraw</td>
                                <td>
                                    <span class="rejected-amount">0</span>
                                </td>
                                <td>
                                    <button type="button" class="btn-reject" data-transaction-id="5">
                                        Reject
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="form-actions">
                        <button type="button" id="cancelVerifyBtn" class="btn-secondary">Cancel</button>
                        <button type="submit" id="submitVerifyBtn" class="btn-primary">Verify Report</button>
                    </div>
                </form>
            </div>
            
            <!-- Reject card form (initially hidden) -->
            <div id="rejectCardContainer" class="modal-view" style="display: none;">
                <div class="reject-header">
                    <button id="backToVerificationBtn" class="back-button">
                        <i class="fas fa-arrow-left"></i> Back to verification
                    </button>
                    <h3>Reject Card</h3>
                </div>
                
                <form id="rejectCardForm" class="reject-form">
                    <input type="hidden" id="reject-transaction-id" name="transaction_id" value="">
                    <input type="hidden" id="reject-report-id" name="report_id" value="">
                    <input type="hidden" id="reject-bank-id" name="bank_id" value="">
                    
                    <div class="form-group">
                        <label for="reject-card-name">Card:</label>
                        <input type="text" id="reject-card-name" readonly class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="reject-quantity">Withdrawn Quantity:</label>
                        <input type="number" id="reject-original-quantity" readonly class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="reject-quantity">Reject Quantity:</label>
                        <input type="number" id="reject-quantity" name="quantity" min="1" required class="form-control">
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" id="cancelRejectBtn" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-primary">Reject Cards</button>
                    </div>
                </form>
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

<!-- Reports styling -->
<style>
    .reports-container {
        margin: 20px 0;
    }
    
    .reports-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }
    
    .report-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 15px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    }
    
    .report-header {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
    }
    
    .report-bank-logo {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        background-color: #f0f0f0;
        margin-right: 10px;
    }
    
    .report-logo {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .report-bank-name {
        font-weight: 600;
        font-size: 16px;
    }
    
    .report-date {
        color: #666;
        font-size: 14px;
        margin-bottom: 10px;
    }
    
    .report-date i {
        margin-right: 5px;
    }
    
    .report-status {
        display: flex;
        align-items: center;
        font-size: 14px;
        margin-bottom: 15px;
    }
    
    .status-indicator {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 8px;
    }
    
    .status-pending .status-indicator {
        background-color: #f5a623;
    }
    
    .status-verified .status-indicator {
        background-color: #47c98e;
    }
    
    .status-pending {
        color: #f5a623;
    }
    
    .status-verified {
        color: #47c98e;
    }
    
    .status-rejected .status-indicator {
        background-color: #f44336;
    }
    
    .status-rejected {
        color: #f44336;
    }
    
    .status-in-progress .status-indicator {
        background-color: #2196f3;
    }
    
    .status-in-progress {
        color: #2196f3;
    }
    
    .report-actions {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }
    
    .btn-view-report, .btn-verify-report {
        padding: 8px 12px;
        border-radius: 4px;
        border: none;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.2s;
    }
    
    .btn-view-report {
        background-color: #f0f0f0;
        color: #333;
        flex: 1;
    }
    
    .btn-verify-report {
        background-color: #47c98e;
        color: white;
        flex: 1;
    }
    
    .btn-view-report:hover {
        background-color: #e0e0e0;
    }
    
    .btn-verify-report:hover {
        background-color: #3db57e;
    }
    
    .btn-view-report i, .btn-verify-report i {
        margin-right: 5px;
    }
    
    /* Reports filter styling */
    .reports-filter {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
        padding: 15px;
        background-color: #f9f9f9;
        border-radius: 8px;
    }
    
    .filter-group {
        flex: 1;
        min-width: 200px;
    }
    
    .filter-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
    }
    
    /* Report details modal styling */
    .report-overview-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .report-bank-info {
        display: flex;
        align-items: center;
    }
    
    .bank-logo-lg {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        margin-right: 15px;
        object-fit: cover;
    }
    
    .bank-info-text h3 {
        margin: 0 0 5px 0;
        font-size: 22px;
        color: #333;
    }
    
    .bank-info-text p {
        margin: 0;
        color: #666;
        font-size: 14px;
    }
    
    .report-status-badge {
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
        background-color: #f5a623;
        color: white;
    }
    
    .report-details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
        background-color: #f9f9f9;
        padding: 15px;
        border-radius: 8px;
    }
    
    .report-detail-item {
        padding: 10px;
    }
    
    .detail-label {
        font-size: 13px;
        color: #666;
        margin-bottom: 5px;
    }
    
    .detail-value {
        font-size: 16px;
        font-weight: 500;
        color: #333;
    }
    
    .report-content h3 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 18px;
    }
    
    .report-cards-table, .verify-cards-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    
    .report-cards-table th, .report-cards-table td,
    .verify-cards-table th, .verify-cards-table td {
        border: 1px solid #e0e0e0;
        padding: 10px 15px;
        text-align: left;
    }
    
    .report-cards-table th, .verify-cards-table th {
        background-color: #f2f2f2;
        font-weight: 600;
    }
    
    .report-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
    }
    
    .btn-action {
        padding: 8px 15px;
        border-radius: 4px;
        border: none;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        transition: background-color 0.2s;
    }
    
    .btn-action i {
        margin-right: 8px;
    }
    
    .btn-action:hover {
        opacity: 0.9;
    }
    
    .btn-verify {
        background-color: #47c98e;
        color: white;
    }
    
    .verification-header, .reject-header {
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .verification-header h3, .reject-header h3 {
        margin: 15px 0 0 0;
        font-size: 20px;
    }
    
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }
    
    .alert-info {
        color: #31708f;
        background-color: #d9edf7;
        border-color: #bce8f1;
    }
    
    .btn-reject {
        padding: 6px 12px;
        border-radius: 4px;
        background-color: #f44336;
        color: white;
        border: none;
        cursor: pointer;
    }
    
    .btn-reject:hover {
        background-color: #d32f2f;
    }
    
    .rejected-amount {
        font-weight: 500;
    }
    
    /* Form styling for the reject and verify forms */
    .verify-form, .reject-form {
        width: 100%;
    }
    
    .back-button {
        background: none;
        border: none;
        color: #2196f3;
        cursor: pointer;
        padding: 0;
        font-size: 14px;
        display: flex;
        align-items: center;
    }
    
    .back-button i {
        margin-right: 5px;
    }
    
    .back-button:hover {
        text-decoration: underline;
    }
    
    /* Reports carousel styling */
    .reports-carousel {
        display: flex;
        gap: 16px;
        transition: transform 0.3s ease;
    }
    
    .report-carousel-item {
        min-width: 280px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 15px;
        transition: transform 0.2s, box-shadow 0.2s;
        flex-shrink: 0;
    }
    
    .report-carousel-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    }
    
    .reports-prev-arrow, .reports-next-arrow {
        background-color: rgba(255, 255, 255, 0.9);
    }
</style>

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
