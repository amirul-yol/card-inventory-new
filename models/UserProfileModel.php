<?php
require_once __DIR__ . '/../db/database.php';

class UserProfileModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getUserProfile($userId) {
        $query = "
            SELECT 
                u.id, u.name, u.email, u.phone, u.profile_picture, 
                r.name AS role_name, 
                b.name AS bank_name, 
                u.created_at AS joined_date
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            LEFT JOIN banks b ON u.bank_id = b.id
            WHERE u.id = ?
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }
    
    public function updateUserProfile($userId, $name, $phone, $profilePicture = null) {
        // Build the query based on which fields need to be updated
        $updateFields = [];
        $params = [];
        $types = '';
        
        if (!empty($name)) {
            $updateFields[] = "name = ?";
            $params[] = $name;
            $types .= 's';
        }
        
        if (!empty($phone)) {
            $updateFields[] = "phone = ?";
            $params[] = $phone;
            $types .= 's';
        }
        
        if (!empty($profilePicture)) {
            $updateFields[] = "profile_picture = ?";
            $params[] = $profilePicture;
            $types .= 's';
        }
        
        // If no fields are being updated, return success
        if (empty($updateFields)) {
            return true;
        }
        
        // Add the user ID to the params array
        $params[] = $userId;
        $types .= 'i';
        
        $query = "UPDATE users SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            error_log("Error preparing statement: " . $this->db->error);
            return false;
        }
        
        // Dynamically bind parameters
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $result = $stmt->execute();
        
        if (!$result) {
            error_log("Error executing statement: " . $stmt->error);
        }
        
        return $result;
    }
}
?>
