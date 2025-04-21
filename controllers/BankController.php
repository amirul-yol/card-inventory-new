<?php
require_once 'models/BankModel.php';
require_once 'controllers/AuthController.php';

class BankController {
    private $authController;
    private $bankModel;
    
    public function __construct() {
        $this->authController = new AuthController();
        $this->bankModel = new BankModel();
    }
    
    // Get all banks with minimal information
    public function getAllBanks() {
        return $this->bankModel->getBanksWithCardCount();
    }
    
    // Get banks as JSON for AJAX requests
    public function getBanksJson() {
        try {
            $banks = $this->getAllBanks();
            
            // Return JSON response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'banks' => $banks
            ]);
            exit;
        } catch (Exception $e) {
            // Return error response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            exit;
        }
    }
    
    public function index() {
        // Redirect Bank users to their specific bank details page
        if ($this->authController->isBank() && isset($_SESSION['bank_id'])) {
            $bankId = $_SESSION['bank_id'];
            header('Location: index.php?path=bank/details&id=' . $bankId);
            exit;
        }
        
        // For Admin, PO, and LO users, show all banks
        $banks = $this->bankModel->getBanksWithCardCount();
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
        // Get the form data
        $name = $_POST['name'] ?? '';
        
        // Process logo upload if provided
        $logo = '';
        if (isset($_FILES['logo_url']) && $_FILES['logo_url']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/logos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileExtension = pathinfo($_FILES['logo_url']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('bank_') . '.' . $fileExtension;
            $targetFile = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['logo_url']['tmp_name'], $targetFile)) {
                $logo = $targetFile;
            }
        }
        
        if (empty($logo)) {
            // Default logo if none provided
            $logo = 'uploads/logos/default_bank.png';
        }
        
        // Validate input
        if (empty($name)) {
            // Check if this is an AJAX request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'Bank name is required'
                ]);
                exit;
            }
            
            $_SESSION['error'] = 'Bank name is required';
            header('Location: index.php?path=bank/create');
            exit;
        }
        
        // Create the bank
        $bankData = [
            'name' => $name,
            'logo_url' => $logo
        ];
        
        $result = $this->bankModel->addBank($name, $logo);
        
        // Check if this is an AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Bank added successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to add bank'
                ]);
            }
            exit;
        }
        
        // Regular form submission response
        if ($result) {
            $_SESSION['success'] = 'Bank added successfully';
        } else {
            $_SESSION['error'] = 'Failed to add bank';
        }
        
        header('Location: index.php');
        exit;
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
        
        $bank = $this->bankModel->getBankDetails($bankId);
        
        if (!$bank) {
            die("Bank not found.");
        }
        
        include 'views/bank/details.php';
    }
}
