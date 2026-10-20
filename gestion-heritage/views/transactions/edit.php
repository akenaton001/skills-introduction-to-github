<div class="card p-4 shadow-sm mb-4">
    <h4 class="mb-3" style="color: #01452f;"><i class="bi bi-pencil-square me-2"></i>Modifier la Transaction</h4>
    
    <form action="index.php?action=update" method="POST">
        <input type="hidden" name="id" value="<?= $transaction['id'] ?>">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Type de Transaction :</label>
                <select name="type" class="form-select" required>
                    <option value="entree" <?= $transaction['type'] == 'entree' ? 'selected' : '' ?>>Entrée (Recette)</option>
                    <option value="sortie" <?= $transaction['type'] == 'sortie' ? 'selected' : '' ?>>Sortie (Dépense)</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Date :</label>
                <input type="date" name="date_transaction" class="form-control" value="<?= $transaction['date_transaction'] ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Libellé :</label>
                <input type="text" name="libelle" class="form-control" value="<?= htmlspecialchars($transaction['libelle']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Rubrique :</label>
                <select name="rubrique" id="rubriqueSelect" class="form-select" onchange="toggleTerrainFields()" required>
                    <?php 
                    $transactionModel = new Transaction($this->db);
                    foreach ($transactionModel->getAllRubriques() as $rub): 
                    ?>
                        <option value="<?= $rub ?>" <?= ($transaction['rubrique'] == $rub) ? 'selected' : '' ?>><?= $rub ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Montant (FCFA) :</label>
                <input type="number" step="0.01" name="montant" id="montantInput" class="form-control" value="<?= $transaction['montant'] ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Banque (optionnel) :</label>
                <select name="banque" class="form-select">
                    <option value="">-- Aucune / Non bancarisé --</option>
                    <option value="BIS" <?= ($transaction['banque'] == 'BIS') ? 'selected' : '' ?>>BIS</option>
                    <option value="BHS" <?= ($transaction['banque'] == 'BHS') ? 'selected' : '' ?>>BHS</option>
                    <option value="CBAO" <?= ($transaction['banque'] == 'CBAO') ? 'selected' : '' ?>>CBAO</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Bénéficiaire (optionnel) :</label>
                <select name="beneficiaire" class="form-select">
                    <option value="">-- Aucun --</option>
                    <?php foreach ($transactionModel->getAllBeneficiaries() as $ben): ?>
                        <option value="<?= $ben ?>" <?= ($transaction['beneficiaire'] == $ben) ? 'selected' : '' ?>><?= $ben ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- BLOC SPÉCIFIQUE : VENTE DE TERRAIN -->
        <?php $isTerrain = ($transaction['rubrique'] === 'Vente de terrain'); ?>
        <div id="terrainBloc" class="p-3 my-3 border border-success rounded-3 bg-light" style="display: <?= $isTerrain ? 'block' : 'none' ?>;">
            <h6 class="text-success fw-bold mb-3"><i class="bi bi-geo-alt-fill me-1"></i> Informations spécifiques - Vente de terrain</h6>
            <div class="row">
                <!-- Cessionnaire -->
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold small text-secondary">Cessionnaire :</label>
                    <input type="text" class="form-control shadow-none border-secondary-subtle terrain-field" name="cessionnaire" value="<?= htmlspecialchars($transaction['cessionnaire'] ?? '') ?>" placeholder="Nom du cessionnaire" <?= $isTerrain ? '' : 'disabled' ?>>
                </div>
                <!-- Nombre de terrains -->
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold small text-secondary">Nombre de terrains :</label>
                    <input type="number" class="form-control shadow-none border-secondary-subtle terrain-field" name="nombre_terrain" id="nombreTerrain" value="<?= $transaction['nombre_terrain'] ?? '' ?>" placeholder="Ex: 1" min="1" oninput="calculerMontantTerrain()" <?= $isTerrain ? '' : 'disabled' ?>>
                </div>
            </div>
            <div class="row">
                <!-- Prix Unitaire -->
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold small text-secondary">Prix Unitaire (FCFA) :</label>
                    <input type="number" step="0.01" class="form-control shadow-none border-secondary-subtle terrain-field" name="prix_unitaire" id="prixUnitaire" value="<?= $transaction['prix_unitaire'] ?? '' ?>" placeholder="Ex: 1500000" oninput="calculerMontantTerrain()" <?= $isTerrain ? '' : 'disabled' ?>>
                </div>
                <!-- Commission -->
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold small text-secondary">Commission (FCFA) :</label>
                    <input type="number" step="0.01" class="form-control shadow-none border-secondary-subtle terrain-field" name="commission" value="<?= $transaction['commission'] ?? '' ?>" placeholder="Ex: 50000" <?= $isTerrain ? '' : 'disabled' ?>>
                </div>
                <!-- Type de paiement -->
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold small text-secondary">Type de paiement :</label>
                    <select class="form-select shadow-none border-secondary-subtle terrain-field" name="type_paiement" <?= $isTerrain ? '' : 'disabled' ?>>
                        <option value="">-- Choisir --</option>
                        <option value="Cash" <?= ($transaction['type_paiement'] == 'Cash') ? 'selected' : '' ?>>Cash</option>
                        <option value="Virement" <?= ($transaction['type_paiement'] == 'Virement') ? 'selected' : '' ?>>Virement</option>
                        <option value="Chèque" <?= ($transaction['type_paiement'] == 'Chèque') ? 'selected' : '' ?>>Chèque</option>
                        <option value="Échelonné" <?= ($transaction['type_paiement'] == 'Échelonné') ? 'selected' : '' ?>>Échelonné</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-between">
            <a href="index.php?action=list" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
        </div>
    </form>
</div>

<!-- JavaScript pour la gestion dynamique des champs terrain -->
<script>
function toggleTerrainFields() {
    const select = document.getElementById('rubriqueSelect');
    const terrainBloc = document.getElementById('terrainBloc');
    const terrainFields = document.querySelectorAll('.terrain-field');

    if (select.value === 'Vente de terrain') {
        terrainBloc.style.display = 'block';
        terrainFields.forEach(field => {
            field.disabled = false;
        });
    } else {
        terrainBloc.style.display = 'none';
        terrainFields.forEach(field => {
            field.disabled = true;
        });
    }
}

function calculerMontantTerrain() {
    const nombre = parseFloat(document.getElementById('nombreTerrain').value) || 0;
    const prixUnit = parseFloat(document.getElementById('prixUnitaire').value) || 0;
    const montantInput = document.getElementById('montantInput');
    
    if (nombre > 0 && prixUnit > 0) {
        montantInput.value = nombre * prixUnit;
    }
}
</script>