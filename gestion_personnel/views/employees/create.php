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
        <h3 class="fw-bold m-0" style="color: var(--primary-color);">Ajouter un employé</h3>
        <small class="text-muted">Remplissez les informations ci-dessous pour créer un nouveau dossier personnel</small>
    </div>
    <a href="index.php" class="btn btn-custom-primary">
        <i class="fa-solid fa-arrow-left me-1"></i> Retour à la liste
    </a>
</div>

<form action="index.php?action=create" method="POST" enctype="multipart/form-data">
    
    <!-- Rubrique 1 : Informations Personnelles & État Civil -->
    <div class="card card-section p-4">
        <div class="section-title">
            <i class="fa-solid fa-user me-2"></i> Informations Personnelles & État Civil
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Entreprise <span class="text-danger">*</span></label>
                <select name="entreprise" class="form-select" required>
                    <option value="STC" selected>STC</option>
                    <option value="SITCOM">SITCOM</option>
                    <option value="TNDF">TNDF</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Matricule</label>
                <input type="text" class="form-control bg-light" value="Généré automatiquement (6 chiffres)" disabled>
                <small class="text-muted">Ex: 000001, 000002...</small>
            </div>
            <div class="col-md-4">
                <label class="form-label">Nom <span class="text-danger">*</span></label>
                <input type="text" name="nom" class="form-control" placeholder="Nom de famille" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Prénom <span class="text-danger">*</span></label>
                <input type="text" name="prenom" class="form-control" placeholder="Prénom" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">État Civil</label>
                <select name="etat_civil" class="form-select">
                    <option value="">-- Sélectionner --</option>
                    <option value="Célibataire">Célibataire</option>
                    <option value="Marié(e)">Marié(e)</option>
                    <option value="Divorcé(e)">Divorcé(e)</option>
                    <option value="Veuf(ve)">Veuf(ve)</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">N° CNI</label>
                <input type="text" name="cni" class="form-control" placeholder="Numéro de pièce d'identité">
            </div>
            <div class="col-md-4">
                <label class="form-label">Téléphone</label>
                <input type="tel" name="telephone" class="form-control" placeholder="Ex: +221 77 000 00 00">
            </div>
            <div class="col-md-4">
                <label class="form-label">Adresse E-mail</label>
                <input type="email" name="email" class="form-control" placeholder="exemple@domaine.com">
            </div>
            <div class="col-md-4">
                <label class="form-label">Adresse de résidence</label>
                <input type="text" name="adresse" class="form-control" placeholder="Adresse complète">
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
                <input type="text" name="intitule_poste" class="form-control" placeholder="Ex: Chauffeur...">
            </div>
            <div class="col-md-4">
                <label class="form-label">Type de contrat <span class="text-danger">*</span></label>
                <select name="type_contrat" class="form-select" required>
                    <option value="CDI">CDI</option>
                    <option value="CDD">CDD</option>
                    <option value="Stage">Stage</option>
                    <option value="Autre">Autre</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Date d'embauche</label>
                <input type="date" name="date_embauche" class="form-control">
            </div>
            <div class="col-md-12">
                <label class="form-label">Joindre le contrat (PDF)</label>
                <input type="file" name="contrat_pdf" accept="application/pdf" class="form-control">
                <small class="text-muted">Format accepté : .pdf uniquement</small>
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
                <input type="text" name="numero_permis" class="form-control" placeholder="Numéro du permis">
            </div>
            <div class="col-md-4">
                <label class="form-label">Catégories de permis</label>
                <input type="text" name="categories_permis" class="form-control" placeholder="Ex: B, C, D, E">
            </div>
            <div class="col-md-4">
                <label class="form-label">Date d'expiration du permis</label>
                <input type="date" name="date_expiration_permis" class="form-control">
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="d-flex justify-content-end gap-2 mb-5">
        <a href="index.php" class="btn btn-light btn-cancel">Annuler</a>
        <button type="submit" class="btn btn-save">
            <i class="fa-solid fa-check me-1"></i> Enregistrer l'employé
        </button>
    </div>

</form>

<?php include __DIR__ . '/../layout/footer.php'; ?>