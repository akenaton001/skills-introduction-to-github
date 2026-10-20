<?php
// views/admin/liste_connexions.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/bd.php';

// SÉCURITÉ ACCÈS : Seul le Superadmin peut voir cette page
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Superadmin') {
    // Si l'utilisateur n'est pas Superadmin, on le redirige vers l'accueil ou le profil
    header('Location: /mjs_personnel/views/profil.php');
    exit();
}

try {
    // Récupération de toutes les connexions avec les détails de l'utilisateur et de l'agent
    $stmt = $pdo->query("
        SELECT 
            c.date_connexion, 
            c.adresse_ip,
            u.username, 
            u.role,
            a.prenom, 
            a.nom, 
            a.matricule_solde
        FROM connexions c
        JOIN utilisateurs u ON c.id_utilisateur = u.id_utilisateur
        LEFT JOIN agents a ON u.id_agent = a.id_agent
        ORDER BY c.date_connexion DESC
        LIMIT 100 -- Limité aux 100 dernières connexions pour les performances
    ");
    $toutes_les_connexions = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Erreur lors de la récupération des connexions : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MJS - Audit des Connexions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root { --sn-green: #008751; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #1e293b; }
        .badge-superadmin { background-color: #ef4444; color: white; }
        .badge-admin { background-color: #f59e0b; color: white; }
        .badge-agent { background-color: #10b981; color: white; }
    </style>
</head>
<body>

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="container mt-5">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
            
            <!-- En-tête de la page de contrôle -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h4 text-dark fw-bold mb-1">
                        <i class="fa-solid fa-shield-halved text-danger me-2"></i> 
                        Journal Général des Connexions
                    </h2>
                    <p class="text-muted small mb-0">Espace de contrôle et d'audit de sécurité du personnel connecté au système MJS.</p>
                </div>
                <span class="badge bg-danger px-3 py-2 rounded-pill fw-bold">Espace Superadmin</span>
            </div>

            <hr class="my-3">

            <!-- Tableau d'audit -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th>Date & Heure</th>
                            <th>Agent / Utilisateur</th>
                            <th>Matricule Solde</th>
                            <th>Rôle applicatif</th>
                            <th>Adresse IP</th>
                            <th class="text-center">Action détectée</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($toutes_les_connexions) > 0): ?>
                            <?php foreach ($toutes_les_connexions as $log): ?>
                                <tr>
                                    <!-- Date et Heure -->
                                    <td class="fw-medium text-secondary">
                                        <i class="fa-regular fa-clock text-muted me-2"></i>
                                        <?= date('d/m/Y à H:i:s', strtotime($log['date_connexion'])) ?>
                                    </td>
                                    
                                    <!-- Identité complète de l'agent -->
                                    <td>
                                        <div class="fw-bold text-dark">
                                            <?= htmlspecialchars(($log['prenom'] ?? '') . ' ' . ($log['nom'] ?? 'Compte Virtuel')) ?>
                                        </div>
                                        <div class="text-muted small">@<?= htmlspecialchars($log['username']) ?></div>
                                    </td>
                                    
                                    <!-- Matricule -->
                                    <td class="font-monospace text-secondary">
                                        <?= htmlspecialchars($log['matricule_solde'] ?? 'N/A') ?>
                                    </td>
                                    
                                    <!-- Rôle avec couleur dynamique -->
                                    <td>
                                        <?php 
                                        $badgeClass = 'badge-agent';
                                        if ($log['role'] === 'Superadmin') $badgeClass = 'badge-superadmin';
                                        elseif ($log['role'] === 'Admin') $badgeClass = 'badge-admin';
                                        ?>
                                        <span class="badge <?= $badgeClass ?> px-2.5 py-1 rounded-pill fw-semibold text-xs">
                                            <?= htmlspecialchars($log['role']) ?>
                                        </span>
                                    </td>
                                    
                                    <!-- Adresse IP -->
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace px-2 py-1">
                                            <i class="fa-solid fa-network-wired text-muted me-1"></i>
                                            <?= htmlspecialchars($log['adresse_ip']) ?>
                                        </span>
                                    </td>
                                    
                                    <!-- Statut / Action -->
                                    <td class="text-center">
                                        <span class="badge bg-success-subtle text-success px-2 py-1 rounded border border-success-subtle fw-semibold">
                                            Connexion établie
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-circle-info fs-3 mb-2"></i>
                                    <p class="mb-0">Aucun historique de connexion enregistré pour le moment.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>