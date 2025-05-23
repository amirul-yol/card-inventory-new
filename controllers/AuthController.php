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
            
            // Store bank_id in session if it exists (for Bank users)
            if ($user['role_id'] == 2 && isset($user['bank_id'])) {
                $_SESSION['bank_id'] = $user['bank_id'];
            } else {
                $_SESSION['bank_id'] = null; // Ensure admin/others have NULL bank_id
            }
            
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
    
    // Check if the current user has a specific role
    public function hasRole($roleId) {
        return $this->isLoggedIn() && $_SESSION['user_role'] == $roleId;
    }
    
    // Check if the current user is a Logistics Officer (LO)
    public function isLogisticsOfficer() {
        return $this->hasRole(4); // Role ID 4 is for LO
    }
    
    // Check if the current user is a Production Officer (PO)
    public function isProductionOfficer() {
        return $this->hasRole(3); // Role ID 3 is for PO
    }
    
    // Check if the current user is an Admin
    public function isAdmin() {
        return $this->hasRole(1); // Role ID 1 is for Admin
    }
    
    // Check if the current user is a Bank user
    public function isBank() {
        return $this->hasRole(2); // Role ID 2 is for Bank
    }
    
    // Check if the user can access data for a specific bank
    public function canAccessBank($bankId) {
        // Admin, PO, and LO can access all banks
        if (!$this->isBank()) {
            return true;
        }
        
        // Bank users can only access their own bank's data
        return isset($_SESSION['bank_id']) && $_SESSION['bank_id'] == $bankId;
    }
}
?> 