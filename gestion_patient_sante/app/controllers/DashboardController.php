<?php
class DashboardController {
    
    public function index() {
        // Sécurité : Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }
        
        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}