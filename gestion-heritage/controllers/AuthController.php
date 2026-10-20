<?php
class AuthController {
    public function showLogin() {
        include 'views/auth/login.php';
    }

    public function login($db) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if ($username === 'admin' && $password === 'admin123') {
                $_SESSION['admin_logged'] = true;
                header('Location: index.php?action=dashboard');
                exit();
            } else {
                $error = "Identifiant ou mot de passe incorrect.";
                include 'views/auth/login.php';
            }
        }
    }

    public function logout() {
        session_destroy();
        header('Location: index.php?action=login');
        exit();
    }
}
?>