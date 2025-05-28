<?php
// BankReportsModal.php
// This component displays bank reports in a Bootstrap modal.

// Expected variables from parent scope:
// $modalBankId - The ID of the bank to show reports for (may be null for Admin/LO/PO)
// $modalBankName - The name of the bank (may be null for Admin/LO/PO)
// $isPO - Whether the current user is a Processing Officer
// $isLO - Whether the current user is a Logistics Officer

// Initialize required models
require_once __DIR__ . '/../../models/ReportModel.php';
require_once __DIR__ . '/../../models/BankModel.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

// Get user roles
$authController = new AuthController();
$isPO = $authController->isProductionOfficer();
$isLO = $authController->isLogisticsOfficer();
$isAdmin = $authController->isAdmin();
$isBank = $authController->isBank();

// Get all banks for dropdown (only needed for non-bank users)
$allBanks = [];
$selectedBankId = $modalBankId;

// For non-bank users (Admin, LO, PO), we need to show bank selection
if (!$isBank) {
    $bankModel = new BankModel();
    $allBanks = $bankModel->getBanksWithCardCount();
    
    // Reset bank selection if change_bank is posted
    if (isset($_POST['change_bank'])) {
        $selectedBankId = null;
        $modalBankName = null;
    }
    // If bank was selected from the dropdown
    elseif (isset($_POST['selected_bank_id'])) {
        $selectedBankId = intval($_POST['selected_bank_id']);
        // Get bank name
        foreach ($allBanks as $bank) {
            if ($bank['bank_id'] == $selectedBankId) {
                $modalBankName = $bank['bank_name'];
                break;
            }
        }
    }
}

// Initialize report data
$reports = [];
$reportExistsForToday = false;

// Only fetch reports if we have a bank ID
if ($selectedBankId) {
    $reportModel = new ReportModel();
    $reports = $reportModel->getReportsByBank($selectedBankId);
    
    // Check if a report exists for today
    $todayDate = date('Y-m-d');
    foreach ($reports as $report) {
        if ($report['report_date'] === $todayDate) {
            $reportExistsForToday = true;
            break;
        }
    }
}


?>

<div class="modal fade" id="bankReportsModal" tabindex="-1" aria-labelledby="bankReportsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bankReportsModalLabel">
                    Bank Reports
                    <?php if ($modalBankName): ?>
                        <small class="text-muted">for <?= htmlspecialchars($modalBankName) ?></small>
                    <?php endif; ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Include the Bank Selection Component -->                
                <?php include_once __DIR__ . '/BankSelection.php'; ?>
                
                <?php if (!empty($selectedBankId) || $isBank): ?>
                <!-- Action Buttons Section -->
                <div class="mb-3 d-flex justify-content-start gap-2">
                    <?php if ($isLO): ?>
                        <?php if ($reportExistsForToday): ?>
                            <div class="tooltip">
                                <button class="btn btn-warning disabled" title="A withdrawal report already exists for today">Withdraw Card</button>
                                <span class="tooltip-text">A withdrawal report already exists for today</span>
                            </div>
                        <?php else: ?>
                            <button type="button" id="withdrawCardBtn" class="btn btn-warning" data-bank-id="<?= $selectedBankId; ?>">Withdraw Card</button>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="tooltip">
                            <button class="btn btn-warning disabled" title="Only Logistics Officers can withdraw cards">Withdraw Card</button>
                            <span class="tooltip-text">Only Logistics Officers can withdraw cards</span>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if ($isBank && empty($selectedBankId)): ?>
                    <p class="text-center my-4">No reports available for your bank.</p>
                <?php endif; ?>
                
                <!-- Modal content sections -->
                <?php if (!empty($selectedBankId)): ?>
                <div id="modalContentSections">
                    <!-- Include the Reports List Component -->
                    <?php include_once __DIR__ . '/ReportsList.php'; ?>
                    
                    <!-- Withdrawal Section (initially hidden) -->
                    <div id="withdrawalSection" class="modal-section" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6>Withdraw Cards - <span id="withdrawalBankName"><?= htmlspecialchars($modalBankName) ?></span></h6>
                            <button type="button" id="backToReportsBtn" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back to Reports
                            </button>
                        </div>
                        <div id="withdrawalContent">
                            <!-- This will be filled with AJAX content -->
                            <div class="text-center p-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-3">Loading withdrawal interface...</p>
                            </div>
                        </div>
                    </div><!-- End of Withdrawal Section -->
                </div><!-- End of Modal Content Sections -->
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for AJAX form submission to keep modal open -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initial call to set up event listeners
        initializeModalEventListeners();
    });
    
    // Initialize event listeners for dynamically loaded content
    function initializeModalEventListeners() {
        console.log('Initializing modal event listeners');
        
        // Bank selection form handling
        const viewReportsBtn = document.getElementById('viewReportsBtn');
        if (viewReportsBtn) {
            console.log('Found viewReportsBtn, attaching listener');
            // Remove any existing event listeners first to prevent duplicates
            viewReportsBtn.replaceWith(viewReportsBtn.cloneNode(true));
            
            // Get the fresh reference after replacement
            const freshViewReportsBtn = document.getElementById('viewReportsBtn');
            freshViewReportsBtn.addEventListener('click', function() {
                console.log('View Reports button clicked');
                const bankId = document.getElementById('bankSelect').value;
                if (!bankId) {
                    alert('Please select a bank');
                    return;
                }
                
                // Create form data
                const formData = new FormData();
                formData.append('selected_bank_id', bankId);
                
                // Send AJAX request
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(html => {
                    // Replace modal content
                    const modalContent = document.querySelector('#bankReportsModal .modal-content');
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    const newModalContent = tempDiv.querySelector('#bankReportsModal .modal-content');
                    if (newModalContent) {
                        modalContent.innerHTML = newModalContent.innerHTML;
                        // Re-initialize event listeners
                        initializeModalEventListeners();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        }
        
        // Change bank button handling
        const changeBankBtn = document.getElementById('changeBankBtn');
        if (changeBankBtn) {
            console.log('Found changeBankBtn, attaching listener');
            // Remove any existing event listeners first to prevent duplicates
            changeBankBtn.replaceWith(changeBankBtn.cloneNode(true));
            
            // Get the fresh reference after replacement
            const freshChangeBankBtn = document.getElementById('changeBankBtn');
            freshChangeBankBtn.addEventListener('click', function() {
                console.log('Change Bank button clicked');
                // Create form data
                const formData = new FormData();
                formData.append('change_bank', '1');
                
                // Send AJAX request
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(html => {
                    // Replace modal content
                    const modalContent = document.querySelector('#bankReportsModal .modal-content');
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    const newModalContent = tempDiv.querySelector('#bankReportsModal .modal-content');
                    if (newModalContent) {
                        modalContent.innerHTML = newModalContent.innerHTML;
                        // Re-initialize event listeners
                        initializeModalEventListeners();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        }
        
        // Withdraw Card button handling
        const withdrawCardBtn = document.getElementById('withdrawCardBtn');
        if (withdrawCardBtn) {
            console.log('Found withdrawCardBtn, attaching listener');
            // Remove any existing event listeners first to prevent duplicates
            withdrawCardBtn.replaceWith(withdrawCardBtn.cloneNode(true));
            
            // Get the fresh reference after replacement
            const freshWithdrawCardBtn = document.getElementById('withdrawCardBtn');
            freshWithdrawCardBtn.addEventListener('click', function() {
                console.log('Withdraw Card button clicked');
                const bankId = this.getAttribute('data-bank-id');
                
                // Show withdrawal section, hide reports section
                document.getElementById('reportsSection').style.display = 'none';
                document.getElementById('withdrawalSection').style.display = 'block';
                
                // In the future, we'll load the actual withdrawal content here via AJAX
                console.log('Will load withdrawal interface for bank ID:', bankId);
                
                // For now, just show a placeholder message
                document.getElementById('withdrawalContent').innerHTML = `
                    <div class="alert alert-info">
                        <p>Withdrawal interface for bank ID ${bankId} will be loaded here.</p>
                        <p>This is a placeholder. The actual withdrawal interface will be implemented in the next step.</p>
                    </div>
                `;
            });
        }
        
        // Back to Reports button handling
        const backToReportsBtn = document.getElementById('backToReportsBtn');
        if (backToReportsBtn) {
            console.log('Found backToReportsBtn, attaching listener');
            // Remove any existing event listeners first to prevent duplicates
            backToReportsBtn.replaceWith(backToReportsBtn.cloneNode(true));
            
            // Get the fresh reference after replacement
            const freshBackToReportsBtn = document.getElementById('backToReportsBtn');
            freshBackToReportsBtn.addEventListener('click', function() {
                console.log('Back to Reports button clicked');
                
                // Show reports section, hide withdrawal section
                document.getElementById('reportsSection').style.display = 'block';
                document.getElementById('withdrawalSection').style.display = 'none';
            });
        }
    }
</script>
