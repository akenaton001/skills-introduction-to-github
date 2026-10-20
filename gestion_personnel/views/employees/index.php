<?php include __DIR__ . '/../layout/header.php'; ?>

<style>
    .btn-outline-custom-secondary {
        color: #f6b036;
        border-color: #f6b036;
        border-radius: 20px;
        transition: all 0.3s ease;
    }
    .btn-outline-custom-secondary:hover {
        background-color: #f6b036;
        color: #ffffff;
        border-color: #f6b036;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold m-0" style="color: var(--primary-color);">Liste du personnel</h3>
        <small class="text-muted">Gestion dynamique des fichiers et informations employés</small>
    </div>
    <div>
        <a href="index.php?action=import" class="btn btn-outline-custom-secondary me-2">
            <i class="fa-solid fa-file-import me-1"></i> Importer CSV
        </a>
        <a href="index.php?action=create" class="btn btn-custom-primary">
            <i class="fa-solid fa-plus me-1"></i> Ajouter un employé
        </a>
    </div>
</div>

<!-- Notification Flash (Succès / Erreur) -->
<?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-<?= $_SESSION['flash']['type']; ?> alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
        <i class="fa-solid <?= $_SESSION['flash']['type'] === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation'; ?> me-2"></i>
        <?= htmlspecialchars($_SESSION['flash']['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<!-- Formulaire de Filtres & Recherche -->
<form method="GET" action="index.php" class="row g-2 mb-4">
    <div class="col-md-3">
        <select name="contrat" class="form-select form-select-custom" onchange="this.form.submit()">
            <option value="">Tous les contrats (CDD, CDI...)</option>
            <option value="CDI" <?= isset($_GET['contrat']) && $_GET['contrat'] === 'CDI' ? 'selected' : '' ?>>CDI</option>
            <option value="CDD" <?= isset($_GET['contrat']) && $_GET['contrat'] === 'CDD' ? 'selected' : '' ?>>CDD</option>
            <option value="Stage" <?= isset($_GET['contrat']) && $_GET['contrat'] === 'Stage' ? 'selected' : '' ?>>Stage</option>
            <option value="Autre" <?= isset($_GET['contrat']) && $_GET['contrat'] === 'Autre' ? 'selected' : '' ?>>Autre</option>
        </select>
    </div>
    
    <div class="col-md-3">
        <select name="poste" class="form-select form-select-custom" onchange="this.form.submit()">
            <option value="">Tous les postes</option>
            <?php if (!empty($postes)): ?>
                <?php foreach ($postes as $p): ?>
                    <option value="<?= htmlspecialchars($p['intitule_poste']) ?>" <?= isset($_GET['poste']) && $_GET['poste'] === $p['intitule_poste'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['intitule_poste']) ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <div class="col-md-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control form-control-custom" placeholder="Rechercher par nom, CNI, téléphone..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button type="submit" class="btn btn-custom-primary" style="border-radius: 0 20px 20px 0;">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </div>

    <?php if (!empty($_GET['contrat']) || !empty($_GET['poste']) || !empty($_GET['search'])): ?>
        <div class="col-md-2 d-flex align-items-center">
            <a href="index.php" class="btn btn-sm btn-link text-danger text-decoration-none">
                <i class="fa-solid fa-xmark me-1"></i> Réinitialiser
            </a>
        </div>
    <?php endif; ?>
</form>

<!-- Tableau -->
<div class="card card-table">
    <div class="table-responsive">
        <table class="table table-custom table-hover m-0">
            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Nom & Prénom</th>
                    <th>CNI / Tél</th>
                    <th>Poste</th>
                    <th>Contrat</th>
                    <th>Permis</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($employees)): ?>
                    <?php foreach ($employees as $emp): ?>
                    <tr>
                        <td class="fw-bold"><?= htmlspecialchars($emp['matricule']) ?></td>
                        <td>
                            <div class="fw-semibold"><?= htmlspecialchars($emp['nom'] . ' ' . $emp['prenom']) ?></div>
                            <small class="text-muted"><?= htmlspecialchars($emp['etat_civil'] ?? '') ?></small>
                        </td>
                        <td>
                            <div><?= htmlspecialchars($emp['cni'] ?? '') ?></div>
                            <small class="text-muted"><?= htmlspecialchars($emp['telephone'] ?? '') ?></small>
                        </td>
                        <td><?= htmlspecialchars($emp['intitule_poste'] ?? '') ?></td>
                        <td><span class="badge-contrat"><?= htmlspecialchars($emp['type_contrat']) ?></span></td>
                        <td>
                            <?php if (!empty($emp['numero_permis'])): ?>
                                <span class="badge bg-light text-dark border">Cat. <?= htmlspecialchars($emp['categories_permis'] ?? '') ?></span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <!-- Bouton Oeil pour voir les détails -->
                            <a href="index.php?action=show&id=<?= $emp['id'] ?>" class="btn btn-sm btn-light me-1" style="color: var(--primary-color);" title="Voir les détails">
                                <i class="fa-solid fa-eye"></i>
                            </a>

                            <?php if (!empty($emp['contrat_pdf'])): ?>
                                <a href="uploads/contracts/<?= htmlspecialchars($emp['contrat_pdf']) ?>" target="_blank" class="btn btn-sm btn-light text-danger me-1" title="Voir PDF">
                                    <i class="fa-solid fa-file-pdf"></i>
                                </a>
                            <?php endif; ?>
                            <a href="index.php?action=edit&id=<?= $emp['id'] ?>" class="btn btn-sm btn-light text-success me-1" title="Modifier">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <button type="button" 
                                    class="btn btn-sm btn-light text-danger" 
                                    onclick="openDeleteModal(<?= $emp['id'] ?>, '<?= htmlspecialchars(addslashes($emp['prenom'] . ' ' . $emp['nom'])) ?>')" 
                                    title="Supprimer">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Aucun employé ne correspond à votre recherche.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de Confirmation de Suppression -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="deleteModalLabel" style="color: var(--primary-color);">
                    <i class="fa-solid fa-circle-exclamation text-danger me-2"></i>Confirmation de suppression
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body py-4">
                Êtes-vous sûr de vouloir supprimer définitivement l'employé <strong id="deleteEmployeeName"></strong> ?
                <div class="text-muted small mt-2">
                    Cette action est irréversible et supprimera le fichier de contrat associé s'il existe.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                <a id="confirmDeleteBtn" href="#" class="btn btn-danger rounded-pill px-4">
                    <i class="fa-solid fa-trash me-1"></i> Supprimer
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function openDeleteModal(id, name) {
    document.getElementById('deleteEmployeeName').innerText = name;
    document.getElementById('confirmDeleteBtn').href = 'index.php?action=delete&id=' + id;
    
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    deleteModal.show();
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>