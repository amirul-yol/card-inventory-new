<?php
require_once '../../models/UserModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role_id = $_POST['role'];

    $userModel = new UserModel();
    $userModel->addUser($name, $email, $phone, $password, $role_id);

    header('Location: index.php?path=user');
    exit;
}
?>
