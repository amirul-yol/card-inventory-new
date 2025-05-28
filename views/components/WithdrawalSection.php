<?php
// WithdrawalSection.php
// This component displays the card withdrawal interface in a modal section.

// Expected variables from parent scope:
// $modalBankName - The name of the bank
// $selectedBankId - The ID of the selected bank

// If this component is included without the required variables, we'll show an error message
if (!isset($modalBankName)) {
    echo '<div class="alert alert-danger">Error: $modalBankName variable is required for WithdrawalSection component</div>';
    return;
}

if (!isset($selectedBankId)) {
    echo '<div class="alert alert-danger">Error: $selectedBankId variable is required for WithdrawalSection component</div>';
    return;
}
?>

<!-- Withdrawal Section (initially hidden) -->
<div id="withdrawalSection" class="modal-section" style="display: none;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6>Withdraw Cards - <span id="withdrawalBankName"><?= htmlspecialchars($modalBankName) ?></span></h6>
        <button type="button" id="backToReportsBtn" class="btn btn-outline-secondary btn-sm" data-action="back-to-reports">
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
