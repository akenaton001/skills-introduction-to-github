<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Modifier le dossier : <?= htmlspecialchars($dispute['objet']) ?></h2>
            <p class="text-muted small mb-0">Ajustez les informations initiales du dossier de contentieux.</p>
        </div>
        <a href="index.php?action=disputes_show&id=<?= $dispute['id'] ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Annuler
        </a>
    </div>

    <form action="index.php?action=disputes_update" method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm p-4">
        <input type="hidden" name="id" value="<?= $dispute['id'] ?>">

        <div class="row g-3">
            <!-- Employé concerné -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Employé concerné</label>
                <select name="employee_id" class="form-select">
                    <option value="">-- Aucun / Non applicable --</option>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= $emp['id'] ?>" <?= (isset($dispute['employee_id']) && $dispute['employee_id'] == $emp['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($emp['prenom'] . ' ' . $emp['nom']) ?> (<?= htmlspecialchars($emp['matricule'] ?? '') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Type -->
            <div class="col-md-3">
                <label class="form-label fw-bold">Type de litige</label>
                <select name="type" class="form-select">
                    <option value="Interne" <?= ($dispute['type'] ?? '') === 'Interne' ? 'selected' : '' ?>>Interne</option>
                    <option value="Externe" <?= ($dispute['type'] ?? '') === 'Externe' ? 'selected' : '' ?>>Externe</option>
                </select>
            </div>

            <!-- Gravité -->
            <div class="col-md-3">
                <label class="form-label fw-bold">Niveau de gravité</label>
                <select name="gravite" class="form-select">
                    <option value="Faible" <?= ($dispute['gravite'] ?? '') === 'Faible' ? 'selected' : '' ?>>Faible</option>
                    <option value="Moyen" <?= ($dispute['gravite'] ?? '') === 'Moyen' ? 'selected' : '' ?>>Moyen</option>
                    <option value="Élevé" <?= ($dispute['gravite'] ?? '') === 'Élevé' ? 'selected' : '' ?>>Élevé</option>
                    <option value="Critique" <?= ($dispute['gravite'] ?? '') === 'Critique' ? 'selected' : '' ?>>Critique</option>
                </select>
            </div>

            <!-- Catégorie -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Catégorie</label>
                <input type="text" name="categorie" class="form-control" value="<?= htmlspecialchars($dispute['categorie'] ?? '') ?>" placeholder="Ex: Bagarre, Vol, Retard de livraison...">
            </div>

            <!-- Impact financier -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Impact financier (FCFA)</label>
                <input type="number" name="impact_financier" class="form-control" value="<?= htmlspecialchars($dispute['impact_financier'] ?? 0) ?>">
            </div>

            <!-- Objet -->
            <div class="col-12">
                <label class="form-label fw-bold">Objet du dossier <span class="text-danger">*</span></label>
                <input type="text" name="objet" class="form-control" required value="<?= htmlspecialchars($dispute['objet'] ?? '') ?>">
            </div>

            <!-- Description -->
            <div class="col-12">
                <label class="form-label fw-bold">Description des faits</label>
                <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($dispute['description'] ?? '') ?></textarea>
            </div>

            <!-- Tiers impliqués -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Parties / Tiers impliqués</label>
                <input type="text" name="tiers_impliques" class="form-control" value="<?= htmlspecialchars($dispute['tiers_impliques'] ?? '') ?>">
            </div>

            <!-- Date ouverture -->
            <div class="col-md-3">
                <label class="form-label fw-bold">Date d'ouverture</label>
                <input type="date" name="date_ouverture" class="form-control" value="<?= $dispute['date_ouverture'] ?? date('Y-m-d') ?>">
            </div>

            <!-- Date limite -->
            <div class="col-md-3">
                <label class="form-label fw-bold">Date limite de réponse</label>
                <input type="date" name="date_limite_reponse" class="form-control" value="<?= $dispute['date_limite_reponse'] ?? '' ?>">
            </div>

            <!-- Fichier joint -->
            <div class="col-12">
                <label class="form-label fw-bold">Remplacer le document principal</label>
                <input type="file" name="fichier_joint" class="form-control">
                <?php if (!empty($dispute['fichier_joint'])): ?>
                    <div class="form-text text-muted mt-1">
                        Fichier actuel : <strong><?= htmlspecialchars($dispute['fichier_joint']) ?></strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-end gap-2">
            <a href="index.php?action=disputes_show&id=<?= $dispute['id'] ?>" class="btn btn-light">Annuler</a>
            <button type="submit" class="btn btn-warning fw-bold px-4">
                <i class="fas fa-save me-1"></i> Enregistrer les modifications
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>