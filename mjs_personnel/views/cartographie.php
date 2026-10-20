<?php
session_start();
require_once __DIR__ . '/../config/bd.php';

if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['SuperAdmin', 'DAGE_RH'])) {
    header('Location: /mjs_personnel/index.php');
    exit();
}

try {
    $sql = "SELECT a.*, s.nom_structure as cdeps, p.nom_structure as region 
            FROM agents a 
            LEFT JOIN structures s ON a.id_structure_actuelle = s.id_structure 
            LEFT JOIN structures p ON s.id_parent = p.id_structure
            ORDER BY s.nom_structure ASC, a.nom ASC";
    $agents = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    $structs = $pdo->query("SELECT DISTINCT nom_structure FROM structures WHERE type_structure = 'CDEPS'")->fetchAll(PDO::FETCH_COLUMN);
    $regions = $pdo->query("SELECT DISTINCT nom_structure FROM structures WHERE id_parent IS NULL")->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) { die("Erreur : " . $e->getMessage()); }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MJS - Cartographie des Agents</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
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

    <div class="container mt-4">
        <h2 class="h3 text-dark mb-4 fw-bold"><i class="fa-solid fa-map-location-dot text-success me-2"></i> Cartographie du Personnel</h2>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <select id="filterRegion" class="form-select filter-select shadow-sm"><option value="">Toutes Régions</option>
                    <?php foreach ($regions as $r) echo "<option value='".htmlspecialchars($r)."'>".htmlspecialchars($r)."</option>"; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select id="filterCDEPS" class="form-select filter-select shadow-sm"><option value="">Toutes Structures</option>
                    <?php foreach ($structs as $s) echo "<option value='".htmlspecialchars($s)."'>".htmlspecialchars($s)."</option>"; ?>
                </select>
            </div>
        </div>

        <div class="card p-4 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle w-100" id="cartoTable">
                    <thead class="table-light text-success">
                        <tr><th>Agent</th><th>Matricule</th><th>Localisation</th><th>Contact</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($agents as $a): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($a['prenom'].' '.$a['nom']) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($a['statut_agent']) ?></small>
                                </td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($a['matricule_solde']) ?></span></td>
                                <td data-region="<?= htmlspecialchars($a['region'] ?? '') ?>" 
                                    data-cdeps="<?= htmlspecialchars($a['cdeps'] ?? '') ?>">
                                    <span class="medallion medallion-green"><?= htmlspecialchars($a['cdeps'] ?? 'N/A') ?></span>
                                    <span class="medallion"><?= htmlspecialchars($a['region'] ?? 'N/A') ?></span>
                                </td>
                                <td>
                                    <small class="d-block text-success"><i class="fa fa-phone"></i> <?= htmlspecialchars($a['telephone']) ?></small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
		<?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
    $(document).ready(function() {
        var table = $('#cartoTable').DataTable({
            pageLength: 5,
            lengthMenu: [[5, 10, 20, 50], [5, 10, 20, 50]]
        });

        // Fonction de filtrage personnalisée
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var regionFilter = $('#filterRegion').val();
            var cdepsFilter = $('#filterCDEPS').val();
            
            // On récupère les valeurs réelles via les data-attributes de la ligne
            var row = table.row(dataIndex).node();
            var region = $(row).find('td').eq(2).data('region');
            var cdeps = $(row).find('td').eq(2).data('cdeps');

            if ((regionFilter === "" || region === regionFilter) && 
                (cdepsFilter === "" || cdeps === cdepsFilter)) {
                return true;
            }
            return false;
        });

        // Redessiner le tableau à chaque changement
        $('#filterRegion, #filterCDEPS').on('change', function() {
            table.draw();
        });
    });
    </script>
</body>
</html>