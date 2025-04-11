<?php
require_once 'models/RoleModel.php';

class RoleController {
    public function index() {
        $model = new RoleModel();
        $roles = $model->getAllRoles();
        include 'views/role/index.php';
    }
}
?>
