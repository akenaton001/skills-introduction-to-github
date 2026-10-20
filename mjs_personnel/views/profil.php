<?php
// views/profil.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/bd.php';

// Vérification de connexion
if (!isset($_SESSION['user_id'])) {
    header('Location: /mjs_personnel/index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT 
            u.username, u.role, u.avatar, 
            a.id_agent, a.matricule_solde, a.cni, a.prenom, a.nom, a.email_professionnel, a.telephone, 
            a.statut_agent, a.statut_activite, a.date_entree_ministere, a.adresse,
            a.id_structure_actuelle,
            s.nom_structure AS structure_nom
        FROM utilisateurs u
        LEFT JOIN agents a ON u.id_agent = a.id_agent
        LEFT JOIN structures s ON a.id_structure_actuelle = s.id_structure
        WHERE u.id_utilisateur = :id
    ");
    $stmt->execute([':id' => $user_id]);
    $mon_profil = $stmt->fetch();
    
    $_SESSION['user_avatar'] = $mon_profil['avatar'];

} catch (PDOException $e) {
    die("Erreur de chargement du profil : " . $e->getMessage());
}

$date_entree = !empty($mon_profil['date_entree_ministere']) ? date('d/m/Y', strtotime($mon_profil['date_entree_ministere'])) : 'Non renseignée';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MJS - Mon Espace Profil</title>
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

    <div class="container mt-4 pb-5">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm text-center p-4 rounded-4 bg-white">
                    <form id="form-avatar" enctype="multipart/form-data">
                        <div class="avatar-wrapper" onclick="document.getElementById('input-avatar').click();">
                            <?php if (!empty($mon_profil['avatar'])): ?>
                                <img src="/mjs_personnel/uploads/avatars/<?= htmlspecialchars($mon_profil['avatar']) ?>" class="avatar-image" id="avatar-preview" alt="Avatar">
                            <?php else: ?>
                                <div class="avatar-initials" id="avatar-preview-fallback">
                                    <?= strtoupper(substr($mon_profil['prenom'] ?? 'U', 0, 1) . substr($mon_profil['nom'] ?? 'N', 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            <div class="avatar-overlay"><i class="fa-solid fa-camera mb-1"></i><span>Modifier</span></div>
                        </div>
                        <input type="file" id="input-avatar" name="avatar" accept="image/jpeg,image/png,image/webp" style="display: none;">
                    </form>
                    <div id="avatar-alert" class="mt-2"></div>

                    <h4 class="mb-1 text-dark fw-bold"><?= htmlspecialchars(($mon_profil['prenom'] ?? '') . ' ' . ($mon_profil['nom'] ?? 'Utilisateur')) ?></h4>
                    <p class="text-muted small mb-3">Matricule : <span class="fw-bold text-dark"><?= htmlspecialchars($mon_profil['matricule_solde'] ?? 'N/A') ?></span></p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge bg-success-subtle text-success border px-3 py-1 rounded-pill fw-semibold"><?= htmlspecialchars($mon_profil['role']) ?></span>
                    </div>

                    <?php if ($mon_profil['role'] === 'Agent' || $mon_profil['role'] === 'Secretaire'): ?>
                        <div class="mt-2">
                            <a href="/mjs_personnel/views/formulaire_conge.php" class="btn btn-outline-success w-100 rounded-pill fw-bold">
                                <i class="fa-solid fa-plane-departure me-2"></i>Demander un congé
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <hr class="w-100 my-3">
                    <p class="text-muted small mb-0"><i class="fa-solid fa-calendar-days me-1"></i> Entré le <?= $date_entree ?></p>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                    <ul class="nav nav-pills mb-4 gap-2 bg-light p-1 rounded-pill" role="tablist">
                        <li class="nav-item"><button class="nav-link active rounded-pill" data-bs-toggle="pill" data-bs-target="#tab-admin" type="button"><i class="fa-solid fa-user me-2"></i>Infos RH</button></li>
                        <li class="nav-item"><button class="nav-link rounded-pill" data-bs-toggle="pill" data-bs-target="#tab-security" type="button"><i class="fa-solid fa-shield-halved me-2"></i>Sécurité</button></li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab-admin">
                            <h3 class="h5 text-dark fw-bold mb-3">Affectation & Statut</h3>
                            <div class="row g-3 bg-light p-3 rounded-3 mb-4">
                                <div class="col-md-6"><div class="info-label">Structure</div><div class="info-value"><?= htmlspecialchars($mon_profil['structure_nom'] ?? 'Non spécifiée') ?></div></div>
                                <div class="col-md-6"><div class="info-label">Statut agent</div><div class="info-value"><?= htmlspecialchars($mon_profil['statut_agent'] ?? 'Non spécifié') ?></div></div>
                                <div class="col-md-6 mt-2"><div class="info-label">CNI</div><div class="info-value"><?= htmlspecialchars($mon_profil['cni'] ?? 'Non renseigné') ?></div></div>
                                <div class="col-md-6 mt-2"><div class="info-label">Adresse</div><div class="info-value"><?= htmlspecialchars($mon_profil['adresse'] ?? 'Non renseignée') ?></div></div>
                            </div>
                            <h3 class="h5 text-dark fw-bold mb-3">Contacts</h3>
                            <div class="row g-3">
                                <div class="col-md-6"><div class="border p-3 rounded-3"><div class="info-label">Email</div><div class="info-value"><?= htmlspecialchars($mon_profil['email_professionnel'] ?? 'N/A') ?></div></div></div>
                                <div class="col-md-6"><div class="border p-3 rounded-3"><div class="info-label">Téléphone</div><div class="info-value"><?= htmlspecialchars($mon_profil['telephone'] ?? 'N/A') ?></div></div></div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-security">
                            <h3 class="h5 text-dark fw-bold mb-3">Changer mot de passe</h3>
                            <div id="profil-alert"></div>
                            <form id="form-changer-mdp">
                                <div class="mb-3"><label class="form-label small fw-bold">Ancien mot de passe</label><input type="password" name="ancien_mdp" class="form-control" required></div>
                                <div class="row mb-3">
                                    <div class="col-md-6"><label class="form-label small fw-bold">Nouveau</label><input type="password" name="nouveau_mdp" class="form-control" required></div>
                                    <div class="col-md-6"><label class="form-label small fw-bold">Confirmer</label><input type="password" name="confirmer_mdp" class="form-control" required></div>
                                </div>
                                <button type="submit" class="btn btn-success rounded-pill px-4"><i class="fa-solid fa-save me-1"></i> Sauvegarder</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>