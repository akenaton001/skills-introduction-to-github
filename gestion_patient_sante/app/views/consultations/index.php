<?php
$pageTitle = "Gestion des Consultations - Jàmm Santé";
$page = 'consultations';

// Séparation des éléments : Aujourd'hui vs À venir
$today = date('Y-m-d');
$consultationsJour = [];
$consultationsAVenir = [];

if (!empty($consultations)) {
    foreach ($consultations as $c) {
        $dateConsult = date('Y-m-d', strtotime($c['date_heure']));
        if ($dateConsult === $today) {
            $consultationsJour[] = $c;
        } elseif ($dateConsult > $today) {
            $consultationsAVenir[] = $c;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
        .search-box { position: relative; width: 350px; }
        .search-box input { background-color: #f8f9fa; border: 1px solid #e2e8f0; border-radius: 50px; padding: 0.5rem 1rem 0.5rem 2.5rem; font-size: 0.85rem; width: 100%; }
        .search-box i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #a0aec0; }
        
        /* Style inspiré des badges de statut pour les boutons d'actions */
        .btn-action-badge {
            font-size: 0.78rem;
            font-weight: 600;
            padding: 0.35rem 0.85rem;
            border-radius: 50rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
            border: none;
        }
        .btn-action-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        .btn-action-danger:hover {
            background-color: rgba(220, 53, 69, 0.2);
            color: #b02a37;
        }
        .btn-action-primary {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }
        .btn-action-primary:hover {
            background-color: rgba(13, 110, 253, 0.2);
            color: #0b5ed7;
        }
        .btn-action-warning {
            background-color: rgba(255, 193, 7, 0.15);
            color: #b58100;
        }
        .btn-action-warning:hover {
            background-color: rgba(255, 193, 7, 0.3);
            color: #8c6300;
        }
    </style>
</head>
<body>

    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>

    <div class="main-content">
        <?php include __DIR__ . '/../layouts/topbar.php'; ?>

        <div class="container-fluid px-0">
            <div class="row align-items-center mb-4">
                <div class="col-md-6">
                    <h2 class="fw-bold text-dark mb-1"><i class="bi bi-journal-medical text-primary me-2"></i> Gestion des Consultations</h2>
                    <p class="text-muted mb-0">Suivi des consultations du jour, rendez-vous et programmations à venir.</p>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <a href="index.php?action=consultations_nouveau" class="btn btn-primary fw-semibold px-4 py-2 rounded-pill shadow-sm">
                        <i class="bi bi-plus-circle-fill me-2"></i> Nouvelle Consultation
                    </a>
                </div>
            </div>

            <!-- SECTION 1 : CONSULTATIONS ET RDV DU JOUR -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold text-danger m-0">
                        <i class="bi bi-exclamation-circle-fill me-2"></i> Éléments d'aujourd'hui (<?= date('d/m/Y'); ?>)
                    </h5>
                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill fw-bold">
                        <?= count($consultationsJour); ?> Prévu(s)
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th class="py-3 ps-3">Heure</th>
                                <th class="py-3">Type</th>
                                <th class="py-3">Patient</th>
                                <th class="py-3">Téléphone</th>
                                <th class="py-3">Motif / Notes</th>
                                <th class="py-3">Diagnostic / Statut</th>
                                <th class="py-3 text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($consultationsJour)): ?>
                                <?php foreach ($consultationsJour as $item): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold text-danger fs-6">
                                            <i class="bi bi-clock-fill me-1"></i> <?= date('H:i', strtotime($item['date_heure'])); ?>
                                        </td>
                                        <td>
                                            <?php if (($item['type_source'] ?? 'consultation') === 'rendez_vous'): ?>
                                                <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-calendar-event me-1"></i> RDV</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary px-2 py-1"><i class="bi bi-stethoscope me-1"></i> Consultation</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="index.php?action=patient_dossier&id=<?= $item['patient_id']; ?>" class="text-decoration-none fw-bold text-dark">
                                                <?= htmlspecialchars(($item['prenom'] ?? '') . ' ' . ($item['nom'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($item['telephone'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?= htmlspecialchars($item['motif'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?= htmlspecialchars($item['diagnostic'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="text-end pe-3">
                                            <?php if (($item['type_source'] ?? 'consultation') === 'rendez_vous'): ?>
                                                <a href="index.php?action=consultations_nouveau&patient_id=<?= $item['patient_id']; ?>" class="btn-action-badge btn-action-warning" title="Transformer le rendez-vous en consultation">
                                                    <i class="bi bi-check2-circle me-1"></i> Démarrer
                                                </a>
                                            <?php else: ?>
                                                <a href="index.php?action=patient_dossier&id=<?= $item['patient_id']; ?>" class="btn-action-badge btn-action-danger" title="Ouvrir le dossier du patient">
                                                    <i class="bi bi-pencil-square me-1"></i> Traiter
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-check-circle fs-2 d-block mb-1 text-success opacity-75"></i>
                                        Aucun élément programmé pour aujourd'hui. Bon travail Docteur !
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- SECTION 2 : CONSULTATIONS ET RDV À VENIR -->
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold text-dark m-0">
                        <i class="bi bi-calendar-event-fill text-primary me-2"></i> Éléments à venir
                    </h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold">
                        <?= count($consultationsAVenir); ?> Programmé(s)
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th class="py-3 ps-3">Date & Heure</th>
                                <th class="py-3">Type</th>
                                <th class="py-3">Patient</th>
                                <th class="py-3">Téléphone</th>
                                <th class="py-3">Motif / Notes</th>
                                <th class="py-3">Diagnostic / Statut</th>
                                <th class="py-3 text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($consultationsAVenir)): ?>
                                <?php foreach ($consultationsAVenir as $item): ?>
                                    <tr>
                                        <td class="ps-3 fw-semibold text-secondary">
                                            <?= date('d/m/Y H:i', strtotime($item['date_heure'])); ?>
                                        </td>
                                        <td>
                                            <?php if (($item['type_source'] ?? 'consultation') === 'rendez_vous'): ?>
                                                <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-calendar-event me-1"></i> RDV</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary px-2 py-1"><i class="bi bi-stethoscope me-1"></i> Consultation</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="index.php?action=patient_dossier&id=<?= $item['patient_id']; ?>" class="text-decoration-none fw-bold text-primary">
                                                <?= htmlspecialchars(($item['prenom'] ?? '') . ' ' . ($item['nom'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($item['telephone'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?= htmlspecialchars($item['motif'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?= htmlspecialchars($item['diagnostic'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="text-end pe-3">
                                            <a href="index.php?action=patient_dossier&id=<?= $item['patient_id']; ?>" class="btn-action-badge btn-action-primary" title="Ouvrir le dossier du patient">
                                                <i class="bi bi-eye me-1"></i> Détails
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-calendar-x fs-2 d-block mb-1 text-secondary opacity-50"></i>
                                        Aucun élément ultérieur programmé.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>