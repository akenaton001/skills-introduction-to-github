<?php
require_once '../app/models/Utilisateur.php';
require_once '../core/Database.php';

class AuthController {
    private $db;
    private $userModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->userModel = new Utilisateur($this->db);
    }

    public function login() {
        if(isset($_SESSION['user_id'])) {
            header("Location: /gestion_o_bon_cafe/public/index.php?controller=rapport&action=index");
            exit();
        }
        require_once '../app/views/auth/login.php';
    }

    public function traiterConnexion() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $identifiant = trim($_POST['email']);
            $password = trim($_POST['mot_de_passe']);

            $user = $this->userModel->connecter($identifiant, $password);

            if($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_role'] = $user['role'];

                header("Location: /gestion_o_bon_cafe/public/index.php?controller=rapport&action=index");
                exit();
            } else {
                $erreur = "Identifiant ou mot de passe incorrect.";
                require_once '../app/views/auth/login.php';
            }
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: /gestion_o_bon_cafe/public/index.php?controller=auth&action=login");
        exit();
    }
}