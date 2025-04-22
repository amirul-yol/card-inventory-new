<?php
require_once 'db/database.php';
include_once __DIR__ . '/../models/DashboardModel.php';
require_once 'controllers/AuthController.php';

class DashboardController {
    private $model;
    private $authController;

    public function __construct() {
        $db = Database::getInstance()->getConnection();
        $this->model = new DashboardModel($db);
        $this->authController = new AuthController();
    }

    public function index() {
        try {
            $isBank = $this->authController->isBank();
            
            // Different data retrieval strategy based on user role
            if ($isBank && isset($_SESSION['bank_id'])) {
                $bankId = $_SESSION['bank_id'];
                $data = [
                    'totalReports' => $this->model->getTotalReportsByBank($bankId),
                    'totalCards' => $this->model->getTotalCardsByBank($bankId),
                ];
                
                // Load reports for this bank
                require_once 'models/ReportModel.php';
                $reportModel = new ReportModel();
                $recentReports = $reportModel->getRecentReportsByBank($bankId, 12);
            } else {
                // For Admin, PO, LO users - show system-wide totals
                $data = [
                    'totalReports' => $this->model->getTotalReports(),
                    'totalCards' => $this->model->getTotalCards(),
                    'totalBanks' => $this->model->getTotalBanks(),
                    'totalUsers' => $this->model->getTotalUsers(),
                ];
                
                // Load reports for all banks
                require_once 'models/ReportModel.php';
                $reportModel = new ReportModel();
                $recentReports = $reportModel->getRecentReports(12);
            }
            
            // Get all banks with their logos for the reports display
            require_once 'controllers/BankController.php';
            $bankController = new BankController();
            $allBanks = $bankController->getAllBanks();
            $bankData = [];
            foreach ($allBanks as $bank) {
                $bankData[$bank['bank_id']] = $bank;
            }

            include 'views/dashboard/index.php';
        } catch (Exception $e) {
            // Log the actual error for debugging (you should implement proper logging)
            error_log("Dashboard Error: " . $e->getMessage());
            // Show generic error to user
            echo "Unable to load dashboard data. Please try again later.";
        }
    }
}
?>
