<?php
// modules/auth/deconnexion.php

// 1. Démarrer la session pour pouvoir y accéder et la détruire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Vider toutes les variables de session
$_SESSION = array();

// 3. Si un cookie de session existe, le détruire également (Recommandé pour la sécurité)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 4. Détruire définitivement la session sur le serveur
session_destroy();

// 5. Rediriger l'utilisateur vers la page de connexion à la racine
header("Location: /mjs_personnel/index.php");
exit();