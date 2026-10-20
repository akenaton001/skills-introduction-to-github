<?php
// config/securite.php

/**
 * Fonction pour restreindre l'accès en fonction du rôle de l'utilisateur
 * @param array $roles_autorises Un tableau contenant les rôles permis (ex: ['SuperAdmin', 'DAGE_RH'])
 */
function restreindre_acces_a($roles_autorises) {
    // On s'assure que la session est démarrée
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Vérifie si l'utilisateur est connecté et possède l'un des rôles autorisés
    if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $roles_autorises)) {
        // Redirection vers une page d'erreur ou le login si l'accès est refusé
        $_SESSION['erreur'] = "Vous n'avez pas les droits nécessaires pour accéder à cette fonctionnalité.";
        header('Location: /mjs_personnel/index.php');
        exit();
    }
}
?>