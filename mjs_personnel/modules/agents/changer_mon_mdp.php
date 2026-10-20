<?php
// modules/agents/changer_mon_mdp.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/bd.php';

// Sécurité : l'utilisateur doit être connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Session expirée. Veuillez vous reconnecter.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = (int)$_SESSION['user_id'];
    $ancien_mdp = isset($_POST['ancien_mdp']) ? trim($_POST['ancien_mdp']) : '';
    $nouveau_mdp = isset($_POST['nouveau_mdp']) ? trim($_POST['nouveau_mdp']) : '';
    $confirmer_mdp = isset($_POST['confirmer_mdp']) ? trim($_POST['confirmer_mdp']) : '';

    // Validations de base
    if (empty($ancien_mdp) || empty($nouveau_mdp) || empty($confirmer_mdp)) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires.']);
        exit();
    }

    if ($nouveau_mdp !== $confirmer_mdp) {
        echo json_encode(['success' => false, 'message' => 'Le nouveau mot de passe et sa confirmation ne correspondent pas.']);
        exit();
    }

    if (strlen($nouveau_mdp) < 6) {
        echo json_encode(['success' => false, 'message' => 'Le nouveau mot de passe doit contenir au moins 6 caractères.']);
        exit();
    }

    try {
        // Récupérer le mot de passe actuel en BDD pour vérification
        $stmt = $pdo->prepare("SELECT password_hash FROM utilisateurs WHERE id_utilisateur = :id");
        $stmt->execute([':id' => $id_user]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($ancien_mdp, $user['password_hash'])) {
            echo json_encode(['success' => false, 'message' => 'L\'ancien mot de passe est incorrect.']);
            exit();
        }

        // Empêcher de remettre "Passer123" pour des raisons de sécurité
        if ($nouveau_mdp === 'Passer123') {
            echo json_encode(['success' => false, 'message' => 'Veuillez choisir un mot de passe plus sécurisé que le mot de passe par défaut.']);
            exit();
        }

        // Tout est bon : hachage et mise à jour
        $nouveau_hash = password_hash($nouveau_mdp, PASSWORD_BCRYPT);
        $update = $pdo->prepare("UPDATE utilisateurs SET password_hash = :hash WHERE id_utilisateur = :id");
        $update->execute([
            ':hash' => $nouveau_hash,
            ':id'   => $id_user
        ]);

        echo json_encode(['success' => true, 'message' => 'Votre mot de passe a été modifié avec succès !']);
        exit();

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur technique lors de la mise à jour.']);
        exit();
    }
}