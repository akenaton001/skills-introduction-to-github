<?php
// views/utilisateur_profil.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/bd.php';

// Sécurité : Réservé exclusivement au SuperAdmin
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'SuperAdmin') {
    header('Location: /mjs_personnel/views/dashboard.php');
    exit();
}

$id_user = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$erreur = '';

// Traitement de la réinitialisation du mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_password') {
    $new_password = trim($_POST['new_password']);
    
    if (strlen($new_password) >= 6) {
        try {
            $hash = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE utilisateurs SET password_hash = :pass WHERE id_utilisateur = :id");
            $stmt->execute([':pass' => $hash, ':id' => $id_user]);
            $message = "Le mot de passe a été modifié avec succès !";
        } catch (PDOException $e) {
            $erreur = "Erreur technique lors de la modification du mot de passe.";
        }
    } else {
        $erreur = "Le mot de passe doit contenir au moins 6 caractères.";
    }
}

// Récupération des données du profil de l'utilisateur
try {
		$stmt = $pdo->prepare("
		SELECT u.*, 
			   a.prenom, 
			   a.nom, 
			   a.email_professionnel AS agent_email, 
			   a.matricule_solde, 
			   s.nom_structure AS service_affectation
		FROM utilisateurs u
		LEFT JOIN agents a ON u.id_agent = a.id_agent
		LEFT JOIN structures s ON a.id_structure_actuelle = s.id_structure
		WHERE u.id_utilisateur = :id
		LIMIT 1
	");
    $stmt->execute([':id' => $id_user]);
    $user_profile = $stmt->fetch();

    if (!$user_profile) {
        die("Utilisateur introuvable.");
    }
} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Utilisateur - Administration MJS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sn-green: #008751;
            --sn-yellow: #FCD116;
            --sn-red: #E31B23;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            color: #1e293b;
            min-height: 100vh;
        }
        .navbar-custom {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 3px solid var(--sn-green);
            padding: 0.5rem 1.5rem;
        }
        
        .avatar-wrapper {
            position: relative;
            width: 110px;
            height: 110px;
            margin: 0 auto 1rem;
            cursor: pointer;
            overflow: hidden;
            border-radius: 50%;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            border: 3px solid #fff;
        }
        .avatar-image, .avatar-initials {
            width: 100%; height: 100%; object-fit: cover;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.5rem; font-weight: bold; color: white;
            background-color: var(--sn-green); transition: transform 0.3s ease;
        }
        .avatar-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6); display: flex; flex-direction: column;
            align-items: center; justify-content: center; color: white;
            opacity: 0; transition: opacity 0.3s ease; font-size: 0.75rem;
        }
        .avatar-wrapper:hover .avatar-overlay { opacity: 1; }
        
        .nav-pills .nav-link {
            color: #475569;
            font-weight: 500;
            padding: 0.6rem 1.2rem;
            border-radius: 30px;
            transition: all 0.2s ease;
        }
        .nav-pills .nav-link.active, .nav-pills .nav-link:hover {
            background-color: var(--sn-green) !important;
            color: white !important;
        }
        .info-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            font-weight: 600;
        }
        .info-value {
            font-size: 1rem;
            color: #0f172a;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="container mt-4">
        <div class="mb-3">
            <a href="utilisateurs_liste.php" class="btn btn-sm btn-light border rounded-pill px-3 text-muted">
                <i class="fa-solid fa-arrow-left me-1"></i> Retour à la liste
            </a>
        </div>

        <!-- Alertes de confirmation -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (!empty($erreur)): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i> <?= $erreur ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Fiche d'identité de l'utilisateur -->
            <div class="col-lg-7">
                <div class="card profile-card shadow-sm p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="avatar-box text-primary">
                            <i class="fa-solid fa-user-gear fa-2x"></i>
                        </div>
                        <div>
                            <h3 class="h5 fw-bold text-dark mb-1"><?= htmlspecialchars($user_profile['prenom'] . ' ' . $user_profile['nom']) ?></h3>
                            <p class="text-muted small mb-0">Identifiant système : <span class="font-monospace fw-bold text-primary">@<?= htmlspecialchars($user_profile['username']) ?></span></p>
                        </div>
                    </div>

                    <hr class="text-muted">

                    <h5 class="fw-bold text-secondary mb-3" style="font-size: 0.85rem; text-transform: uppercase;">Informations de Compte</h5>
                    <table class="table table-borderless align-middle small mb-4">
                        <tr>
                            <td class="text-muted" style="width: 40%;">Rôle Applicatif :</td>
                            <td class="fw-bold">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                    <?= htmlspecialchars($user_profile['role']) ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Statut du Compte :</td>
                            <td>
                                <?php if ((int)$user_profile['actif'] === 1): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fa-solid fa-circle text-success me-1" style="font-size:0.5rem;"></i> Actif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="fa-solid fa-circle text-danger me-1" style="font-size:0.5rem;"></i> Suspendu</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>

                    <h5 class="fw-bold text-secondary mb-3" style="font-size: 0.85rem; text-transform: uppercase;">Liaison Agent Ministériel</h5>
                    <table class="table table-borderless align-middle small mb-0">
                        <tr>
                            <td class="text-muted" style="width: 40%;">Matricule de Solde :</td>
                            <td class="fw-semibold font-monospace"><?= htmlspecialchars($user_profile['matricule_solde'] ?? 'Non lié') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Affectation / Service :</td>
                            <td class="fw-semibold"><?= htmlspecialchars($user_profile['service_affectation'] ?? 'Non renseigné') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Adresse Email :</td>
                            <td><a href="mailto:<?= htmlspecialchars($user_profile['agent_email']) ?>" class="text-decoration-none"><?= htmlspecialchars($user_profile['agent_email'] ?? 'Aucun email') ?></a></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Bloc Action Sécurité (Forcer le mot de passe) -->
            <div class="col-lg-5">
                <div class="card profile-card shadow-sm p-4 border-top border-warning" style="border-top: 4px solid var(--sn-yellow) !important;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="text-warning"><i class="fa-solid fa-shield-halved fa-lg"></i></div>
                        <h4 class="h6 fw-bold text-dark mb-0">Actions de Sécurité / Attribution</h4>
                    </div>
                    <p class="text-muted small mb-4">Le protocole de sécurité de l'application chiffre les mots de passe. En tant que SuperAdmin, vous pouvez attribuer directement un nouveau mot de passe d'accès à cet agent.</p>

                    <form action="" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir redéfinir le mot de passe de cet utilisateur ?');">
                        <input type="hidden" name="action" value="reset_password">
                        <div class="mb-3">
                            <label for="new_password" class="form-label small fw-bold">Nouveau mot de passe de remplacement</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-key"></i></span>
                                <input type="text" class="form-control form-control-sm" name="new_password" id="new_password" placeholder="Ex: Mjs2026!Dakar" required>
                            </div>
                            <div class="form-text" style="font-size: 0.75rem;">Attribuez un mot de passe temporaire et transmettez-le à l'agent.</div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-dark w-100 py-2 fw-bold">
                            <i class="fa-solid fa-rotate me-1"></i> Mettre à jour le mot de passe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>