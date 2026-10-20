<div class="card p-4 shadow-sm mb-4">
    <h4>Filtres & Rapports Détaillés (Recherche par date, rubrique, bénéficiaire, banque)</h4>
    <form method="GET" action="index.php" class="row g-3 mt-2">
        <input type="hidden" name="action" value="list">
        
        <div class="col-md-2">
            <label class="form-label">Date Début</label>
            <input type="date" class="form-control" name="start_date" value="<?= $_GET['start_date'] ?? '' ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label">Date Fin</label>
            <input type="date" class="form-control" name="end_date" value="<?= $_GET['end_date'] ?? '' ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label">Type</label>
            <select class="form-select" name="type">
                <option value="">Tous</option>
                <option value="entree" <?= (($_GET['type']??'')=='entree')?'selected':''?>>Entrée</option>
                <option value="sortie" <?= (($_GET['type']??'')=='sortie')?'selected':''?>>Sortie</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Banque</label>
            <select class="form-select" name="banque">
                <option value="">Toutes</option>
                <option value="BIS" <?= (($_GET['banque']??'')=='BIS')?'selected':''?>>BIS</option>
                <option value="BHS" <?= (($_GET['banque']??'')=='BHS')?'selected':''?>>BHS</option>
                <option value="CBAO" <?= (($_GET['banque']??'')=='CBAO')?'selected':''?>>CBAO</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Bénéficiaire</label>
            <select class="form-select" name="beneficiaire">
                <option value="">Tous</option>
                <?php foreach(['MARIAMA', 'THIERNO', 'ABD.AZIZ', 'MATY', 'ABDALALAH', 'MME SECK', 'OUSMANE', 'WOULY', 'BAAY', 'KADOU', 'BADOU', 'BINETA', 'NGOULOU', 'THIATHIOU', 'HABY'] as $ben): ?>
                    <option value="<?= $ben; ?>" <?= (($_GET['beneficiaire']??'')==$ben)?'selected':''?>><?= $ben; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filtrer</button>
        </div>
    </form>
</div>

<div class="card p-4 shadow-sm">
    <h4>Résultats des Transactions :</h4>
    <div class="table-responsive mt-3">
        <table class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Libellé & Détails</th>
                    <th>Montant</th>
                    <th>Rubrique</th>
                    <th>Bénéficiaire</th>
                    <th>Banque</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($transactions)): ?>
                    <?php foreach($transactions as $t): ?>
                        <tr>
                            <td><?= $t['date_transaction']; ?></td>
                            <td>
                                <span class="badge bg-<?= $t['type'] == 'entree' ? 'success' : 'danger'; ?>">
                                    <?= ucfirst($t['type']); ?>
                                </span>
                            </td>
                            <td>
                                <?= htmlspecialchars($t['libelle']); ?>
                                <!-- Affichage conditionnel des détails de la vente de terrain -->
                                <?php if($t['rubrique'] === 'Vente de terrain'): ?>
                                    <div class="mt-1 p-2 bg-light border border-success-subtle rounded small text-secondary">
                                        <div><strong class="text-success">Cessionnaire :</strong> <?= htmlspecialchars($t['cessionnaire'] ?? '-'); ?></div>
                                        <div><strong class="text-success">Nombre :</strong> <?= $t['nombre_terrain'] ?? 0; ?> | <strong class="text-success">Prix Unit. :</strong> <?= number_format($t['prix_unitaire'] ?? 0, 0, ',', ' '); ?> FCFA</div>
                                        <div><strong class="text-success">Commission :</strong> <?= number_format($t['commission'] ?? 0, 0, ',', ' '); ?> FCFA | <strong class="text-success">Paiement :</strong> <?= htmlspecialchars($t['type_paiement'] ?? '-'); ?></div>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= number_format($t['montant'], 0, ',', ' '); ?> FCFA</strong></td>
                            <td><?= $t['rubrique']; ?></td>
                            <td><?= $t['beneficiaire'] ?? '-'; ?></td>
                            <td><?= $t['banque']; ?></td>
                            <td class="text-center">
                                <a href="index.php?action=edit&id=<?= $t['id'] ?>" class="btn btn-sm btn-warning text-white me-1" title="Modifier">
                                    <i class="bi bi-pencil"></i> Modif.
                                </a>
                                <a href="index.php?action=delete&id=<?= $t['id'] ?>" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette transaction ?');">
                                    <i class="bi bi-trash"></i> Supp.
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center">Aucune transaction trouvée pour ces critères.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>