<div class="card p-4 shadow-sm mb-4">
    <h4 class="mb-3" style="color: #01452f;"><i class="bi bi-calendar-date me-2" style="color: var(--bs-accent);"></i>Rapport Journalier Détaillé (Depuis 2018)</h4>
    <form method="GET" action="index.php" class="row g-3 mt-2">
        <input type="hidden" name="action" value="report_journalier">
        
        <div class="col-md-5">
            <label class="form-label">Sélectionner l'Année :</label>
            <select name="annee" class="form-select" onchange="this.form.submit()">
                <option value="">-- Toutes les années (Depuis 2018) --</option>
                <?php foreach ($years as $y): ?>
                    <option value="<?= $y ?>" <?= (isset($_GET['annee']) && $_GET['annee'] == $y) ? 'selected' : '' ?>><?= $y ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-5">
            <label class="form-label">Sélectionner le Mois (optionnel) :</label>
            <select name="mois" class="form-select" onchange="this.form.submit()">
                <option value="">-- Tous les mois de l'année --</option>
                <?php 
                $moisNoms = [1=>'Janvier', 2=>'Février', 3=>'Mars', 4=>'Avril', 5=>'Mai', 6=>'Juin', 7=>'Juillet', 8=>'Août', 9=>'Septembre', 10=>'Octobre', 11=>'Novembre', 12=>'Décembre'];
                foreach ($moisNoms as $mNum => $mNom): 
                ?>
                    <option value="<?= $mNum ?>" <?= (isset($_GET['mois']) && $_GET['mois'] == $mNum) ? 'selected' : '' ?>><?= $mNom ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <a href="index.php?action=report_journalier" class="btn btn-secondary w-100">Réinitialiser</a>
        </div>
    </form>
</div>

<div class="card p-4 shadow-sm">
    <h4 class="mb-3" style="color: #01452f;">Résultats du Rapport Journalier</h4>
    
    <!-- Conteneur avec scroll vertical et en-tête fixe pour une meilleure ergonomie -->
    <div class="table-responsive mt-3" style="max-height: 500px; overflow-y: auto;">
        <table class="table table-striped table-bordered align-middle mb-0">
            <thead class="table-light sticky-top">
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Nom / Libellé & Détails</th>
                    <th>Rubrique / Affectation</th>
                    <th>Bénéficiaire</th>
                    <th>Banque</th>
                    <th class="text-end">Montant (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($dailyReports)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">Aucune transaction enregistrée pour cette période.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($dailyReports as $row): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($row['date_transaction'])) ?></td>
                            <td>
                                <span class="badge bg-<?= $row['type'] == 'entree' ? 'success' : 'danger'; ?>">
                                    <?= $row['type'] == 'entree' ? 'Recette' : 'Dépense'; ?>
                                </span>
                            </td>
                            <td>
                                <?= htmlspecialchars($row['libelle']) ?>
                                <!-- Affichage conditionnel des détails de la vente de terrain -->
                                <?php if(isset($row['rubrique']) && $row['rubrique'] === 'Vente de terrain'): ?>
                                    <div class="mt-1 p-2 bg-light border border-success-subtle rounded small text-secondary">
                                        <div><strong class="text-success">Cessionnaire :</strong> <?= htmlspecialchars($row['cessionnaire'] ?? '-'); ?></div>
                                        <div><strong class="text-success">Nombre :</strong> <?= $row['nombre_terrain'] ?? 0; ?> | <strong class="text-success">Prix Unit. :</strong> <?= number_format($row['prix_unitaire'] ?? 0, 0, ',', ' '); ?> FCFA</div>
                                        <div><strong class="text-success">Commission :</strong> <?= number_format($row['commission'] ?? 0, 0, ',', ' '); ?> FCFA | <strong class="text-success">Paiement :</strong> <?= htmlspecialchars($row['type_paiement'] ?? '-'); ?></div>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['rubrique']) ?></td>
                            <td><?= !empty($row['beneficiaire']) ? htmlspecialchars($row['beneficiaire']) : '<span class="text-muted">-</span>' ?></td>
                            <td><?= !empty($row['banque']) ? htmlspecialchars($row['banque']) : '<span class="text-muted">-</span>' ?></td>
                            <td class="text-end fw-bold <?= ($row['type'] == 'entree') ? 'text-success' : 'text-danger' ?>">
                                <?= number_format($row['montant'], 0, ',', ' ') ?> FCFA
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>