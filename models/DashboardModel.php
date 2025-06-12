<?php

require_once 'db/database.php';

class DashboardModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection(); // Ensure proper connection retrieval
        if (!$this->conn) {
            die("Database connection failed.");
        }
    }

    public function getTotalReports() {
        $sql = "SELECT COUNT(*) as total FROM reports";
        $result = $this->conn->query($sql);
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total'];
        }
        return 0;
    }

    public function getTotalReportsByBank($bankId) {
        $sql = "SELECT COUNT(*) as total FROM reports WHERE bank_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $bankId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total'];
        }
        return 0;
    }

    public function getTotalCards() {
        $sql = "SELECT COUNT(*) as total FROM cards";
        $result = $this->conn->query($sql);
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total'];
        }
        return 0;
    }

    public function getTotalCardsByBank($bankId) {
        $sql = "SELECT COUNT(*) as total FROM cards WHERE bank_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $bankId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total'];
        }
        return 0;
    }

    public function getTotalBanks() {
        $sql = "SELECT COUNT(*) as total FROM banks";
        $result = $this->conn->query($sql);
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total'];
        }
        return 0;
    }

    public function getTotalUsers() {
        $sql = "SELECT COUNT(*) as total FROM users";
        $result = $this->conn->query($sql);
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total'];
        }
        return 0;
    }


    public function getCardCountsByTypeForBank($bankId) {
        $sql = "SELECT type, COUNT(*) as count FROM cards WHERE bank_id = ? GROUP BY type ORDER BY type";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed in getCardCountsByTypeForBank: (" . $this->conn->errno . ") " . $this->conn->error);
            return [];
        }
        $stmt->bind_param("i", $bankId);
        if (!$stmt->execute()) {
            error_log("Execute failed in getCardCountsByTypeForBank: (" . $stmt->errno . ") " . $stmt->error);
            $stmt->close();
            return [];
        }
        $result = $stmt->get_result();
        $cardCounts = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                if (!empty($row['type'])) {
                    $cardCounts[$row['type']] = $row['count'];
                }
            }
        }
        $stmt->close();
        return $cardCounts;
    }
}
