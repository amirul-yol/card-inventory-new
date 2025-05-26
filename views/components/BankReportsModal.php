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
                <?php if (!$isBank): ?>
                <!-- Bank Selection for Admin, LO, PO users -->
                <div class="bank-selection mb-4">
                    <?php if (empty($selectedBankId)): ?>
                    <h6>Select a Bank</h6>
                    <form method="post" id="bankSelectionForm" class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label for="bankSelect" class="form-label">Choose a bank to view reports</label>
                            <select class="form-select" id="bankSelect" name="selected_bank_id" required>
                                <option value="">-- Select Bank --</option>
                                <?php foreach ($allBanks as $bank): ?>
                                    <option value="<?= $bank['bank_id'] ?>">
                                        <?= htmlspecialchars($bank['bank_name']) ?> (<?= $bank['card_count'] ?> cards)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">View Reports</button>
                        </div>
                    </form>
                    <?php else: ?>
                    <!-- Bank Change Option -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Currently viewing: <span class="fw-bold"><?= htmlspecialchars($modalBankName) ?></span></h6>
                        <form method="post" id="changeBankForm" class="d-inline">
                            <input type="hidden" name="change_bank" value="1">
                            <button type="submit" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-exchange-alt me-1"></i> Change Bank
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <!-- Action Buttons Section -->
                <div class="mb-3 d-flex justify-content-start gap-2">
                    <?php if ($isPO): ?>
                        <a href="index.php?path=report/create" class="btn btn-primary">Create New Report</a>
                    <?php else: ?>
                        <button class="btn btn-primary" disabled title="Only Processing Officers can create reports">Create New Report</button>
                    <?php endif; ?>

                    <?php if ($isLO): ?>
                        <?php if ($reportExistsForToday): ?>
                            <div class="tooltip">
                                <button class="btn btn-warning disabled" title="A withdrawal report already exists for today">Withdraw Card</button>
                                <span class="tooltip-text">A withdrawal report already exists for today</span>
                            </div>
                        <?php else: ?>
                            <a href="index.php?path=report/withdrawCard&bank_id=<?= $selectedBankId; ?>" class="btn btn-warning">Withdraw Card</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="tooltip">
                            <button class="btn btn-warning disabled" title="Only Logistics Officers can withdraw cards">Withdraw Card</button>
                            <span class="tooltip-text">Only Logistics Officers can withdraw cards</span>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($selectedBankId)): ?>
                <!-- Existing Reports Table -->
                <h6>Existing Reports</h6>
                <?php if (!empty($reports)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Details</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reports as $report): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($report['report_date'] ?? 'N/A'); ?></td>
                                        <td><?= htmlspecialchars($report['status'] ?? 'N/A'); ?></td>
                                        <td><?= htmlspecialchars($report['details'] ?? 'No Details Available'); ?></td>
                                        <td class="text-center">
                                            <?php if ($isPO): ?>
                                                <a href="index.php?path=report/verify&report_id=<?= $report['id']; ?>&bank_id=<?= $selectedBankId; ?>" 
                                                   class="btn btn-sm btn-outline-primary me-1" 
                                                   title="<?= $report['status'] === 'Verified' ? 'View Report' : 'Verify Report' ?>">
                                                    <?= $report['status'] === 'Verified' ? 'View' : 'Verify' ?>
                                                </a>
                                            <?php else: ?>
                                                <a href="index.php?path=report/verify&report_id=<?= $report['id']; ?>&bank_id=<?= $selectedBankId; ?>" 
                                                   class="btn btn-sm btn-outline-primary me-1" 
                                                   title="View Report Details">
                                                    View
                                                </a>
                                            <?php endif; ?>
                                            <a href="index.php?path=report/download&report_id=<?= $report['id']; ?>" 
                                               class="btn btn-sm btn-outline-secondary" 
                                               title="Generate Report">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>No reports found.</p>
                <?php endif; ?>
                <?php else: ?>
                    <?php if ($isBank): ?>
                        <p>No reports available.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
