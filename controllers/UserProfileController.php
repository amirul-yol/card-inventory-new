<?php
require_once 'models/UserProfileModel.php';

class UserProfileController {
    public function index() {
        $model = new UserProfileModel();
        $userId = 1; // Replace with dynamic ID based on session or authentication in the future
        $userProfile = $model->getUserProfile($userId);
        include 'views/user_profile/index.php';
    }
}
?>
