<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="index.php?action=incidents_show&id=<?= $incident['id'] ?>" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="fas fa-arrow-left me-1"></i> Annuler et retourner
        </a>
        <h2 class="h4 text-dark fw-bold mb-0">Modifier l'incident <?= htmlspecialchars($incident['reference'] ?? '#' . $incident['id']) ?></h2>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="index.php?action=incidents_update" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $incident['id'] ?>">

                <div class="row g-3 mb-3">
                    <!-- Titre de l'incident -->
                    <div class="col-md-8">
                        <label for="titre" class="form-label fw-semibold">Titre / Objet de l'incident <span class="text-danger">*</span></label>
                        <input type="text" name="titre" id="titre" class="form-control" value="<?= htmlspecialchars($incident['titre']) ?>" required>
                    </div>

                    <!-- Date et Heure -->
                    <div class="col-md-4">
                        <label for="date_incident" class="form-label fw-semibold">Date et Heure <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="date_incident" id="date_incident" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($incident['date_incident'])) ?>" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <!-- Type d'incident -->
                    <div class="col-md-4">
                        <label for="type_incident" class="form-label fw-semibold">Type d'incident <span class="text-danger">*</span></label>
                        <select name="type_incident" id="type_incident" class="form-select" required>
                            <?php 
                                $types = ['Accident du travail', 'Matériel / Véhicule', 'Discipline / Insubordination', 'Sécurité / Vol', 'Autre'];
                                foreach ($types as $t): 
                            ?>
                                <option value="<?= $t ?>" <?= $incident['type_incident'] === $t ? 'selected' : '' ?>><?= $t ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Gravité -->
                    <div class="col-md-4">
                        <label for="gravite" class="form-label fw-semibold">Niveau de Gravité <span class="text-danger">*</span></label>
                        <select name="gravite" id="gravite" class="form-select" required>
                            <?php 
                                $gravites = ['Faible', 'Moyen', 'Élevé', 'Critique'];
                                foreach ($gravites as $g): 
                            ?>
                                <option value="<?= $g ?>" <?= $incident['gravite'] === $g ? 'selected' : '' ?>><?= $g ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Statut -->
                    <div class="col-md-4">
                        <label for="statut" class="form-label fw-semibold">Statut <span class="text-danger">*</span></label>
                        <select name="statut" id="statut" class="form-select" required>
                            <?php 
                                $statuts = ['Signalé', 'En cours', 'Résolu', 'Classé sans suite'];
                                foreach ($statuts as $s): 
                            ?>
                                <option value="<?= $s ?>" <?= $incident['statut'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endforeach; ?>
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
                                    <option value="<?= $emp['id'] ?>" <?= $incident['employee_id'] == $emp['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($emp['nom'] . ' ' . $emp['prenom']) ?> (<?= htmlspecialchars($emp['matricule']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Lieu -->
                    <div class="col-md-6">
                        <label for="lieu" class="form-label fw-semibold">Lieu de l'événement</label>
                        <input type="text" name="lieu" id="lieu" class="form-control" value="<?= htmlspecialchars($incident['lieu'] ?? '') ?>">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <!-- Description détaillée -->
                    <div class="col-md-6">
                        <label for="description" class="form-label fw-semibold">Description détaillée des faits <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" class="form-control" rows="4" required><?= htmlspecialchars($incident['description']) ?></textarea>
                    </div>

                    <!-- Actions correctives -->
                    <div class="col-md-6">
                        <label for="actions_correctives" class="form-label fw-semibold">Actions immédiates / correctives</label>
                        <textarea name="actions_correctives" id="actions_correctives" class="form-control" rows="4"><?= htmlspecialchars($incident['actions_correctives'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <!-- Impact Financier -->
                    <div class="col-md-6">
                        <label for="impact_financier" class="form-label fw-semibold">Impact financier estimé (FCFA)</label>
                        <input type="number" step="0.01" min="0" name="impact_financier" id="impact_financier" class="form-control" value="<?= htmlspecialchars($incident['impact_financier']) ?>">
                    </div>

                    <!-- Pièces Jointes Multiples -->
                    <div class="col-md-6">
                        <label for="fichiers_joints" class="form-label fw-semibold">Ajouter des pièces jointes / photos (PDF, JPG, PNG)</label>
                        <input type="file" name="fichiers_joints[]" id="fichiers_joints" class="form-control" accept=".pdf,.png,.jpg,.jpeg" multiple>
                        <small class="text-muted d-block mt-1">Maintenez <kbd>Ctrl</kbd> pour sélectionner plusieurs fichiers à ajouter.</small>
                    </div>
                </div>

                <div class="hr mb-4"></div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="index.php?action=incidents_show&id=<?= $incident['id'] ?>" class="btn btn-light border">Annuler</a>
                    <button type="submit" class="btn btn-warning px-4 fw-semibold">
                        <i class="fas fa-save me-1"></i> Mettre à jour l'incident
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>