<?php
require_once 'models/BankModel.php';

class BankController {
    public function index() {
        $model = new BankModel();
        $banks = $model->getBanksWithCardCount();
        include 'views/bank/index.php';
    }

    public function create() {
        include 'views/bank/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $logoUrl = $_POST['logo_url'];

            $model = new BankModel();
            $model->addBank($name, $logoUrl);

            header('Location: index.php?path=bank');
            exit;
        }
    }

    public function show($bankId) {
        $model = new BankModel();
        $bank = $model->getBankDetails($bankId);
        include 'views/bank/show.php';
    }
}
