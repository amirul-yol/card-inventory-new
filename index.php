<?php
// Start the session
session_start();

require_once 'controllers/DashboardController.php';
require_once 'controllers/CardController.php';
require_once 'controllers/BankController.php';
require_once 'controllers/ReportController.php';
require_once 'controllers/UserController.php';
require_once 'controllers/AuthController.php';

$path = $_GET['path'] ?? 'dashboard';

// Create an instance of AuthController for authentication checks
$authController = new AuthController();

// Define routes that don't require authentication
$publicRoutes = ['auth/login', 'auth/process_login', 'auth/logout'];

// Check if the current route requires authentication
if (!in_array($path, $publicRoutes)) {
    $authController->requireAuth();
}

switch ($path) {
    // Authentication routes
    case 'auth/login':
        $authController->showLoginForm();
        break;
        
    case 'auth/process_login':
        $authController->login();
        break;
        
    case 'auth/logout':
        $authController->logout();
        break;

    // Existing routes
    case 'dashboard':
        $controller = new DashboardController();
        $controller->index();
        break;

    case 'card':
        $controller = new CardController();
        $controller->index();
        break;

    case 'card/create':
        $controller = new CardController();
        $controller->create();
        break;

    case 'card/store':
        $controller = new CardController();
        $controller->store();
        break;

    case 'card/details':
        $controller = new CardController();
        $controller->details();
        break;

    case 'card/viewTransactions':
        $controller = new CardController();
        $controller->viewTransactions();
        break;
    
    // Deposit card form
    case 'card/depositCardForm':
        $controller = new CardController();
        $controller->depositCardForm();
        break;
    
    // Process deposit card
    case 'card/processDepositCard':
        $controller = new CardController();
        $controller->processDepositCard();
        break;
    
    // Edit transaction form
    case 'card/editTransactionForm':
        $controller = new CardController();
        $controller->editTransactionForm();
        break;
    
    // Process edited transaction
    case 'card/processEditTransaction':
        $controller = new CardController();
        $controller->processEditTransaction();
        break;
    
    case 'report':
        $controller = new ReportController();
        $controller->index();
        break;

    case 'report/create':
        $controller = new ReportController();
        $controller->create();
        break;

    case 'report/bankReports': // Corrected from 'report/bank_reports'
        $controller = new ReportController();
        $controller->bankReports();
        break;        
        
    case 'report/withdrawCard':
        $controller = new ReportController();
        $controller->withdrawCard();
        break;      
        
    case 'report/processWithdraw':
        $controller = new ReportController();
        $controller->processWithdraw();
        break;

    case 'report/processWithdrawEdit':
        $controller = new ReportController();
        $controller->processWithdrawEdit();
        break;

    case 'report/withdrawCardForm':
        $controller = new ReportController();
        $controller->withdrawCardForm();
        break;
        
    case 'report/editWithdrawalForm':
        $controller = new ReportController();
        $controller->editWithdrawalForm();
        break;
        
    case 'report/cancelWithdrawal':
        $controller = new ReportController();
        $controller->cancelWithdrawal();
        break;
        
    case 'report/verify':
        $controller = new ReportController();
        $controller->verify();  // Call the verify method for verifying the report
        break;

    case 'report/reject':
        $controller = new ReportController();
        $controller->reject();
        break;

    case 'report/verifySubmit':
        $controller = new ReportController();
        $controller->verifySubmit();
        break;

    case 'report/submitVerification':
        $controller = new ReportController();
        $controller->submitVerification();
        break;

    case 'report/generateReport':
        $controller = new ReportController();
        $controller->generateReport();
        break;

    case 'report/submitWithdrawReport':
        $controller = new ReportController();
        $controller->submitWithdrawReport();
        break;        

    case 'report/verifyWithdrawReport':
        $controller = new ReportController();
        $controller->verifyWithdrawReport();
        break;
        
    case 'report/rejectCard':
        $controller = new ReportController();
        $controller->rejectCard();
        break;

    case 'report/download': // New route for downloading reports
        $controller = new ReportController();
        $controller->downloadWithdrawalReport();
        break;
        
    case 'bank':
        $controller = new BankController();
        $controller->index();
        break;

    case 'bank/create':
        $controller = new BankController();
        $controller->create();
        break;

    case 'bank/store':
        $controller = new BankController();
        $controller->store();
        break;

    case 'bank/details':
        $controller = new BankController();
        $controller->details();
        break;

    case 'user':
        $controller = new UserController();
        $controller->index();
        break;

    case 'user/addUser':
        $controller = new UserController();
        $controller->addUser();
        break;
        
    case 'user/storeUser':
        $controller = new UserController();
        $controller->storeUser();
        break;

    case 'user/editUser':
        $controller = new UserController();
        $controller->editUser();
        break;
    
    case 'user/updateUser':
        $controller = new UserController();
        $controller->updateUser();
        break;
    
    case 'user/deleteUser':
        $controller = new UserController();
        $controller->deleteUser();
        break;
    
    case 'role':
        require_once 'controllers/RoleController.php';
        $controller = new RoleController();
        $controller->index();
        break;

    case 'user_profile':
        require_once 'controllers/UserProfileController.php';
        $controller = new UserProfileController();
        $controller->index();
        break;
        
    case 'user_profile/edit':
        require_once 'controllers/UserProfileController.php';
        $controller = new UserProfileController();
        $controller->edit();
        break;
        
    case 'user_profile/update':
        require_once 'controllers/UserProfileController.php';
        $controller = new UserProfileController();
        $controller->update();
        break;

    default:
        echo '404 - Page Not Found';
        break;
}
?>
