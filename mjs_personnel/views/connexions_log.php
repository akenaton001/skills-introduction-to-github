<?php
// views/connexions_log.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/bd.php';

// MODIFICATION : Autorisation étendue au SuperAdmin ET au DAGE_RH
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'] ?? '', ['SuperAdmin', 'DAGE_RH'])) {
    header('Location: /mjs_personnel/views/dashboard.php');
    exit();
}

try {
    // Récupération des 50 dernières connexions avec les infos de l'utilisateur et de l'agent
    $query = "
        SELECT h.*, u.username, u.role, a.prenom, a.nom 
        FROM historique_connexions h
        JOIN utilisateurs u ON h.id_utilisateur = u.id_utilisateur
        LEFT JOIN agents a ON u.id_agent = a.id_agent
        ORDER BY h.date_connexion DESC 
        LIMIT 50
    ";
    $logs = $pdo->query($query)->fetchAll();
} catch (PDOException $e) {
    die("Erreur lors de la récupération de l'historique : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MJS Personnel - Audit des Connexions</title>
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
        .log-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: 16px;
        }
        .table th {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background-color: #f8fafc !important;
            color: #64748b;
        }
        .badge-role {
            font-weight: 600;
            padding: 0.35rem 0.6rem;
            border-radius: 20px;
            font-size: 0.75rem;
        }
        .role-SuperAdmin { background: rgba(227, 27, 35, 0.1); color: var(--sn-red); border: 1px solid rgba(227, 27, 35, 0.2); }
        .role-DAGE_RH { background: rgba(252, 209, 22, 0.15); color: #b28e00; border: 1px solid rgba(252, 209, 22, 0.3); }
        .role-Agent { background: rgba(0, 135, 51, 0.1); color: var(--sn-green); border: 1px solid rgba(0, 135, 51, 0.2); }
        .navbar-custom {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 3px solid var(--sn-green);
            padding: 0.5rem 1.5rem;
        }
    </style>
</head>
<body>

    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="container mt-4">
        <div class="mb-3">
            <a href="dashboard.php" class="btn btn-sm btn-light border rounded-pill px-3 text-muted">
                <i class="fa-solid fa-arrow-left me-1"></i> Retour au Tableau de bord
            </a>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="p-4 bg-white rounded-4 shadow-sm">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 bg-danger-subtle text-danger rounded-3">
                            <i class="fa-solid fa-clock-rotate-left fa-xl"></i>
                        </div>
                        <div>
                            <h2 class="h4 fw-bold text-dark mb-1">Journal de Surveillance des Connexions</h2>
                            <p class="text-muted mb-0 small">Historique des 50 derniers accès à la plateforme de gestion du personnel du Ministère.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card log-card shadow-sm border-0 mb-5">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Date & Heure</th>
                                <th>Utilisateur / Agent</th>
                                <th>Rôle</th>
                                <th>Adresse IP</th>
                                <th class="pe-4">Navigateur / Système</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="fa-solid fa-folder-open fa-2x mb-2 d-block"></i>
                                        Aucune connexion enregistrée pour le moment.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td class="ps-4 fw-medium text-secondary" style="font-size: 0.9rem;">
                                            <i class="fa-regular fa-calendar me-1 text-muted"></i>
                                            <?= date('d/m/Y à H:i:s', strtotime($log['date_connexion'])) ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">
                                                <?= htmlspecialchars($log['prenom'] . ' ' . $log['nom']) ?>
                                            </div>
                                            <div class="text-muted font-monospace" style="font-size: 0.8rem;">
                                                @<?= htmlspecialchars($log['username']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge-role role-<?= $log['role'] ?>">
                                                <?= htmlspecialchars($log['role']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border font-monospace px-2 py-1">
                                                <?= htmlspecialchars($log['ip_adresse']) ?>
                                            </span>
                                        </td>
                                        <td class="pe-4 text-muted small text-truncate" style="max-width: 300px;" title="<?= htmlspecialchars($log['user_agent']) ?>">
                                            <?= htmlspecialchars($log['user_agent']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>