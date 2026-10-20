<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 rounded-4" style="background-color: #ffffff;">
                
                <!-- En-tête -->
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle me-3 fs-4">
                        <i class="bi bi-plus-circle-fill" style="color: #01452f;"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1" style="color: #01452f;">Saisir une Recette ou une Dépense</h3>
                        <p class="text-muted small mb-0">Remplissez les informations ci-dessous pour enregistrer le mouvement financier.</p>
                    </div>
                </div>

                <form action="index.php?action=store" method="POST" class="needs-validation" novalidate>
                    
                    <div class="row">
                        <!-- Date -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small text-secondary">Date :</label>
                            <input type="date" class="form-control form-control-lg fs-6 shadow-none border-secondary-subtle" name="date_transaction" value="<?= date('Y-m-d'); ?>" required>
                        </div>

                        <!-- Type de flux -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small text-secondary">Type de flux :</label>
                            <select class="form-select form-select-lg fs-6 shadow-none border-secondary-subtle" name="type" required>
                                <option value="entree">Recette (Entrée)</option>
                                <option value="sortie">Dépense (Sortie)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Nom / Libellé -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small text-secondary">Nom / Libellé :</label>
                            <input type="text" class="form-control form-control-lg fs-6 shadow-none border-secondary-subtle" name="libelle" placeholder="Ex: Loyer mois de Janvier..." required>
                        </div>

                        <!-- Montant -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small text-secondary">Montant (FCFA) :</label>
                            <input type="number" step="0.01" class="form-control form-control-lg fs-6 shadow-none border-secondary-subtle" name="montant" id="montantInput" placeholder="Ex: 50000" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Rubrique / Affectation mise à jour -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small text-secondary">Rubrique / Affectation :</label>
                            <select class="form-select form-select-lg fs-6 shadow-none border-secondary-subtle" name="rubrique" id="rubriqueSelect" onchange="toggleTerrainFields()" required>
                                <option value="" disabled selected>-- Choisir une affectation --</option>
                                <optgroup label="🟩 [REVENUS & ENTRÉES DE FONDS]">
                                    <option value="Loyer">Loyer</option>
                                    <option value="Coopérative Poste">Coopérative Poste</option>
                                    <option value="Remboursement prêt">Remboursement prêt</option>
                                    <option value="Prêt bancaire (Entrant)">Prêt bancaire (Entrant / Ajouté au patrimoine)</option>
                                    <option value="Vente de terrain">Vente de terrain</option>
                                    <option value="Perso (Entrée)">Perso (Entrée)</option>
                                    <option value="CCP (Entrée)">CCP (Entrée)</option>
                                    <option value="B9 (Entrée)">B9 (Entrée)</option>
                                    <option value="Retrait">Retrait (Banque)</option>
                                    <option value="Autre revenu">Autre revenu</option>
                                </optgroup>
                                <optgroup label="🟥 [DÉPENSES FAMILLES]">
                                    <option value="WAK">WAK</option>
                                    <option value="YOFF">YOFF</option>
                                    <option value="MCD">MCD</option>
                                </optgroup>
                                <optgroup label="🟧 [AUTRES DÉPENSES & SORTIES]">
                                    <option value="Transport">Transport</option>
                                    <option value="Entretien">Entretien</option>
                                    <option value="Gardiennage">Gardiennage</option>
                                    <option value="Frais de notaire">Frais de notaire</option>
                                    <option value="Niagga">Niagga</option>
                                    <option value="Prêt accordé à un tiers (Sortant)">Prêt accordé à un tiers (Sortant)</option>
                                    <option value="Versement">Versement (Banque)</option>
                                    <option value="Perso (Extra)">Perso (Extra)</option>
                                    <option value="Perso (Dépenses)">Perso (Dépenses)</option>
                                    <option value="CCP (Sortie)">CCP (Sortie)</option>
                                    <option value="B9 (Sortie)">B9 (Sortie)</option>
                                    <option value="Autre dépense">Autre dépense</option>
                                </optgroup>
                            </select>
                        </div>

                        <!-- Bénéficiaire (15 noms officiels) -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small text-secondary">Bénéficiaire (Si concerné) :</label>
                            <select class="form-select form-select-lg fs-6 shadow-none border-secondary-subtle" name="beneficiaire">
                                <option value="">-- Aucun / Non applicable --</option>
                                <option value="MARIAMA">Mariama</option>
                                <option value="THIERNO">Thierno</option>
                                <option value="ABD.AZIZ">Abou Aziz</option>
                                <option value="MATY">Maty</option>
                                <option value="ABDALALAH">Abdallah</option>
                                <option value="MME SECK">Mme SECK</option>
                                <option value="OUSMANE">Ousmane</option>
                                <option value="WOULY">Wouly</option>
                                <option value="BAAY">Baay</option>
                                <option value="KADOU">Kadou</option>
                                <option value="BADOU">Badou</option>
                                <option value="BINETA">Bineta</option>
                                <option value="NGOULOU">Ngoulou</option>
                                <option value="THIATHIOU">Thiathiou</option>
                                <option value="HABY">Haby</option>
                            </select>
                        </div>
                    </div>

                    <!-- BLOC SPÉCIFIQUE : VENTE DE TERRAIN (Désactivé et masqué par défaut) -->
                    <div id="terrainBloc" class="p-3 mb-3 border border-success rounded-3 bg-light" style="display: none;">
                        <h6 class="text-success fw-bold mb-3"><i class="bi bi-geo-alt-fill me-1"></i> Informations spécifiques - Vente de terrain</h6>
                        <div class="row">
                            <!-- Cessionnaire -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold small text-secondary">Cessionnaire :</label>
                                <input type="text" class="form-control shadow-none border-secondary-subtle terrain-field" name="cessionnaire" placeholder="Nom du cessionnaire" disabled>
                            </div>
                            <!-- Nombre de terrains -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold small text-secondary">Nombre de terrains :</label>
                                <input type="number" class="form-control shadow-none border-secondary-subtle terrain-field" name="nombre_terrain" id="nombreTerrain" placeholder="Ex: 1" min="1" oninput="calculerMontantTerrain()" disabled>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Prix Unitaire -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold small text-secondary">Prix Unitaire (FCFA) :</label>
                                <input type="number" step="0.01" class="form-control shadow-none border-secondary-subtle terrain-field" name="prix_unitaire" id="prixUnitaire" placeholder="Ex: 1500000" oninput="calculerMontantTerrain()" disabled>
                            </div>
                            <!-- Commission -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold small text-secondary">Commission (FCFA) :</label>
                                <input type="number" step="0.01" class="form-control shadow-none border-secondary-subtle terrain-field" name="commission" placeholder="Ex: 50000" disabled>
                            </div>
                            <!-- Type de paiement -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold small text-secondary">Type de paiement :</label>
                                <select class="form-select shadow-none border-secondary-subtle terrain-field" name="type_paiement" disabled>
                                    <option value="">-- Choisir --</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Virement">Virement</option>
                                    <option value="Chèque">Chèque</option>
                                    <option value="Échelonné">Échelonné</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Banque utilisée -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-secondary">Banque utilisée (Optionnel) :</label>
                        <select class="form-select form-select-lg fs-6 shadow-none border-secondary-subtle" name="banque">
                            <option value="">-- Aucune / Non bancarisé --</option>
                            <option value="BIS">BIS</option>
                            <option value="BHS">BHS</option>
                            <option value="CBAO">CBAO</option>
                        </select>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="index.php?action=dashboard" class="btn btn-light px-4 py-2 border fw-semibold">Annuler</a>
                        <button type="submit" class="btn text-white px-5 py-2 fw-semibold shadow-sm" style="background-color: #01452f;">Enregistrer la transaction</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script JavaScript pour la gestion des champs et du calcul automatique du montant -->
<script>
function toggleTerrainFields() {
    const select = document.getElementById('rubriqueSelect');
    const terrainBloc = document.getElementById('terrainBloc');
    const terrainFields = document.querySelectorAll('.terrain-field');

    if (select.value === 'Vente de terrain') {
        terrainBloc.style.display = 'block';
        terrainFields.forEach(field => {
            field.disabled = false; // Active les champs
        });
    } else {
        terrainBloc.style.display = 'none';
        terrainFields.forEach(field => {
            field.disabled = true; // Désactive strictement les champs
            field.value = '';     // Vide les valeurs pour éviter les envois parasites
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