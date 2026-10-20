<?php
// views/utilisateurs_liste.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/bd.php';

// Contrôle d'accès modifié : Autoriser SuperAdmin et DAGE_RH
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['SuperAdmin', 'DAGE_RH'])) {
    header('Location: /mjs_personnel/index.php');
    exit();
}

// Récupération de tous les comptes utilisateurs
try {
    $stmt = $pdo->query("
        SELECT u.id_utilisateur, u.username, u.role, u.actif,
               a.prenom, a.nom, a.email_professionnel, a.telephone
        FROM utilisateurs u
        LEFT JOIN agents a ON u.id_agent = a.id_agent
        ORDER BY u.id_utilisateur DESC
    ");
    $utilisateurs = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erreur de récupération des données : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MJS - Gestion des Utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Harmonisation visuelle globale */
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
        /* Styles spécifiques au Header Global */
        .navbar-custom {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 3px solid var(--sn-green);
            padding: 0.5rem 1.5rem;
        }
        .logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .logo-ministere {
            height: 45px;
            width: auto;
            object-fit: contain;
            background: white;
            padding: 2px;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Style de la barre de recherche */
        .search-wrapper { background: #ffffff; border-radius: 12px; padding: 5px; border: 1px solid #e2e8f0; transition: all 0.3s ease; }
        .search-wrapper:focus-within { border-color: var(--sn-green); box-shadow: 0 0 0 4px rgba(0, 135, 81, 0.1); }
        .search-input { border: none !important; background: transparent !important; padding: 0.6rem 1rem !important; font-size: 0.95rem; }
        .search-input:focus { outline: none !important; box-shadow: none !important; }
        .search-icon { padding-left: 15px; color: #94a3b8; }
    </style>
</head>
<body class="bg-light">

    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="container mt-4">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h2 class="h3 text-gray-800"><i class="fa-solid fa-users-gear text-primary me-2"></i> Gestion des Droits & Comptes d'Accès</h2>
                <p class="text-muted mb-0">Pilotez les habilitations de sécurité des agents du Ministère.</p>
            </div>
        </div>

        <div id="alert-container"></div>

        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="search-wrapper d-flex align-items-center shadow-sm">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" id="searchInput" class="form-control search-input" 
                           placeholder="Rechercher par nom, identifiant ou rôle..." autocomplete="off">
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-4">Agent connecté</th>
                                <th>Identifiant</th>
                                <th>Rôle Applicatif</th>
                                <th class="text-center">Statut</th>
                                <th class="text-center">Profil & Sécurité</th>
                                <th class="pe-4 text-end">Activer/Bloquer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($utilisateurs as $user): ?>
                                <?php 
                                    $est_moi = ((int)$user['id_utilisateur'] === (int)$_SESSION['user_id']);
                                ?>
                                <tr class="<?= $est_moi ? 'table-warning' : '' ?>">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                                                <?= strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">
                                                    <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?>
                                                    <?php if ($est_moi): ?>
                                                        <span class="badge bg-primary ms-1">Vous</span>
                                                    <?php endif; ?>
                                                </div>
                                                <small class="text-muted"><?= htmlspecialchars($user['email_professionnel'] ?? 'Pas d\'email') ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <code class="text-dark fw-bold">@<?= htmlspecialchars($user['username']) ?></code>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm w-100 select-role" 
                                                data-id="<?= $user['id_utilisateur'] ?>" 
                                                <?= $est_moi ? 'disabled' : '' ?>>
                                            <option value="SuperAdmin" <?= $user['role'] === 'SuperAdmin' ? 'selected' : '' ?>>SuperAdmin</option>
                                            <option value="DAGE_RH" <?= $user['role'] === 'DAGE_RH' ? 'selected' : '' ?>>DAGE / RH</option>
                                            <option value="Secrétaire" <?= $user['role'] === 'Secrétaire' ? 'selected' : '' ?>>Secrétaire</option>
                                            <option value="Agent" <?= $user['role'] === 'Agent' ? 'selected' : '' ?>>Agent</option>
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <span id="badge-statut-<?= $user['id_utilisateur'] ?>" class="badge px-3 py-2 <?= $user['actif'] == 1 ? 'bg-success' : 'bg-danger' ?>">
                                            <?= $user['actif'] == 1 ? '<i class="fa-solid fa-check-circle me-1"></i> Actif' : '<i class="fa-solid fa-ban me-1"></i> Suspendu' ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="utilisateur_profil.php?id=<?= $user['id_utilisateur'] ?>" class="btn btn-xs btn-sm btn-outline-dark px-2 py-1">
                                            <i class="fa-solid fa-user-shield me-1"></i> Gérer le compte
                                        </a>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <?php if ($est_moi): ?>
                                            <button class="btn btn-sm btn-outline-secondary" disabled title="Protection : Vous ne pouvez pas vous désactiver vous-même.">
                                                <i class="fa-solid fa-lock"></i> Protégé
                                            </button>
                                        <?php else: ?>
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input switch-statut" type="checkbox" role="switch" 
                                                       data-id="<?= $user['id_utilisateur'] ?>" 
                                                       <?= $user['actif'] == 1 ? 'checked' : '' ?>>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="mt-4 text-start mb-5">
            <a href="dashboard.php" class="btn btn-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Retour au Tableau de bord</a>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Recherche en temps réel
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            document.querySelectorAll('table tbody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
            });
        });

        // 1. Gestion du changement de rôle via le menu déroulant
        document.querySelectorAll('.select-role').forEach(select => {
            select.addEventListener('change', function() {
                const idUser = this.getAttribute('data-id');
                const nouveauRole = this.value;
                envoyerModification(idUser, 'role', nouveauRole);
            });
        });

        // 2. Gestion de l'activation/désactivation via l'interrupteur (Switch)
        document.querySelectorAll('.switch-statut').forEach(switchInput => {
            switchInput.addEventListener('change', function() {
                const idUser = this.getAttribute('data-id');
                const active = this.checked ? '1' : '0';
                
                envoyerModification(idUser, 'statut', active, function() {
                    const badge = document.getElementById('badge-statut-' + idUser);
                    if (active === '1') {
                        badge.className = 'badge px-3 py-2 bg-success';
                        badge.innerHTML = '<i class="fa-solid fa-check-circle me-1"></i> Actif';
                    } else {
                        badge.className = 'badge px-3 py-2 bg-danger';
                        badge.innerHTML = '<i class="fa-solid fa-ban me-1"></i> Suspendu';
                    }
                });
            });
        });
    });
    </script>
</body>
</html>