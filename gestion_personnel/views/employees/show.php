<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold m-0" style="color: var(--primary-color);">Fiche de l'employé</h3>
        <small class="text-muted">Détails complets de l'agent</small>
    </div>
    <div>
        <!-- Bouton Modifier harmonisé -->
        <a href="index.php?action=edit&id=<?= $employee['id'] ?>" class="btn rounded-pill me-2" style="border: 1px solid var(--primary-color); color: var(--primary-color); background-color: transparent;">
            <i class="fa-solid fa-pen me-1"></i> Modifier
        </a>
        <!-- Bouton Retour harmonisé -->
        <a href="index.php" class="btn btn-custom-primary">
            <i class="fa-solid fa-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Informations Générales -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3" style="color: var(--primary-color);">
                    <i class="fa-solid fa-user me-2"></i>Informations Personnelles
                </h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Entreprise :</span>
                        <span class="badge fw-semibold px-3 py-2 rounded-pill" style="background-color: var(--accent-color); color: #333;">
                            <?= htmlspecialchars($employee['entreprise'] ?? '-') ?>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Matricule :</span>
                        <strong class="text-dark"><?= htmlspecialchars($employee['matricule']) ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Nom & Prénom :</span>
                        <strong><?= htmlspecialchars($employee['nom'] . ' ' . $employee['prenom']) ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">État Civil :</span>
                        <span><?= htmlspecialchars($employee['etat_civil'] ?? '-') ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">CNI :</span>
                        <span><?= htmlspecialchars($employee['cni'] ?? '-') ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Téléphone :</span>
                        <span><?= htmlspecialchars($employee['telephone'] ?? '-') ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">E-mail :</span>
                        <span><?= htmlspecialchars($employee['email'] ?? '-') ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Adresse :</span>
                        <span><?= htmlspecialchars($employee['adresse'] ?? '-') ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Informations Professionnelles & Permis -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3" style="color: var(--primary-color);">
                    <i class="fa-solid fa-briefcase me-2"></i>Informations Professionnelles
                </h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Poste :</span>
                        <strong><?= htmlspecialchars($employee['intitule_poste'] ?? '-') ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Type de contrat :</span>
                        <!-- Badge utilisant le style de la charte principale -->
                        <span class="badge fw-semibold px-3 py-2 rounded-pill" style="background-color: var(--primary-color); color: #ffffff;">
                            <?= htmlspecialchars($employee['type_contrat']) ?>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Date d'embauche :</span>
                        <span><?= htmlspecialchars($employee['date_embauche'] ?? '-') ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Contrat (PDF) :</span>
                        <?php if (!empty($employee['contrat_pdf'])): ?>
                            <a href="uploads/contracts/<?= htmlspecialchars($employee['contrat_pdf']) ?>" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                <i class="fa-solid fa-file-pdf me-1"></i> Consulter le document
                            </a>
                        <?php else: ?>
                            <span class="text-muted">Aucun fichier</span>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3" style="color: var(--primary-color);">
                    <i class="fa-solid fa-id-card me-2"></i>Permis de Conduire
                </h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">N° Permis :</span>
                        <span><?= htmlspecialchars($employee['numero_permis'] ?? '-') ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Catégories :</span>
                        <span><?= htmlspecialchars($employee['categories_permis'] ?? '-') ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Expiration :</span>
                        <span><?= htmlspecialchars($employee['date_expiration_permis'] ?? '-') ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>