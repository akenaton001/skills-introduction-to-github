<?php 
$pageTitle = "Gestion des Hospitalisations - Jàmm Santé";
$page = 'hospitalisations';

$hospitalisations = $hospitalisations ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle; ?></title>
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --sidebar-width: 260px; --bg-light: #f4f6f9; }
        body { font-family: 'Lexend', sans-serif; background-color: var(--bg-light); color: #333; margin: 0; }
        .sidebar { width: var(--sidebar-width); background: linear-gradient(180deg, #1b365d 0%, #0f1f38 100%); position: fixed; top: 0; bottom: 0; left: 0; z-index: 100; display: flex; flex-direction: column; justify-content: space-between; padding: 1.5rem 1rem; color: white; }
        .sidebar-brand { font-weight: 700; font-size: 1.25rem; color: white; text-decoration: none; display: flex; align-items: center; margin-bottom: 2rem; }
        .nav-menu { list-style: none; padding: 0; margin: 0; flex-grow: 1; }
        .nav-link { color: rgba(255, 255, 255, 0.75); padding: 0.75rem 1rem; border-radius: 0.75rem; display: flex; align-items: center; font-weight: 500; font-size: 0.9rem; text-decoration: none; transition: all 0.2s ease; }
        .nav-link:hover, .nav-link.active { color: white; background-color: rgba(255, 255, 255, 0.1); }
        .nav-link i { font-size: 1.15rem; margin-right: 0.75rem; }
        .main-content { margin-left: var(--sidebar-width); padding: 1.5rem; }
        .top-navbar { background: white; border-radius: 1rem; padding: 0.75rem 1.5rem; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 10px rgba(0,0,0,0.02); margin-bottom: 1.5rem; }
    </style>
</head>
<body>

    <!-- Inclusion de la Sidebar -->
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>

    <div class="main-content">
        <!-- Inclusion de la Topbar dynamique -->
        <?php include __DIR__ . '/../layouts/topbar.php'; ?>

        <div class="container-fluid px-0">
            
            <!-- En-tête de page -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">
                        <i class="fas fa-procedures text-primary me-2"></i> Gestion des Hospitalisations
                    </h2>
                    <p class="text-muted mb-0">Suivi en temps réel des lits, chambres et patients hospitalisés.</p>
                </div>
                <a href="index.php?action=hospitalisations_create" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                    <i class="fas fa-plus-circle me-2"></i> Nouvelle Admission
                </a>
            </div>

            <!-- Tableau des hospitalisations avec effet Wow -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary text-uppercase fs-7" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                                <tr>
                                    <th class="py-3 ps-4">Patient</th>
                                    <th>Chambre / Lit</th>
                                    <th>Motif</th>
                                    <th>Date d'entrée</th>
                                    <th>Statut</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($hospitalisations)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <div class="my-3">
                                                <i class="fas fa-bed fa-3x text-light mb-3"></i>
                                                <p class="mb-0">Aucune hospitalisation enregistrée pour le moment.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($hospitalisations as $hosp): ?>
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <span class="fw-bold text-dark d-block"><?= htmlspecialchars($hosp['prenom'] . ' ' . $hosp['nom']) ?></span>
                                                <small class="text-muted"><i class="fas fa-phone-alt me-1 text-primary"></i> <?= htmlspecialchars($hosp['telephone']) ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-semibold shadow-none">
                                                    <i class="fas fa-door-closed text-secondary me-1"></i> Chambre <?= htmlspecialchars($hosp['numero_chambre']) ?> - Lit <?= htmlspecialchars($hosp['numero_lit']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-truncate d-inline-block text-secondary" style="max-width: 220px;" title="<?= htmlspecialchars($hosp['motif_hospitalisation']) ?>">
                                                    <?= htmlspecialchars($hosp['motif_hospitalisation']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-dark fw-medium"><?= date('d/m/Y H:i', strtotime($hosp['date_entree'])) ?></span>
                                            </td>
                                            <td>
                                                <?php if ($hosp['statut'] === 'en_cours'): ?>
                                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-2 rounded-pill fw-bold">
                                                        <i class="fas fa-spinner fa-spin me-1"></i> En cours
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill fw-bold">
                                                        <i class="fas fa-check me-1"></i> Sortie validée
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end pe-4">
                                                <?php if ($hosp['statut'] === 'en_cours'): ?>
                                                    <a href="index.php?action=hospitalisations_discharge&id=<?= $hosp['id'] ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold shadow-sm" onclick="return confirm('Confirmer la sortie de ce patient et libérer le lit ?')">
                                                        <i class="fas fa-sign-out-alt me-1"></i> Libérer le lit
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted small fst-italic">Clôturé le <?= date('d/m/Y H:i', strtotime($hosp['date_sortie'])) ?></span>
                                                <?php endif; ?>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>