<?php
require_once 'models/UserProfileModel.php';
require_once 'controllers/AuthController.php';

class UserProfileController {
    private $authController;
    
    public function __construct() {
        $this->authController = new AuthController();
    }
    
    public function index() {
        // Check if user is logged in
        if (!$this->authController->isLoggedIn()) {
            header('Location: index.php?path=auth/login');
            exit;
        }
        
        // Get the logged-in user's ID from the session
        $userId = $_SESSION['user_id'];
        
        // Get the user profile data
        $model = new UserProfileModel();
        $userProfile = $model->getUserProfile($userId);
        
        include 'views/user_profile/index.php';
    }
    
    public function edit() {
        // Check if user is logged in
        if (!$this->authController->isLoggedIn()) {
            header('Location: index.php?path=auth/login');
            exit;
        }
        
        // Get the logged-in user's ID from the session
        $userId = $_SESSION['user_id'];
        
        // Get the user profile data
        $model = new UserProfileModel();
        $userProfile = $model->getUserProfile($userId);
        
        include 'views/user_profile/edit.php';
    }
    
    public function update() {
        // Check if user is logged in
        if (!$this->authController->isLoggedIn()) {
            header('Location: index.php?path=auth/login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?path=user_profile');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $name = $_POST['name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        
        // Handle profile picture upload
        $profilePicture = null;
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
            $uploadDir = 'assets/uploads/profiles/';
            
            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = $userId . '_' . time() . '_' . $_FILES['profile_picture']['name'];
            $uploadPath = $uploadDir . $fileName;
            
            // Move the uploaded file
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadPath)) {
                $profilePicture = $uploadPath;
            }
        }
        
        // Update user profile
        $model = new UserProfileModel();
        $result = $model->updateUserProfile($userId, $name, $phone, $profilePicture);
        
        if ($result) {
            // Update session name if it was changed
            if (!empty($name)) {
                $_SESSION['user_name'] = $name;
            }
            header('Location: index.php?path=user_profile&success=profile_updated');
        } else {
            header('Location: index.php?path=user_profile/edit&error=update_failed');
        }
        exit;
    }
}
?>
