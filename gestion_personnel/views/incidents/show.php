<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid py-4">
    <!-- En-tête de navigation -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="index.php?action=incidents" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="fas fa-arrow-left me-1"></i> Retour à la liste
            </a>
            <h2 class="h4 text-dark fw-bold mb-0">
                Incident <?= htmlspecialchars($incident['reference'] ?? '#' . $incident['id']) ?>
            </h2>
        </div>
        <div class="d-flex gap-2">
            <?php if (!empty($incident['employee_id'])): ?>
                <a href="index.php?action=disputes_create&employee_id=<?= $incident['employee_id'] ?>&incident_id=<?= $incident['id'] ?>" class="btn btn-outline-danger shadow-sm">
                    <i class="fas fa-gavel me-1"></i> Transférer en Contentieux
                </a>
            <?php endif; ?>
            <a href="index.php?action=incidents_edit&id=<?= $incident['id'] ?>" class="btn btn-warning shadow-sm">
                <i class="fas fa-edit me-1"></i> Modifier
            </a>
            <a href="index.php?action=incidents_delete&id=<?= $incident['id'] ?>" class="btn btn-danger shadow-sm" onclick="return confirm('Voulez-vous vraiment supprimer cet incident ?');">
                <i class="fas fa-trash me-1"></i> Supprimer
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Colonne principale (Détails de l'incident) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <?= htmlspecialchars($incident['titre'] ?? 'Sans titre') ?>
                    </h5>
                    <?php
                        $badgeStatut = match($incident['statut'] ?? '') {
                            'Signalé' => 'bg-secondary',
                            'En cours' => 'bg-primary',
                            'Résolu' => 'bg-success',
                            'Classé sans suite' => 'bg-dark',
                            default => 'bg-secondary'
                        };
                    ?>
                    <span class="badge <?= $badgeStatut ?> fs-6"><?= htmlspecialchars($incident['statut'] ?? 'Inconnu') ?></span>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-bold small mb-2">Description des faits</h6>
                        <div class="p-3 bg-light rounded text-dark" style="white-space: pre-line;">
                            <?= htmlspecialchars($incident['description'] ?? '') ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-bold small mb-2">Actions immédiates / correctives</h6>
                        <div class="p-3 bg-light rounded text-dark" style="white-space: pre-line;">
                            <?= !empty($incident['actions_correctives']) ? htmlspecialchars($incident['actions_correctives']) : '<span class="text-muted fst-italic">Aucune action enregistrée pour le moment.</span>' ?>
                        </div>
                    </div>

                    <!-- Affichage du dénouement s'il existe -->
                    <?php if (!empty($incident['denouement'])): ?>
                        <div class="mb-4">
                            <h6 class="text-uppercase text-success fw-bold small mb-2">
                                <i class="fas fa-check-circle me-1"></i> Dénouement / Clôture du dossier
                            </h6>
                            <div class="p-3 bg-success bg-opacity-10 border border-success border-opacity-25 rounded text-dark" style="white-space: pre-line;">
                                <?= htmlspecialchars($incident['denouement']) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Section Pièces Jointes avec Aperçus -->
                    <?php 
                        $fichiers = [];
                        $rawFiles = $incident['fichiers_joints'] ?? $incident['fichier_joint'] ?? null;

                        if (!empty($rawFiles)) {
                            $decoded = json_decode($rawFiles, true);
                            if (is_array($decoded)) {
                                $fichiers = $decoded;
                            } else {
                                $fichiers = array_filter(array_map('trim', explode(',', $rawFiles)));
                            }
                        }
                    ?>

                    <?php if (!empty($fichiers)): ?>
                        <div>
                            <h6 class="text-uppercase text-muted fw-bold small mb-3">
                                Pièces jointes / Documents (<?= count($fichiers) ?>)
                            </h6>
                            <div class="row g-3">
                                <?php foreach ($fichiers as $idx => $fichier): ?>
                                    <?php 
                                        $fichierTrim = trim($fichier);
                                        if (empty($fichierTrim)) continue;

                                        $ext = strtolower(pathinfo($fichierTrim, PATHINFO_EXTENSION));
                                        $filePath = 'uploads/incidents/' . $fichierTrim;
                                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        $isPdf = ($ext === 'pdf');
                                    ?>
                                    <div class="col-md-6">
                                        <div class="card h-100 border shadow-sm">
                                            <!-- Visuel / Miniature -->
                                            <?php if ($isImage): ?>
                                                <img src="<?= $filePath ?>" class="card-img-top object-fit-cover" style="height: 130px;" alt="Aperçu">
                                            <?php elseif ($isPdf): ?>
                                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 130px;">
                                                    <i class="fas fa-file-pdf text-danger display-4"></i>
                                                </div>
                                            <?php else: ?>
                                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 130px;">
                                                    <i class="fas fa-file-alt text-secondary display-4"></i>
                                                </div>
                                            <?php endif; ?>

                                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                                <span class="fw-semibold text-truncate small d-block mb-2" title="<?= htmlspecialchars($fichierTrim) ?>">
                                                    <?= htmlspecialchars($fichierTrim) ?>
                                                </span>
                                                <div class="d-flex gap-2">
                                                    <?php if ($isImage || $isPdf): ?>
                                                        <button class="btn btn-sm btn-outline-primary flex-grow-1" data-bs-toggle="modal" data-bs-target="#modalFile<?= $idx ?>">
                                                            <i class="fas fa-eye me-1"></i> Aperçu
                                                        </button>
                                                    <?php endif; ?>
                                                    <a href="<?= $filePath ?>" download class="btn btn-sm btn-primary flex-grow-1">
                                                        <i class="fas fa-download me-1"></i> Télécharger
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal d'aperçu -->
                                    <?php if ($isImage || $isPdf): ?>
                                        <div class="modal fade" id="modalFile<?= $idx ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title text-truncate"><?= htmlspecialchars($fichierTrim) ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                    </div>
                                                    <div class="modal-body text-center p-0">
                                                        <?php if ($isImage): ?>
                                                            <img src="<?= $filePath ?>" class="img-fluid rounded-bottom" alt="Aperçu">
                                                        <?php elseif ($isPdf): ?>
                                                            <iframe src="<?= $filePath ?>" style="width: 100%; height: 500px;" frameborder="0"></iframe>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Colonne latérale (Informations, Formulaire d'évolution & Métadonnées) -->
        <div class="col-lg-4">
            <!-- Mise à jour rapide / Dénouement -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="card-title fw-bold mb-0"><i class="fas fa-stream text-primary me-2"></i>Évolution du dossier</h6>
                </div>
                <div class="card-body">
                    <form action="index.php?action=incidents_update" method="POST">
                        <input type="hidden" name="id" value="<?= $incident['id'] ?>">
                        <input type="hidden" name="titre" value="<?= htmlspecialchars($incident['titre'] ?? '') ?>">
                        <input type="hidden" name="description" value="<?= htmlspecialchars($incident['description'] ?? '') ?>">

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Statut du dossier</label>
                            <select name="statut" class="form-select form-select-sm">
                                <option value="Signalé" <?= ($incident['statut'] ?? '') === 'Signalé' ? 'selected' : '' ?>>Signalé</option>
                                <option value="En cours" <?= ($incident['statut'] ?? '') === 'En cours' ? 'selected' : '' ?>>En cours d'instruction</option>
                                <option value="Résolu" <?= ($incident['statut'] ?? '') === 'Résolu' ? 'selected' : '' ?>>Résolu / Clôturé</option>
                                <option value="Classé sans suite" <?= ($incident['statut'] ?? '') === 'Classé sans suite' ? 'selected' : '' ?>>Classé sans suite</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Dénouement / Décision finale</label>
                            <textarea name="denouement" class="form-control form-control-sm" rows="3" placeholder="Saisissez la résolution finale..."><?= htmlspecialchars($incident['denouement'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-sm btn-success w-100 shadow-sm">
                            <i class="fas fa-save me-1"></i> Enregistrer l'évolution
                        </button>
                    </form>
                </div>
            </div>

            <!-- Carte Employé -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="card-title fw-bold mb-0">Employé Impliqué</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($incident['nom'])): ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                <i class="fas fa-user fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold"><?= htmlspecialchars(($incident['prenom'] ?? '') . ' ' . $incident['nom']) ?></h6>
                                <span class="badge bg-light text-muted border">Matricule : <?= htmlspecialchars($incident['matricule'] ?? 'N/A') ?></span>
                            </div>
                        </div>
                        <a href="index.php?action=show&id=<?= $incident['employee_id'] ?>" class="btn btn-sm btn-outline-secondary w-100">
                            <i class="fas fa-id-card me-1"></i> Voir la fiche employé
                        </a>
                    <?php else: ?>
                        <p class="text-muted mb-0 fst-italic">Aucun employé directement lié à cet incident.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Carte Métadonnées -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="card-title fw-bold mb-0">Informations & Gravité</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Niveau de Gravité</span>
                            <?php
                                $badgeGravite = match($incident['gravite'] ?? '') {
                                    'Faible' => 'bg-info text-dark',
                                    'Moyen' => 'bg-warning text-dark',
                                    'Élevé' => 'bg-danger text-white',
                                    'Critique' => 'bg-dark text-white',
                                    default => 'bg-secondary'
                                };
                            ?>
                            <span class="badge <?= $badgeGravite ?>"><?= htmlspecialchars($incident['gravite'] ?? 'N/A') ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Type d'incident</span>
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($incident['type_incident'] ?? 'Non précisé') ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Date & Heure</span>
                            <span class="fw-semibold text-dark">
                                <?= !empty($incident['date_incident']) ? date('d/m/Y H:i', strtotime($incident['date_incident'])) : 'Non renseignée' ?>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Lieu</span>
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($incident['lieu'] ?? 'Non spécifié') ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Impact financier</span>
                            <span class="fw-bold text-danger"><?= number_format($incident['impact_financier'] ?? 0, 0, ',', ' ') ?> FCFA</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>