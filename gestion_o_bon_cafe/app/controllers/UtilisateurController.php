<?php
require_once '../app/models/Utilisateur.php';
require_once '../core/Database.php';

class UtilisateurController {
    private $db;
    private $userModel;

    public function __construct() {
        // Vérification de sécurité : Seul l'admin accède à la gestion des utilisateurs
        if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
            header("Location: /gestion_o_bon_cafe/public/index.php?controller=rapport&action=index");
            exit();
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->userModel = new Utilisateur($this->db);
    }

    public function index() {
        $utilisateurs = $this->userModel->listerTous();
        require_once '../app/views/utilisateurs/index.php';
    }

    public function creer() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nom = trim($_POST['nom']);
            $email = trim($_POST['email']);
            $mot_de_passe = trim($_POST['mot_de_passe']);
            $role = $_POST['role'];

            if(!empty($nom) && !empty($email) && !empty($mot_de_passe)) {
                $this->userModel->ajouter($nom, $email, $mot_de_passe, $role);
                header("Location: /gestion_o_bon_cafe/public/index.php?controller=utilisateur&action=index");
                exit();
            }
        }
        require_once '../app/views/utilisateurs/creer.php';
    }

    public function supprimer() {
        if(isset($_GET['id'])) {
            $this->userModel->supprimer($_GET['id']);
        }
        header("Location: /gestion_o_bon_cafe/public/index.php?controller=utilisateur&action=index");
        exit();
    }
}