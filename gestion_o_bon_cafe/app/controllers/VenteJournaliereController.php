<?php
require_once '../app/models/VenteJournaliere.php';
require_once '../app/models/Categorie.php';
require_once '../core/Database.php';

class VenteJournaliereController {
    private $db;
    private $venteModel;
    private $categorieModel;

    public function __construct() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: /gestion_o_bon_cafe/public/index.php?controller=auth&action=login");
            exit();
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->venteModel = new VenteJournaliere($this->db);
        $this->categorieModel = new Categorie($this->db);
    }

    public function saisir() {
        $date_selectionnee = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
        $categories = $this->categorieModel->listerTous();
        $ventesExistantes = $this->venteModel->getDetailsParDate($date_selectionnee);

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $date_vente = $_POST['date_vente'];
            $recettes = isset($_POST['recettes']) ? $_POST['recettes'] : [];

            if($this->venteModel->enregistrerBilan($date_vente, $recettes)) {
                header("Location: /gestion_o_bon_cafe/public/index.php?controller=vente&action=saisir&date=" . $date_vente . "&succes=1");
                exit();
            }
        }

        require_once '../app/views/ventes/saisir.php';
    }
}