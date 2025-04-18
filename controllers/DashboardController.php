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
            
            // Base data that all users need
            $data = [
                'totalReports' => $this->model->getTotalReports(),
                'totalCards' => $this->model->getTotalCards(),
            ];
            
            // Only fetch banks and users count if not a Bank user
            if (!$isBank) {
                $data['totalBanks'] = $this->model->getTotalBanks();
                $data['totalUsers'] = $this->model->getTotalUsers();
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
