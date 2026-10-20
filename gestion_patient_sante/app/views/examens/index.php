<?php 
$pageTitle = "Gestion des Examens - Jàmm Santé";
$page = 'examens';

$examens = $examens ?? [];
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
                        <i class="bi bi-file-earmark-medical text-info me-2"></i> Gestion des Examens
                    </h2>
                    <p class="text-muted mb-0">Suivi global de la file d'attente, des prélèvements et des résultats d'examens.</p>
                </div>
            </div>

            <!-- Tableau des examens -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                                <tr>
                                    <th class="py-3 ps-4">Date / Heure</th>
                                    <th>Patient</th>
                                    <th>Type d'Examen / Titre</th>
                                    <th>Prescripteur</th>
                                    <th>Statut</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($examens)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <div class="my-3">
                                                <i class="bi bi-file-earmark-medical fa-3x text-light mb-3 d-block" style="font-size: 3rem;"></i>
                                                <p class="mb-0">Aucune demande d'examen enregistrée pour le moment.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($examens as $ex): ?>
                                        <tr>
                                            <td class="ps-4 py-3"><?= htmlspecialchars($ex['date_prescription'] ?? date('Y-m-d H:i')); ?></td>
                                            <td>
                                                <span class="fw-bold text-dark d-block"><?= htmlspecialchars(($ex['patient_prenom'] ?? $ex['prenom'] ?? '') . ' ' . ($ex['patient_nom'] ?? $ex['nom'] ?? '')); ?></span>
                                                <small class="text-muted">Réf: <?= htmlspecialchars($ex['code_patient'] ?? ''); ?></small>
                                            </td>
                                            <td>
                                                <span class="fw-semibold text-dark d-block"><?= htmlspecialchars($ex['titre'] ?? 'Examen'); ?></span>
                                                <small class="text-muted text-uppercase"><?= htmlspecialchars($ex['type_examen'] ?? ''); ?></small>
                                            </td>
                                            <td>
                                                <?php 
                                                    if (!empty($ex['medecin_nom'])) {
                                                        echo "Dr. " . htmlspecialchars($ex['medecin_prenom'] . ' ' . $ex['medecin_nom']);
                                                    } else {
                                                        echo htmlspecialchars($ex['medecin_prescripteur'] ?? 'Dr. Non spécifié');
                                                    }
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                    $statut = $ex['statut'] ?? 'En attente';
                                                    $badgeClass = 'bg-secondary';
                                                    if ($statut === 'En attente') {
                                                        $badgeClass = 'bg-warning text-dark bg-opacity-15 border border-warning border-opacity-25';
                                                    } elseif ($statut === 'En cours') {
                                                        $badgeClass = 'bg-info text-dark bg-opacity-10 border border-info border-opacity-25';
                                                    } elseif ($statut === 'Terminé' || $statut === 'Validé') {
                                                        $badgeClass = 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
                                                    }
                                                ?>
                                                <span class="badge <?= $badgeClass ?> px-3 py-2 rounded-pill fw-bold">
                                                    <?= htmlspecialchars($statut); ?>
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <button type="button" class="btn btn-outline-info btn-sm rounded-pill px-3 me-1 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalExamen<?= $ex['id']; ?>">
                                                    <i class="bi bi-pencil-square me-1"></i> Résultats
                                                </button>
                                                
                                                <a href="index.php?action=patient_dossier&id=<?= $ex['patient_id']; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm" title="Voir le dossier">
                                                    <i class="bi bi-folder2-open"></i>
                                                </a>
                                            </td>
                                        </tr>

                                        <!-- Modal de saisie des résultats -->
                                        <div class="modal fade" id="modalExamen<?= $ex['id']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content rounded-4 border-0 shadow">
                                                    <!-- Ajout de enctype pour permettre l'upload du fichier -->
                                                    <form action="index.php?action=update_global_examen" method="POST" enctype="multipart/form-data">
                                                        <div class="modal-header border-0 pb-0">
                                                            <h5 class="modal-title fw-bold">Traitement de l'examen : <?= htmlspecialchars($ex['titre']); ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                            <input type="hidden" name="examen_id" value="<?= $ex['id']; ?>">
                                                            
                                                            <div class="alert alert-light border rounded-3 mb-3">
                                                                <strong>Patient :</strong> <?= htmlspecialchars(($ex['patient_prenom'] ?? $ex['prenom'] ?? '') . ' ' . ($ex['patient_nom'] ?? $ex['nom'] ?? '')); ?> (Réf: <?= htmlspecialchars($ex['code_patient'] ?? ''); ?>)<br>
                                                                <strong>Sexe :</strong> <?= htmlspecialchars($ex['sexe'] ?? 'N/C'); ?>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Statut de l'examen</label>
                                                                <select name="statut" class="form-select rounded-3">
                                                                    <option value="En attente" <?= ($statut === 'En attente') ? 'selected' : ''; ?>>🟡 En attente</option>
                                                                    <option value="En cours" <?= ($statut === 'En cours') ? 'selected' : ''; ?>>🔵 En cours</option>
                                                                    <option value="Terminé" <?= ($statut === 'Terminé' || $statut === 'Validé') ? 'selected' : ''; ?>>🟢 Terminé / Validé</option>
                                                                </select>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Résultats / Compte-rendu</label>
                                                                <textarea name="resultats" rows="4" class="form-control rounded-3" placeholder="Saisir les résultats biologiques ou le compte-rendu..."><?= htmlspecialchars($ex['resultats'] ?? ''); ?></textarea>
                                                            </div>

                                                            <!-- AJOUT : Champ pour joindre le fichier de résultat -->
                                                            <div class="mb-3">
                                                                <label for="fichier_resultat_<?= $ex['id']; ?>" class="form-label fw-semibold">
                                                                    <i class="bi bi-paperclip text-info me-1"></i> Joindre un fichier de résultat (PDF, Image...)
                                                                </label>
                                                                <input type="file" class="form-control rounded-3" id="fichier_resultat_<?= $ex['id']; ?>" name="fichier_resultat" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                                                <div class="form-text text-muted">Ce fichier sera automatiquement enregistré dans les pièces jointes du dossier du patient.</div>
                                                            </div>

                                                        </div>
                                                        <div class="modal-footer border-0 pt-0">
                                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                                                            <button type="submit" class="btn btn-info text-white rounded-pill px-4 fw-semibold">Enregistrer</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
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