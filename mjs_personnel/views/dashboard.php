<?php
// views/dashboard.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/bd.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /mjs_personnel/index.php');
    exit();
}

$role_actuel = $_SESSION['user_role'] ?? 'Agent';
$user_nom_complet = $_SESSION['user_nom_complet'] ?? 'Utilisateur';

$stats = ['total_agents' => 0, 'total_comptes' => 0, 'comptes_suspendus' => 0];
// Accès aux statistiques étendu au DAGE_RH
if (in_array($role_actuel, ['SuperAdmin', 'DAGE_RH'])) {
    try {
        $stats['total_agents'] = $pdo->query("SELECT COUNT(*) FROM agents")->fetchColumn();
        $stats['total_comptes'] = $pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
        $stats['comptes_suspendus'] = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE actif = 0")->fetchColumn();
    } catch (PDOException $e) {}
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MJS Personnel - Tableau de bord</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
    :root {
        --sn-green: #008751;
        --sn-yellow: #FCD116;
        --sn-red: #E31B23;
        --dark-slate: #0F172A;
    }

    body { font-family: 'Inter', sans-serif; background: #f1f5f9; color: #1e293b; min-height: 100vh; }

    .welcome-gradient-card {
        background: #c8f4db; 
        border: 1px solid rgba(0, 135, 81, 0.2);
        border-radius: 12px;
        padding: 1rem 2rem; 
        display: flex;
        align-items: center;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .welcome-title { font-size: 1.25rem !important; color: var(--sn-green) !important; font-weight: 800; margin: 0 !important; }
    .welcome-subtitle { font-size: 1rem !important; color: #475569 !important; margin: 0 !important; }

    .role-badge-premium {
        background: var(--sn-yellow);
        color: var(--dark-slate) !important;
        font-weight: 800;
        padding: 0.35rem 1rem;
        border-radius: 8px;
        font-size: 0.75rem;
        text-transform: uppercase;
        border: 1px solid #d4ad00;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
	
    .user-name-highlight { color: #f00020 !important; font-weight: 800 !important; }

    .navbar-custom { background: rgba(15, 23, 42, 0.9); backdrop-filter: blur(10px); border-bottom: 3px solid var(--sn-green); padding: 0.5rem 1.5rem; }
    .section-title { font-size: 0.8rem !important; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b !important; font-weight: 600; margin-bottom: 1rem !important; }
    .stat-card-premium { background: #FFFFFF; border-radius: 14px; border: 1px solid rgba(226, 232, 240, 0.8); padding: 1.15rem; transition: all 0.3s ease; }
    .card-agents { border-left: 4px solid var(--sn-green); }
    .card-comptes { border-left: 4px solid var(--sn-yellow); }
    .card-suspendus { border-left: 4px solid var(--sn-red); }
    .stat-label { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; }
    .stat-number { font-size: 1.85rem; font-weight: 800; color: var(--dark-slate); line-height: 1; margin-top: 0.25rem; }
    .icon-box-premium { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.05rem; }
    .action-card-container { background: #FFFFFF; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.01); border: 1px solid #E2E8F0; }
    .action-link-premium { text-decoration: none; display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: #F8FAFC; border-radius: 12px; border: 1px solid #F1F5F9; transition: all 0.25s ease; }
    .action-link-premium:hover { background: #f0fdf4; border-color: rgba(0, 135, 81, 0.2); }
    .action-title-text { font-size: 0.95rem; font-weight: 700; color: var(--dark-slate); }
    .action-desc-text { font-size: 0.8rem; color: #64748B; }
    .btn-premium-green { background: var(--sn-green); color: #FFFFFF; font-weight: 700; padding: 0.6rem 1.75rem; border-radius: 50px; border: none; }
    .btn-premium-yellow { background: var(--sn-yellow); color: var(--dark-slate); font-weight: 700; padding: 0.75rem; border-radius: 10px; border: none; width: 100%; }
    .btn-audit-premium { border-radius: 10px; font-size: 0.9rem; padding: 0.75rem; border: 1px solid #333; }
</style>
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

    <div class="container my-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="welcome-gradient-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h2 class="welcome-title mb-1">Système Intégré de Gestion du Personnel (SIGP)</h2>
                        <p class="text-white-50 welcome-subtitle mb-0">Bienvenue, M./Mme <strong class="user-name-highlight"><?= htmlspecialchars($user_nom_complet) ?></strong> • République du Sénégal</p>
                    </div>
                    <div>
                        <span class="role-badge-premium"><?= htmlspecialchars($role_actuel) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <?php if (in_array($role_actuel, ['SuperAdmin', 'DAGE_RH'])): ?>
            <div class="row">
                <div class="col-12">
                    <h4 class="section-title"><i class="fa-solid fa-chart-pie me-1"></i> Indicateurs de Performance RH</h4>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card stat-card-premium card-agents shadow-sm">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="stat-label">Effectif Global</span>
                                <h3 class="stat-number"><?= $stats['total_agents'] ?></h3>
                            </div>
                            <div class="icon-box-premium" style="background: rgba(0, 135, 81, 0.06); color: var(--sn-green);"><i class="fa-solid fa-users-viewfinder"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card-premium card-comptes shadow-sm">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="stat-label">Comptes Utilisateurs</span>
                                <h3 class="stat-number"><?= $stats['total_comptes'] ?></h3>
                            </div>
                            <div class="icon-box-premium" style="background: rgba(252, 209, 22, 0.12); color: #A18200;"><i class="fa-solid fa-user-check"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card-premium card-suspendus shadow-sm">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="stat-label">Accès Suspendus</span>
                                <h3 class="stat-number"><?= $stats['comptes_suspendus'] ?></h3>
                            </div>
                            <div class="icon-box-premium" style="background: rgba(227, 27, 35, 0.06); color: var(--sn-red);"><i class="fa-solid fa-user-lock"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row g-3">
            <?php if (in_array($role_actuel, ['SuperAdmin', 'DAGE_RH', 'Secrétaire'])): ?>
                <div class="col-lg-7">
                    <div class="action-card-container h-100">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="p-2 rounded-3" style="background: rgba(0, 135, 81, 0.05); color: var(--sn-green);"><i class="fa-solid fa-folder-tree"></i></div>
                            <h4 class="m-0 text-dark fs-6 fw-bold" style="color: var(--dark-slate) !important;">Gestion des Métiers & Ressources Humaines</h4>
                        </div>
                        
                        <div class="d-flex flex-column gap-2">
                            <a href="agents_liste.php" class="action-link-premium">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="fs-6 text-muted"><i class="fa-solid fa-id-card text-success"></i></div>
                                    <div>
                                        <div class="action-title-text">Annuaire Permanent des Agents</div>
                                        <div class="action-desc-text">Recherche, consultation et suivi des fiches des agents du Ministère.</div>
                                    </div>
                                </div>
                                <div class="text-success small"><i class="fa-solid fa-arrow-right-long"></i></div>
                            </a>
                            
                            <a href="agent_ajouter.php" class="action-link-premium">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="fs-6 text-muted"><i class="fa-solid fa-user-plus text-success"></i></div>
                                    <div>
                                        <div class="action-title-text">Enrôler un Nouvel Agent</div>
                                        <div class="action-desc-text">Créer un dossier d'agent et configurer son affectation initiale.</div>
                                    </div>
                                </div>
                                <div class="text-success small"><i class="fa-solid fa-plus-circle"></i></div>
                            </a>

                            <a href="utilisateurs_liste.php" class="action-link-premium">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="fs-6 text-muted"><i class="fa-solid fa-users-gear text-success"></i></div>
                                    <div>
                                        <div class="action-title-text">Gestion des Comptes Utilisateurs</div>
                                        <div class="action-desc-text">Gérer les accès applicatifs, réinitialiser les mots de passe et modifier les rôles.</div>
                                    </div>
                                </div>
                                <div class="text-success small"><i class="fa-solid fa-arrow-right-long"></i></div>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (in_array($role_actuel, ['SuperAdmin', 'DAGE_RH'])): ?>
                <div class="col-lg-5">
                    <div class="action-card-container h-100" style="border-top: 3px solid var(--sn-yellow);">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="p-2 rounded-3" style="background: rgba(227, 27, 35, 0.05); color: var(--sn-red);"><i class="fa-solid fa-shield-halved"></i></div>
                            <h4 class="m-0 text-dark fs-6 fw-bold" style="color: var(--dark-slate) !important;">Sécurité & Habilitations</h4>
                        </div>
                        
                        <p class="text-muted mb-3" style="font-size: 0.82rem;">Espace réservé à l'administration du système. Supervisez les accès et attribuez les privilèges.</p>
                        
                        <div class="d-flex flex-column gap-2">
                            <a href="utilisateurs_liste.php" class="btn btn-premium-yellow d-flex align-items-center justify-content-center gap-2">
                                <i class="fa-solid fa-users-gear"></i> Gérer les Droits & Comptes
                            </a>
                            
                            <a href="connexions_log.php" class="btn btn-outline-dark btn-audit-premium py-2 fw-bold border-2 d-flex align-items-center justify-content-center gap-2">
                                <i class="fa-solid fa-clock-rotate-left"></i> Historique des accès & Audit
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($role_actuel === 'Agent'): ?>
                <div class="col-12">
                    <div class="agent-space-card text-center">
                        <div class="icon-box-premium bg-light text-success mx-auto mb-2" style="width: 52px; height: 52px; border-radius: 50%;"><i class="fa-solid fa-fingerprint fa-lg" style="color: var(--sn-green);"></i></div>
                        <h3 class="fw-bold text-dark mb-1 fs-6" style="color: var(--dark-slate) !important;">Votre Espace Agent Privé</h3>
                        <p class="text-muted mx-auto mb-3" style="max-width: 550px; font-size: 0.85rem;">Vous disposez d'un accès en lecture seule. Pour signaler un changement, transmettez les justificatifs à la DAGE.</p>
                        <a href="profil.php" class="btn btn-premium-green"><i class="fa-solid fa-user-lock me-1"></i> Accéder à mon profil</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>