<?php
// views/agent_modifier.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/bd.php';
require_once __DIR__ . '/../config/securite.php';

restreindre_acces_a(['SuperAdmin', 'DAGE_RH']);

$id_agent = $_GET['id'] ?? null;
$agent = [];

if ($id_agent) {
    $stmt = $pdo->prepare("SELECT * FROM agents WHERE id_agent = ?");
    $stmt->execute([$id_agent]);
    $agent = $stmt->fetch(PDO::FETCH_ASSOC);
}

function v($key) { global $agent; return htmlspecialchars($agent[$key] ?? ''); }
function is_sel($key, $val) { global $agent; return (($agent[$key] ?? '') == $val) ? 'selected' : ''; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un agent - MJS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
       @import url('https://fonts.googleapis.com/css2?family=Gilroy:wght@400;500;600&display=swap');
        
        :root { --sn-green: #198754; --sn-yellow: #FCD116; --sn-red: #E31B23; }
        body { font-family: 'Gilroy', sans-serif; background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); min-height: 100vh; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); }
        
        /* Filtres Styles - Mise en forme élégante et stable */
        .filter-select {
            appearance: none;
            -webkit-appearance: none;
            background-color: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 50px;
            padding: 0.6rem 2.5rem 0.6rem 1.2rem;
            font-weight: 500;
            color: #475569;
            transition: all 0.3s ease;
            cursor: pointer;
            font-family: 'Gilroy', sans-serif;
            outline: none;
            /* Ajout de la flèche personnalisée pour le look moderne */
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23198754' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 16px 12px;
        }

        /* Effet visuel élégant au clic et au focus */
        .filter-select:focus {
            border-color: #157547;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(21, 117, 71, 0.15);
        }

        /* Style de base des options */
        .filter-select option {
            padding: 12px;
            background-color: #ffffff;
            color: #475569;
            font-family: 'Gilroy', sans-serif;
        }
        
        /* Recherche Moderne */
        .dataTables_filter input { border-radius: 50px !important; border: 2px solid #e2e8f0 !important; padding: 0.5rem 1.5rem !important; transition: 0.3s; }
        .dataTables_filter input:focus { border-color: var(--sn-green) !important; box-shadow: 0 0 10px rgba(25, 135, 84, 0.2) !important; }

        /* Pagination Verte */
        .pagination .page-item.active .page-link { background-color: var(--sn-green) !important; border-color: var(--sn-green) !important; }
        .pagination .page-link { color: var(--sn-green) !important; }

        /* Médaillons */
        .medallion { display: inline-block; padding: 0.2rem 0.6rem; border-radius: 50px; font-size: 0.70rem; font-weight: 600; background: #e2e8f0; border: 1px solid #cbd5e1; }
        .medallion-green { background: #d1e7dd; color: var(--sn-green); border-color: #badbcc; }
        
        .navbar-custom {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 3px solid var(--sn-green);
            padding: 0.5rem 1.5rem;
        }
		/* Fixe le footer en bas de page */
		body {
			display: flex;
			flex-direction: column;
			min-height: 100vh;
			margin: 0;
		}

		/* Pousse le contenu principal pour occuper l'espace restant */
		.container {
		flex: 1;
		}
    </style>
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>
<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="agents_liste.php" class="btn btn-outline-secondary me-3"><i class="fa-solid fa-arrow-left"></i></a>
        <h3 class="mb-0">Modifier : <?= v('prenom') . ' ' . v('nom') ?></h3>
    </div>
    <form action="../modules/agents/modifier_traitement.php" method="POST">
    <input type="hidden" name="agent_id" value="<?= $agent['id_agent'] ?>">
        
        <div class="card mb-4">
            <div class="card-header"><i class="fa-solid fa-id-card me-2"></i> 1. Identité et État Civil</div>
            <div class="card-body row g-3">
                <div class="col-md-4"><label>MATRICULE SOLDE</label><input type="text" name="matricule_solde" class="form-control" value="<?= v('matricule_solde') ?>"></div>
                <div class="col-md-4"><label>NUMÉRO CNI</label><input type="text" name="cni" class="form-control" value="<?= v('cni') ?>"></div>
                <div class="col-md-4"><label>SEXE</label>
                    <select name="sexe" class="form-select">
                        <option value="M" <?= is_sel('sexe', 'M') ?>>Masculin</option>
                        <option value="F" <?= is_sel('sexe', 'F') ?>>Féminin</option>
                    </select>
                </div>
                <div class="col-md-6"><label>PRÉNOM</label><input type="text" name="prenom" class="form-control" value="<?= v('prenom') ?>"></div>
                <div class="col-md-6"><label>NOM</label><input type="text" name="nom" class="form-control" value="<?= v('nom') ?>"></div>
                <div class="col-md-4"><label>DATE NAISSANCE</label><input type="date" name="date_naissance" class="form-control" value="<?= v('date_naissance') ?>"></div>
                <div class="col-md-4"><label>LIEU NAISSANCE</label><input type="text" name="lieu_naissance" class="form-control" value="<?= v('lieu_naissance') ?>"></div>
                <div class="col-md-4"><label>SITUATION MATRIMONIALE</label>
                    <select name="situation_matrimoniale" class="form-select">
                        <option value="Célibataire" <?= is_sel('situation_matrimoniale', 'Célibataire') ?>>Célibataire</option>
                        <option value="Marié(e)" <?= is_sel('situation_matrimoniale', 'Marié(e)') ?>>Marié(e)</option>
                        <option value="Divorcé(e)" <?= is_sel('situation_matrimoniale', 'Divorcé(e)') ?>>Divorcé(e)</option>
                        <option value="Veuf(ve)" <?= is_sel('situation_matrimoniale', 'Veuf(ve)') ?>>Veuf(ve)</option>
                    </select>
                </div>
                <div class="col-md-6"><label>SITUATION MILITAIRE</label>
                    <select name="situation_militaire" class="form-select">
                        <option value="Non effectué" <?= is_sel('situation_militaire', 'Non effectué') ?>>Non effectué</option>
                        <option value="Libéré" <?= is_sel('situation_militaire', 'Libéré') ?>>Libéré</option>
                        <option value="Exempté" <?= is_sel('situation_militaire', 'Exempté') ?>>Exempté</option>
                    </select>
                </div>
                <div class="col-md-6"><label>NOMBRE D'ENFANTS</label><input type="number" name="nombre_enfants" class="form-control" value="<?= v('nombre_enfants') ?>"></div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><i class="fa-solid fa-address-book me-2"></i> 2. Coordonnées</div>
            <div class="card-body row g-3">
                <div class="col-md-6"><label>TÉLÉPHONE</label><input type="text" name="telephone" class="form-control" value="<?= v('telephone') ?>"></div>
                <div class="col-md-6"><label>COMPTE BANCAIRE</label><input type="text" name="numero_compte_bancaire" class="form-control" value="<?= v('numero_compte_bancaire') ?>"></div>
                <div class="col-12"><label>ADRESSE</label><input type="text" name="adresse" class="form-control" value="<?= v('adresse') ?>"></div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><i class="fa-solid fa-briefcase me-2"></i> 3. Cadre Administratif</div>
            <div class="card-body row g-3">
                <div class="col-md-4"><label>STATUT</label>
                    <select name="statut_agent" class="form-select">
                        <option value="Fonctionnaire" <?= is_sel('statut_agent', 'Fonctionnaire') ?>>Fonctionnaire Titulaire</option>
                        <option value="Contractuel" <?= is_sel('statut_agent', 'Contractuel') ?>>Contractuel de l'État</option>
                        <option value="Stagiaire" <?= is_sel('statut_agent', 'Stagiaire') ?>>Stagiaire / Prestataire</option>
                        <option value="Détaché" <?= is_sel('statut_agent', 'Détaché') ?>>En Détachement</option>
                    </select>
                </div>
                <div class="col-md-4"><label>SERVICE</label>
                    <select name="direction_service" class="form-select">
                        <option value="DAGE" <?= is_sel('direction_service', 'DAGE') ?>>DAGE</option>
                        <option value="DAPS" <?= is_sel('direction_service', 'DAPS') ?>>DAPS</option>
                        <option value="DFDS" <?= is_sel('direction_service', 'DFDS') ?>>DFDS</option>
                        <option value="DIS" <?= is_sel('direction_service', 'DIS') ?>>DIS</option>
                        <option value="DLS" <?= is_sel('direction_service', 'DLS') ?>>DLS</option>
                        <option value="DSHN" <?= is_sel('direction_service', 'DSHN') ?>>DSHN</option>
                        <option value="DASEVA" <?= is_sel('direction_service', 'DASEVA') ?>>DASEVA</option>
                        <option value="DPSJ" <?= is_sel('direction_service', 'DPSJ') ?>>DPSJ</option>
                        <option value="DEP" <?= is_sel('direction_service', 'DEP') ?>>DEP</option>
                        <option value="DRJS" <?= is_sel('direction_service', 'DRJS') ?>>DRJS</option>
                        <option value="Cabinet" <?= is_sel('direction_service', 'Cabinet') ?>>Cabinet</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>AFFECTATION CDEPS</label>
                    <select name="id_structure_actuelle" class="form-select">
                        <option value="">Sélectionner un CDEPS...</option>
                        <?php
                        $regions = $pdo->query("SELECT id_structure, nom_structure FROM structures WHERE type_structure = 'Service régional'");
                        foreach ($regions as $region) {
                            echo "<optgroup label='{$region['nom_structure']}'>";
                            $stmt = $pdo->prepare("SELECT id_structure, nom_structure FROM structures WHERE type_structure = 'CDEPS' AND id_parent = ?");
                            $stmt->execute([$region['id_structure']]);
                            while ($cdeps = $stmt->fetch()) {
                                $selected = ($cdeps['id_structure'] == $agent['id_structure_actuelle']) ? 'selected' : '';
                                echo "<option value='{$cdeps['id_structure']}' $selected>{$cdeps['nom_structure']}</option>";
                            }
                            echo "</optgroup>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-4"><label>CORPS</label><input type="text" name="corps_agent" class="form-control" value="<?= v('corps_agent') ?>"></div>
                <div class="col-md-4"><label>HIÉRARCHIE</label>
                    <select name="hierarchie" class="form-select">
                        <option value="" <?= is_sel('hierarchie', '') ?>>Sélectionner...</option>
                        <option value="A1" <?= is_sel('hierarchie', 'A1') ?>>A1</option>
                        <option value="A2" <?= is_sel('hierarchie', 'A2') ?>>A2</option>
                        <option value="A3" <?= is_sel('hierarchie', 'A3') ?>>A3</option>
                        <option value="B" <?= is_sel('hierarchie', 'B') ?>>B</option>
                        <option value="C" <?= is_sel('hierarchie', 'C') ?>>C</option>
                        <option value="D" <?= is_sel('hierarchie', 'D') ?>>D</option>
                        <option value="E" <?= is_sel('hierarchie', 'E') ?>>E</option>
                    </select>
                </div>
                <div class="col-md-4"><label>ENTRÉE MINISTÈRE</label><input type="date" name="date_entree_ministere" class="form-control" value="<?= v('date_entree_ministere') ?>"></div>
                <div class="col-md-4"><label>PRISE SERVICE</label><input type="date" name="date_prise_service" class="form-control" value="<?= v('date_prise_service') ?>"></div>
            </div>
        </div>

        <div class="text-end mb-5"><button type="submit" class="btn btn-success px-5"><i class="fa-solid fa-floppy-disk me-2"></i> Enregistrer</button></div>
    </form>
</div>
</body>
</html>