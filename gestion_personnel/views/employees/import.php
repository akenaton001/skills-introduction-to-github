<?php
$pageTitle = "Importer des employés via CSV";
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Styles spécifiques aux couleurs de l'application BDG -->
<style>
    .text-app-primary { color: #721519 !important; }
    .text-app-secondary { color: #f6b036 !important; }
    
    .btn-app-primary {
        background-color: #721519;
        color: #ffffff;
        border-color: #721519;
    }
    .btn-app-primary:hover {
        background-color: #581013;
        color: #ffffff;
        border-color: #581013;
    }

    .btn-outline-app-primary {
        color: #721519;
        border-color: #721519;
        background-color: transparent;
    }
    .btn-outline-app-primary:hover {
        background-color: #721519;
        color: #ffffff;
    }

    .btn-app-secondary {
        background-color: #f6b036;
        color: #000000;
        border-color: #f6b036;
    }
    .btn-app-secondary:hover {
        background-color: #e09d26;
        color: #000000;
        border-color: #e09d26;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-dark fw-bold">
            <i class="fas fa-file-import text-app-primary me-2"></i>Importer des employés
        </h1>
        <p class="text-muted small mb-0">Ajoutez ou mettez à jour plusieurs employés simultanément via un fichier CSV.</p>
    </div>
    <a href="index.php" class="btn btn-custom-primary">
        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
    </a>
</div>

<div class="row">
    <!-- Colonne des instructions -->
    <div class="col-lg-5 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-bold py-3 border-bottom">
                <i class="fas fa-info-circle text-app-primary me-2"></i>Instructions pour le fichier CSV
            </div>
            <div class="card-body">
                <p>Pour garantir une importation sans erreur, veuillez respecter le format suivant :</p>
                <ul class="small ps-3 text-secondary">
                    <li class="mb-2">Le fichier doit être au format <strong>.csv</strong> encodé en UTF-8.</li>
                    <li class="mb-2">Les champs <strong>Matricule</strong>, <strong>Nom</strong> et <strong>Prénom</strong> sont obligatoires pour chaque ligne.</li>
                    <li class="mb-2">Le séparateur recommandé est le point-virgule (<code>;</code>) ou la virgule (<code>,</code>).</li>
                    <li class="mb-2">Les dates doivent être au format <strong>JJ/MM/AAAA</strong> (ex: <code class="text-app-primary">15/01/2023</code>) ou <strong>YYYY-MM-DD</strong>.</li>
                    <li class="mb-2">Si un employé avec le même <strong>matricule</strong> existe déjà, ses informations seront automatiquement mises à jour.</li>
                </ul>

                <hr class="my-4">

                <div class="d-grid">
                    <a href="index.php?action=downloadTemplate" class="btn btn-outline-app-primary">
                        <i class="fas fa-download me-2"></i>Télécharger le modèle CSV exemple
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Colonne du formulaire -->
    <div class="col-lg-7 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-bold py-3 border-bottom">
                <i class="fas fa-upload text-app-primary me-2"></i>Sélectionner le fichier CSV
            </div>
            <div class="card-body d-flex flex-column justify-content-center">
                <form action="index.php?action=processImport" method="POST" enctype="multipart/form-data">
                    <div class="mb-4 text-center p-4 border border-2 rounded bg-light" id="drop-zone" style="border-style: dashed !important;">
                        <i class="fas fa-file-csv fa-3x text-muted mb-3"></i>
                        <h5 class="fw-bold">Choisissez un fichier CSV</h5>
                        <p class="text-muted small mb-3">Glissez votre fichier ici ou cliquez pour parcourir</p>
                        
                        <input type="file" class="form-control d-none" id="csv_file" name="csv_file" accept=".csv" required onchange="updateFileName(this)">
                        
                        <button type="button" class="btn btn-app-primary px-4" onclick="document.getElementById('csv_file').click();">
                            <i class="fas fa-folder-open me-2"></i>Parcourir
                        </button>
                        
                        <div id="file-name-display" class="mt-3 text-app-primary fw-bold small"></div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="index.php" class="btn btn-light border px-4">Annuler</a>
                        <button type="submit" class="btn btn-app-primary px-4">
                            <i class="fas fa-upload me-2"></i>Lancer l'importation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function updateFileName(input) {
    const display = document.getElementById('file-name-display');
    if (input.files && input.files[0]) {
        display.innerHTML = '<i class="fas fa-check-circle me-1 text-app-secondary"></i> Fichier sélectionné : ' + input.files[0].name;
    } else {
        display.innerHTML = '';
    }
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>