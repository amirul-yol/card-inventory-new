<?php
// BankReportsModal.php
// This component displays bank reports in a Bootstrap modal.
// Mock data is used for now.

// Expected to be included in a page where $isPO (is Processing Officer) 
// and $reportExistsForToday variables might be available for button logic.
// For mock purposes, we'll assume some defaults or omit complex logic.
$isPO = false; // Mock: Assume not a Processing Officer for button display
$isLO = true; // Mock: Assume is a Logistics Officer for withdraw button
$reportExistsForToday = false; // Mock

$mockReports = [
    [
        'id' => 1,
        'report_date' => '23 May 2025',
        'status' => 'Approved',
        'details' => 'Monthly card stock reconciliation.',
    ],
    [
        'id' => 2,
        'report_date' => '22 May 2025',
        'status' => 'Pending',
        'details' => 'Daily withdrawal summary.',
    ],
    [
        'id' => 3,
        'report_date' => '20 May 2025',
        'status' => 'Generated',
        'details' => 'Card activation report.',
    ],
    [
        'id' => 4,
        'report_date' => '20 May 2025',
        'status' => 'Generated',
        'details' => 'Card activation report.',
    ],
    [
        'id' => 5,
        'report_date' => '20 May 2025',
        'status' => 'Generated',
        'details' => 'Card activation report.',
    ],
    [
        'id' => 6,
        'report_date' => '20 May 2025',
        'status' => 'Generated',
        'details' => 'Card activation report.',
    ],
    [
        'id' => 7,
        'report_date' => '20 May 2025',
        'status' => 'Generated',
        'details' => 'Card activation report.',
    ],
    [
        'id' => 8,
        'report_date' => '20 May 2025',
        'status' => 'Generated',
        'details' => 'Card activation report.',
    ],
    [
        'id' => 9,
        'report_date' => '20 May 2025',
        'status' => 'Generated',
        'details' => 'Card activation report.',
    ],
];

?>

<div class="modal fade" id="bankReportsModal" tabindex="-1" aria-labelledby="bankReportsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bankReportsModalLabel">Bank Reports</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Action Buttons Section -->
                <div class="mb-3 d-flex justify-content-start gap-2">
                    <?php if ($isPO): ?>
                        <a href="index.php?path=report/create" class="btn btn-primary">Create New Report</a>
                    <?php else: ?>
                        <button class="btn btn-primary" disabled title="Only Processing Officers can create reports">Create New Report</button>
                    <?php endif; ?>

                    <?php if ($isLO): // Assuming LO can withdraw, and PO might have different view ?>
                        <?php if ($reportExistsForToday): ?>
                            <button class="btn btn-warning disabled" title="A withdrawal report already exists for today">Withdraw Card (Exists)</button>
                        <?php else: ?>
                            <a href="index.php?path=report/withdraw" class="btn btn-warning">Withdraw Card</a>
                        <?php endif; ?>
                    <?php else: ?>
                         <button class="btn btn-warning disabled" title="Only Logistics Officers can withdraw cards">Withdraw Card</button>
                    <?php endif; ?>
                </div>

                <!-- Existing Reports Table -->
                <h6>Existing Reports</h6>
                <?php if (!empty($mockReports)): ?>
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
                                <?php foreach ($mockReports as $report): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($report['report_date']); ?></td>
                                        <td><?= htmlspecialchars($report['status']); ?></td>
                                        <td><?= htmlspecialchars($report['details']); ?></td>
                                        <td class="text-center">
                                            <a href="#" class="btn btn-sm btn-outline-primary me-1" title="View Report Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-outline-secondary" title="Download Report">
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
