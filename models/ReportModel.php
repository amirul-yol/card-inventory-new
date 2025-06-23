<?php
require_once __DIR__ . '/../db/database.php';

class ReportModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getBanks() {
        $query = "SELECT id AS bank_id, name, logo_url FROM banks ORDER BY name";
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getBankById($bankId) {
        $query = "SELECT id AS bank_id, name, logo_url FROM banks WHERE id = ?";
        $stmt = $this->db->prepare($query);
    
        if (!$stmt) {
            die("Prepare failed: " . $this->db->error);
        }
    
        $stmt->bind_param('i', $bankId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        return $result->fetch_assoc();
    }

    public function getReportsByBank($bankId) {
        $query = "SELECT r.id, r.report_date, r.details, r.status 
                  FROM reports r
                  WHERE r.bank_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $bankId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getRejectionsByReports($reportIds) {
        if (empty($reportIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($reportIds), '?'));
        $query = "
            SELECT t.report_id, SUM(rej.quantity) AS total_rejected
            FROM rejections rej
            INNER JOIN transactions t ON rej.transaction_id = t.id
            WHERE t.report_id IN ($placeholders)
            GROUP BY t.report_id
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param(str_repeat('i', count($reportIds)), ...$reportIds);
        $stmt->execute();
        $result = $stmt->get_result();

        $rejections = [];
        while ($row = $result->fetch_assoc()) {
            $rejections[$row['report_id']] = $row['total_rejected'];
        }

        return $rejections;
    }

    public function withdrawCard($cardId, $bankId, $quantity, $withdrawType, $transactionDate) {
        $query = "INSERT INTO transactions (card_id, bank_id, transaction_date, transaction_type, quantity, verified, remarks, created_at)
                  VALUES (?, ?, ?, 'withdraw', ?, NULL, ?, NOW())";
        $stmt = $this->db->prepare($query); 
        $stmt->bind_param("iisis", $cardId, $bankId, $transactionDate, $quantity, $withdrawType);
    
        if ($stmt->execute()) {
            // Update card quantity
            $updateQuery = "UPDATE cards SET quantity = quantity - ? WHERE id = ?";
            $updateStmt = $this->db->prepare($updateQuery);
            $updateStmt->bind_param("ii", $quantity, $cardId);
    
            if ($updateStmt->execute()) {
                return true; // Success
            } else {
                die("Failed to update card quantity: " . $updateStmt->error);
            }
        } else {
            die("Failed to insert withdrawal transaction: " . $stmt->error);
        }    
    }
    

    public function getCardsByBankId($bankId) {
        $query = "SELECT id, name, quantity, chip_type, type FROM cards WHERE bank_id = ?";
        $stmt = $this->db->prepare($query);
    
        if (!$stmt) {
            die("Prepare failed: " . $this->db->error);
        }
    
        $stmt->bind_param('i', $bankId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if (!$result) {
            die("Query failed: " . $this->db->error);
        }
    
        $cards = $result->fetch_all(MYSQLI_ASSOC);
    
    
        return $cards;
    }
    
    public function checkCardWithdrawal($cardId, $currentDate)
    {
        $sql = "SELECT COUNT(*) AS count 
                FROM transactions 
                WHERE card_id = ? AND transaction_date = ? AND transaction_type = 'withdrawal'";
        $stmt = $this->db->prepare($sql);

        if ($stmt === false) {
            die('Prepare failed: ' . $this->db->error);
        }

        $stmt->bind_param("is", $cardId, $currentDate);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $stmt->close();

        return $row['count'] > 0;
    }
    
    public function getCardById($cardId) {
        $stmt = $this->db->prepare("SELECT * FROM cards WHERE id = ?");
        $stmt->bind_param('i', $cardId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function getCardWithdrawalDetails($cardId, $date) {

        $query = "SELECT id, quantity, transaction_date, remarks, card_id, bank_id 
                  FROM transactions
                  WHERE card_id = ? AND transaction_date = ? AND transaction_type = 'withdraw'";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("is", $cardId, $date);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $data = $result->fetch_assoc();
    
        return $data; // Return a single withdrawal record if it exists
    }
    
    public function addTransaction($cardId, $bankId, $date, $type, $quantity, $remarks, $verified) {
        $query = "INSERT INTO transactions (card_id, bank_id, transaction_date, transaction_type, quantity, remarks, verified, created_at, updated_at)
                  VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iississs", $cardId, $bankId, $date, $type, $quantity, $remarks, $verified);
        return $stmt->execute();
    }

    public function updateWithdrawal($withdrawalId, $quantity, $date) {
        $sql = "UPDATE transactions SET quantity = ?, transaction_date = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$quantity, $date, $withdrawalId]);
    }
    
    public function getWithdrawalById($withdrawalId) {
        $sql = "SELECT * FROM transactions WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $withdrawalId); // Bind the parameter as an integer
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Fetch the row as an associative array
    }
    
    public function updateCardQuantity($cardId, $quantityChange) {
        $stmt = $this->db->prepare("UPDATE cards SET quantity = quantity + ? WHERE id = ?");
        return $stmt->execute([$quantityChange, $cardId]);
    }
    
    public function deleteWithdrawal($withdrawalId) {
        $sql = "DELETE FROM transactions WHERE id = ? AND transaction_type = 'withdraw'";
        $stmt = $this->db->prepare($sql);
        
        if (!$stmt) {
            error_log("Error preparing delete statement: " . $this->db->error);
            return false;
        }
        
        $stmt->bind_param("i", $withdrawalId);
        $result = $stmt->execute();
        
        if (!$result) {
            error_log("Error executing delete statement: " . $stmt->error);
            return false;
        }
        
        return true;
    }
    
    public function createWithdrawalReport($bankId) {
        $currentDate = date('Y-m-d');
    
        // Insert the report into the database with 'pending' status
        $stmt = $this->db->prepare("INSERT INTO reports (bank_id, report_date, status) VALUES (?, ?, 'pending')");
        $stmt->bind_param("is", $bankId, $currentDate);
        $stmt->execute();
        
        // Get the ID of the newly created report
        $reportId = $this->db->insert_id;
        $stmt->close();
    
        // Link transactions for the current day to the report
        $transactionModel = new TransactionModel();
        $transactionModel->assignToReport($reportId, $bankId, $currentDate);
    
        return $reportId; // Return the created report ID
    }
    
    
    public function updateReportStatus($reportId, $status) {
        $stmt = $this->db->prepare("UPDATE reports SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $reportId);
        $stmt->execute();
        $stmt->close();
    }
    
    public function getLatestReportByBankAndDate($bankId, $currentDate) {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM reports 
            WHERE bank_id = ? AND report_date = ? 
            ORDER BY id DESC 
            LIMIT 1
        ");
        $stmt->bind_param("is", $bankId, $currentDate);
        $stmt->execute();
        $result = $stmt->get_result();
        $report = $result->fetch_assoc();
        $stmt->close();
        return $report;
    }

    public function verifyReport($reportId) {
        $stmt = $this->db->prepare("
            UPDATE reports 
            SET status = 'verified', updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$reportId]);
    }
    
    // Fetch report by ID
    public function getReportById($reportId) {
        $sql = "SELECT * FROM reports WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $reportId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // In ReportModel.php
    public function markReportVerified($reportId) {
        // Update the `status` column to 'verified' for the given report ID
        $sql = "UPDATE reports SET status = 'verified' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $reportId);
        $stmt->execute();
    }

    public function getReportDetails($reportId, $bankId)
    {
        $query = "SELECT t.*, c.name AS card_name 
                  FROM transactions t
                  JOIN cards c ON t.card_id = c.id
                  WHERE t.report_id = ? AND t.bank_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $reportId, $bankId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        // Fetch results as an associative array
        $transactions = [];
        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }
    
        return $transactions;
    }
    
    
    public function createReport($bankId, $reportDate) {
        $sql = "INSERT INTO reports (bank_id, report_date, created_at) VALUES (?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute([$bankId, $reportDate])) {
            // Use mysqli's insert_id property to get the last inserted ID
            return $this->db->insert_id;
        }
        return false;
    }

    public function markTransactionVerified($transactionId) {
        $sql = "UPDATE transactions SET is_verified = 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $transactionId);
    
        if (!$stmt->execute()) {
            throw new Exception("Failed to update transaction status.");
        }
    }
    
    public function getUnverifiedTransactions($reportId) {
        $sql = "SELECT * FROM transactions WHERE report_id = ? AND is_verified = 0";
        $stmt = $this->db->prepare($sql);
    
        if (!$stmt) {
            die("SQL preparation failed: " . $this->db->error);
        }
    
        $stmt->bind_param("i", $reportId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        // Debugging: Check if the query returns any rows
        if ($result->num_rows === 0) {
            echo "No unverified transactions found for report_id: $reportId";
            return [];
        }
    
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function hasRejections($reportId) {
        $sql = "SELECT COUNT(*) as rejection_count FROM rejections 
                WHERE transaction_id IN (
                    SELECT id FROM transactions WHERE report_id = ?
                )";
    
        $stmt = $this->db->prepare($sql); // Use $this->db if your constructor initializes the connection as $this->db
    
        if (!$stmt) {
            die("Error preparing statement: " . $this->db->error);
        }
    
        $stmt->bind_param("i", $reportId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
    
        return $data['rejection_count'] > 0;
    }
    
    public function addRejection($transactionId, $rejectedAmount, $rejectionReason)
    {
        $query = "INSERT INTO rejections (transaction_id, quantity, reason, created_at) 
                VALUES (?, ?, ?, NOW())";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iis", $transactionId, $rejectedAmount, $rejectionReason);
        $stmt->execute();
        $stmt->close();
    }


    public function getTransactionById($transactionId)
    {
        $query = "SELECT * FROM transactions WHERE id = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            die("Failed to prepare statement: " . $this->db->error);
        }
        $stmt->bind_param("i", $transactionId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getTransactionsForReport($reportId)
    {
        $query = "SELECT 
                t.id, 
                t.card_id, 
                t.quantity AS transaction_quantity, 
                t.transaction_date, 
                t.remarks, 
                c.name AS card_name, 
                c.quantity AS card_balance,
                COALESCE(SUM(r.quantity), 0) AS rejected_quantity
            FROM transactions t
            LEFT JOIN cards c ON t.card_id = c.id
            LEFT JOIN rejections r ON t.id = r.transaction_id
            WHERE t.report_id = ? AND t.transaction_type = 'withdraw'
            GROUP BY t.id, t.card_id, t.quantity, t.transaction_date, t.remarks, c.name, c.quantity";
        
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            error_log("Failed to prepare statement: " . $this->db->error);
            return []; // Return empty array on failure
        }
        $stmt->bind_param("i", $reportId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getRejectedAmount($transactionId)
    {
        $query = "SELECT SUM(quantity) AS rejected_amount FROM rejections WHERE transaction_id = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            die("Failed to prepare statement: " . $this->db->error);
        }
        $stmt->bind_param("i", $transactionId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['rejected_amount'] ?? 0;
    }

    public function updateTransactionAmount($transactionId, $newQuantity) {
        $stmt = $this->db->prepare("UPDATE transactions SET quantity = ? WHERE id = ?");
        return $stmt->execute([$newQuantity, $transactionId]);
    }

    public function getRejectedDetails($transactionId) {
        $sql = "
            SELECT 
                reason,
                SUM(quantity) AS total_quantity
            FROM rejections
            WHERE transaction_id = ?
            GROUP BY reason";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $transactionId);
        $stmt->execute();
        $result = $stmt->get_result();

        // Initialize default reject quantities
        $details = [
            'quality' => 0,
            'system' => 0
        ];

        while ($row = $result->fetch_assoc()) {
            // Map database values to expected keys
            if ($row['reason'] === 'Quality Error') {
                $details['quality'] = (int)$row['total_quantity'];
            } elseif ($row['reason'] === 'System Error') {
                $details['system'] = (int)$row['total_quantity'];
            }
        }

        return $details;
    }




}
?>
