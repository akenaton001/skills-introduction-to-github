<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/user.php';

class AuthController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    public function login() {
        // Si l'utilisateur est déjà connecté, on le redirige directement vers l'accueil
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (!empty($email) && !empty($password)) {
                $userData = $this->user->findByEmail($email);

                if ($userData && password_verify($password, $userData['password'])) {
                    // Authentification réussie
                    $_SESSION['user_id'] = $userData['id'];
                    $_SESSION['user_nom'] = $userData['nom'];
                    $_SESSION['user_email'] = $userData['email'];

                    $_SESSION['flash'] = [
                        'type' => 'success',
                        'message' => 'Connexion réussie. Bienvenue ' . htmlspecialchars($userData['nom']) . ' !'
                    ];

                    header("Location: index.php");
                    exit;
                } else {
                    $_SESSION['flash'] = [
                        'type' => 'danger',
                        'message' => 'E-mail ou mot de passe incorrect.'
                    ];
                }
            } else {
                $_SESSION['flash'] = [
                    'type' => 'danger',
                    'message' => 'Veuillez remplir tous les champs.'
                ];
            }
        }

        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function logout() {
        session_unset();
        session_destroy();
        session_start();
        
        $_SESSION['flash'] = [
            'type' => 'info',
            'message' => 'Vous avez été déconnecté avec succès.'
        ];
        
        header("Location: index.php?action=login");
        exit;
    }
}