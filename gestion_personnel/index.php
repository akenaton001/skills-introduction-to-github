<?php
session_start();

require_once __DIR__ . '/controllers/EmployeeController.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/DisputeController.php';
require_once __DIR__ . '/controllers/IncidentController.php';

$action = $_GET['action'] ?? 'index';

// Redirection vers le login si non connecté
if (!isset($_SESSION['user_id']) && $action !== 'login') {
    header("Location: index.php?action=login");
    exit;
}

switch ($action) {
    case 'login':
        $authController = new AuthController();
        $authController->login();
        break;

    case 'logout':
        $authController = new AuthController();
        $authController->logout();
        break;

    case 'create':
        $employeeController = new EmployeeController();
        $employeeController->create();
        break;

    case 'edit':
        $employeeController = new EmployeeController();
        $employeeController->edit();
        break;

    case 'show': // Visualisation des détails
        $employeeController = new EmployeeController();
        $employeeController->show();
        break;

    case 'import': // Affichage du formulaire d'import
        $employeeController = new EmployeeController();
        $employeeController->import();
        break;

    case 'processImport': // Traitement du fichier CSV soumis
        $employeeController = new EmployeeController();
        $employeeController->processImport();
        break;

    case 'downloadTemplate': // Téléchargement du modèle CSV
        $employeeController = new EmployeeController();
        $employeeController->downloadTemplate();
        break;

    case 'delete':
        $employeeController = new EmployeeController();
        $employeeController->delete();
        break;

    // --- MODULE CONTENTIEUX ---
    case 'disputes':
        $disputeController = new DisputeController();
        $disputeController->index();
        break;

    case 'disputes_create':
        $disputeController = new DisputeController();
        $disputeController->create();
        break;

    case 'disputes_store':
        $disputeController = new DisputeController();
        $disputeController->store();
        break;

    case 'disputes_show':
        $disputeController = new DisputeController();
        $disputeController->show();
        break;

    case 'disputes_edit':
        $disputeController = new DisputeController();
        $disputeController->edit();
        break;

    case 'disputes_update':
        $disputeController = new DisputeController();
        $disputeController->update();
        break;

    case 'disputes_update_decision':
        $disputeController = new DisputeController();
        $disputeController->updateDecision();
        break;

    case 'disputes_update_document':
        $disputeController = new DisputeController();
        $disputeController->updateDocument();
        break;

    case 'disputes_delete':
        $disputeController = new DisputeController();
        $disputeController->delete();
        break;

    // --- MODULE INCIDENTS ---
    case 'incidents':
        $incidentController = new IncidentController();
        $incidentController->index();
        break;

    case 'incidents_create':
        $incidentController = new IncidentController();
        $incidentController->create();
        break;

    case 'incidents_store':
        $incidentController = new IncidentController();
        $incidentController->store();
        break;

    case 'incidents_show':
        $incidentController = new IncidentController();
        $incidentController->show();
        break;

    case 'incidents_edit':
        $incidentController = new IncidentController();
        $incidentController->edit();
        break;

    case 'incidents_update':
        $incidentController = new IncidentController();
        $incidentController->update();
        break;

    case 'incidents_delete':
        $incidentController = new IncidentController();
        $incidentController->delete();
        break;

    case 'index':
    default:
        $employeeController = new EmployeeController();
        $employeeController->index();
        break;
}