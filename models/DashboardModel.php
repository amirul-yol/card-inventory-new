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
}
