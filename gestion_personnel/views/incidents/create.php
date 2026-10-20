<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="index.php?action=incidents" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="fas fa-arrow-left me-1"></i> Retour à la liste
        </a>
        <h2 class="h4 text-dark fw-bold mb-0">Déclarer un nouvel incident</h2>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="index.php?action=incidents_store" method="POST" enctype="multipart/form-data">
                
                <div class="row g-3 mb-3">
                    <!-- Titre de l'incident -->
                    <div class="col-md-8">
                        <label for="titre" class="form-label fw-semibold">Titre / Objet de l'incident <span class="text-danger">*</span></label>
                        <input type="text" name="titre" id="titre" class="form-control" required placeholder="Ex: Casse matériel lors de la manutention">
                    </div>

                    <!-- Date et Heure -->
                    <div class="col-md-4">
                        <label for="date_incident" class="form-label fw-semibold">Date et Heure <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="date_incident" id="date_incident" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <!-- Type d'incident -->
                    <div class="col-md-4">
                        <label for="type_incident" class="form-label fw-semibold">Type d'incident <span class="text-danger">*</span></label>
                        <select name="type_incident" id="type_incident" class="form-select" required>
                            <option value="Accident du travail">Accident du travail</option>
                            <option value="Matériel / Véhicule">Matériel / Véhicule</option>
                            <option value="Discipline / Insubordination">Discipline / Insubordination</option>
                            <option value="Sécurité / Vol">Sécurité / Vol</option>
                            <option value="Autre" selected>Autre</option>
                        </select>
                    </div>

                    <!-- Gravité -->
                    <div class="col-md-4">
                        <label for="gravite" class="form-label fw-semibold">Niveau de Gravité <span class="text-danger">*</span></label>
                        <select name="gravite" id="gravite" class="form-select" required>
                            <option value="Faible">Faible</option>
                            <option value="Moyen" selected>Moyen</option>
                            <option value="Élevé">Élevé</option>
                            <option value="Critique">Critique</option>
                        </select>
                    </div>

                    <!-- Statut initial -->
                    <div class="col-md-4">
                        <label for="statut" class="form-label fw-semibold">Statut Initial</label>
                        <select name="statut" id="statut" class="form-select">
                            <option value="Signalé" selected>Signalé</option>
                            <option value="En cours">En cours</option>
                            <option value="Résolu">Résolu</option>
                            <option value="Classé sans suite">Classé sans suite</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <!-- Employé impliqué -->
                    <div class="col-md-6">
                        <label for="employee_id" class="form-label fw-semibold">Employé impliqué / concerné</label>
                        <select name="employee_id" id="employee_id" class="form-select">
                            <option value="">-- Aucun / Non identifié --</option>
                            <?php if (!empty($employees)): ?>
                                <?php foreach ($employees as $emp): ?>
                                    <option value="<?= $emp['id'] ?>">
                                        <?= htmlspecialchars($emp['nom'] . ' ' . $emp['prenom']) ?> (<?= htmlspecialchars($emp['matricule']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Lieu -->
                    <div class="col-md-6">
                        <label for="lieu" class="form-label fw-semibold">Lieu de l'événement</label>
                        <input type="text" name="lieu" id="lieu" class="form-control" placeholder="Ex: Entrepôt B, Bureau 102, Sur la route...">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <!-- Description détaillée -->
                    <div class="col-md-6">
                        <label for="description" class="form-label fw-semibold">Description détaillée des faits <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" class="form-control" rows="4" required placeholder="Expliquez ce qui s'est passé..."></textarea>
                    </div>

                    <!-- Actions correctives -->
                    <div class="col-md-6">
                        <label for="actions_correctives" class="form-label fw-semibold">Actions immédiates / correctives</label>
                        <textarea name="actions_correctives" id="actions_correctives" class="form-control" rows="4" placeholder="Mesures prises suite à l'incident..."></textarea>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <!-- Impact Financier -->
                    <div class="col-md-6">
                        <label for="impact_financier" class="form-label fw-semibold">Impact financier estimé (FCFA)</label>
                        <input type="number" step="0.01" min="0" name="impact_financier" id="impact_financier" class="form-control" placeholder="0">
                    </div>

                    <!-- Pièces Jointes Multiples -->
                    <div class="col-md-6">
                        <label for="fichiers_joints" class="form-label fw-semibold">Pièces jointes / Photos (PDF, JPG, PNG)</label>
                        <input type="file" name="fichiers_joints[]" id="fichiers_joints" class="form-control" accept=".pdf,.png,.jpg,.jpeg" multiple>
                        <small class="text-muted">Maintenez la touche <kbd>Ctrl</kbd> (ou <kbd>Cmd</kbd>) enfoncée pour sélectionner plusieurs fichiers.</small>
                    </div>
                </div>

                <div class="hr mb-4"></div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="index.php?action=incidents" class="btn btn-light border">Annuler</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Enregistrer l'incident
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>