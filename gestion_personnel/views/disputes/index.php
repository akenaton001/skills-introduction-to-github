<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--primary-color);">Gestion des Contentieux</h3>
            <p class="text-muted small mb-0">Suivi des dossiers litigieux internes et externes</p>
        </div>
        <div>
            <a href="index.php?action=disputes_create" class="btn btn-custom-primary w-100 w-md-auto">
                <i class="fa-solid fa-plus me-2"></i>Nouveau Dossier
            </a>
        </div>
    </div>

    <!-- Barre de Filtres et Recherche -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="index.php" class="row g-2 align-items-center">
                <input type="hidden" name="action" value="disputes">

                <!-- Filtre par Statut -->
                <div class="col-12 col-md-3">
                    <select name="statut" class="form-select form-select-custom" onchange="this.form.submit()">
                        <option value="">Tous les statuts</option>
                        <option value="En cours" <?= ($_GET['statut'] ?? '') === 'En cours' ? 'selected' : '' ?>>En cours</option>
                        <option value="En attente" <?= ($_GET['statut'] ?? '') === 'En attente' ? 'selected' : '' ?>>En attente</option>
                        <option value="En justice" <?= ($_GET['statut'] ?? '') === 'En justice' ? 'selected' : '' ?>>En justice</option>
                        <option value="Résolu" <?= ($_GET['statut'] ?? '') === 'Résolu' ? 'selected' : '' ?>>Résolu</option>
                        <option value="Classé sans suite" <?= ($_GET['statut'] ?? '') === 'Classé sans suite' ? 'selected' : '' ?>>Classé sans suite</option>
                    </select>
                </div>

                <!-- Filtre par Gravité -->
                <div class="col-12 col-md-2">
                    <select name="gravite" class="form-select form-select-custom" onchange="this.form.submit()">
                        <option value="">Toutes gravités</option>
                        <option value="Faible" <?= ($_GET['gravite'] ?? '') === 'Faible' ? 'selected' : '' ?>>Faible</option>
                        <option value="Moyen" <?= ($_GET['gravite'] ?? '') === 'Moyen' ? 'selected' : '' ?>>Moyen</option>
                        <option value="Élevé" <?= ($_GET['gravite'] ?? '') === 'Élevé' ? 'selected' : '' ?>>Élevé</option>
                        <option value="Critique" <?= ($_GET['gravite'] ?? '') === 'Critique' ? 'selected' : '' ?>>Critique</option>
                    </select>
                </div>

                <!-- Filtre par Type -->
                <div class="col-12 col-md-2">
                    <select name="type" class="form-select form-select-custom" onchange="this.form.submit()">
                        <option value="">Tous les types</option>
                        <option value="Interne" <?= ($_GET['type'] ?? '') === 'Interne' ? 'selected' : '' ?>>Interne</option>
                        <option value="Externe" <?= ($_GET['type'] ?? '') === 'Externe' ? 'selected' : '' ?>>Externe</option>
                    </select>
                </div>

                <!-- Recherche globale -->
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control form-control-custom" placeholder="Rechercher par objet, employé, tiers..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        <button class="btn btn-custom-primary" type="submit">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                        <?php if (!empty($_GET['statut']) || !empty($_GET['gravite']) || !empty($_GET['type']) || !empty($_GET['search'])): ?>
                            <a href="index.php?action=disputes" class="btn btn-outline-secondary" title="Réinitialiser les filtres">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau Liste des Contentieux -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Réf / Objet</th>
                            <th>Type / Catégorie</th>
                            <th>Employé / Tiers</th>
                            <th>Gravité</th>
                            <th>Statut</th>
                            <th>Date ouverture</th>
                            <th>Document</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($disputes)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fa-solid fa-folder-open fs-3 d-block mb-2"></i>
                                    Aucun contentieux trouvé.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($disputes as $d): ?>
                                <tr>
                                    <td class="ps-3 fw-bold">
                                        #<?= $d['id'] ?><br>
                                        <span class="fw-normal text-secondary"><?= htmlspecialchars($d['objet']) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-outline-dark border text-dark mb-1 d-block w-fit">
                                            <?= htmlspecialchars($d['type']) ?>
                                        </span>
                                        <small class="text-muted"><?= htmlspecialchars($d['categorie']) ?></small>
                                    </td>
                                    <td>
                                        <?php if ($d['employee_id']): ?>
                                            <div class="fw-semibold"><?= htmlspecialchars($d['prenom'] . ' ' . $d['nom']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($d['matricule']) ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">Hors personnel</span>
                                        <?php endif; ?>
                                        <?php if (!empty($d['tiers_impliques'])): ?>
                                            <small class="d-block text-primary"><i class="fa-solid fa-user-group me-1"></i><?= htmlspecialchars($d['tiers_impliques']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $graviteClass = [
                                                'Faible' => 'bg-info text-dark',
                                                'Moyen' => 'bg-warning text-dark',
                                                'Élevé' => 'bg-danger text-white',
                                                'Critique' => 'bg-dark text-white'
                                            ][$d['gravite']] ?? 'bg-secondary';
                                        ?>
                                        <span class="badge <?= $graviteClass ?>"><?= htmlspecialchars($d['gravite']) ?></span>
                                    </td>
                                    <td>
                                        <?php 
                                            $statutClass = [
                                                'En cours' => 'bg-primary',
                                                'En attente' => 'bg-warning text-dark',
                                                'En justice' => 'bg-danger',
                                                'Résolu' => 'bg-success',
                                                'Classé sans suite' => 'bg-secondary'
                                            ][$d['statut']] ?? 'bg-secondary';
                                        ?>
                                        <span class="badge <?= $statutClass ?>"><?= htmlspecialchars($d['statut']) ?></span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($d['date_ouverture'])) ?></td>
                                    <td>
                                        <?php if (!empty($d['fichier_joint'])): ?>
                                            <a href="uploads/disputes/<?= urlencode($d['fichier_joint']) ?>" target="_blank" class="btn btn-sm btn-outline-danger" title="Voir le document">
                                                <i class="fa-solid fa-file-pdf"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-3">
                                        <!-- Bouton Voir le dossier (Œil) -->
                                        <a href="index.php?action=disputes_show&id=<?= $d['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Détails du dossier">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        <!-- Bouton Supprimer (Déclenche la Modal) -->
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $d['id'] ?>" title="Supprimer">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>

                                        <!-- Modal de confirmation de suppression -->
                                        <div class="modal fade text-start" id="deleteModal<?= $d['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $d['id'] ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow rounded-4 p-2">
                                                    <div class="modal-header border-0 pb-0 align-items-start">
                                                        <div class="d-flex align-items-center">
                                                            <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                                                <i class="fa-solid fa-circle-exclamation fs-5"></i>
                                                            </div>
                                                            <h5 class="modal-title fw-bold text-dark fs-5" id="deleteModalLabel<?= $d['id'] ?>">
                                                                Confirmation de suppression
                                                            </h5>
                                                        </div>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                    </div>
                                                    <div class="modal-body py-3">
                                                        <p class="mb-2 text-dark fs-6">
                                                            Êtes-vous sûr de vouloir supprimer définitivement le dossier de contentieux concernant <strong><?= !empty($d['employee_id']) ? htmlspecialchars($d['prenom'] . ' ' . $d['nom']) : '' ?> <?= !empty($d['employee_id']) ? '-' : '' ?> <?= htmlspecialchars($d['objet']) ?></strong> ?
                                                        </p>
                                                        <small class="text-muted d-block fs-7">
                                                            Cette action est irréversible et supprimera le fichier joint associé s'il existe.
                                                        </small>
                                                    </div>
                                                    <div class="modal-footer border-0 pt-0 gap-2 justify-content-end">
                                                        <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Annuler</button>
                                                        <a href="index.php?action=disputes_delete&id=<?= $d['id'] ?>" class="btn btn-danger rounded-pill px-4 fw-semibold">
                                                            <i class="fa-solid fa-trash me-2"></i>Supprimer
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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

<?php require_once __DIR__ . '/../layout/footer.php'; ?>