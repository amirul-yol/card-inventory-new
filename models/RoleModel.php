<?php
require_once __DIR__ . '/../db/database.php';

class RoleModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllRoles() {
        $query = "SELECT id, name, created_at FROM roles ORDER BY id";
        $result = $this->db->query($query);

        $roles = [];
        while ($row = $result->fetch_assoc()) {
            $roles[] = $row;
        }
        return $roles;
    }
}
?>
