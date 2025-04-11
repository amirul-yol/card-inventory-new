<?php
require_once 'models/UserModel.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    // Show login form
    public function showLoginForm() {
        // Check if user is already logged in
        if ($this->isLoggedIn()) {
            header('Location: index.php?path=dashboard');
            exit;
        }
        
        // Include the login view
        include 'views/auth/login.php';
    }

    // Process login form submission
    public function login() {
        // Get form data
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $error = '';

        // Validate input
        if (empty($email) || empty($password)) {
            $error = 'Please enter email and password';
            include 'views/auth/login.php';
            return;
        }

        // Get user by email
        $user = $this->userModel->getUserByEmail($email);

        // Check if user exists and password is correct
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role_id'];
            
            // Log login attempt
            error_log("User {$user['id']} ({$user['email']}) logged in successfully");
            
            // Redirect to dashboard
            header('Location: index.php?path=dashboard');
            exit;
        } else {
            // Log failed login attempt
            error_log("Failed login attempt for email: $email");
            
            // Show error message
            $error = 'Invalid email or password';
            include 'views/auth/login.php';
        }
    }

    // Logout user
    public function logout() {
        // Log user logout if logged in
        if (isset($_SESSION['user_id'])) {
            error_log("User {$_SESSION['user_id']} ({$_SESSION['user_email']}) logged out");
        }
        
        // Destroy session
        session_unset();
        session_destroy();
        
        // Redirect to login page
        header('Location: index.php?path=auth/login');
        exit;
    }

    // Check if user is logged in
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    // Middleware to check if user is authenticated
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header('Location: index.php?path=auth/login');
            exit;
        }
    }
}
?> 