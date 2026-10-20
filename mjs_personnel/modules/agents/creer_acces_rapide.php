<?php
// modules/agents/creer_acces_rapide.php

// 1. Forcer l'affichage de toutes les erreurs PHP cachées
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nettoyer les tampons de sortie pour s'assurer qu'aucun espace blanc parasite ne casse le JSON
while (ob_get_level()) {
    ob_end_clean();
}

// Entête HTTP stricte pour le format de réponse
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/bd.php';

// Sécurité : Contrôle d'accès temporairement assoupli pour le test si nécessaire, 
// mais vérifie bien si tes variables de session correspondent.
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['SuperAdmin', 'DAGE_RH'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Accès refusé. Votre rôle de session actuel est : ' . ($_SESSION['user_role'] ?? 'Aucun')
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_agent = isset($_POST['id_agent']) ? (int)$_POST['id_agent'] : 0;
    $role = isset($_POST['role']) ? trim($_POST['role']) : 'Agent';

    if ($id_agent <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID Agent invalide reçu par le serveur.']);
        exit();
    }

    try {
        // 1. Vérifier si l'agent a déjà un compte utilisateur
        $stmtCheck = $pdo->prepare("SELECT id_utilisateur FROM utilisateurs WHERE id_agent = :id");
        $stmtCheck->execute([':id' => $id_agent]);
        $compteExistant = $stmtCheck->fetch();

        if ($compteExistant) {
            // MODE MISE À JOUR : On change juste son rôle
            $stmtUpdate = $pdo->prepare("UPDATE utilisateurs SET role = :role WHERE id_agent = :id");
            $stmtUpdate->execute([
                ':role' => $role,
                ':id'   => $id_agent
            ]);

            echo json_encode(['success' => true, 'message' => "Le rôle de l'agent a été mis à jour avec succès (Nouveau rôle : $role)."]);
            exit();

        } else {
            // MODE CRÉATION : L'agent n'a pas de compte, on le génère
            $stmtAgent = $pdo->prepare("SELECT matricule_solde FROM agents WHERE id_agent = :id");
            $stmtAgent->execute([':id' => $id_agent]);
            $agent = $stmtAgent->fetch();

            if (!$agent) {
                echo json_encode(['success' => false, 'message' => 'Agent introuvable dans la table agents (ID: ' . $id_agent . ').']);
                exit();
            }

            $matricule = trim($agent['matricule_solde']);
            if (empty($matricule)) {
                echo json_encode(['success' => false, 'message' => 'Impossible de créer un accès : le matricule de cet agent est vide en base de données.']);
                exit();
            }

            // Configuration des identifiants (Harmonisation : Identifiant et mot de passe initial basés sur le matricule)
            $username = $matricule;
            $password_initial = strtolower($username);

            // Vérifier si ce username exact est déjà pris par quelqu'un d'autre
            $stmtCheckUsername = $pdo->prepare("SELECT id_utilisateur FROM utilisateurs WHERE username = :username");
            $stmtCheckUsername->execute([':username' => $username]);
            if ($stmtCheckUsername->fetch()) {
                echo json_encode(['success' => false, 'message' => "L'identifiant '$username' est déjà associé à un autre compte utilisateur."]);
                exit();
            }

            // Génération sécurisée du Hash du mot de passe
            $hash = password_hash($password_initial, PASSWORD_BCRYPT);

            // Requête d'insertion explicite, s'alignant sur mjs_personnel_db
            // Utilisation d'un entier 1 pour le statut actif
            $stmtUser = $pdo->prepare("INSERT INTO utilisateurs (username, password_hash, role, id_agent, actif, doit_changer_mdp) VALUES (:user, :hash, :role, :id, 1, 1)");
            
            $stmtUser->execute([
                ':user' => $username,
                ':hash' => $hash,
                ':role' => $role,
                ':id'   => $id_agent
            ]);

            echo json_encode([
                'success' => true, 
                'message' => "Compte d'accès créé avec succès ! Identifiant : " . $username . " (Mot de passe initial identique au matricule en minuscules)."
            ]);
            exit();
        }

    } catch (PDOException $e) {
        // Renvoie l'erreur SQL exacte pour comprendre si une colonne coince
        echo json_encode([
            'success' => false, 
            'message' => 'Erreur SQL fatale du serveur : ' . $e->getMessage()
        ]);
        exit();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode de requête non autorisée. Requis: POST.']);
    exit();
}