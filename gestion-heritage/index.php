<?php
session_start();
require_once 'config/Database.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/DashboardController.php';
require_once 'controllers/TransactionController.php';
require_once 'controllers/ReportController.php';

$database = new Database();
$db = $database->getConnection();

$action = isset($_GET['action']) ? $_GET['action'] : 'login';

// Gestion de la connexion et routage
if (!isset($_SESSION['admin_logged']) && !in_array($action, ['login', 'authenticate'])) {
    $action = 'login';
}

switch ($action) {
    case 'login':
        (new AuthController())->showLogin();
        break;
    case 'authenticate':
        (new AuthController())->login($db);
        break;
    case 'logout':
        (new AuthController())->logout();
        break;
    case 'dashboard':
        (new DashboardController($db))->index();
        break;
    case 'create':
        (new TransactionController($db))->create();
        break;
    case 'store':
        (new TransactionController($db))->store();
        break;
    case 'list':
        (new TransactionController($db))->index();
        break;
    case 'edit': // <-- AJOUTÉ
        (new TransactionController($db))->edit();
        break;
    case 'update': // <-- AJOUTÉ
        (new TransactionController($db))->update();
        break;
    case 'delete': // <-- AJOUTÉ
        (new TransactionController($db))->delete();
        break;
    case 'report_rubrique':
        (new ReportController($db))->rubrique();
        break;
    case 'report_journalier':
        (new ReportController($db))->journalier();
        break;
    case 'report_beneficiaire':
        (new ReportController($db))->beneficiaire();
        break;
    default:
        (new DashboardController($db))->index();
        break;
}
?>