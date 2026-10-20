<?php
// modules/auth/modifier_statut_utilisateur.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/bd.php';

// Sécurité : Seul un SuperAdmin a le droit d'être ici
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'SuperAdmin') {
    echo json_encode(['success' => false, 'message' => 'Accès refusé.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_utilisateur = isset($_POST['id_utilisateur']) ? (int)$_POST['id_utilisateur'] : 0;
    $action = isset($_POST['action']) ? trim($_POST['action']) : '';
    $valeur = isset($_POST['valeur']) ? trim($_POST['valeur']) : '';

    // Sécurité critique : On empêche l'utilisateur connecté de s'auto-modifier
    if ($id_utilisateur === (int)$_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'Action impossible : vous ne pouvez pas modifier vos propres privilèges ou désactiver votre propre compte depuis cette interface.']);
        exit();
    }

    try {
        // ACTION 1 : Modification du rôle
        if ($action === 'role') {
            // Validation du rôle pour éviter l'injection de rôles fantômes
            $roles_autorises = ['SuperAdmin', 'DAGE_RH', 'Secrétaire', 'Agent'];
            if (!in_array($valeur, $roles_autorises)) {
                echo json_encode(['success' => false, 'message' => 'Rôle non valide.']);
                exit();
            }

            $stmt = $pdo->prepare("UPDATE utilisateurs SET role = :role WHERE id_utilisateur = :id");
            $stmt->execute([':role' => $valeur, ':id' => $id_utilisateur]);
            echo json_encode(['success' => true, 'message' => 'Rôle mis à jour avec succès.']);
            exit();

        // ACTION 2 : Activation / Suspension du compte
        } elseif ($action === 'statut') {
            $nouvel_etat = ($valeur === '1') ? 1 : 0;
            
            $stmt = $pdo->prepare("UPDATE utilisateurs SET actif = :actif WHERE id_utilisateur = :id");
            $stmt->execute([':actif' => $nouvel_etat, ':id' => $id_utilisateur]);
            echo json_encode(['success' => true, 'message' => $nouvel_etat === 1 ? 'Compte activé.' : 'Compte désactivé.']);
            exit();

        // ACTION 3 : Réinitialisation du mot de passe (Mis à jour avec "passer" et forçage)
        } elseif ($action === 'reinitialiser_pwd') {
            $mdp_par_defaut = 'passer'; 
            $mot_de_passe_hashe = password_hash($mdp_par_defaut, PASSWORD_BCRYPT);

            // On met à jour le mot de passe ET on passe doit_changer_mdp à 1
            $stmt = $pdo->prepare("UPDATE utilisateurs SET password = :password, doit_changer_mdp = 1 WHERE id_utilisateur = :id");
            $stmt->execute([
                ':password' => $mot_de_passe_hashe, 
                ':id' => $id_utilisateur
            ]);

            echo json_encode([
                'success' => true, 
                'message' => "Le mot de passe a été réinitialisé ! Code temporaire : <strong class='text-danger'>passer</strong> (Changement obligatoire requis à la prochaine connexion)."
            ]);
            exit();
        }

        echo json_encode(['success' => false, 'message' => 'Action inconnue.']);
        exit();

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour en base de données.']);
        exit();
    }
}