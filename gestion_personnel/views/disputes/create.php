<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="mb-4">
        <a href="index.php?action=disputes" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="fa-solid fa-arrow-left me-1"></i>Retour à la liste
        </a>
        <h3 class="fw-bold" style="color: var(--primary-color);">Nouveau Dossier de Contentieux</h3>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-3 p-md-4">
            <form action="index.php?action=disputes_store" method="POST" enctype="multipart/form-data">
                
                <div class="row g-3">
                    <!-- Type de contentieux -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-bold">Type de contentieux <span class="text-danger">*</span></label>
                        <select name="type" class="form-select form-select-custom" required>
                            <option value="Interne">Interne</option>
                            <option value="Externe">Externe</option>
                        </select>
                    </div>

                    <!-- Catégorie -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-bold">Catégorie <span class="text-danger">*</span></label>
                        <input type="text" name="categorie" class="form-control form-control-custom" placeholder="Ex: Discipline, Prud'hommes..." required>
                    </div>

                    <!-- Degré de gravité -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-bold">Degré de gravité <span class="text-danger">*</span></label>
                        <select name="gravite" class="form-select form-select-custom" required>
                            <option value="Faible">Faible</option>
                            <option value="Moyen" selected>Moyen</option>
                            <option value="Élevé">Élevé</option>
                            <option value="Critique">Critique</option>
                        </select>
                    </div>

                    <!-- Employé rattaché (Optionnel) -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold">Employé concerné (Optionnel)</label>
                        <select name="employee_id" class="form-select form-select-custom">
                            <option value="">-- Aucun / Litige général --</option>
                            <?php if (!empty($employees)): ?>
                                <?php foreach ($employees as $emp): ?>
                                    <option value="<?= $emp['id'] ?>">
                                        <?= htmlspecialchars($emp['prenom'] . ' ' . $emp['nom'] . ' (' . $emp['matricule'] . ')') ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Tiers impliqués -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold">Tiers / Parties impliquées</label>
                        <input type="text" name="tiers_impliques" class="form-control form-control-custom" placeholder="Ex: Cabinet Avocat X, Inspecteur M. Diop...">
                    </div>

                    <!-- Objet -->
                    <div class="col-12">
                        <label class="form-label fw-bold">Objet du litige <span class="text-danger">*</span></label>
                        <input type="text" name="objet" class="form-control form-control-custom" placeholder="Titre explicite du dossier" required>
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label class="form-label fw-bold">Description / Résumé des faits</label>
                        <textarea name="description" class="form-control" rows="3" style="border-radius: 12px;" placeholder="Explication détaillée du conflit..."></textarea>
                    </div>

                    <!-- Date ouverture -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-bold">Date d'ouverture <span class="text-danger">*</span></label>
                        <input type="date" name="date_ouverture" value="<?= date('Y-m-d') ?>" class="form-control form-control-custom" required>
                    </div>

                    <!-- Date limite réponse -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-bold">Date limite de réponse</label>
                        <input type="date" name="date_limite_reponse" class="form-control form-control-custom">
                    </div>

                    <!-- Statut -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-bold">Statut du dossier <span class="text-danger">*</span></label>
                        <select name="statut" class="form-select form-select-custom" required>
                            <option value="En cours">En cours</option>
                            <option value="En attente">En attente</option>
                            <option value="En justice">En justice</option>
                            <option value="Résolu">Résolu</option>
                            <option value="Classé sans suite">Classé sans suite</option>
                        </select>
                    </div>

                    <!-- Décision / Issue -->
                    <div class="col-12 col-md-8">
                        <label class="form-label fw-bold">Décision / Solution apportée</label>
                        <input type="text" name="decision" class="form-control form-control-custom" placeholder="Ex: Accord transactionnel, Blâme notifié...">
                    </div>

                    <!-- Impact Financier -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-bold">Impact financier (FCFA)</label>
                        <input type="number" step="0.01" name="impact_financier" class="form-control form-control-custom" placeholder="0.00">
                    </div>

                    <!-- Document joint (Upload) -->
                    <div class="col-12">
                        <label class="form-label fw-bold">Pièce jointe (PDF, Scan, Image...)</label>
                        <input type="file" name="fichier_joint" class="form-control form-control-custom" accept=".pdf,.doc,.docx,.jpg,.png">
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-custom-primary px-4">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Enregistrer le dossier
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>