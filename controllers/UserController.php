<?php
require_once 'models/UserModel.php';

class UserController {
    public function index() {
        $model = new UserModel();
        $users = $model->getAllUsersWithRoles();
        include 'views/user/index.php';
    }

    public function addUser() {
        $model = new UserModel();
        $roles = $model->getRoles();
        
        // Fetch banks for the bank selection dropdown
        $banks = $model->getBanks();
        
        include 'views/user/addUser.php';
    }

    public function storeUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? null;
            $email = $_POST['email'] ?? null;
            $password = $_POST['password'] ?? null;
            $phone = $_POST['phone'] ?? null;
            $role_id = $_POST['role_id'] ?? null;
            $bank_id = $_POST['bank_id'] ?? null;
    
            if ($name && $email && $password && $role_id) {
                // Validate role ID first
                $model = new UserModel();
                if ($model->roleExists($role_id)) {
                    // Validate bank_id is required for Bank role
                    if ($role_id == 2 && empty($bank_id)) {
                        // Redirect with an error message if bank_id is missing for Bank role
                        header('Location: index.php?path=user/addUser&error=missing_bank');
                        exit();
                    }
                    
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                    $model->addUser($name, $email, $hashedPassword, $phone, $role_id, $bank_id);
    
                    // Redirect to user management page after successful insert
                    header('Location: index.php?path=user');
                    exit();
                } else {
                    // Redirect with an error message if role_id is invalid
                    header('Location: index.php?path=user/addUser&error=invalid_role');
                    exit();
                }
            } else {
                // Redirect with an error message if fields are missing
                header('Location: index.php?path=user/addUser&error=missing_fields');
                exit();
            }
        }
    }
    
    public function editUser() {
        $id = $_GET['id'];
        $model = new UserModel();
        $user = $model->getUserById($id);
        
        // Get roles and banks for the dropdowns
        $roles = $model->getRoles();
        $banks = $model->getBanks();
        
        include 'views/user/editUser.php';
    }

    public function updateUser() {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $role_id = $_POST['role_id'];
        $bank_id = $_POST['bank_id'] ?? null;

        // Validate bank_id is required for Bank role
        if ($role_id == 2 && empty($bank_id)) {
            // Redirect with an error message if bank_id is missing for Bank role
            header('Location: index.php?path=user/editUser&id='.$id.'&error=missing_bank');
            exit();
        }

        $model = new UserModel();
        $model->updateUser($id, $name, $email, $phone, $role_id, $bank_id);

        header('Location: index.php?path=user');
    }

    public function deleteUser() {
        if (isset($_GET['id'])) {
            $userId = $_GET['id'];
            $model = new UserModel();
            
            // Delete the user by ID
            $model->deleteUser($userId);
    
            // Redirect to the users list page
            header('Location: index.php?path=user');
            exit();
        }
    }

    
}
?>
