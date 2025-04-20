<?php
require_once 'models/CardModel.php';
require_once 'controllers/AuthController.php';

class CardController {
    private $authController;
    
    public function __construct() {
        $this->authController = new AuthController();
    }
    
    public function getBanksWithCards() {
        $model = new CardModel();
        $allBanks = $model->getBanksWithCards();
        
        // If user is a Bank user, filter to show only their bank
        if ($this->authController->isBank() && isset($_SESSION['bank_id'])) {
            $bankId = $_SESSION['bank_id'];
            $banks = [];
            
            // Only include the bank that the user is associated with
            if (isset($allBanks[$bankId])) {
                $banks[$bankId] = $allBanks[$bankId];
            }
            return $banks;
        } else {
            // For Admin, PO, LO users, show all banks
            return $allBanks;
        }
    }
    
    public function index() {
        $model = new CardModel();
        $allBanks = $model->getBanksWithCards();
        
        // If user is a Bank user, filter to show only their bank
        if ($this->authController->isBank() && isset($_SESSION['bank_id'])) {
            $bankId = $_SESSION['bank_id'];
            $banks = [];
            
            // Only include the bank that the user is associated with
            if (isset($allBanks[$bankId])) {
                $banks[$bankId] = $allBanks[$bankId];
            }
        } else {
            // For Admin, PO, LO users, show all banks
            $banks = $allBanks;
        }
        
        include 'views/card/index.php';
    }

    public function create() {
        $model = new CardModel();
        
        // Get all banks for Admin, PO, LO or only the user's bank for Bank users
        if ($this->authController->isBank() && isset($_SESSION['bank_id'])) {
            $bankId = $_SESSION['bank_id'];
            $allBanks = $model->getBanks();
            $banks = array_filter($allBanks, function($bank) use ($bankId) {
                return $bank['id'] == $bankId;
            });
        } else {
            $banks = $model->getBanks();
        }
        
        include 'views/card/create.php';
    }

    public function store() {
        $model = new CardModel();
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $bankId = $_POST['bank_id'];
            $association = $_POST['association'];
            $chipType = $_POST['chip_type'];
            $type = $_POST['type'];
            $expiredAt = $_POST['expired_at'];
            $quantity = $_POST['quantity'];
            
            // Check if the user can access this bank
            if (!$this->authController->canAccessBank($bankId)) {
                die("You do not have permission to add cards to this bank.");
            }
    
            $model->addCard($name, $bankId, $association, $chipType, $type, $expiredAt, $quantity);
        }
    
        header('Location: index.php?path=card');
        exit;
    }
    
    public function details() {
        $model = new CardModel();
        $cardId = $_GET['id'] ?? null;
        
        if (!$cardId || !is_numeric($cardId)) {
            die("Invalid Card ID.");
        }
        
        $card = $model->getCardDetails($cardId);
        
        if (!$card) {
            die("Card not found.");
        }
        
        // Check if the user can access this bank
        if (!$this->authController->canAccessBank($card['bank_id'])) {
            die("You do not have permission to view this card.");
        }
        
        include 'views/card/details.php';
    }
    
    public function viewTransactions() {
        $cardId = $_GET['card_id'] ?? null;
    
        if (!$cardId || !is_numeric($cardId)) {
            die("Invalid Card ID.");
        }
    
        $cardModel = new CardModel();
        $card = $cardModel->getCardById($cardId);
    
        if (!$card) {
            die("Card not found.");
        }
        
        // Check if the user can access this bank
        if (!$this->authController->canAccessBank($card['bank_id'])) {
            die("You do not have permission to view transactions for this card.");
        }
        
        $transactions = $cardModel->getTransactionsByCardId($cardId);
    
        require_once 'views/card/view_transactions.php';
    }
    
    public function depositCardForm() {
        $cardId = $_GET['card_id'] ?? null;
    
        if (!$cardId || !is_numeric($cardId)) {
            die("Invalid Card ID.");
        }
    
        $cardModel = new CardModel();
        $card = $cardModel->getCardById($cardId);
    
        if (!$card) {
            die("Card not found.");
        }
        
        // Check if the user can access this bank
        if (!$this->authController->canAccessBank($card['bank_id'])) {
            die("You do not have permission to deposit cards for this bank.");
        }
    
        require_once 'views/card/deposit_card_form.php';
    }
    
    public function processDepositCard() {
        $cardId = $_POST['card_id'] ?? null;
        $quantity = $_POST['quantity'] ?? null;
    
        if (!$cardId || !$quantity || !is_numeric($quantity) || $quantity <= 0) {
            die("Invalid input.");
        }
        
        $cardModel = new CardModel();
        $card = $cardModel->getCardById($cardId);
        
        if (!$card) {
            die("Card not found.");
        }
        
        // Check if the user can access this bank
        if (!$this->authController->canAccessBank($card['bank_id'])) {
            die("You do not have permission to deposit cards for this bank.");
        }
    
        $cardModel->addDeposit($cardId, $quantity);
    
        header("Location: index.php?path=card/viewTransactions&card_id=" . $cardId);
        exit;
    }
    
    public function editTransactionForm() {
        $transactionId = $_GET['transaction_id'] ?? null;
    
        if (!$transactionId || !is_numeric($transactionId)) {
            die("Invalid Transaction ID.");
        }
    
        $cardModel = new CardModel();
        $transaction = $cardModel->getTransactionById($transactionId);
    
        if (!$transaction) {
            die("Transaction not found.");
        }
        
        // Get the card to check bank permissions
        $card = $cardModel->getCardById($transaction['card_id']);
        
        if (!$card) {
            die("Card not found.");
        }
        
        // Check if the user can access this bank
        if (!$this->authController->canAccessBank($card['bank_id'])) {
            die("You do not have permission to edit transactions for this bank.");
        }
    
        require_once 'views/card/edit_transaction_form.php';
    }
    
    public function processEditTransaction() {
        $transactionId = $_POST['transaction_id'] ?? null;
        $cardId = $_POST['card_id'] ?? null;
        $quantity = $_POST['quantity'] ?? null;
    
        if (!$transactionId || !$cardId || !$quantity || !is_numeric($quantity) || $quantity <= 0) {
            die("Invalid input.");
        }
        
        $cardModel = new CardModel();
        $card = $cardModel->getCardById($cardId);
        
        if (!$card) {
            die("Card not found.");
        }
        
        // Check if the user can access this bank
        if (!$this->authController->canAccessBank($card['bank_id'])) {
            die("You do not have permission to edit transactions for this bank.");
        }
    
        $cardModel->updateTransaction($transactionId, $cardId, $quantity);
    
        header("Location: index.php?path=card/viewTransactions&card_id=" . $cardId);
        exit;
    }
    
    // Add a method to get transactions as JSON for AJAX
    public function getTransactionsJson() {
        $cardId = $_GET['card_id'] ?? null;
    
        if (!$cardId || !is_numeric($cardId)) {
            echo json_encode(['error' => 'Invalid Card ID']);
            return;
        }
    
        $cardModel = new CardModel();
        $card = $cardModel->getCardById($cardId);
    
        if (!$card) {
            echo json_encode(['error' => 'Card not found']);
            return;
        }
        
        // Check if the user can access this bank
        if (!$this->authController->canAccessBank($card['bank_id'])) {
            echo json_encode(['error' => 'Permission denied']);
            return;
        }
        
        $transactions = $cardModel->getTransactionsByCardId($cardId);
        
        // Return card and transactions data
        echo json_encode([
            'success' => true,
            'card' => $card,
            'transactions' => $transactions
        ]);
    }
    
}    
?>
