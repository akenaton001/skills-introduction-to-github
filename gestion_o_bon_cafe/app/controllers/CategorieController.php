<?php
require_once '../app/models/Categorie.php';
require_once '../core/Database.php';

class CategorieController {
    private $db;
    private $categorieModel;

    public function __construct() {
        // Sécurité : Seul l'admin peut gérer les catégories
        if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
            header("Location: /gestion_o_bon_cafe/public/index.php?controller=rapport&action=index");
            exit();
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->categorieModel = new Categorie($this->db);
    }

    public function index() {
        $categories = $this->categorieModel->listerTous();
        require_once '../app/views/categories/index.php';
    }

    public function creer() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nom = trim($_POST['nom']);
            $description = trim($_POST['description']);

            if(!empty($nom)) {
                $this->categorieModel->ajouter($nom, $description);
                header("Location: /gestion_o_bon_cafe/public/index.php?controller=categorie&action=index");
                exit();
            }
        }
        require_once '../app/views/categories/creer.php';
    }

    public function supprimer() {
        if(isset($_GET['id'])) {
            $this->categorieModel->supprimer($_GET['id']);
        }
        header("Location: /gestion_o_bon_cafe/public/index.php?controller=categorie&action=index");
        exit();
    }
}