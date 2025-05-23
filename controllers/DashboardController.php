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
                    'totalDebitCards' => $this->model->getTotalDebitCardsByBank($bankId),
                    'totalCreditCards' => $this->model->getTotalCreditCardsByBank($bankId),
                ];
            } else {
                // For Admin, PO, LO users - show system-wide totals
                $data = [
                    'totalReports' => $this->model->getTotalReports(),
                    'totalCards' => $this->model->getTotalCards(),
                    'totalBanks' => $this->model->getTotalBanks(),
                    'totalUsers' => $this->model->getTotalUsers(),
                ];
            }

            include 'views/dashboard/index.php';
        } catch (Exception $e) {
            // Log the actual error for debugging (you should implement proper logging)
            error_log("Dashboard Error: " . $e->getMessage());
            // Show generic error to user
            echo "Unable to load dashboard data. Please try again later.";
        }
    }

    public function displayNewDashboard() {
        try {
            $isBank = $this->authController->isBank();
            $bankId = $isBank && isset($_SESSION['bank_id']) ? $_SESSION['bank_id'] : null;

            // Fetch real data based on user role
            if ($isBank && $bankId) {
                $data = [
                    'totalReports' => $this->model->getTotalReportsByBank($bankId),
                    'totalCards' => $this->model->getTotalCardsByBank($bankId),
                    'totalDebitCards' => $this->model->getTotalDebitCardsByBank($bankId),
                    'totalCreditCards' => $this->model->getTotalCreditCardsByBank($bankId),
                ];
            } else {
                // For Admin or other non-bank users, show system-wide totals
                $data = [
                    'totalReports' => $this->model->getTotalReports(),
                    'totalCards' => $this->model->getTotalCards(),
                    'totalBanks' => $this->model->getTotalBanks(),
                    'totalUsers' => $this->model->getTotalUsers(),
                ];
            }

            // Fetch banks for the carousel, typically for Admin/non-bank users
            $banksForCarousel = [];
            if (!$isBank) {
                require_once 'models/BankModel.php'; // Ensure BankModel is available
                $bankModel = new BankModel();
                $banksForCarousel = $bankModel->getBanksWithCardCount();
            }

            // Make $isBank, $bankId available to the view as well, if needed directly in the view
            // (though $data already contains role-specific info for cards)
            // For now, dashboardNew.php reconstructs its $infoCards based on $data and these roles.

            // Pass all necessary data to the view
            include 'views/dashboard/dashboardNew.php'; 
        } catch (Exception $e) {
            error_log("New Dashboard Error: " . $e->getMessage());
            echo "Unable to load new dashboard data. Please try again later.";
        }
    }
}
?>
