<?php
require_once '../app/models/Rapport.php';
require_once '../app/models/Depense.php';
require_once '../core/Database.php';

class RapportController {
    private $db;
    private $rapportModel;

    public function __construct() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: /gestion_o_bon_cafe/public/index.php?controller=auth&action=login");
            exit();
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->rapportModel = new Rapport($this->db);
    }

    // 1. Le Tableau de Bord d'accueil (Page principale après connexion)
    public function index() {
        $bilans = $this->rapportModel->listerBilans();
        
        $totalRecettes = 0;
        foreach($bilans as $b) {
            $totalRecettes += $b['recette_totale'];
        }

        // Récupération et calcul dynamique des dépenses globales
        $depenseModel = new Depense($this->db);
        $toutesDepenses = $depenseModel->listerTous();
        
        $totalDepenses = 0;
        foreach($toutesDepenses as $d) {
            $totalDepenses += $d['montant'];
        }

        $beneficeNet = $totalRecettes - $totalDepenses;

        require_once '../app/views/rapports/dashboard.php';
    }

    // 2. L'historique détaillé des ventes / Suivi avec filtres temporels
    public function historique() {
        $date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : date('Y-m-01');
        $date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : date('Y-m-t');

        if(isset($_GET['periode'])) {
            if($_GET['periode'] === 'semaine') {
                $date_debut = date('Y-m-d', strtotime('monday this week'));
                $date_fin = date('Y-m-d', strtotime('sunday this week'));
            } elseif($_GET['periode'] === 'mois') {
                $date_debut = date('Y-m-01');
                $date_fin = date('Y-m-t');
            } elseif($_GET['periode'] === 'annee') {
                $date_debut = date('Y-01-01');
                $date_fin = date('Y-12-31');
            }
        }

        $bilans = $this->rapportModel->listerBilansFiltres($date_debut, $date_fin);
        
        $caTotalGlobal = 0;
        foreach($bilans as $b) {
            $caTotalGlobal += $b['recette_totale'];
        }

        require_once '../app/views/rapports/index.php';
    }

    // Supprimer un bilan (Réservé à l'admin)
    public function supprimer() {
        if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin' && isset($_GET['id'])) {
            $this->rapportModel->supprimerBilan($_GET['id']);
        }
        header("Location: /gestion_o_bon_cafe/public/index.php?controller=rapport&action=historique");
        exit();
    }
}