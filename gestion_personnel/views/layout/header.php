<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion RH - BDG</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Font: Lexend -->
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #721519;
            --accent-color: #f6b036;
            --bg-light: #f8f9fa;
        }

        body {
            font-family: 'Lexend', sans-serif;
            background-color: #f4f5f7;
            color: #333;
        }

        /* Sidebar Style */
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #ffffff;
            border-right: 1px solid #e9ecef;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            z-index: 100;
        }

        .sidebar-brand {
            padding: 20px 15px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }

        .sidebar-brand img {
            max-height: 80px;
            width: auto;
            object-fit: contain;
            margin-bottom: 8px;
        }

        .brand-title {
            color: var(--primary-color);
            font-weight: 700;
            text-transform: uppercase;
            line-height: 1.2;
            margin: 0;
        }

        .brand-title .main-text {
            font-size: 1.3rem;
            display: block;
            color: var(--primary-color);
        }

        .brand-title .sub-text {
            font-size: 1.1rem;
            color: var(--accent-color);
            font-weight: 700;
            display: block;
            letter-spacing: 1px;
        }

        .sidebar-menu {
            padding: 20px 10px;
            list-style: none;
            margin: 0;
        }

        .sidebar-menu .nav-link {
            color: #555;
            padding: 12px 15px;
            border-radius: 10px;
            font-weight: 500;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .sidebar-menu .nav-link:hover, 
        .sidebar-menu .nav-link.active {
            background-color: rgba(114, 21, 25, 0.08);
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Style pour les modules désactivés / grisés */
        .sidebar-menu .nav-link.disabled-link {
            color: #aaa;
            background-color: transparent;
            cursor: not-allowed;
        }

        .sidebar-menu .nav-link.disabled-link:hover {
            color: #aaa;
            background-color: transparent;
        }

        .sidebar-menu .nav-link.logout-link {
            color: #d32f2f;
        }

        .sidebar-menu .nav-link.logout-link:hover {
            background-color: #ffebee;
            color: #c62828;
        }

        /* Classe personnalisée pour le badge avec la couleur primaire */
        .badge-primary-custom {
            background-color: var(--primary-color) !important;
            color: #ffffff !important;
        }

        /* Styles spécifiques aux boutons personnalisés */
        .btn-custom-primary {
            background-color: var(--primary-color);
            color: #ffffff;
            border: none;
            border-radius: 20px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-custom-primary:hover {
            background-color: #581013;
            color: #ffffff;
        }

        .btn-search-custom {
            background-color: var(--primary-color);
            color: #ffffff;
            border: none;
            border-radius: 0 20px 20px 0;
            padding: 8px 16px;
        }

        .btn-search-custom:hover {
            background-color: #581013;
            color: #ffffff;
        }

        .form-control-custom, .form-select-custom {
            border-radius: 20px;
            border: 1px solid #ced4da;
            padding: 8px 15px;
        }

        /* Style personnalisé pour la modale Bootstrap (Gestion Parc) */
        .modal-content-custom {
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .modal-header-custom {
            background-color: var(--primary-color);
            color: #ffffff;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            padding: 15px 20px;
        }

        .modal-header-custom .btn-close {
            filter: brightness(0) invert(1);
        }

        .modal-footer-custom {
            border-top: 1px solid #f0f0f0;
            padding: 12px 20px;
        }

        /* Main Content wrapper */
        .main-wrapper {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Topbar Style */
        .topbar {
            height: 70px;
            background: #ffffff;
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            border-bottom: 1px solid #e9ecef;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--accent-color);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .btn-logout-icon {
            color: #721519;
            border: 1px solid #721519;
            border-radius: 8px;
            padding: 6px 12px;
            transition: all 0.2s;
        }

        .btn-logout-icon:hover {
            background-color: #721519;
            color: #ffffff;
        }
    </style>
</head>
<body>

<?php if (isset($_SESSION['user_id'])): ?>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div>
            <div class="sidebar-brand">
                <a href="index.php" class="d-flex flex-column align-items-center text-decoration-none">
                    <?php if (file_exists('assets/images/logo.png')): ?>
                        <img src="assets/images/logo.png" alt="Logo BDG">
                    <?php elseif (file_exists('assets/images/logo.jpg')): ?>
                        <img src="assets/images/logo.jpg" alt="Logo BDG">
                    <?php endif; ?>
                    
                    <div class="brand-title mt-1">
                        <span class="main-text">GESTION</span>
                        <span class="main-text">RH</span>
                    </div>
                </a>
            </div>

            <ul class="sidebar-menu">
                <!-- 1. Liste Employés -->
                <li>
                    <a href="index.php" class="nav-link <?= (!isset($_GET['action']) || $_GET['action'] == 'index') ? 'active' : '' ?>">
                        <i class="fa-solid fa-users"></i>
                        <span>Liste Employés</span>
                    </a>
                </li>
                
                <!-- 2. Gestion des incidents -->
                <li>
                    <a href="index.php?action=incidents" class="nav-link <?= (isset($_GET['action']) && strpos($_GET['action'], 'incidents') === 0) ? 'active' : '' ?>">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Gestion des incidents</span>
                    </a>
                </li>

                <!-- 3. Contentieux / Suivi -->
                <li>
                    <a href="index.php?action=disputes" class="nav-link <?= (isset($_GET['action']) && strpos($_GET['action'], 'disputes') === 0) ? 'active' : '' ?>">
                        <i class="fa-solid fa-gavel"></i>
                        <span>Contentieux - Suivi</span>
                    </a>
                </li>

                <!-- 4. Gestion Parc (Modale intégrée avec les couleurs de base) -->
                <li>
                    <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#parcModal">
                        <i class="fa-solid fa-car-side"></i>
                        <span>Gestion Parc</span>
                    </a>
                </li>

                <!-- 5. Gestion Congés (Grisé / Inactif) -->
                <li>
                    <a href="#" class="nav-link disabled-link d-flex justify-content-between align-items-center" onclick="return false;" title="Bientôt disponible">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-calendar-days"></i>
                            <span>Gestion Congés</span>
                        </div>
                        <span class="badge badge-primary-custom" style="font-size: 0.65rem;">Bientôt</span>
                    </a>
                </li>

                <!-- 6. Bulletins de salaire (Grisé / Inactif) -->
                <li>
                    <a href="#" class="nav-link disabled-link d-flex justify-content-between align-items-center" onclick="return false;" title="Bientôt disponible">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                            <span>Bulletins de salaire</span>
                        </div>
                        <span class="badge badge-primary-custom" style="font-size: 0.65rem;">Bientôt</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="p-3 border-top">
            <a href="index.php?action=logout" class="nav-link logout-link d-flex align-items-center gap-2 text-decoration-none">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </aside>

    <!-- Modale Bootstrap personnalisée pour Gestion Parc -->
    <div class="modal fade" id="parcModal" tabindex="-1" aria-labelledby="parcModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title fs-6 fw-semibold" id="parcModalLabel">
                        <i class="fa-solid fa-car-side me-2"></i> Gestion Parc
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body text-center py-4 px-4">
                    <div class="mb-3">
                        <i class="fa-solid fa-person-digging fa-2x" style="color: var(--accent-color);"></i>
                    </div>
                    <p class="mb-0 text-secondary">Ce module sera bientôt disponible.</p>
                </div>
                <div class="modal-footer modal-footer-custom justify-content-center border-0 pb-3">
                    <button type="button" class="btn btn-custom-primary px-4" data-bs-dismiss="modal">Compris</button>
                </div>
            </div>
        </div>
    </div>

    <div class="main-wrapper">
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-sm-block">
                    <div class="fw-bold small text-dark mb-0"><?= htmlspecialchars($_SESSION['user_nom'] ?? 'Utilisateur') ?></div>
                    <small class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></small>
                </div>
                <div class="user-avatar">
                    <?= strtoupper(substr($_SESSION['user_nom'] ?? 'A', 0, 1)) ?>
                </div>
                <a href="index.php?action=logout" class="btn btn-logout-icon ms-2" title="Déconnexion">
                    <i class="fa-solid fa-power-off"></i>
                </a>
            </div>
        </header>

        <main class="p-4">
<?php else: ?>
    <div>
<?php endif; ?>