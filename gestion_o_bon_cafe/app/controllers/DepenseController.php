<?php
require_once '../app/models/Depense.php';
require_once '../core/Database.php';

class DepenseController {
    private $db;
    private $depenseModel;

    public function __construct() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: /gestion_o_bon_cafe/public/index.php?controller=auth&action=login");
            exit();
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->depenseModel = new Depense($this->db);
    }

    public function index() {
        // Dates par défaut : le mois en cours
        $date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : date('Y-m-01');
        $date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : date('Y-m-t');

        // Traitement des raccourcis de tri rapide
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

        // Ajout d'une nouvelle dépense via le formulaire
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $libelle = trim($_POST['libelle']);
            $montant = floatval($_POST['montant']);
            $type_depense = $_POST['type_depense'];
            $date_depense = $_POST['date_depense'];

            if(!empty($libelle) && $montant > 0) {
                $this->depenseModel->ajouter($libelle, $montant, $type_depense, $date_depense);
                header("Location: /gestion_o_bon_cafe/public/index.php?controller=depense&action=index&succes=1");
                exit();
            }
        }

        // Récupération des dépenses filtrées
        $depenses = $this->depenseModel->listerTous($date_debut, $date_fin);

        $totalDepenses = 0;
        foreach($depenses as $d) {
            $totalDepenses += $d['montant'];
        }

        require_once '../app/views/depenses/index.php';
    }

    public function supprimer() {
        if(isset($_GET['id'])) {
            $this->depenseModel->supprimer($_GET['id']);
        }
        header("Location: /gestion_o_bon_cafe/public/index.php?controller=depense&action=index");
        exit();
    }
}