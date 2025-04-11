<?php
require_once __DIR__ . '/../db/database.php';

class UserModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllUsersWithRoles() {
        $query = "
            SELECT 
                u.id AS user_id,
                u.name AS user_name,
                u.email AS user_email,
                u.phone AS user_phone,
                r.name AS role_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            ORDER BY u.id;
        ";
        $result = $this->db->query($query);

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        return $users;
    }

    public function getRoles() {
        $query = "SELECT id, name FROM roles ORDER BY id";
        $result = $this->db->query($query);

        $roles = [];
        while ($row = $result->fetch_assoc()) {
            $roles[] = $row;
        }

        return $roles;
    }

    public function addUser($name, $email, $password, $phone, $role_id) {
        $query = "
            INSERT INTO users (name, email, password, phone, role_id, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW());
        ";
    
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssssi", $name, $email, $password, $phone, $role_id);
    
        if (!$stmt->execute()) {
            // Handle potential errors during insertion
            die("Error inserting user: " . $stmt->error);
        }
    }
    

    public function roleExists($role_id) {
        $query = "SELECT id FROM roles WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $role_id);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
    
    public function getUserById($id) {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateUser($id, $name, $email, $phone, $role_id) {
        $query = "UPDATE users SET name = ?, email = ?, phone = ?, role_id = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sssii', $name, $email, $phone, $role_id, $id);
        $stmt->execute();
    }

    public function deleteUser($id) {
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
    }


    
    
}
?>
