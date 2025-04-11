<?php
require_once 'models/ReportModel.php';

class TransactionModel
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection(); // Fetch the mysqli connection
    }

    public function rejectTransaction($transactionId, $rejectQuantity, $reason)
    {
        $query = "INSERT INTO rejections (transaction_id, quantity, reason, created_at)
                VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iis", $transactionId, $rejectQuantity, $reason);
        $stmt->execute();

        // Update transaction availability
        $query = "UPDATE transactions SET quantity = quantity + ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $rejectQuantity, $transactionId);
        $stmt->execute();
    }


    private function getTransactionQuantity($transactionId)
    {
        $db = Database::getInstance()->getConnection();

        $query = "SELECT quantity FROM transactions WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('i', $transactionId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row['quantity'];
        }

        return 0; // Default if transaction not found
    }

    public function assignToReport($reportId, $bankId, $reportDate) {
        $query = "
            UPDATE transactions
            SET report_id = ?
            WHERE bank_id = ? AND DATE(transaction_date) = ?
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iis", $reportId, $bankId, $reportDate);
        $stmt->execute();
        $stmt->close();
    }
    
    
    public function updateRejectId($transactionId, $rejectionId) {
        $stmt = $this->db->prepare("
            UPDATE transactions 
            SET reject_id = ? 
            WHERE id = ?
        ");
        $stmt->execute([$rejectionId, $transactionId]);
    }

    public function getReportDetails($reportId, $bankId)
    {
        $reportModel = new ReportModel($this->db); // Pass the database connection
        return $reportModel->getReportDetails($reportId, $bankId);
    }

    public function submitVerification($reportId)
    {
        $query = "UPDATE reports SET status = 'verified', updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$reportId]);
    }

    public function createRejection($transaction_id, $quantity, $reason) {
        $db = Database::getInstance()->getConnection();
    
        $sql = "INSERT INTO rejections (transaction_id, quantity, reason, created_at) 
                VALUES (?, ?, ?, NOW())";
        $stmt = $db->prepare($sql);
    
        if ($stmt) {
            $stmt->bind_param('iis', $transaction_id, $quantity, $reason);
            if ($stmt->execute()) {
                return $db->insert_id;
            }
        }
    
        return false;
    }

    public function updateTransactionForRejection($transaction_id, $reject_quantity) {
        $db = Database::getInstance()->getConnection();
    
        $sql = "UPDATE transactions 
                SET quantity = quantity + ? 
                WHERE id = ?";
        $stmt = $db->prepare($sql);
    
        if ($stmt) {
            $stmt->bind_param('ii', $reject_quantity, $transaction_id);
            return $stmt->execute();
        }
    
        return false;
    }
    
    public function getTransactionsByReportId($reportId)
    {
        $query = "SELECT t.id, c.name AS card_name, t.quantity, t.transaction_date, t.remarks 
                FROM transactions t
                INNER JOIN cards c ON t.card_id = c.id
                WHERE t.report_id = ? AND t.transaction_type = 'withdraw'";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $reportId);
        $stmt->execute();
        $result = $stmt->get_result();

        $transactions = [];
        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }

        return $transactions;
    }

    public function assignReportToTransactions($bankId, $date, $reportId) {
        $sql = "UPDATE transactions 
                SET report_id = ? 
                WHERE bank_id = ? AND report_id IS NULL AND DATE(created_at) = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$reportId, $bankId, $date]);
    }
    
    // In TransactionModel.php
    public function getUnverifiedTransactions($reportId) {
        // Fetch transactions where `verified` is 0 (unverified)
        $sql = "SELECT * FROM transactions WHERE report_id = ? AND verified = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $reportId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // In TransactionModel.php
    public function markTransactionVerified($transactionId) {
        // Update the `verified` column to 1 for the given transaction ID
        $sql = "UPDATE transactions SET verified = 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $transactionId);
        $stmt->execute();
    }

}
?>
