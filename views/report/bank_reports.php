<?php
require_once 'models/ReportModel.php';
require_once 'controllers/AuthController.php';

if (!isset($_GET['bank_id']) || !is_numeric($_GET['bank_id'])) {
    die("Bank ID is missing or invalid.");
}

$bankId = intval($_GET['bank_id']);
$reportModel = new ReportModel();

// Create Auth Controller instance to check user roles
$authController = new AuthController();
$isLO = $authController->isLogisticsOfficer();
$isPO = $authController->isProductionOfficer();

// Fetch bank details
$bank = $reportModel->getBankById($bankId);
if (!$bank) {
    die("Bank not found.");
}

// Fetch reports for the bank
$reports = $reportModel->getReportsByBank($bankId);

// Check if a report exists for today's date
$todayDate = date('Y-m-d');
$reportExistsForToday = false;

foreach ($reports as $report) {
    if ($report['report_date'] === $todayDate) {
        $reportExistsForToday = true;
        break;
    }
}
?>
<?php include 'views/includes/header.php'; ?>
<?php include 'views/includes/sidebar.php'; ?>

<style>
    .withdraw-btn.disabled, .btn-action.disabled {
        opacity: 0.6;
        cursor: not-allowed;
        pointer-events: none;
    }
    
    .tooltip {
        position: relative;
        display: inline-block;
    }
    
    .tooltip .tooltip-text {
        visibility: hidden;
        width: 220px;
        background-color: #555;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        margin-left: -110px;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .tooltip .tooltip-text::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #555 transparent transparent transparent;
    }
    
    .tooltip:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }
</style>

<div class="content">
    <h1>Bank Reports</h1>
    <h2>Reports for <?= htmlspecialchars($bank['name']); ?></h2>

    <!-- Withdraw Button -->
    <div class="action-buttons">
        <?php if ($isLO && !$reportExistsForToday): ?>
            <a href="index.php?path=report/withdrawCard&bank_id=<?= $bank['bank_id']; ?>" class="btn withdraw-btn">Withdraw Card</a>
        <?php elseif ($isLO && $reportExistsForToday): ?>
            A withdrawal report already exists for today
        <?php endif; ?>
    </div>

    <!-- Filters -->
    <br />
    <h3 style="display: inline-block;">Existing Reports</h3>
    <div style="float: right;">
        <label for="month_filter">Filter by Month:</label>
        <select name="month" id="month_filter">
            <option value="">All Months</option>
            <?php
            for ($m = 1; $m <= 12; $m++) {
                $monthName = date("F", mktime(0, 0, 0, $m, 1));
                echo "<option value=\"$m\">$monthName</option>";
            }
            ?>
        </select>

        <label for="rejection_filter">Rejected Cards:</label>
        <select name="rejection" id="rejection_filter">
            <option value="">All Reports</option>
            <option value="yes">With Rejections</option>
            <option value="no">Without Rejections</option>
        </select>

        <button id="applyFilter" class="btn btn-filter">Apply</button>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="reportTableBody">
            <?php foreach ($reports as $report): ?>
                <?php
                // Check if the report has rejections
                $hasRejections = !empty($rejections[$report['id']]);
                ?>
                <tr 
                    data-month="<?= date('n', strtotime($report['report_date'])); ?>" 
                    data-rejection="<?= $hasRejections ? 'yes' : 'no'; ?>">
                    <td><?= htmlspecialchars($report['report_date'] ?? 'N/A'); ?></td>
                    <td><?= htmlspecialchars($report['status'] ?? 'N/A'); ?></td>
                    <td>
                        <?php if ($isPO): ?>
                            <a href="index.php?path=report/verify&report_id=<?= $report['id']; ?>&bank_id=<?= $bank['bank_id']; ?>" class="btn btn-action">
                                <?= $report['status'] === 'Verified' ? 'View' : 'Verify' ?>
                            </a>
                        <?php else: ?>
                            <a href="index.php?path=report/verify&report_id=<?= $report['id']; ?>&bank_id=<?= $bank['bank_id']; ?>" class="btn btn-action">
                                View
                            </a>
                        <?php endif; ?>
                        <a href="index.php?path=report/download&report_id=<?= $report['id']; ?>" class="btn btn-action">Generate Report</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>


    </table>

    <script>
        document.getElementById('applyFilter').addEventListener('click', function () {
            const selectedMonth = document.getElementById('month_filter').value;
            const selectedRejection = document.getElementById('rejection_filter').value;
            const rows = document.querySelectorAll('#reportTableBody tr');

            rows.forEach(row => {
                const rowMonth = row.getAttribute('data-month');
                const rowRejection = row.getAttribute('data-rejection');
                const showByMonth = selectedMonth === "" || rowMonth === selectedMonth;
                const showByRejection = selectedRejection === "" || rowRejection === selectedRejection;

                row.style.display = showByMonth && showByRejection ? "" : "none"; // Show or hide the row
            });
        });

    </script>
</div>

<?php include 'views/includes/footer.php'; ?>
