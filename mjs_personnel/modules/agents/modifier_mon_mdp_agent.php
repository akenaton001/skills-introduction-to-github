<?php
// modules/agents/modifier_mon_mdp_agent.php

// 1. Forcer l'affichage des erreurs en cas de pépin
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Vérifier que l'utilisateur est bien connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: /mjs_personnel/index.php');
    exit();
}

require_once __DIR__ . '/../../config/bd.php';

$erreur = '';
$succes = '';
$premiere_connexion = isset($_GET['premiere_connexion']) && $_GET['premiere_connexion'] == 1;

// 3. Traitement du formulaire lors de la soumission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_utilisateur = $_SESSION['user_id'];
    $ancien_mdp = isset($_POST['ancien_mdp']) ? trim($_POST['ancien_mdp']) : '';
    $nouveau_mdp = isset($_POST['nouveau_mdp']) ? trim($_POST['nouveau_mdp']) : '';
    $confirmer_mdp = isset($_POST['confirmer_mdp']) ? trim($_POST['confirmer_mdp']) : '';

    if (empty($ancien_mdp) || empty($nouveau_mdp) || empty($confirmer_mdp)) {
        $erreur = "Tous les champs sont obligatoires.";
    } elseif ($nouveau_mdp !== $confirmer_mdp) {
        $erreur = "Le nouveau mot de passe et sa confirmation ne correspondent pas.";
    } elseif (strlen($nouveau_mdp) < 6) {
        $erreur = "Le nouveau mot de passe doit contenir au moins 6 caractères.";
    } else {
        try {
            // Récupérer le mot de passe actuel en BDD
            $stmt = $pdo->prepare("SELECT password_hash FROM utilisateurs WHERE id_utilisateur = :id");
            $stmt->execute([':id' => $id_utilisateur]);
            $user = $stmt->fetch();

            if ($user && password_verify($ancien_mdp, $user['password_hash'])) {
                // Mettre à jour le mot de passe ET passer doit_changer_mdp à 0
                $nouveau_hash = password_hash($nouveau_mdp, PASSWORD_BCRYPT);
                $update = $pdo->prepare("UPDATE utilisateurs SET password_hash = :hash, doit_changer_mdp = 0 WHERE id_utilisateur = :id");
                $update->execute([
                    ':hash' => $nouveau_hash,
                    ':id' => $id_utilisateur
                ]);

                // Mettre à jour la variable de session pour libérer l'accès aux autres pages
                $_SESSION['doit_changer_mdp'] = 0;
                $succes = "Votre mot de passe a été modifié avec succès !";
                
                // Redirection après 2 secondes vers le tableau de bord
                header("Refresh: 2; URL=/mjs_personnel/views/dashboard.php");
            } else {
                $erreur = "L'ancien mot de passe est incorrect.";
            }
        } catch (PDOException $e) {
            $erreur = "Erreur technique : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modification du mot de passe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card-mdp { width: 100%; max-width: 450px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="card card-mdp p-4">
    <div class="card-body">
        <h4 class="card-title text-center mb-3">Sécuriser votre compte</h4>
        
        <?php if ($premiere_connexion && empty($succes)): ?>
            <div class="alert alert-warning text-center small">
                <strong>Première connexion :</strong> Pour des raisons de sécurité, vous devez personnaliser votre mot de passe initial avant d'accéder à l'application.
            </div>
        <?php endif; ?>

        <?php if (!empty($erreur)): ?>
            <div class="alert alert-danger small"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <?php if (!empty($succes)): ?>
            <div class="alert alert-success text-center small">
                <?= htmlspecialchars($succes) ?><br>
                <span class="text-muted">Redirection en cours...</span>
            </div>
        <?php endif; ?>

        <?php if (empty($succes)): ?>
            <form action="" method="POST" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Mot de passe actuel / Provisoire</label>
                    <input type="password" name="ancien_mdp" class="form-content form-control" required placeholder="Saisissez votre mot de passe actuel">
                </div>
                
                <hr>
                
                <div class="mb-3">
                    <label class="form-label">Nouveau mot de passe</label>
                    <input type="password" name="nouveau_mdp" class="form-control" required placeholder="Minimum 6 caractères">
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirmer le nouveau mot de passe</label>
                    <input type="password" name="confirmer_mdp" class="form-control" required placeholder="Répétez le nouveau mot de passe">
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-2">Enregistrer le mot de passe</button>
            </form>
        <?php endif; ?>
    </div>
</div>

</body>
</html>