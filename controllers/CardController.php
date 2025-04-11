<?php
require_once 'models/CardModel.php';

class CardController {
    public function index() {
        $model = new CardModel();
        $banks = $model->getBanksWithCards(); // Correct method name
        include 'views/card/index.php';
    }

    public function create() {
        $model = new CardModel();
        $banks = $model->getBanks(); // For populating the bank dropdown in the add card form
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
    
            $model->addCard($name, $bankId, $association, $chipType, $type, $expiredAt, $quantity);
        }
    
        header('Location: index.php?path=card');
        exit;
    }
    public function details() {
        $model = new CardModel();
        $cardId = $_GET['id']; // Assuming the card ID is passed via URL.
        $card = $model->getCardDetails($cardId);
        include 'views/card/details.php';
    }
    
    public function viewTransactions() {
        $cardId = $_GET['card_id'] ?? null;
    
        if (!$cardId || !is_numeric($cardId)) {
            die("Invalid Card ID.");
        }
    
        $cardModel = new CardModel();
        $transactions = $cardModel->getTransactionsByCardId($cardId);
        $card = $cardModel->getCardById($cardId);
    
        if (!$card) {
            die("Card not found.");
        }
    
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
    
        require_once 'views/card/deposit_card_form.php';
    }
    
    public function processDepositCard() {
        $cardId = $_POST['card_id'] ?? null;
        $quantity = $_POST['quantity'] ?? null;
    
        if (!$cardId || !$quantity || !is_numeric($quantity) || $quantity <= 0) {
            die("Invalid input.");
        }
    
        $cardModel = new CardModel();
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
        $cardModel->updateTransaction($transactionId, $cardId, $quantity);
    
        header("Location: index.php?path=card/viewTransactions&card_id=" . $cardId);
        exit;
    }
    
}    
?>
