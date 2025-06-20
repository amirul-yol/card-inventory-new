<?php
require_once 'models/ReportModel.php';
require_once 'models/TransactionModel.php';
require_once 'controllers/AuthController.php';


class ReportController {
    private $authController;
    private $reportModel;
    
    public function __construct() {
        $this->authController = new AuthController();
        $this->reportModel = new ReportModel();
    }
    
    public function index() {
        $model = new ReportModel();
        
        // Filter banks for Bank users
        if ($this->authController->isBank() && isset($_SESSION['bank_id'])) {
            $bankId = $_SESSION['bank_id'];
            $allBanks = $model->getBanks();
            $banks = array_filter($allBanks, function($bank) use ($bankId) {
                return $bank['bank_id'] == $bankId;
            });
        } else {
            // For Admin, PO, LO users, show all banks
            $banks = $model->getBanks();
        }
        
        include 'views/report/index.php';
    }

    public function withdraw() {
        $model = new ReportModel();
        
        // Filter banks for Bank users
        if ($this->authController->isBank() && isset($_SESSION['bank_id'])) {
            $bankId = $_SESSION['bank_id'];
            $allBanks = $model->getBanks();
            $banks = array_filter($allBanks, function($bank) use ($bankId) {
                return $bank['bank_id'] == $bankId;
            });
        } else {
            // For Admin, PO, LO users, show all banks
            $banks = $model->getBanks();
        }
        
        include 'views/report/withdraw.php';
    }


    public function bankReports() {
    
        // Validate and sanitize `id`
        $bankId = isset($_GET['bank_id']) ? (int) $_GET['bank_id'] : null;
    
        if (!$bankId) {
            die("Bank ID is missing or invalid.");
        }
        
        // Check if the user can access this bank
        if (!$this->authController->canAccessBank($bankId)) {
            die("You do not have permission to view reports for this bank.");
        }
    
        $reportModel = new ReportModel();
        $bank = $reportModel->getBankById($bankId);
    
        if (!$bank) {
            die("Bank not found.");
        }
    
        $reports = $reportModel->getReportsByBank($bankId);
    
        include 'views/report/bank_reports.php';
    }
    
    public function withdrawCard() {
        $cardId = $_GET['card_id'] ?? null;
        $bankId = $_GET['bank_id'] ?? null;
    
        if (!$bankId) {
            die('Bank ID is missing.');
        }
        
        // Check if the user can access this bank
        if (!$this->authController->canAccessBank($bankId)) {
            die("You do not have permission to withdraw cards for this bank.");
        }
    
        $reportModel = new ReportModel();
        $bank = $reportModel->getBankById($bankId);
        $cards = $reportModel->getCardsByBankId($bankId);
    
        // Check withdrawals for each card for the current date
        $currentDate = date('Y-m-d');
        foreach ($cards as &$card) {
            $card['withdrawal'] = $reportModel->getCardWithdrawalDetails($cardId, $currentDate);
        }
    
        require_once 'views/report/withdraw.php';
    }
    
    public function editWithdrawal() {
        $withdrawalId = $_GET['withdrawal_id'] ?? null;
        if (!$withdrawalId) {
            die("Withdrawal ID is missing.");
        }
        
        $reportModel = new ReportModel();
        $withdrawal = $reportModel->getWithdrawalById($withdrawalId);
        
        if (!$withdrawal) {
            die("Withdrawal not found.");
        }
        
        // Check if the user can access this bank
        if (!$this->authController->canAccessBank($withdrawal['bank_id'])) {
            die("You do not have permission to edit withdrawals for this bank.");
        }
    
        include 'views/report/edit_withdrawal_form.php';
    }

    public function processWithdraw() {
        // Get POST data
        $cardId = $_POST['card_id'] ?? null;
        $bankId = $_POST['bank_id'] ?? null;
        $quantity = $_POST['quantity'] ?? null;
        $date = $_POST['date'] ?? null;
        $remarks = $_POST['remarks'] ?? null;
    
        // Validate inputs
        if (!$cardId || !$bankId || !$quantity || !$date || !$remarks) {
            die("All fields are required.");
        }
        
        // Check if the user can access this bank
        if (!$this->authController->canAccessBank($bankId)) {
            die("You do not have permission to withdraw cards for this bank.");
        }
    
        // Prepare transaction details
        $transactionType = 'withdraw'; // Fixed for this operation
        $rejectType = null; // No reject type for withdrawal
        $verified = null; // Verified is null for now
    
        $reportModel = new ReportModel();
    
        if ($reportModel->withdrawCard($cardId, $bankId, $quantity, $remarks, $date)) {
            // Redirect to the withdraw page
            header("Location: ?path=report/withdrawCard&bank_id=$bankId&status=success");
            exit;
        } else {
            die("Failed to process withdrawal.");
        }
    }
    
    public function withdrawCardForm() {
        $cardId = $_GET['card_id'] ?? null;
        $bankId = $_GET['bank_id'] ?? null;
    
        if (!$cardId || !$bankId) {
            die("Missing parameters.");
        }
        
        // Check if the user can access this bank
        if (!$this->authController->canAccessBank($bankId)) {
            die("You do not have permission to withdraw cards for this bank.");
        }
    
        $reportModel = new ReportModel();
        $card = $reportModel->getCardById($cardId);
        
        if (!$card) {
            die("Card not found.");
        }
    
        include 'views/report/withdraw_card_form.php'; // Form for withdrawal
    }
    
    public function editWithdrawalForm() {
        $cardId = $_GET['card_id'] ?? null;
        $bankId = $_GET['bank_id'] ?? null;
    
        if (!$cardId || !$bankId) {
            die("Missing card_id or bank_id.");
        }
        
        // Check if the user can access this bank
        if (!$this->authController->canAccessBank($bankId)) {
            die("You do not have permission to edit withdrawals for this bank.");
        }
    
        $currentDate = date('Y-m-d');
        $reportModel = new ReportModel();
    
        $withdrawal = $reportModel->getCardWithdrawalDetails($cardId, $currentDate);
        if (!$withdrawal) {
            die("Invalid withdrawal data.");
        }
    
        require_once 'views/report/edit_withdrawal_form.php';
    }
    
    public function processWithdrawEdit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $withdrawalId = $_POST['withdrawal_id'] ?? null;
            $bankId = $_POST['bank_id'] ?? null;
            $newQuantity = $_POST['quantity'] ?? null;
            $date = $_POST['date'] ?? null;
    
            if (!$withdrawalId || !$bankId || !$newQuantity || !$date) {
                die("Missing required fields.");
            }
    
            // Load the ReportModel
            $reportModel = new ReportModel();
    
            // Get the existing withdrawal details
            $existingWithdrawal = $reportModel->getWithdrawalById($withdrawalId);
    
            if (!$existingWithdrawal) {
                die("Withdrawal record not found.");
            }
    
            // Calculate the quantity difference
            $oldQuantity = $existingWithdrawal['quantity'];
            $quantityDifference = $newQuantity - $oldQuantity;
    
            // Update the withdrawal record
            $updateSuccess = $reportModel->updateWithdrawal($withdrawalId, $newQuantity, $date);
    
            if (!$updateSuccess) {
                die("Failed to update the withdrawal record.");
            }
    
            // Update the card's quantity
            $cardId = $existingWithdrawal['card_id']; // Assuming `card_id` exists in the withdrawal record
            $updateCardSuccess = $reportModel->updateCardQuantity($cardId, -$quantityDifference); // Subtract difference
    
            if (!$updateCardSuccess) {
                die("Failed to update the card's quantity.");
            }
    
            // Redirect back with success message
            header("Location: index.php?path=report/withdrawCard&bank_id=$bankId");
            exit;
        } else {
            die("Invalid request method.");
        }
    }
    
    public function cancelWithdrawal() {
        // Get withdrawal ID and bank ID from URL parameters
        $withdrawalId = isset($_GET['withdrawal_id']) ? intval($_GET['withdrawal_id']) : null;
        $bankId = isset($_GET['bank_id']) ? intval($_GET['bank_id']) : null;
        
        if (!$withdrawalId || !$bankId) {
            die("Missing withdrawal ID or bank ID");
        }
        
        // Initialize the ReportModel
        $reportModel = new ReportModel();
        
        // Get the withdrawal details to get the quantity and card_id
        $withdrawal = $reportModel->getWithdrawalById($withdrawalId);
        
        if (!$withdrawal) {
            die("Withdrawal not found");
        }
        
        // Get the card ID and quantity from the withdrawal
        $cardId = $withdrawal['card_id'];
        $quantity = $withdrawal['quantity'];
        
        // First, update the card quantity to add back the withdrawn amount
        $updateCardSuccess = $reportModel->updateCardQuantity($cardId, $quantity);
        
        if (!$updateCardSuccess) {
            die("Failed to update card quantity");
        }
        
        // Then, delete the withdrawal transaction
        $deleteSuccess = $reportModel->deleteWithdrawal($withdrawalId);
        
        if (!$deleteSuccess) {
            die("Failed to delete withdrawal transaction");
        }
        
        // Redirect back to the withdraw page with success message
        header("Location: index.php?path=report/withdrawCard&bank_id=$bankId&status=cancelled");
        exit;
    }
    
    public function submitWithdrawReport() {
        if (isset($_GET['bank_id'])) {
            $bankId = intval($_GET['bank_id']);
            $date = date('Y-m-d');
    
            $reportModel = new ReportModel();
            $transactionModel = new TransactionModel();
    
            // Generate a new report for the bank
            $reportId = $reportModel->createReport($bankId, $date);
    
            if ($reportId) {
                // Update all unreported transactions with the new report_id
                $updateSuccess = $transactionModel->assignReportToTransactions($bankId, $date, $reportId);
    
                if ($updateSuccess) {
                    // Redirect to bank_reports.php with a success message
                    header("Location: index.php?path=report/bankReports&bank_id=$bankId&success=withdraw_completed");
                    exit;
                } else {
                    // Redirect with an error message if transactions update failed
                    header("Location: index.php?path=report/bankReports&bank_id=$bankId&error=transaction_update_failed");
                    exit;
                }
            } else {
                // Redirect with an error message if report generation failed
                header("Location: index.php?path=report/bankReports&bank_id=$bankId&error=report_creation_failed");
                exit;
            }
        } else {
            // Redirect with an error message if bank_id is missing
            header("Location: index.php?path=report/bankReports&error=missing_bank_id");
            exit;
        }
    }

    

    public function submitVerification() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die("Invalid request method.");
        }
    
        $reportId = intval($_POST['report_id']);
    
        $reportModel = new ReportModel();
        $transactionModel = new TransactionModel();
    
        // Fetch the report to check its status
        $report = $reportModel->getReportById($reportId);
        if (!$report) {
            die("Report not found.");
        }
    
        if ($report['status'] === 'verified') {
            die("This report has already been verified.");
        }
    
        // Fetch unverified transactions for the report
        $transactions = $transactionModel->getUnverifiedTransactions($reportId);
        if (empty($transactions)) {
            // If no unverified transactions, mark the report as verified
            $reportModel->markReportVerified($reportId);
            echo "Verification completed successfully. Report marked as verified.";
            return;
        }
    
        // Verify all transactions associated with the report
        foreach ($transactions as $transaction) {
            $transactionModel->markTransactionVerified($transaction['id']);
        }
    
        // Mark the report as verified
        $reportModel->markReportVerified($reportId);
    
        echo "Verification completed successfully.";
    }
    
    
    public function finishWithdraw() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Retrieve bank_id from the POST request
            $bankId = intval($_POST['bank_id']);
            $date = date('Y-m-d');
    
            $reportModel = new ReportModel();
            $transactionModel = new TransactionModel();
    
            // Generate a new report for the bank
            $reportId = $reportModel->createReport($bankId, $date);
    
            if ($reportId) {
                // Update all unreported transactions with the new report_id
                $updateSuccess = $transactionModel->assignReportToTransactions($bankId, $date, $reportId);
    
                if ($updateSuccess) {
                    // Redirect to bank_reports.php with success message
                    header("Location: bank_reports.php?bank_id=$bankId&success=withdraw_completed");
                    exit;
                } else {
                    echo "Failed to update transactions with the new report.";
                }
            } else {
                echo "Failed to generate a new report.";
            }
        }
    }
    
    public function verify()
    {
        $reportModel = new ReportModel();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_report'])) {
            $bankId = intval($_POST['bank_id']);
            $reportId = intval($_POST['report_id']);
            $rejectedAmounts = $_POST['rejected_amount'] ?? [];

            // Process rejected amounts if provided
            foreach ($rejectedAmounts as $transactionId => $rejectedAmount) {
                $reason = $_POST['rejection_reason'][$transactionId] ?? 'Unknown';
                $reportModel->addRejection($transactionId, intval($rejectedAmount), $reason);
            }

            // Mark the report as verified
            $reportModel->markReportVerified($reportId);

            // Redirect back to the report page with a success message
            header("Location: index.php?path=report/bankReports&bank_id=$bankId&status=verified");
            exit();
        }

        // Fetch transactions and load the verify view
        $bankId = intval($_GET['bank_id']);
        $reportId = intval($_GET['report_id']);
        $transactions = $reportModel->getTransactionsForReport($reportId);

        include 'views/report/verify.php';
    }

    public function verifyWithdrawReport()
    {
        $reportModel = new ReportModel();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_report'])) {
            $bankId = intval($_POST['bank_id']);
            $reportId = intval($_POST['report_id']);
            $rejectedAmounts = $_POST['rejected_amount'] ?? [];

            // Process rejected amounts if provided
            foreach ($rejectedAmounts as $transactionId => $rejectedAmount) {
                $reason = $_POST['rejection_reason'][$transactionId] ?? 'Unknown';
                $reportModel->addRejection($transactionId, intval($rejectedAmount), $reason);
            }

            // Mark the report as verified
            $reportModel->markReportVerified($reportId);

            // Redirect back to the bank reports page with a success message
            header("Location: index.php?path=report/bankReports&bank_id=$bankId&status=verified");
            exit();
        }

        // Fetch transactions and load the verify view
        $bankId = intval($_GET['bank_id']);
        $reportId = intval($_GET['report_id']);
        $transactions = $reportModel->getTransactionsForReport($reportId);

        include 'views/report/verify.php';
    }
    
    
    public function rejectCard()
    {
        $transactionId = isset($_POST['transaction_id']) ? intval($_POST['transaction_id']) : (isset($_GET['transaction_id']) ? intval($_GET['transaction_id']) : null);

        if (!$transactionId) {
            die("Transaction ID is required.");
        }

        $reportModel = new ReportModel();
        $transaction = $reportModel->getTransactionById($transactionId);

        if (!$transaction) {
            die("Transaction not found.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $transactionId = intval($_POST['transaction_id']);
            $rejectedAmount = intval($_POST['rejected_amount']);
            $reason = $_POST['reason'];
            $bankId = intval($_POST['bank_id']);
            $reportId = intval($_POST['report_id']);

            $reportModel = new ReportModel();
            $transaction = $reportModel->getTransactionById($transactionId);

            if (!$transaction) {
                die("Transaction not found.");
            }

            if ($rejectedAmount <= 0 || $rejectedAmount > $transaction['quantity']) {
                die("Invalid rejected amount.");
            }
            if (!in_array($reason, ['System Error', 'Quality Error'])) {
                die("Invalid rejection reason.");
            }

            // Record rejection
            $reportModel->addRejection($transactionId, $rejectedAmount, $reason);

            // Adjust card inventory only
            $cardId = $transaction['card_id'];
            $reportModel->updateCardQuantity($cardId, -$rejectedAmount); 

            // Redirect to verify page
            header("Location: index.php?path=report/verifyWithdrawReport&bank_id=$bankId&report_id=$reportId");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $bankId = intval($_GET['bank_id']);
            $reportId = intval($_GET['report_id']);

            // Redirect to the reject_card view
            include 'views/report/reject_card.php';
            exit();
        }

        include 'views/report/reject_card.php';
    }

    public function downloadWithdrawalReport()
    {
        if (!isset($_GET['report_id'])) {
            // Or handle error more gracefully, e.g., redirect with message
            die('Report ID is required.');
        }
        $reportId = intval($_GET['report_id']);

        $report = $this->reportModel->getReportById($reportId);
        if (!$report) {
            die('Report not found.');
        }

        $bank = $this->reportModel->getBankById($report['bank_id']);
        if (!$bank) {
            die('Bank not found for this report.');
        }

        // Format report_date (YYYY-MM-DD) to ddmmyyyy for the filename
        $reportDateFormatted = DateTime::createFromFormat('Y-m-d', $report['report_date'])->format('dmY');
        $bankName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $bank['name']); // Sanitize bank name for filename
        $filename = "Withdrawal_Report_for_" . $bankName . "_" . $reportDateFormatted . ".csv";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // CSV Headers
        $headers = ['Card Name', 'Withdraw Quantity', 'Reject Quantity', 'Card Balance', 'Report Date', 'Report Status'];
        fputcsv($output, $headers);

        // Fetch transactions including rejected quantities
        $transactions = $this->reportModel->getTransactionsForReport($reportId);

        // Report Date and Status for all rows in this CSV
        $csvReportDate = $report['report_date']; // Use original YYYY-MM-DD or format as needed for display
        $csvReportStatus = $report['status'];

        if (empty($transactions)) {
            // Optional: Write a row indicating no transactions if that's preferred over an empty data section
            // fputcsv($output, ['No transactions found for this report.', '', '', '', $csvReportDate, $csvReportStatus]);
        } else {
            foreach ($transactions as $transaction) {
                $row = [
                    $transaction['card_name'],
                    $transaction['transaction_quantity'],
                    $transaction['rejected_quantity'], // This now comes from the modified model method
                    $transaction['card_balance'],
                    $csvReportDate,
                    $csvReportStatus
                ];
                fputcsv($output, $row);
            }
        }

        fclose($output);
        exit;
    }

    public function viewTransactions() {
    $transactions = $this->transactionModel->getTransactionsByCardId($cardId);

    foreach ($transactions as &$transaction) {
        $transaction['rejection_details'] = $this->reportModel->getRejectedDetails($transaction['id']);
    }

    require 'views/report/verify.php';
}

}
?>
