<?php
require_once __DIR__ . '/../db/database.php';

class BankModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getBanksWithCardCount() {
        $query = "
            SELECT 
                b.id AS bank_id, 
                b.name AS bank_name, 
                b.logo_url AS bank_logo, 
                COUNT(c.id) AS card_count
            FROM banks b
            LEFT JOIN cards c ON b.id = c.bank_id
            GROUP BY b.id;
        ";
        $result = $this->db->query($query);

        $banks = [];
        while ($row = $result->fetch_assoc()) {
            $banks[] = [
                'bank_id' => $row['bank_id'],
                'bank_name' => $row['bank_name'],
                'bank_logo' => $row['bank_logo'],
                'card_count' => $row['card_count']
            ];
        }
        return $banks;
    }

    public function getBankDetails($bankId) {
        $query = "SELECT id, name, logo_url FROM banks WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $bankId);
        $stmt->execute();
        $result = $stmt->get_result();
        $bank = $result->fetch_assoc();
        $stmt->close();
        return $bank;
    }

    public function addBank($name, $logoUrl) {
        $query = "
            INSERT INTO banks (name, logo_url, created_at) 
            VALUES (?, ?, NOW())
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $name, $logoUrl);
        if (!$stmt->execute()) {
            die("Error adding bank: " . $this->db->error);
        }
        $stmt->close();
    }
}
