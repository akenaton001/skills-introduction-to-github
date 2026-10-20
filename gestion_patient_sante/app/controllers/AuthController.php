<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    
    public function showLogin() {
        // Si l'utilisateur est déjà connecté, on le redirige vers le tableau de bord
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?action=dashboard');
            exit();
        }
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (!empty($email) && !empty($password)) {
                $userModel = new User();
                $user = $userModel->findByEmail($email);

                if ($user && password_verify($password, $user['mot_de_passe'])) {
                    // Régénération de l'ID de session pour contrer la fixation de session
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_nom'] = $user['nom'];
                    $_SESSION['user_prenom'] = $user['prenom'];
                    $_SESSION['user_role'] = $user['role'];

                    header('Location: index.php?action=dashboard');
                    exit();
                }
            }

            $_SESSION['error'] = "Email ou mot de passe incorrect.";
            header('Location: index.php?action=login');
            exit();
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header('Location: index.php?action=login');
        exit();
    }
}