<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Dossier de Contentieux - <?= htmlspecialchars(($dispute['prenom'] ?? '') . ' ' . ($dispute['nom'] ?? 'Non spécifié')) ?></h2>
            <p class="text-muted small mb-0">Détails, évolution et historique des décisions</p>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php?action=disputes" class="btn btn-dark btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Retour à la liste
            </a>
            <a href="index.php?action=disputes_edit&id=<?= $dispute['id'] ?>" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold text-dark">
                <i class="fas fa-edit me-1"></i> Modifier le dossier
            </a>
            <a href="index.php?action=disputes_delete&id=<?= $dispute['id'] ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce dossier ?');">
                <i class="fas fa-trash me-1"></i> Supprimer
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Colonne Gauche : Détails & Historique -->
        <div class="col-lg-8">
            <!-- Bloc Détails -->
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold text-danger mb-3">Objet du dossier</h5>
                <p class="fs-5 fw-semibold mb-4"><?= htmlspecialchars($dispute['objet']) ?></p>

                <h5 class="fw-bold text-danger mb-3">Description des faits</h5>
                <div class="bg-light p-3 rounded text-secondary mb-3">
                    <?= nl2br(htmlspecialchars($dispute['description'] ?? 'Aucune description fournie.')) ?>
                </div>
            </div>

            <!-- Historique des évolutions -->
            <div class="card border-0 shadow-sm p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Historique des évolutions / Dénouements</h5>
                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addUpdateModal">
                        <i class="fas fa-plus me-1"></i> Ajouter un dénouement
                    </button>
                </div>

                <?php if (!empty($updates)): ?>
                    <div class="timeline position-relative ps-4 border-start border-2 border-primary">
                        <?php foreach ($updates as $up): ?>
                            <div class="mb-4 position-relative">
                                <span class="position-absolute top-0 start-0 translate-middle p-2 bg-primary border border-light rounded-circle ms-n4"></span>
                                <div class="bg-light p-3 rounded shadow-sm">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-secondary"><?= htmlspecialchars($up['statut']) ?></span>
                                        <small class="text-muted">
                                            <i class="far fa-clock me-1"></i>
                                            <?= date('d/m/Y à H:i', strtotime($up['created_at'])) ?>
                                        </small>
                                    </div>
                                    <p class="mb-2 text-dark"><?= nl2br(htmlspecialchars($up['decision'])) ?></p>
                                    
                                    <?php if (!empty($up['fichier_joint'])): ?>
                                        <a href="uploads/disputes/<?= htmlspecialchars($up['fichier_joint']) ?>" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill mt-2">
                                            <i class="fas fa-paperclip me-1"></i> Consulter la pièce jointe
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted italic mb-0">Aucune évolution enregistrée pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Colonne Droite : Infos & Métadonnées -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold text-danger mb-4">Statut & Classification</h5>
                
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <span class="text-muted">Statut Actuel</span>
                    <span class="badge bg-primary px-3 py-2"><?= htmlspecialchars($dispute['statut']) ?></span>
                </div>

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <span class="text-muted">Gravité</span>
                    <span class="badge bg-warning text-dark px-3 py-2"><?= htmlspecialchars($dispute['gravite']) ?></span>
                </div>

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <span class="text-muted">Type</span>
                    <span class="fw-bold"><?= htmlspecialchars($dispute['type']) ?></span>
                </div>

                <?php if (!empty($dispute['fichier_joint'])): ?>
                    <hr class="my-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Document (PDF) :</span>
                        <a href="uploads/disputes/<?= htmlspecialchars($dispute['fichier_joint']) ?>" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill">
                            <i class="fas fa-file-pdf me-1"></i> Consulter le document
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold text-danger mb-3">Parties impliquées</h5>
                <p class="mb-1 text-muted small">Employé concerné</p>
                <p class="fw-bold text-dark mb-0">
                    <?= htmlspecialchars(($dispute['prenom'] ?? '') . ' ' . ($dispute['nom'] ?? 'Non spécifié')) ?>
                </p>
                <?php if (!empty($dispute['tiers_impliques'])): ?>
                    <p class="mb-1 text-muted small mt-3">Tiers / Autres intervenants</p>
                    <p class="fw-semibold text-dark mb-0"><?= htmlspecialchars($dispute['tiers_impliques']) ?></p>
                <?php endif; ?>
            </div>

            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold text-danger mb-3">Impact</h5>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Impact financier</span>
                    <span class="fw-bold text-danger fs-5"><?= number_format($dispute['impact_financier'] ?? 0, 0, ',', ' ') ?> FCFA</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajout Dénouement -->
<div class="modal fade" id="addUpdateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="index.php?action=disputes_update_decision" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
            <input type="hidden" name="id" value="<?= $dispute['id'] ?>">
            
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Ajouter une nouvelle évolution</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nouveau statut</label>
                    <select name="statut" class="form-select">
                        <option value="En cours" <?= $dispute['statut'] === 'En cours' ? 'selected' : '' ?>>En cours</option>
                        <option value="En attente" <?= $dispute['statut'] === 'En attente' ? 'selected' : '' ?>>En attente</option>
                        <option value="En justice" <?= $dispute['statut'] === 'En justice' ? 'selected' : '' ?>>En justice</option>
                        <option value="Résolu" <?= $dispute['statut'] === 'Résolu' ? 'selected' : '' ?>>Résolu</option>
                        <option value="Classé" <?= $dispute['statut'] === 'Classé' ? 'selected' : '' ?>>Classé</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Détails / Décision <span class="text-danger">*</span></label>
                    <textarea name="decision" class="form-control" rows="4" required placeholder="Détaillez les avancements ou la décision prise..."></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Pièce jointe (facultatif)</label>
                    <input type="file" name="fichier_joint" class="form-control">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary fw-bold">Enregistrer l'évolution</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>