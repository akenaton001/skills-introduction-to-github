<?php
// session_start(); // À décommenter si géré dans ton routeur
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Jàmm Santé Sénégal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts : Lexend -->
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-blue: #1e3c72;
            --accent-blue: #2a5298;
            --bg-light: #f4f6f9;
        }

        body {
            font-family: 'Lexend', sans-serif;
            background-color: var(--bg-light);
            color: #333;
            margin: 0;
            overflow-x: hidden;
        }

        /* Sidebar Fixe */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1b365d 0%, #0f1f38 100%);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.5rem 1rem;
            color: white;
        }

        .sidebar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }

        .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            flex-grow: 1;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.75);
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            font-weight: 500;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-link i {
            font-size: 1.15rem;
            margin-right: 0.75rem;
        }

        /* Contenu Principal */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 1.5rem;
        }

        /* Hero Banner */
        .hero-banner {
            background: linear-gradient(135deg, #1b365d 0%, #29487d 100%);
            border-radius: 1.25rem;
            color: white;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 25px rgba(27, 54, 93, 0.15);
        }

        /* Cartes Statistiques */
        .stat-card {
            background: white;
            border-radius: 1rem;
            padding: 1.25rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            border: none;
            height: 100%;
        }

        /* Composants Généraux */
        .card-custom {
            background: white;
            border-radius: 1rem;
            padding: 1.25rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            border: none;
            margin-bottom: 1.5rem;
        }

        .badge-status {
            padding: 0.35em 0.65em;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 50px;
        }
        .badge-encours { background-color: #e6f4ea; color: #137333; }
        .badge-attente { background-color: #fef7e0; color: #b06000; }
        .badge-termine { background-color: #e8f0fe; color: #1967d2; }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
         <?php include __DIR__ . '/../layouts/sidebar.php'; ?>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        
        <!-- TOP NAVBAR (INCLUDE) -->
        <?php include __DIR__ . '/../layouts/topbar.php'; ?>

        <!-- HERO BANNER -->
        <div class="hero-banner">
            <span class="badge bg-white bg-opacity-10 text-white px-3 py-1.5 rounded-pill fw-semibold mb-3" style="font-size: 0.75rem;">
                <span class="text-success me-1">●</span> Plateforme Nationale de Santé - Sénégal
            </span>
            <h1 class="fw-bold h3 mb-2">Bienvenue, Dr. Masse</h1>
            <p class="text-white-50 mb-0 small" style="max-width: 600px;">
                Gérez le Dossier Patient Unique, suivez les consultations, les rendez-vous, les hospitalisations et les examens de manière centralisée.
            </p>
            <div class="position-absolute top-50 end-0 translate-middle-y pe-5 d-none d-lg-block">
                <a href="index.php?action=patients_nouveau" class="btn btn-light fw-bold px-4 py-2.5 rounded-pill shadow-sm text-primary" style="font-size: 0.85rem;">
                    <i class="bi bi-person-plus-fill me-2"></i> Enregistrer un patient
                </a>
            </div>
        </div>

        <!-- STATS CARDS -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="stat-card">
                    <span class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Patients total</span>
                    <div class="d-flex align-items-baseline justify-content-between mt-2">
                        <h3 class="fw-bold mb-0">1 248</h3>
                        <span class="text-success small fw-semibold"><i class="bi bi-arrow-up"></i> +12%</span>
                    </div>
                    <span class="text-muted" style="font-size: 0.7rem;">par rapport au mois dernier</span>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="stat-card">
                    <span class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Consultations aujourd'hui</span>
                    <div class="d-flex align-items-baseline justify-content-between mt-2">
                        <h3 class="fw-bold mb-0">42</h3>
                        <span class="text-success small fw-semibold"><i class="bi bi-arrow-up"></i> +8%</span>
                    </div>
                    <span class="text-muted" style="font-size: 0.7rem;">par rapport à hier</span>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="stat-card">
                    <span class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Rendez-vous</span>
                    <div class="d-flex align-items-baseline justify-content-between mt-2">
                        <h3 class="fw-bold mb-0">286</h3>
                        <span class="text-success small fw-semibold"><i class="bi bi-arrow-up"></i> +6%</span>
                    </div>
                    <span class="text-muted" style="font-size: 0.7rem;">par rapport au mois dernier</span>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="stat-card">
                    <span class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Hospitalisés (lits)</span>
                    <div class="d-flex align-items-baseline justify-content-between mt-2">
                        <h3 class="fw-bold mb-0">18</h3>
                        <span class="text-danger small fw-semibold"><i class="bi bi-arrow-down"></i> -5%</span>
                    </div>
                    <span class="text-muted" style="font-size: 0.7rem;">par rapport au mois dernier</span>
                </div>
            </div>
        </div>

        <!-- SECTION TABLEAUX / DETAILS -->
        <div class="row g-4">
            
            <!-- Colonne de gauche : Dossier Patient unique & Derniers enregistrés -->
            <div class="col-lg-8">
                
                <!-- Bloc Dossier Patient Unique (Mini-Fiche) -->
                <div class="card-custom text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #1b365d 0%, #23487d 100%);">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 small text-uppercase" style="letter-spacing: 0.5px;">Dossier Patient Unique</h5>
                        <span class="badge bg-white text-primary fw-bold" style="font-size: 0.65rem;">DPU</span>
                    </div>
                    
                    <div class="bg-white bg-opacity-10 p-3 rounded-3 mb-3 d-inline-block">
                        <small class="text-white-50 d-block" style="font-size: 0.7rem;">N° Dossier :</small>
                        <span class="fw-bold font-monospace" style="font-size: 0.9rem;">P-2025-1047</span>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h4 class="fw-bold mb-1">Diallo Awa</h4>
                            <p class="text-white-50 small mb-2">28 ans • Femme</p>
                            <div class="small mb-1"><i class="bi bi-telephone me-2"></i> 77 123 45 67</div>
                            <div class="small mb-3"><i class="bi bi-geo-alt me-2"></i> Dakar</div>
                            <span class="badge bg-success bg-opacity-20 text-success border border-success border-opacity-25 px-2 py-1">Actif</span>
                        </div>
                        <div class="col-md-5 text-md-end mt-3 mt-md-0">
                            <a href="index.php?action=patient_voir" class="btn btn-light btn-sm fw-bold px-3 py-2 rounded-pill text-primary">
                                Voir le dossier <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Tableau : Dernières patients enregistrés -->
                <div class="card-custom">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0">Dernières patients enregistrées</h6>
                        <a href="index.php?action=patients" class="text-primary text-decoration-none small fw-semibold">Voir tout <i class="bi bi-chevron-right"></i></a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.82rem;">
                            <thead class="text-muted bg-light">
                                <tr>
                                    <th class="py-2">N° Dossier</th>
                                    <th class="py-2">Nom & Prénom</th>
                                    <th class="py-2">Âge</th>
                                    <th class="py-2">Téléphone</th>
                                    <th class="py-2">Motif de visite</th>
                                    <th class="py-2">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-semibold">P-2025-1047</td>
                                    <td>Diallo Awa</td>
                                    <td>28 ans</td>
                                    <td>77 123 45 67</td>
                                    <td>Suivi de grossesse</td>
                                    <td><span class="badge-status badge-encours">En cours</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">P-2025-1046</td>
                                    <td>Sow Fatou</td>
                                    <td>32 ans</td>
                                    <td>77 987 65 43</td>
                                    <td>Consultation générale</td>
                                    <td><span class="badge-status badge-attente">En attente</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">P-2025-1045</td>
                                    <td>Ndiaye Mariama</td>
                                    <td>26 ans</td>
                                    <td>76 543 21 09</td>
                                    <td>Gynécologie</td>
                                    <td><span class="badge-status badge-termine">Terminé</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">P-2025-1044</td>
                                    <td>Ba Khady</td>
                                    <td>41 ans</td>
                                    <td>77 321 98 76</td>
                                    <td>Suivi de grossesse</td>
                                    <td><span class="badge-status badge-encours">En cours</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Colonne de droite : Rendez-vous à venir & Alertes -->
            <div class="col-lg-4">
                
                <!-- Rendez-vous à venir -->
                <div class="card-custom">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0">Rendez-vous à venir</h6>
                        <a href="index.php?action=rendez_vous" class="text-primary text-decoration-none small fw-semibold">Voir tout <i class="bi bi-chevron-right"></i></a>
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <div class="d-flex align-items-center">
                            <span class="fw-bold text-secondary me-3" style="font-size: 0.85rem;">08:30</span>
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold me-2" style="width: 32px; height: 32px; font-size: 0.75rem;">DA</div>
                            <div>
                                <div class="fw-semibold" style="font-size: 0.85rem;">Diallo Awa</div>
                                <div class="text-muted" style="font-size: 0.7rem;">Suivi de grossesse</div>
                            </div>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success px-2 py-1" style="font-size: 0.7rem;">Confirmé</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <div class="d-flex align-items-center">
                            <span class="fw-bold text-secondary me-3" style="font-size: 0.85rem;">09:15</span>
                            <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center fw-bold me-2" style="width: 32px; height: 32px; font-size: 0.75rem;">SF</div>
                            <div>
                                <div class="fw-semibold" style="font-size: 0.85rem;">Sow Fatou</div>
                                <div class="text-muted" style="font-size: 0.7rem;">Consultation générale</div>
                            </div>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success px-2 py-1" style="font-size: 0.7rem;">Confirmé</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <div class="d-flex align-items-center">
                            <span class="fw-bold text-secondary me-3" style="font-size: 0.85rem;">10:00</span>
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center fw-bold me-2" style="width: 32px; height: 32px; font-size: 0.75rem;">NM</div>
                            <div>
                                <div class="fw-semibold" style="font-size: 0.85rem;">Ndiaye Mariama</div>
                                <div class="text-muted" style="font-size: 0.7rem;">Gynécologie</div>
                            </div>
                        </div>
                        <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1" style="font-size: 0.7rem;">En attente</span>
                    </div>
                </div>

                <!-- Alertes / À traiter -->
                <div class="card-custom">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0">Alertes / À traiter</h6>
                        <span class="text-muted small">Voir tout</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom" style="font-size: 0.83rem;">
                        <div class="d-flex align-items-center text-secondary">
                            <i class="bi bi-calendar-event me-2 text-warning"></i> Rendez-vous en attente
                        </div>
                        <span class="badge bg-danger rounded-pill">7</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom" style="font-size: 0.83rem;">
                        <div class="d-flex align-items-center text-secondary">
                            <i class="bi bi-file-earmark-text me-2 text-primary"></i> Résultats d'examens à consulter
                        </div>
                        <span class="badge bg-danger rounded-pill">12</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between py-2" style="font-size: 0.83rem;">
                        <div class="d-flex align-items-center text-secondary">
                            <i class="bi bi-hospital me-2 text-danger"></i> Dossiers incomplets
                        </div>
                        <span class="badge bg-danger rounded-pill">5</span>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>