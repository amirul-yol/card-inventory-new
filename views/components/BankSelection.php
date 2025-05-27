<?php
// BankSelection.php
// This component displays a bank selection dropdown or the currently selected bank with a change option.

// Expected variables from parent scope:
// $isBank - Whether the current user is a Bank user
// $selectedBankId - The ID of the selected bank (may be null)
// $modalBankName - The name of the selected bank (may be null)
// $allBanks - Array of all banks with their details including card count

// If this component is included without the required variables, we'll show an error message
if (!isset($isBank)) {
    echo '<div class="alert alert-danger">Error: $isBank variable is required for BankSelection component</div>';
    return;
}

// Only non-bank users (Admin, LO, PO) should see the bank selection
if (!$isBank): ?>
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
                <button type="button" id="viewReportsBtn" class="btn btn-primary w-100">View Reports</button>
            </div>
        </form>
        <?php else: ?>
        <!-- Bank Change Option -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Currently viewing: <span class="fw-bold"><?= htmlspecialchars($modalBankName) ?></span></h6>
            <button type="button" id="changeBankBtn" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-exchange-alt me-1"></i> Change Bank
            </button>
        </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
