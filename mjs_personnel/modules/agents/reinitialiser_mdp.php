<?php
// modules/agents/reinitialiser_mdp.php

// 1. Activation des rapports d'erreurs au cas où
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. En-tête JSON obligatoire
header('Content-Type: application/json; charset=utf-8');

// 3. Inclusion de la base de données
require_once __DIR__ . '/../../config/bd.php';

// 4. Contrôle de sécurité strict
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['SuperAdmin', 'DAGE_RH'])) {
    echo json_encode(['success' => false, 'message' => 'Accès refusé. Autorisation insuffisante.']);
    exit();
}

// 5. Traitement de la requête
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // CORRIGÉ : $_POST avec le tiret du bas
    $id_user = isset($_POST['id_utilisateur']) ? (int)$_POST['id_utilisateur'] : 0;

    if ($id_user <= 0) {
        echo json_encode(['success' => false, 'message' => 'Identifiant utilisateur invalide ou manquant.']);
        exit();
    }

    try {
        // Génération du mot de passe sécurisé
        $nouveau_hash = password_hash('Passer123', PASSWORD_BCRYPT);

        // Requête préparée
        $stmt = $pdo->prepare("UPDATE utilisateurs SET password_hash = :hash WHERE id_utilisateur = :id");
        $stmt->execute([
            ':hash' => $nouveau_hash,
            ':id'   => $id_user
        ]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Le mot de passe a été réinitialisé avec succès à : Passer123']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Aucun changement effectué. Le compte existe-t-il ?']);
        }
        exit();

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur SQL : ' . $e->getMessage()]);
        exit();
    }

} else {
    // Sécurité si accès direct par URL
    header('Location: ../../views/agents_liste.php');
    exit();
}