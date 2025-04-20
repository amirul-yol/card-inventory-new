<?php
require_once 'models/BankModel.php';
require_once 'controllers/AuthController.php';

class BankController {
    private $authController;
    
    public function __construct() {
        $this->authController = new AuthController();
    }
    
    // Get all banks with minimal information
    public function getAllBanks() {
        $model = new BankModel();
        return $model->getBanksWithCardCount();
    }
    
    public function index() {
        // Redirect Bank users to their specific bank details page
        if ($this->authController->isBank() && isset($_SESSION['bank_id'])) {
            $bankId = $_SESSION['bank_id'];
            header('Location: index.php?path=bank/details&id=' . $bankId);
            exit;
        }
        
        // For Admin, PO, and LO users, show all banks
        $model = new BankModel();
        $banks = $model->getBanksWithCardCount();
        include 'views/bank/index.php';
    }

    public function create() {
        // Only Admin should be able to create banks
        if (!$this->authController->isAdmin()) {
            die("You do not have permission to create banks.");
        }
        
        include 'views/bank/create.php';
    }

    public function store() {
        // Only Admin should be able to create banks
        if (!$this->authController->isAdmin()) {
            die("You do not have permission to create banks.");
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $logoUrl = $_POST['logo_url'];

            $model = new BankModel();
            $model->addBank($name, $logoUrl);

            header('Location: index.php?path=bank');
            exit;
        }
    }

    public function details() {
        $bankId = $_GET['id'] ?? null;
        
        if (!$bankId || !is_numeric($bankId)) {
            die("Invalid bank ID.");
        }
        
        // Check if the user can access this bank
        if (!$this->authController->canAccessBank($bankId)) {
            die("You do not have permission to view this bank.");
        }
        
        $model = new BankModel();
        $bank = $model->getBankDetails($bankId);
        
        if (!$bank) {
            die("Bank not found.");
        }
        
        include 'views/bank/details.php';
    }
}
