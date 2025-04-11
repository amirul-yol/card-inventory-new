<?php
require_once 'db/database.php';
include_once __DIR__ . '/../models/DashboardModel.php';

class DashboardController {
    private $model;

    public function __construct() {
        $db = Database::getInstance()->getConnection();
        $this->model = new DashboardModel($db);
    }

    public function index() {
        try {
            $data = [
                'totalReports' => $this->model->getTotalReports(),
                'totalCards' => $this->model->getTotalCards(),
                'totalBanks' => $this->model->getTotalBanks(),
                'totalUsers' => $this->model->getTotalUsers(),
            ];

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
