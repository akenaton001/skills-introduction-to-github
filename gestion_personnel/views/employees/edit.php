<?php include __DIR__ . '/../layout/header.php'; ?>

<style>
    .card-section {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        margin-bottom: 25px;
    }
    .section-title {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--primary-color);
        border-bottom: 2px solid rgba(114, 21, 25, 0.1);
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    .form-label {
        font-weight: 500;
        font-size: 0.88rem;
        color: #495057;
    }
    .btn-save {
        background-color: var(--primary-color);
        color: white;
        border-radius: 20px;
        padding: 10px 25px;
        font-weight: 600;
        border: none;
    }
    .btn-save:hover {
        background-color: #581013;
        color: white;
    }
    .btn-cancel {
        border-radius: 20px;
        padding: 10px 25px;
        font-weight: 500;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold m-0" style="color: var(--primary-color);">Modifier l'employé</h3>
        <small class="text-muted">Mettez à jour les informations de <?= htmlspecialchars(($employee['prenom'] ?? '') . ' ' . ($employee['nom'] ?? '')) ?></small>
    </div>
    <a href="index.php" class="btn btn-custom-primary">
        <i class="fa-solid fa-arrow-left me-1"></i> Retour à la liste
    </a>
</div>

<form action="index.php?action=edit&id=<?= htmlspecialchars($employee['id'] ?? '') ?>" method="POST" enctype="multipart/form-data">
    
    <!-- Champ caché pour garder l'ancien PDF si aucun nouveau n'est envoyé -->
    <input type="hidden" name="existing_contrat_pdf" value="<?= htmlspecialchars($employee['contrat_pdf'] ?? '') ?>">

    <!-- Rubrique 1 : Informations Personnelles & État Civil -->
    <div class="card card-section p-4">
        <div class="section-title">
            <i class="fa-solid fa-user me-2"></i> Informations Personnelles & État Civil
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Entreprise <span class="text-danger">*</span></label>
                <select name="entreprise" class="form-select" required>
                    <?php 
                    $currentEnt = $employee['entreprise'] ?? 'STC';
                    $options_ent = ['STC', 'SITCOM', 'TNDF'];
                    foreach ($options_ent as $ent): 
                    ?>
                        <option value="<?= $ent ?>" <?= $currentEnt === $ent ? 'selected' : '' ?>><?= $ent ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Matricule</label>
                <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($employee['matricule'] ?? '') ?>" disabled>
                <small class="text-muted">Généré automatiquement</small>
            </div>
            <div class="col-md-4">
                <label class="form-label">Nom <span class="text-danger">*</span></label>
                <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($employee['nom'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Prénom <span class="text-danger">*</span></label>
                <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($employee['prenom'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">État Civil</label>
                <select name="etat_civil" class="form-select">
                    <?php 
                    $etat = $employee['etat_civil'] ?? '';
                    $options_etat = ['Célibataire', 'Marié(e)', 'Divorcé(e)', 'Veuf(ve)'];
                    foreach ($options_etat as $opt): 
                    ?>
                        <option value="<?= $opt ?>" <?= $etat === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">N° CNI</label>
                <input type="text" name="cni" class="form-control" value="<?= htmlspecialchars($employee['cni'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Téléphone</label>
                <input type="tel" name="telephone" class="form-control" value="<?= htmlspecialchars($employee['telephone'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Adresse E-mail</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($employee['email'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Adresse de résidence</label>
                <input type="text" name="adresse" class="form-control" value="<?= htmlspecialchars($employee['adresse'] ?? '') ?>">
            </div>
        </div>
    </div>

    <!-- Rubrique 2 : Informations Professionnelles & Contrat -->
    <div class="card card-section p-4">
        <div class="section-title">
            <i class="fa-solid fa-briefcase me-2"></i> Poste & Contrat
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Intitulé du poste / Emploi tenu</label>
                <input type="text" name="intitule_poste" class="form-control" value="<?= htmlspecialchars($employee['intitule_poste'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Type de contrat <span class="text-danger">*</span></label>
                <select name="type_contrat" class="form-select" required>
                    <?php 
                    $contrat = $employee['type_contrat'] ?? '';
                    $options_contrat = ['CDI', 'CDD', 'Stage', 'Autre'];
                    foreach ($options_contrat as $opt): 
                    ?>
                        <option value="<?= $opt ?>" <?= $contrat === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Date d'embauche</label>
                <input type="date" name="date_embauche" class="form-control" value="<?= htmlspecialchars($employee['date_embauche'] ?? '') ?>">
            </div>
            <div class="col-md-12">
                <label class="form-label">Joindre / Remplacer le contrat (PDF)</label>
                <input type="file" name="contrat_pdf" accept="application/pdf" class="form-control">
                <div class="mt-1">
                    <?php if (!empty($employee['contrat_pdf'])): ?>
                        <small class="text-muted">Fichier actuel : </small>
                        <a href="uploads/contracts/<?= htmlspecialchars($employee['contrat_pdf']) ?>" target="_blank" class="text-danger text-decoration-none small fw-bold">
                            <i class="fa-solid fa-file-pdf me-1"></i> Voir le PDF enregistré
                        </a>
                    <?php else: ?>
                        <small class="text-muted">Aucun contrat téléversé pour le moment. (Format : .pdf)</small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Rubrique 3 : Informations sur le Permis de Conduire (Optionnel) -->
    <div class="card card-section p-4">
        <div class="section-title">
            <i class="fa-solid fa-id-card me-2"></i> Permis de Conduire
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">N° de Permis de conduire</label>
                <input type="text" name="numero_permis" class="form-control" value="<?= htmlspecialchars($employee['numero_permis'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Catégories de permis</label>
                <input type="text" name="categories_permis" class="form-control" value="<?= htmlspecialchars($employee['categories_permis'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Date d'expiration du permis</label>
                <input type="date" name="date_expiration_permis" class="form-control" value="<?= htmlspecialchars($employee['date_expiration_permis'] ?? '') ?>">
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="d-flex justify-content-end gap-2 mb-5">
        <a href="index.php" class="btn btn-light btn-cancel">Annuler</a>
        <button type="submit" class="btn btn-save">
            <i class="fa-solid fa-rotate me-1"></i> Mettre à jour l'employé
        </button>
    </div>

</form>

<?php include __DIR__ . '/../layout/footer.php'; ?>