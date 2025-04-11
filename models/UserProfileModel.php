<?php
require_once __DIR__ . '/../db/database.php';

class UserProfileModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getUserProfile($userId) {
        $query = "SELECT name, email, phone, profile_picture FROM users WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }
}
?>
