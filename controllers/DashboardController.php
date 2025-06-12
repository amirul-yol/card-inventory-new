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
                    'cardTypes' => $this->model->getCardCountsByTypeForBank($bankId), // New dynamic card type counts
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
}
?>
