<div class="card p-4 shadow-sm mb-4">
    <h4 class="mb-3" style="color: #01452f;"><i class="bi bi-bar-chart-fill me-2" style="color: var(--bs-accent);"></i>Synthèse des Recettes et Dépenses par Rubrique (Depuis 2018)</h4>
    <form method="GET" action="index.php" class="row g-3 mt-2 d-print-none">
        <input type="hidden" name="action" value="report_rubrique">
        
        <div class="col-md-4">
            <label class="form-label">Sélectionner l'Année :</label>
            <select name="annee" class="form-select" onchange="this.form.submit()">
                <option value="">-- Toutes les années (Depuis 2018) --</option>
                <?php foreach ($years as $y): ?>
                    <option value="<?= $y ?>" <?= (isset($_GET['annee']) && $_GET['annee'] == $y) ? 'selected' : '' ?>><?= $y ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Filtrer par Mois (optionnel) :</label>
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

        <div class="col-md-4 d-flex align-items-end">
            <a href="index.php?action=report_rubrique" class="btn btn-secondary w-100">Réinitialiser les filtres</a>
        </div>
    </form>
</div>

<?php
// Définition de toutes les rubriques officielles par catégorie (avec CCP, B9 et Commission)
$rubriquesEntrees = [
    "Loyer" => "Loyer",
    "Coopérative Poste" => "Coopérative Poste",
    "Remboursement prêt" => "Remboursement prêt",
    "Prêt bancaire (Entrant)" => "Prêt bancaire (Entrant / Ajouté au patrimoine)",
    "Vente de terrain" => "Vente de terrain",
    "Perso (Entrée)" => "Perso (Entrée)",
    "CCP (Entrée)" => "CCP (Entrée)",
    "B9 (Entrée)" => "B9 (Entrée)",
    "Retrait" => "Retrait (Banque)",
    "Autre revenu" => "Autre revenu"
];

$rubriquesSorties = [
    "WAK" => "WAK",
    "YOFF" => "YOFF",
    "MCD" => "MCD",
    "Transport" => "Transport",
    "Entretien" => "Entretien",
    "Gardiennage" => "Gardiennage",
    "Frais de notaire" => "Frais de notaire",
    "Niagga" => "Niagga",
    "Prêt accordé à un tiers (Sortant)" => "Prêt accordé à un tiers (Sortant)",
    "Versement" => "Versement (Banque)",
    "Perso (Extra)" => "Perso (Extra)",
    "Perso (Dépenses)" => "Perso (Dépenses)",
    "CCP (Sortie)" => "CCP (Sortie)",
    "B9 (Sortie)" => "B9 (Sortie)",
    "Commission (Vente terrain)" => "Commission (Vente terrain)",
    "Autre dépense" => "Autre dépense"
];

$dataEntrees = [];
$dataSorties = [];

foreach ($reports as $row) {
    if ($row['type'] == 'entree') {
        $dataEntrees[$row['rubrique']] = $row['total'];
    } else {
        $dataSorties[$row['rubrique']] = $row['total'];
    }
}

$totalEntrees = 0;
$totalSorties = 0;

$montantPersoEntree = $dataEntrees['Perso (Entrée)'] ?? 0;
$montantPersoExtra = $dataSorties['Perso (Extra)'] ?? 0;
$montantPersoDepenses = $dataSorties['Perso (Dépenses)'] ?? 0;
$totalPersoSorties = $montantPersoExtra + $montantPersoDepenses;

foreach ($rubriquesEntrees as $key => $lib) { $totalEntrees += ($dataEntrees[$key] ?? 0); }
foreach ($rubriquesSorties as $key => $lib) { $totalSorties += ($dataSorties[$key] ?? 0); }

$totalRevenusFamille = $totalEntrees - $montantPersoEntree;
$totalDepensesFamille = $totalSorties - $totalPersoSorties;
?>

<!-- Données injectées en toute sécurité via des variables JavaScript globales -->
<script>
    const rubriquesEntreesData = <?= json_encode($rubriquesEntrees) ?>;
    const dataEntreesValues = <?= json_encode($dataEntrees) ?>;
    
    const rubriquesSortiesData = <?= json_encode($rubriquesSorties) ?>;
    const dataSortiesValues = <?= json_encode($dataSorties) ?>;
</script>

<div class="row">
    <!-- REVENUS -->
    <div class="col-md-6">
        <div class="card p-4 shadow-sm mb-4">
            <h4 class="text-success mb-3"><i class="bi bi-arrow-down-left-circle me-1"></i> REVENUS (Par Rubrique)</h4>
            <div class="table-responsive mt-2">
                <table class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Rubrique</th>
                            <th class="text-end">Montant Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rubriquesEntrees as $key => $libelle): 
                            $montant = $dataEntrees[$key] ?? 0;
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($libelle) ?></td>
                                <td class="text-end fw-bold text-success"><?= number_format($montant, 0, ',', ' ') ?> FCFA</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Bloc des Totaux et Calculs -->
                <div class="mt-3 p-3 border border-success rounded bg-success bg-opacity-10">
                    <h5 class="text-success fw-bold mb-3 border-bottom pb-2">Synthèse & Calculs - Revenus</h5>
                    
                    <!-- Ligne 1 : Total Revenus -->
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-white rounded border shadow-sm">
                        <div>
                            <span class="fw-bold text-dark">Total Revenus :</span>
                            <span class="text-success fw-bold ms-2"><?= number_format($totalEntrees, 0, ',', ' ') ?> FCFA</span>
                        </div>
                        <button onclick="imprimerRevenusTotal()" class="btn btn-sm btn-success d-print-none">
                            <i class="bi bi-printer"></i> Imprimer
                        </button>
                    </div>

                    <!-- Ligne 2 : Perso (Entrée) -->
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-white rounded border shadow-sm">
                        <div>
                            <span class="fw-bold text-dark">Cumul Perso (Entrée) :</span>
                            <span class="text-success fw-bold ms-2"><?= number_format($montantPersoEntree, 0, ',', ' ') ?> FCFA</span>
                        </div>
                        <button onclick="imprimerPersoEntree()" class="btn btn-sm btn-success d-print-none">
                            <i class="bi bi-printer"></i> Imprimer
                        </button>
                    </div>

                    <!-- Ligne 3 : Total Revenus Famille -->
                    <div class="d-flex justify-content-between align-items-center p-2 bg-success text-white rounded border shadow-sm">
                        <div>
                            <strong class="text-white">Total Revenus Famille:</strong>
                            <span class="fw-bold ms-2"><?= number_format($totalRevenusFamille, 0, ',', ' ') ?> FCFA</span>
                        </div>
                        <button onclick="imprimerRevenusFamille()" class="btn btn-sm btn-light text-success fw-bold d-print-none">
                            <i class="bi bi-printer"></i> Imprimer
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- DÉPENSES -->
    <div class="col-md-6">
        <div class="card p-4 shadow-sm mb-4">
            <h4 class="text-danger mb-3"><i class="bi bi-arrow-up-right-circle me-1"></i> DÉPENSES (Par Rubrique)</h4>
            <div class="table-responsive mt-2">
                <table class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Rubrique</th>
                            <th class="text-end">Montant Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rubriquesSorties as $key => $libelle): 
                            $montant = $dataSorties[$key] ?? 0;
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($libelle) ?></td>
                                <td class="text-end fw-bold text-danger"><?= number_format($montant, 0, ',', ' ') ?> FCFA</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Bloc des Totaux et Calculs -->
                <div class="mt-3 p-3 border border-danger rounded bg-danger bg-opacity-10">
                    <h5 class="text-danger fw-bold mb-3 border-bottom pb-2">Synthèse & Calculs - Dépenses</h5>
                    
                    <!-- Ligne 1 : Total Dépenses -->
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-white rounded border shadow-sm">
                        <div>
                            <span class="fw-bold text-dark">Total Dépenses :</span>
                            <span class="text-danger fw-bold ms-2"><?= number_format($totalSorties, 0, ',', ' ') ?> FCFA</span>
                        </div>
                        <button onclick="imprimerDepensesTotal()" class="btn btn-sm btn-danger d-print-none">
                            <i class="bi bi-printer"></i> Imprimer
                        </button>
                    </div>

                    <!-- Ligne 2 : Cumul Perso Sorties -->
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-white rounded border shadow-sm">
                        <div>
                            <span class="fw-bold text-dark">Total Perso (Extra)/(Dépenses) :</span>
                            <span class="text-danger fw-bold ms-2"><?= number_format($totalPersoSorties, 0, ',', ' ') ?> FCFA</span>
                        </div>
                        <button onclick="imprimerPersoSorties()" class="btn btn-sm btn-danger d-print-none">
                            <i class="bi bi-printer"></i> Imprimer
                        </button>
                    </div>

                    <!-- Ligne 3 : Total Dépenses Famille -->
                    <div class="d-flex justify-content-between align-items-center p-2 bg-danger text-white rounded border shadow-sm">
                        <div>
                            <strong class="text-white">Total Dépenses Famille:</strong>
                            <span class="fw-bold ms-2"><?= number_format($totalDepensesFamille, 0, ',', ' ') ?> FCFA</span>
                        </div>
                        <button onclick="imprimerDepensesFamille()" class="btn btn-sm btn-light text-danger fw-bold d-print-none">
                            <i class="bi bi-printer"></i> Imprimer
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Script JavaScript avec fonctions d'impression séparées et sécurisées -->
<script>
function ouvrirFenetreImpression(titre, lignesHtml, totalTexte, montantFinal) {
    const fenetreImpression = window.open('', '', 'height=700,width=900');
    
    fenetreImpression.document.write('<html><head><title>' + titre + '</title>');
    fenetreImpression.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">');
    fenetreImpression.document.write('<style>body { padding: 30px; font-family: Arial, sans-serif; }</style>');
    fenetreImpression.document.write('</head><body>');
    
    fenetreImpression.document.write('<h3 class="mb-4 text-secondary border-bottom pb-2">' + titre + '</h3>');
    fenetreImpression.document.write('<table class="table table-bordered align-middle">');
    fenetreImpression.document.write('<thead class="table-light"><tr><th>Rubrique concernée</th><th class="text-end">Montant</th></tr></thead><tbody>');
    
    fenetreImpression.document.write(lignesHtml);
    
    if (montantFinal !== null && montantFinal !== undefined) {
        fenetreImpression.document.write('<tr class="table-active fw-bold fs-5"><td>' + totalTexte + '</td><td class="text-end text-success">' + Number(montantFinal).toLocaleString('fr-FR') + ' FCFA</td></tr>');
    }
    
    fenetreImpression.document.write('</tbody></table>');
    fenetreImpression.document.write('</body></html>');
    
    fenetreImpression.document.close();
    fenetreImpression.focus();
    
    setTimeout(() => {
        fenetreImpression.print();
        fenetreImpression.close();
    }, 400);
}

// 1. Impression Total Revenus
function imprimerRevenusTotal() {
    let html = '';
    let somme = 0;
    for (const [key, libelle] of Object.entries(rubriquesEntreesData)) {
        let m = parseFloat(dataEntreesValues[key]) || 0;
        html += '<tr><td>' + libelle + '</td><td class="text-end">' + m.toLocaleString('fr-FR') + ' FCFA</td></tr>';
        somme += m;
    }
    ouvrirFenetreImpression('Rapport - Total Revenus Global', html, 'TOTAL REVENUS', somme);
}

// 2. Impression Perso (Entrée)
function imprimerPersoEntree() {
    let m = parseFloat(dataEntreesValues['Perso (Entrée)']) || 0;
    let html = '<tr><td>Perso (Entrée)</td><td class="text-end fw-bold">' + m.toLocaleString('fr-FR') + ' FCFA</td></tr>';
    ouvrirFenetreImpression('Rapport - Cumul Perso (Entrée)', html, null, null);
}

// 3. Impression Revenus Famille (Toutes les rubriques sauf Perso Entrée)
function imprimerRevenusFamille() {
    let html = '';
    let somme = 0;
    for (const [key, libelle] of Object.entries(rubriquesEntreesData)) {
        if (key === 'Perso (Entrée)') continue;
        let m = parseFloat(dataEntreesValues[key]) || 0;
        html += '<tr><td>' + libelle + '</td><td class="text-end">' + m.toLocaleString('fr-FR') + ' FCFA</td></tr>';
        somme += m;
    }
    ouvrirFenetreImpression('Rapport - Total Revenus Famille ', html, 'TOTAL REVENUS FAMILLE', somme);
}

// 4. Impression Total Dépenses
function imprimerDepensesTotal() {
    let html = '';
    let somme = 0;
    for (const [key, libelle] of Object.entries(rubriquesSortiesData)) {
        let m = parseFloat(dataSortiesValues[key]) || 0;
        html += '<tr><td>' + libelle + '</td><td class="text-end">' + m.toLocaleString('fr-FR') + ' FCFA</td></tr>';
        somme += m;
    }
    ouvrirFenetreImpression('Rapport - Total Dépenses Global', html, 'TOTAL DÉPENSES', somme);
}

// 5. Impression Cumul Perso Sorties (Extra + Dépenses)
function imprimerPersoSorties() {
    let mExtra = parseFloat(dataSortiesValues['Perso (Extra)']) || 0;
    let mDep = parseFloat(dataSortiesValues['Perso (Dépenses)']) || 0;
    let total = mExtra + mDep;
    
    let html = '<tr><td>Perso (Extra)</td><td class="text-end">' + mExtra.toLocaleString('fr-FR') + ' FCFA</td></tr>';
    html += '<tr><td>Perso (Dépenses)</td><td class="text-end">' + mDep.toLocaleString('fr-FR') + ' FCFA</td></tr>';
    
    ouvrirFenetreImpression('Rapport - Cumul Perso Sorties', html, 'TOTAL PERSO SORTIES', total);
}

// 6. Impression Dépenses Famille (Toutes les rubriques sauf Perso Extra et Perso Dépenses)
function imprimerDepensesFamille() {
    let html = '';
    let somme = 0;
    for (const [key, libelle] of Object.entries(rubriquesSortiesData)) {
        if (key === 'Perso (Extra)' || key === 'Perso (Dépenses)') continue;
        let m = parseFloat(dataSortiesValues[key]) || 0;
        html += '<tr><td>' + libelle + '</td><td class="text-end">' + m.toLocaleString('fr-FR') + ' FCFA</td></tr>';
        somme += m;
    }
    ouvrirFenetreImpression('Rapport - Total Dépenses Famille', html, 'TOTAL DÉPENSES FAMILLE', somme);
}
</script>