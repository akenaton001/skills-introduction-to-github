<?php 
$pageTitle = "Nouvelle Consultation - Jàmm Santé";
$page = 'consultations'; // ou 'patients' selon votre menu actif
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --sidebar-width: 260px; --bg-light: #f4f6f9; }
        body { font-family: 'Lexend', sans-serif; background-color: var(--bg-light); color: #333; margin: 0; }
        .sidebar { width: var(--sidebar-width); background: linear-gradient(180deg, #1b365d 0%, #0f1f38 100%); position: fixed; top: 0; bottom: 0; left: 0; z-index: 100; display: flex; flex-direction: column; justify-content: space-between; padding: 1.5rem 1rem; color: white; }
        .sidebar-brand { font-weight: 700; font-size: 1.25rem; color: white; text-decoration: none; display: flex; align-items: center; margin-bottom: 2rem; }
        .nav-menu { list-style: none; padding: 0; margin: 0; flex-grow: 1; }
        .nav-link { color: rgba(255, 255, 255, 0.75); padding: 0.75rem 1rem; border-radius: 0.75rem; display: flex; align-items: center; font-weight: 500; font-size: 0.9rem; text-decoration: none; transition: all 0.2s ease; }
        .nav-link:hover, .nav-link.active { color: white; background-color: rgba(255, 255, 255, 0.1); }
        .nav-link i { font-size: 1.15rem; margin-right: 0.75rem; }
        .main-content { margin-left: var(--sidebar-width); padding: 1.5rem; }
        .top-navbar { background: white; border-radius: 1rem; padding: 0.75rem 1.5rem; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 10px rgba(0,0,0,0.02); margin-bottom: 1.5rem; }
        .search-box { position: relative; width: 400px; }
        .search-box input { background-color: #f8f9fa; border: 1px solid #e2e8f0; border-radius: 50px; padding: 0.5rem 1rem 0.5rem 2.5rem; font-size: 0.85rem; width: 100%; }
        .search-box i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #a0aec0; }
    </style>
</head>
<body>

    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>

    <div class="main-content">
        <?php include __DIR__ . '/../layouts/topbar.php'; ?>

        <div class="container-fluid px-0">
            
            <!-- Fil d'Ariane et En-tête -->
            <div class="row align-items-center mb-4">
                <div class="col-md-8">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="index.php?action=patients" class="text-decoration-none">Gestion des Patients</a></li>
                            <li class="breadcrumb-item"><a href="index.php?action=patient_dossier&id=<?= $patient['id']; ?>" class="text-decoration-none">Dossier Unique</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Nouvelle Consultation</li>
                        </ol>
                    </nav>
                    <h2 class="fw-bold text-dark mb-0">
                        <i class="bi bi-journal-plus text-primary me-2"></i> 
                        Consultation pour <?= htmlspecialchars($patient['prenom'] . ' ' . $patient['nom'], ENT_QUOTES, 'UTF-8'); ?>
                    </h2>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="index.php?action=patient_dossier&id=<?= $patient['id']; ?>" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold shadow-sm">
                        <i class="bi bi-arrow-left me-1"></i> Retour au dossier
                    </a>
                </div>
            </div>

            <!-- Formulaire d'enregistrement -->
            <form action="index.php?action=consultations_store" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="patient_id" value="<?= $patient['id']; ?>">

                <div class="row g-4">
                    <!-- Colonne Principale : Données Cliniques -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4">
                            <h5 class="fw-bold text-primary mb-3">
                                <i class="bi bi-journal-medical me-2"></i> Informations Cliniques
                            </h5>
                            <hr class="mb-4">

                            <div class="row g-4">
                                <!-- Date et Heure de consultation -->
                                <div class="col-md-6">
                                    <label for="date_consultation" class="form-label fw-semibold">Date et Heure de la Consultation <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-calendar-event"></i></span>
                                        <input type="datetime-local" class="form-control border-start-0 ps-0 shadow-none" id="date_consultation" name="date_consultation" value="<?= date('Y-m-d\TH:i'); ?>" required>
                                    </div>
                                </div>

                                <!-- État d'avancement / Évolution -->
                                <div class="col-md-6">
                                    <label for="evolution" class="form-label fw-semibold">État d'avancement / Évolution <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-activity"></i></span>
                                        <select class="form-select border-start-0 ps-0 shadow-none" id="evolution" name="evolution" required>
                                            <option value="Première consultation">Première consultation</option>
                                            <option value="Stable / Stationnaire">Stable / Stationnaire</option>
                                            <option value="Amélioration nette">Amélioration nette</option>
                                            <option value="Aggravation">Aggravation</option>
                                            <option value="Guéri">Guéri</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Motif -->
                                <div class="col-12">
                                    <label for="motif" class="form-label fw-semibold">Motif de la consultation <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-chat-left-text"></i></span>
                                        <input type="text" class="form-control border-start-0 ps-0 shadow-none" id="motif" name="motif" placeholder="Ex: Fièvre persistante, Douleurs abdominales..." required>
                                    </div>
                                </div>

                                <!-- Symptômes -->
                                <div class="col-12">
                                    <label for="symptomes" class="form-label fw-semibold">Symptômes & Signes cliniques</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted align-items-start pt-2"><i class="bi bi-file-medical"></i></span>
                                        <textarea class="form-control border-start-0 ps-0 shadow-none" id="symptomes" name="symptomes" rows="3" placeholder="Décrivez les symptômes observés..."></textarea>
                                    </div>
                                </div>

                                <!-- Diagnostic -->
                                <div class="col-12">
                                    <label for="diagnostic" class="form-label fw-semibold">Diagnostic <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted align-items-start pt-2"><i class="bi bi-clipboard2-pulse"></i></span>
                                        <textarea class="form-control border-start-0 ps-0 shadow-none" id="diagnostic" name="diagnostic" rows="3" placeholder="Diagnostic posé par le médecin..." required></textarea>
                                    </div>
                                </div>

                                <!-- Prescription / Ordonnance -->
                                <div class="col-12">
                                    <label for="prescription" class="form-label fw-semibold">Ordonnance / Traitement prescrit</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted align-items-start pt-2"><i class="bi bi-capsule"></i></span>
                                        <textarea class="form-control border-start-0 ps-0 shadow-none" id="prescription" name="prescription" rows="3" placeholder="Médicaments, posologie, recommandations..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Colonne Secondaire : Suivi & Pièces Jointes -->
                    <div class="col-lg-4">
                        
                        <!-- 1. Suivi & Prescriptions -->
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                            <h5 class="fw-bold text-primary mb-3">
                                <i class="bi bi-clipboard-pulse me-2"></i> Suivi & Prescriptions
                            </h5>
                            <hr class="mb-3">
                            
                            <div class="mb-3">
                                <label for="examens_prescrits" class="form-label fw-semibold small">Examens à faire (Bio, Radio, etc.)</label>
                                <textarea class="form-control shadow-none bg-light" id="examens_prescrits" name="examens_prescrits" rows="3" placeholder="Ex: NFS, Paludisme, Radio pulmonaire..."></textarea>
                                <div class="form-text small text-muted mt-1">Ces examens seront enregistrés pour le suivi de ce patient.</div>
                            </div>

                            <div class="mb-3">
                                <label for="date_prochain_rv" class="form-label fw-semibold small">Prochain rendez-vous (Indicatif / Période)</label>
                                <input type="date" class="form-control shadow-none bg-light" id="date_prochain_rv" name="date_prochain_rv">
                                <div class="form-text small text-muted mt-1">
                                    <i class="bi bi-info-circle"></i> Indiquez une date repère ou la période souhaitée (ex: à 3 ou 6 mois). Le secrétariat calera la date et l'heure exactes.
                                </div>
                            </div>
                        </div>

                        <!-- 2. Rattachement des résultats -->
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                            <h5 class="fw-bold text-primary mb-3">
                                <i class="bi bi-paperclip me-2"></i> Résultats / Documents
                            </h5>
                            <hr class="mb-3">
                            
                            <div class="mb-3">
                                <label for="titre_piece" class="form-label fw-semibold small">Titre général des documents</label>
                                <input type="text" class="form-control shadow-none bg-light" id="titre_piece" name="titre_piece" placeholder="Ex: Résultats d'examens">
                            </div>

                            <div class="mb-3">
                                <label for="type_examen" class="form-label fw-semibold small">Type d'examen</label>
                                <select class="form-select shadow-none bg-light" id="type_examen" name="type_examen">
                                    <option value="radio">Radiographie / Imagerie</option>
                                    <option value="biologie">Analyse Biologique</option>
                                    <option value="scan">Scanner / IRM</option>
                                    <option value="autre">Autre document</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="fichier_examen" class="form-label fw-semibold small">Fichiers (PDF, Images)</label>
                                <input type="file" class="form-control shadow-none bg-light" id="fichier_examen" name="fichiers_examens[]" multiple>
                            </div>
                        </div>

                        <!-- Bouton de Soumission -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary rounded-pill py-3 fw-bold shadow-sm">
                                <i class="bi bi-check-circle-fill me-2"></i> Enregistrer la Consultation
                            </button>
                        </div>

                    </div>
                </div>
            </form>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>