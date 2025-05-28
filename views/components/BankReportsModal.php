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

<!-- Include the modal styles -->
<link rel="stylesheet" href="/css/modal-styles.css">

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
                <div class="action-buttons-section mb-3 d-flex justify-content-start gap-2">
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
                    
                    <!-- Include the Withdrawal Section Component -->
                    <?php include_once __DIR__ . '/WithdrawalSection.php'; ?>
                </div><!-- End of Modal Content Sections -->
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Include the external JavaScript file for the modal functionality -->
<script src="/js/bank-reports-modal.js"></script>
