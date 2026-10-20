<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion de l'Héritage - Darou Salam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bs-primary: #01452f; /* Vert Institutionnel */
            --bs-accent: #fcc005;  /* Or / Jaune */
            --sidebar-width: 270px;
        }
        body {
            font-family: 'Lexend', sans-serif;
            background-color: #F4F6F5;
            color: #2C3E50;
        }
        .app-wrapper {
            display: flex;
            min-height: 100vh;
        }
        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background-color: #01452f;
            color: #A3C1AD;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
        }
        .sidebar .brand {
            padding: 1.5rem;
            font-size: 1.2rem;
            font-weight: 700;
            color: #FFFFFF;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            background-color: rgba(0,0,0,0.15);
        }
        .sidebar .nav-link {
            color: #D1E7DD;
            padding: 0.95rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.85rem;
            font-weight: 400;
            transition: all 0.2s ease;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #FFFFFF;
            background-color: rgba(252, 192, 5, 0.15);
            border-left: 4px solid var(--bs-accent);
        }
        .sidebar .nav-link i {
            font-size: 1.2rem;
            color: var(--bs-accent);
        }
        /* Topbar */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .topbar {
            height: 75px;
            background-color: #FFFFFF;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 99;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        /* Cercles Parfaits (Ronds) */
        .icon-circle {
            width: 55px !important;
            height: 55px !important;
            min-width: 55px;
            min-height: 55px;
            border-radius: 50% !important;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        }
        .btn-primary {
            background-color: #01452f;
            border-color: #01452f;
        }
        .btn-primary:hover {
            background-color: #013523;
            border-color: #013523;
        }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="brand">
                <i class="bi bi-shield-fill-check fs-4 me-2" style="color: var(--bs-accent);"></i> Héritage 2018+
            </div>
            <ul class="nav flex-column mt-3">
                <li class="nav-item">
                    <a class="nav-link <?= (!isset($_GET['action']) || $_GET['action'] == 'dashboard') ? 'active' : ''; ?>" href="index.php?action=dashboard">
                        <i class="bi bi-grid-1x2-fill"></i> Tableau de bord
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($_GET['action']) && $_GET['action'] == 'create') ? 'active' : ''; ?>" href="index.php?action=create">
                        <i class="bi bi-plus-circle-fill"></i> Nouvelle Saisie
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($_GET['action']) && $_GET['action'] == 'list') ? 'active' : ''; ?>" href="index.php?action=list">
                        <i class="bi bi-search"></i> Recherches & Rapports
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($_GET['action']) && $_GET['action'] == 'report_rubrique') ? 'active' : ''; ?>" href="index.php?action=report_rubrique">
                        <i class="bi bi-bar-chart-fill"></i> Rapport Rubriques & Cumuls
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($_GET['action']) && $_GET['action'] == 'report_journalier') ? 'active' : ''; ?>" href="index.php?action=report_journalier">
                        <i class="bi bi-calendar-date-fill"></i> Rapport Journalier
                    </a>
                </li>
                <!-- Ajout du lien vers le rapport par bénéficiaire -->
                <li class="nav-item">
                    <a class="nav-link <?= (isset($_GET['action']) && $_GET['action'] == 'report_beneficiaire') ? 'active' : ''; ?>" href="index.php?action=report_beneficiaire">
                        <i class="bi bi-person-lines-fill"></i> Rapport par Bénéficiaire
                    </a>
                </li>
            </ul>
            <div class="mt-auto p-3 text-center small" style="color: rgba(255,255,255,0.6); border-top: 1px solid rgba(255,255,255,0.1);">
                <strong style="color: var(--bs-accent);">Compte Rendu Familial</strong>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-wrapper">
            <div class="topbar">
                <div class="fw-semibold text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-range text-success"></i> Gestion Financière de l'Héritage (Depuis 2018)
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="fw-semibold text-dark">
                        <i class="bi bi-person-circle text-success me-1"></i> Admin (Ousmane)
                    </span>
                    <a href="index.php?action=logout" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1">
                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                    </a>
                </div>
            </div>

            <div class="container-fluid p-4">
                <?php
                if (isset($view) && file_exists($view)) {
                    include $view;
                } else {
                    // Tableau de bord épuré sans rapports mensuels ni sélecteur d'année
                    $totalsEntrees = $totals['total_entrees'] ?? 0;
                    $totalsSorties = $totals['total_sorties'] ?? 0;
                    $soldeGlobal = $totalsEntrees - $totalsSorties;
                ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="fw-bold mb-0" style="color: #01452f;">Tableau de Bord & Compte Rendu Global</h2>
                    </div>

                    <!-- Cartes avec cercles rigoureusement ronds -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="card bg-white p-4 border-start border-4 border-success shadow-sm">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted text-uppercase small fw-bold mb-1">Total Recettes</p>
                                        <h3 class="fw-bold text-success mb-0"><?= number_format($totalsEntrees, 0, ',', ' '); ?> <span class="fs-6">FCFA</span></h3>
                                    </div>
                                    <div class="icon-circle bg-success bg-opacity-10 text-success fs-3">
                                        <i class="bi bi-arrow-down-left-circle-fill"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-white p-4 border-start border-4 border-danger shadow-sm">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted text-uppercase small fw-bold mb-1">Total Dépenses</p>
                                        <h3 class="fw-bold text-danger mb-0"><?= number_format($totalsSorties, 0, ',', ' '); ?> <span class="fs-6">FCFA</span></h3>
                                    </div>
                                    <div class="icon-circle bg-danger bg-opacity-10 text-danger fs-3">
                                        <i class="bi bi-arrow-up-right-circle-fill"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-white p-4 border-start border-4 shadow-sm" style="border-color: var(--bs-accent) !important;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted text-uppercase small fw-bold mb-1">Solde Net Actuel</p>
                                        <h3 class="fw-bold mb-0" style="color: #01452f;"><?= number_format($soldeGlobal, 0, ',', ' '); ?> <span class="fs-6">FCFA</span></h3>
                                    </div>
                                    <div class="icon-circle text-white fs-3" style="background-color: #01452f;">
                                        <i class="bi bi-wallet2" style="color: var(--bs-accent);"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Situation par Banque -->
                    <h4 class="fw-bold mb-3" style="color: #01452f;">Situation par Banque</h4>
                    <div class="row g-4 mb-5">
                        <?php 
                        $banquesData = ['BIS' => $bis, 'BHS' => $bhs, 'CBAO' => $cbao];
                        foreach($banquesData as $nomBanque => $dataBanque): 
                        ?>
                            <div class="col-md-4">
                                <div class="card p-4 shadow-sm">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-bank me-2 text-success"></i><?= $nomBanque; ?></h5>
                                        <span class="badge bg-light text-success border border-success">Banque Partenaire</span>
                                    </div>
                                    <hr class="text-muted opacity-25">
                                    <p class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Versé (Entrées) :</span>
                                        <strong class="text-success"><?= number_format($dataBanque['entrees'] ?? 0, 0, ',', ' '); ?> FCFA</strong>
                                    </p>
                                    <p class="d-flex justify-content-between mb-0">
                                        <span class="text-muted">Retiré (Sorties) :</span>
                                        <strong class="text-danger"><?= number_format($dataBanque['sorties'] ?? 0, 0, ',', ' '); ?> FCFA</strong>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>