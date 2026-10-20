<div class="card p-4 shadow-sm mb-4">
    <h4 class="mb-3" style="color: #01452f;"><i class="bi bi-person-lines-fill me-2" style="color: var(--bs-accent);"></i>Rapport et Suivi par Bénéficiaire</h4>
    
    <!-- Formulaire de filtre global -->
    <form method="GET" action="index.php" class="row g-3 mt-2">
        <input type="hidden" name="action" value="report_beneficiaire">
        
        <?php
        $beneficiairesList = [
            'MARIAMA', 'THIERNO', 'ABD.AZIZ', 'MATY', 'ABDALALAH', 
            'MME SECK', 'OUSMANE', 'WOULY', 'BAAY', 'KADOU', 
            'BADOU', 'BINETA', 'NGOULOU', 'THIATHIOU', 'HABY'
        ];
        sort($beneficiairesList);
        ?>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Filtrer par un Bénéficiaire :</label>
            <select name="beneficiaire" class="form-select" onchange="this.form.submit()">
                <option value="">-- Afficher tous les détails --</option>
                <?php foreach ($beneficiairesList as $bName): ?>
                    <option value="<?= htmlspecialchars($bName) ?>" <?= (isset($_GET['beneficiaire']) && $_GET['beneficiaire'] == $bName) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($bName) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Année :</label>
            <select name="annee" class="form-select" onchange="this.form.submit()">
                <option value="">-- Toutes --</option>
                <?php foreach ($years as $y): ?>
                    <option value="<?= $y ?>" <?= (isset($_GET['annee']) && $_GET['annee'] == $y) ? 'selected' : '' ?>><?= $y ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Mois :</label>
            <select name="mois" class="form-select" onchange="this.form.submit()">
                <option value="">-- Tous --</option>
                <?php 
                $moisNoms = [1=>'Janvier', 2=>'Février', 3=>'Mars', 4=>'Avril', 5=>'Mai', 6=>'Juin', 7=>'Juillet', 8=>'Août', 9=>'Septembre', 10=>'Octobre', 11=>'Novembre', 12=>'Décembre'];
                foreach ($moisNoms as $mNum => $mNom): 
                ?>
                    <option value="<?= $mNum ?>" <?= (isset($_GET['mois']) && $_GET['mois'] == $mNum) ? 'selected' : '' ?>><?= $mNom ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <a href="index.php?action=report_beneficiaire" class="btn btn-secondary w-100">Réinitialiser</a>
        </div>
    </form>
</div>

<?php
$selectedBeneficiaire = $_GET['beneficiaire'] ?? '';
$selectedYear = $_GET['annee'] ?? null;
$selectedMonth = $_GET['mois'] ?? null;
$transactionModel = new Transaction($this->db);

$listToDisplay = !empty($selectedBeneficiaire) ? [$selectedBeneficiaire] : $beneficiairesList;
?>

<!-- TABLEAU SYNTHÉTIQUE GLOBAL (Évite de scroller si on veut juste voir les totaux) -->
<?php if (empty($selectedBeneficiaire)): ?>
<div class="card p-4 shadow-sm mb-4">
    <h5 class="mb-3" style="color: #01452f;"><i class="bi bi-table me-2"></i>Synthèse des Totaux par Bénéficiaire</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Bénéficiaire</th>
                    <th class="text-end">Total Perçu</th>
                    <th class="text-center">Action rapide</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $grandTotal = 0;
                foreach ($beneficiairesList as $ben):
                    $tList = $transactionModel->getBeneficiaryReport($ben, $selectedYear, $selectedMonth);
                    $tot = 0;
                    foreach ($tList as $t) { $tot += $t['montant']; }
                    $grandTotal += $tot;
                ?>
                <tr>
                    <td class="fw-semibold"><i class="bi bi-person-fill text-success me-2"></i><?= htmlspecialchars($ben) ?></td>
                    <td class="text-end fw-bold text-success"><?= number_format($tot, 0, ',', ' ') ?> FCFA</td>
                    <td class="text-center">
                        <a href="index.php?action=report_beneficiaire&beneficiaire=<?= urlencode($ben) ?>&annee=<?= $selectedYear ?>&mois=<?= $selectedMonth ?>" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-eye"></i> Voir le détail
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td class="fw-bold">TOTAL GÉNÉRAL</td>
                    <td class="text-end fw-bold text-success fs-5"><?= number_format($grandTotal, 0, ',', ' ') ?> FCFA</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- AFFICHAGE DES DÉTAILS (Par bénéficiaire ou tous si sélectionné) -->
<h5 class="mb-3" style="color: #01452f;">Détail des Transactions</h5>

<?php foreach ($listToDisplay as $ben): 
    $transList = $transactionModel->getBeneficiaryReport($ben, $selectedYear, $selectedMonth);
    $totalBen = 0;
    foreach ($transList as $t) {
        $totalBen += $t['montant'];
    }
?>
<div class="card p-3 shadow-sm mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
        <h6 class="m-0 fw-bold" style="color: #01452f;">
            <i class="bi bi-person-fill me-1 text-success"></i> <?= htmlspecialchars($ben) ?>
        </h6>
        <span class="badge bg-success">Total : <?= number_format($totalBen, 0, ',', ' ') ?> FCFA</span>
    </div>

    <!-- Conteneur avec hauteur maximale et scroll interne si la liste est longue -->
    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
        <table class="table table-striped table-sm table-bordered align-middle mb-0">
            <thead class="table-light sticky-top">
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Libellé</th>
                    <th>Rubrique</th>
                    <th>Banque</th>
                    <th class="text-end">Montant</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transList)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted fst-italic py-2">Aucune transaction enregistrée.</td>
                    </tr>
                <?php else: foreach ($transList as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['date_transaction']) ?></td>
                        <td>
                            <span class="badge bg-<?= $row['type'] == 'entree' ? 'success' : 'danger' ?>">
                                <?= ucfirst($row['type']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($row['libelle']) ?></td>
                        <td><?= htmlspecialchars($row['rubrique']) ?></td>
                        <td><?= htmlspecialchars($row['banque'] ?? '-') ?></td>
                        <td class="text-end fw-bold"><?= number_format($row['montant'], 0, ',', ' ') ?> FCFA</td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endforeach; ?>