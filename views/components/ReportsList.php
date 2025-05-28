<?php
// ReportsList.php
// This component displays a list of reports in a table format.

// Expected variables from parent scope:
// $selectedBankId - The ID of the selected bank
// $reports - Array of reports to display
// $isPO - Whether the current user is a Production Officer
// $isBank - Whether the current user is a Bank user

// If this component is included without the required variables, we'll show an error message
if (!isset($selectedBankId)) {
    echo '<div class="alert alert-danger">Error: $selectedBankId variable is required for ReportsList component</div>';
    return;
}

if (!isset($isPO)) {
    echo '<div class="alert alert-danger">Error: $isPO variable is required for ReportsList component</div>';
    return;
}

if (!isset($isBank)) {
    echo '<div class="alert alert-danger">Error: $isBank variable is required for ReportsList component</div>';
    return;
}

if (!isset($reports)) {
    echo '<div class="alert alert-danger">Error: $reports variable is required for ReportsList component</div>';
    return;
}
?>

<!-- Reports Section -->
<div id="reportsSection" class="modal-section">
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
</div><!-- End of Reports Section -->
