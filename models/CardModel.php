<?php
require_once __DIR__ . '/../db/database.php';

class CardModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getBanksWithCards($cardType = null) {
        $query = "
            SELECT 
                b.id AS bank_id, 
                b.name AS bank_name, 
                b.logo_url AS bank_logo, 
                c.id AS card_id, 
                c.name AS card_name, 
                c.association, 
                c.chip_type, 
                c.type AS card_type, 
                c.expired_at, 
                c.quantity AS card_quantity 
            FROM banks b
            LEFT JOIN cards c ON b.id = c.bank_id
        ";
        
        // Add WHERE clause if card type is specified
        if ($cardType) {
            $query .= " WHERE c.type = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('s', $cardType);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $query .= " ORDER BY b.id, c.id";
            $result = $this->db->query($query);
        }

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $bankId = $row['bank_id'];
            if (!isset($data[$bankId])) {
                $data[$bankId] = [
                    'bank_name' => $row['bank_name'],
                    'bank_logo' => $row['bank_logo'],
                    'cards' => []
                ];
            }

            if (!empty($row['card_id'])) { // Only add card details if a card exists
                $data[$bankId]['cards'][] = [
                    'card_id' => $row['card_id'],
                    'card_name' => $row['card_name'],
                    'association' => $row['association'],
                    'chip_type' => $row['chip_type'],
                    'card_type' => $row['card_type'],
                    'expired_at' => $row['expired_at'],
                    'card_quantity' => $row['card_quantity'],
                ];
            }
        }

        return $data;
    }

    public function getBanks() {
        $query = "SELECT id, name FROM banks ORDER BY name";
        $result = $this->db->query($query);
    
        $banks = [];
        while ($row = $result->fetch_assoc()) {
            $banks[] = $row;
        }
        return $banks;
    }
    
    public function addCard($name, $bankId, $association, $chipType, $type, $expiredAt, $quantity) {
        $query = "
            INSERT INTO cards (name, bank_id, association, chip_type, type, expired_at, quantity, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ";
    
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sissssi", $name, $bankId, $association, $chipType, $type, $expiredAt, $quantity);
    
        if (!$stmt->execute()) {
            die("Error adding card: " . $this->db->error);
        }
    
        $stmt->close();
    }
    
    public function getCardDetails($cardId) {
        $query = "
            SELECT id, name, bank_id, association, chip_type, type, expired_at, quantity, created_at, updated_at
            FROM cards
            WHERE id = ?
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $cardId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getTransactionsByCardId($cardId) {
        $sql = " 
            SELECT 
                t.id,
                t.transaction_date,
                t.remarks,
                CASE WHEN t.transaction_type = 'deposit' THEN t.quantity ELSE 0 END AS quantity_in,
                CASE WHEN t.transaction_type = 'withdraw' THEN t.quantity ELSE 0 END AS quantity_out,
                COALESCE(SUM(CASE WHEN r.reason = 'Quality Error' THEN r.quantity ELSE 0 END), 0) AS reject_quality,
                COALESCE(SUM(CASE WHEN r.reason = 'System Error' THEN r.quantity ELSE 0 END), 0) AS reject_system
            FROM transactions t
            LEFT JOIN rejections r ON t.id = r.transaction_id
            WHERE t.card_id = ?
            GROUP BY t.id
            ORDER BY t.transaction_date ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $cardId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function addDeposit($cardId, $quantity) {
        $this->db->begin_transaction();
    
        try {
             // Get the bank_id from the cards table to ensure integrity
            $sqlGetBankId = "SELECT bank_id FROM cards WHERE id = ?";
            $stmtBank = $this->db->prepare($sqlGetBankId);
            $stmtBank->bind_param("i", $cardId);
            $stmtBank->execute();
            $result = $stmtBank->get_result();
            $cardData = $result->fetch_assoc();
            $bankId = $cardData['bank_id'];

            if (!$bankId) {
                throw new Exception("Invalid bank ID.");
            }

            // Update the card quantity
            $sqlUpdateCard = "UPDATE cards SET quantity = quantity + ? WHERE id = ?";
            $stmtCard = $this->db->prepare($sqlUpdateCard);
            $stmtCard->bind_param("ii", $quantity, $cardId);
            $stmtCard->execute();
    
            // Add a new transaction
            $sqlInsertTransaction = "INSERT INTO transactions (card_id, bank_id, quantity, transaction_date, transaction_type, remarks) VALUES (?, ?, ?, NOW(), ?, ?)";
            $stmtTransaction = $this->db->prepare($sqlInsertTransaction);
            $remarks = "Deposit";
            $transaction_type = "deposit";
            $stmtTransaction->bind_param("iiiss", $cardId, $bankId, $quantity, $transaction_type, $remarks);
            $stmtTransaction->execute();
    
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function getTransactionById($transactionId) {
        $sql = "SELECT * FROM transactions WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $transactionId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function updateTransaction($transactionId, $cardId, $quantity) {
        $this->db->begin_transaction();
    
        try {
            // Get the existing transaction
            $sqlGetTransaction = "SELECT quantity FROM transactions WHERE id = ?";
            $stmtGet = $this->db->prepare($sqlGetTransaction);
            $stmtGet->bind_param("i", $transactionId);
            $stmtGet->execute();
            $stmtGet->bind_result($oldQuantity);
            $stmtGet->fetch();
            $stmtGet->close();
    
            // Update the card quantity
            $quantityDifference = $quantity - $oldQuantity;
            $sqlUpdateCard = "UPDATE cards SET quantity = quantity + ? WHERE id = ?";
            $stmtCard = $this->db->prepare($sqlUpdateCard);
            $stmtCard->bind_param("ii", $quantityDifference, $cardId);
            $stmtCard->execute();
    
            // Update the transaction
            $sqlUpdateTransaction = "UPDATE transactions SET quantity = ?, transaction_date = NOW(), remarks = ? WHERE id = ?";
            $stmtTransaction = $this->db->prepare($sqlUpdateTransaction);
            $remarks = "Updated Deposit";
            $stmtTransaction->bind_param("isi", $quantity, $remarks, $transactionId);
            $stmtTransaction->execute();
    
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function getCardById($cardId)
    {
        $sql = "SELECT * FROM cards WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $cardId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getCardTransactions($cardId)
    {
        $sql = "SELECT * FROM transactions WHERE card_id = ? ORDER BY transaction_date ASC"; // ASC for earliest to latest
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $cardId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getCardsWithWithdrawalStatus($bankId) {
        $sql = "
            SELECT c.id, c.name, c.quantity, c.chip_type, c.type, 
                EXISTS(
                    SELECT 1 FROM transactions 
                    WHERE transactions.card_id = c.id 
                    AND transactions.transaction_type = 'withdraw' 
                    AND DATE(transactions.transaction_date) = CURDATE()
                ) AS withdrawn_today
            FROM cards c
            WHERE c.bank_id = ?;

        ";
    
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $bankId);
        $stmt->execute();
    
        $result = $stmt->get_result();
        $cards = $result->fetch_all(MYSQLI_ASSOC);
    
        $stmt->close();
        return $cards;
    }

    public function getDistinctCardTypes($bankId = null) {
        $query = "SELECT DISTINCT type FROM cards WHERE type IS NOT NULL AND type != ''";
        if ($bankId !== null) {
            $query .= " AND bank_id = ?";
        }
        $query .= " ORDER BY type ASC";

        $stmt = $this->db->prepare($query);

        if ($bankId !== null) {
            $stmt->bind_param('i', $bankId);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $types = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $types[] = $row['type'];
            }
        }
        $stmt->close();
        return $types;
    }
    
}
?>
