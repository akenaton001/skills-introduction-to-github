<?php 
// Supposons que $consultation contient les infos de la consultation et $patient les infos du patient
$pageTitle = "Modifier la Consultation - Jàmm Santé";
$page = 'consultations'; 
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
                            <li class="breadcrumb-item active" aria-current="page">Modifier la Consultation</li>
                        </ol>
                    </nav>
                    <h2 class="fw-bold text-dark mb-0">
                        <i class="bi bi-pencil-square text-primary me-2"></i> 
                        Modification Consultation - <?= htmlspecialchars($patient['prenom'] . ' ' . $patient['nom'], ENT_QUOTES, 'UTF-8'); ?>
                    </h2>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="index.php?action=patient_dossier&id=<?= $patient['id']; ?>" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold shadow-sm">
                        <i class="bi bi-arrow-left me-1"></i> Retour au dossier
                    </a>
                </div>
            </div>

            <!-- Formulaire de Modification -->
            <form action="index.php?action=consultations_update" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $consultation['id']; ?>">
                <input type="hidden" name="patient_id" value="<?= $patient['id']; ?>">

                <div class="row g-4">
                    <!-- Colonne Principale : Données Cliniques -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4">
                            <h5 class="fw-bold text-primary mb-3">
                                <i class="bi bi-journal-medical me-2"></i> Informations Cliniques & Patient
                            </h5>
                            <hr class="mb-4">

                            <div class="row g-4">
                                <!-- Code Patient (Non modifiable / Protégé) -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted">Code Patient (Fixe)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-shield-lock"></i></span>
                                        <input type="text" class="form-control border-start-0 ps-0 shadow-none bg-light text-secondary fw-bold" value="<?= htmlspecialchars($patient['code'] ?? $patient['numero_dossier'] ?? 'PAT-' . $patient['id'], ENT_QUOTES, 'UTF-8'); ?>" readonly disabled>
                                    </div>
                                    <div class="form-text small text-muted">Le code unique du patient ne peut pas être modifié.</div>
                                </div>

                                <!-- Date et Heure de consultation (Modifiable) -->
                                <div class="col-md-6">
                                    <label for="date_consultation" class="form-label fw-semibold">Date et Heure de la Consultation <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-calendar-event"></i></span>
                                        <input type="datetime-local" class="form-control border-start-0 ps-0 shadow-none" id="date_consultation" name="date_consultation" value="<?= date('Y-m-d\TH:i', strtotime($consultation['date_consultation'])); ?>" required>
                                    </div>
                                </div>

                                <!-- État d'avancement / Évolution (Modifiable) -->
                                <div class="col-md-6">
                                    <label for="evolution" class="form-label fw-semibold">État d'avancement / Évolution <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-activity"></i></span>
                                        <select class="form-select border-start-0 ps-0 shadow-none" id="evolution" name="evolution" required>
                                            <?php 
                                            $evols = ["Première consultation", "Stable / Stationnaire", "Amélioration nette", "Aggravation", "Guéri"];
                                            foreach($evols as $ev):
                                                $selected = ($consultation['evolution'] == $ev) ? 'selected' : '';
                                            ?>
                                                <option value="<?= $ev; ?>" <?= $selected; ?>><?= $ev; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Motif (Modifiable) -->
                                <div class="col-md-6">
                                    <label for="motif" class="form-label fw-semibold">Motif de la consultation <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-chat-left-text"></i></span>
                                        <input type="text" class="form-control border-start-0 ps-0 shadow-none" id="motif" name="motif" value="<?= htmlspecialchars($consultation['motif'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </div>
                                </div>

                                <!-- Symptômes (Modifiable) -->
                                <div class="col-12">
                                    <label for="symptomes" class="form-label fw-semibold">Symptômes & Signes cliniques</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted align-items-start pt-2"><i class="bi bi-file-medical"></i></span>
                                        <textarea class="form-control border-start-0 ps-0 shadow-none" id="symptomes" name="symptomes" rows="3"><?= htmlspecialchars($consultation['symptomes'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                    </div>
                                </div>

                                <!-- Diagnostic (Modifiable) -->
                                <div class="col-12">
                                    <label for="diagnostic" class="form-label fw-semibold">Diagnostic <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted align-items-start pt-2"><i class="bi bi-clipboard2-pulse"></i></span>
                                        <textarea class="form-control border-start-0 ps-0 shadow-none" id="diagnostic" name="diagnostic" rows="3" required><?= htmlspecialchars($consultation['diagnostic'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                                    </div>
                                </div>

                                <!-- Prescription / Ordonnance (Modifiable) -->
                                <div class="col-12">
                                    <label for="prescription" class="form-label fw-semibold">Ordonnance / Traitement prescrit</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted align-items-start pt-2"><i class="bi bi-capsule"></i></span>
                                        <textarea class="form-control border-start-0 ps-0 shadow-none" id="prescription" name="prescription" rows="3"><?= htmlspecialchars($consultation['prescription'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
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
                                <textarea class="form-control shadow-none bg-light" id="examens_prescrits" name="examens_prescrits" rows="3"><?= htmlspecialchars($consultation['examens_prescrits'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="date_prochain_rv" class="form-label fw-semibold small">Prochain rendez-vous (Échéance)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-calendar-plus"></i></span>
                                    <select class="form-select border-start-0 shadow-none bg-light" id="date_prochain_rv" name="date_prochain_rv">
                                        <option value="">-- Sélectionner l'échéance --</option>
                                        <?php 
                                        for ($i = 1; $i <= 12; $i++): 
                                            $valOption = "Dans $i mois" . ($i > 1 ? 's' : '');
                                            $selectedRV = (isset($consultation['date_prochain_rv']) && $consultation['date_prochain_rv'] == $valOption) ? 'selected' : '';
                                        ?>
                                            <option value="<?= $valOption; ?>" <?= $selectedRV; ?>><?= $valOption; ?></option>
                                        <?php endfor; ?>
                                        <option value="Plus de 12 mois" <?= (isset($consultation['date_prochain_rv']) && $consultation['date_prochain_rv'] == 'Plus de 12 mois') ? 'selected' : ''; ?>>Plus de 12 mois (Libre)</option>
                                        <option value="Selon besoin" <?= (isset($consultation['date_prochain_rv']) && $consultation['date_prochain_rv'] == 'Selon besoin') ? 'selected' : ''; ?>>Selon besoin / Appel du patient</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Rattachement des résultats (Modifiable / Ajout multiple) -->
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                            <h5 class="fw-bold text-primary mb-3">
                                <i class="bi bi-paperclip me-2"></i> Résultats / Documents
                            </h5>
                            <hr class="mb-3">
                            
                            <div class="mb-3">
                                <label for="titre_piece" class="form-label fw-semibold small">Titre général des documents</label>
                                <input type="text" class="form-control shadow-none bg-light" id="titre_piece" name="titre_piece" value="<?= htmlspecialchars($consultation['titre_piece'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Ex: Résultats d'examens">
                            </div>

                            <div class="mb-3">
                                <label for="type_examen" class="form-label fw-semibold small">Type d'examen</label>
                                <select class="form-select shadow-none bg-light" id="type_examen" name="type_examen">
                                    <?php 
                                    $types = ['radio' => 'Radiographie / Imagerie', 'biologie' => 'Analyse Biologique', 'scan' => 'Scanner / IRM', 'autre' => 'Autre document'];
                                    foreach($types as $key => $label):
                                        $selType = (isset($consultation['type_examen']) && $consultation['type_examen'] == $key) ? 'selected' : '';
                                    ?>
                                        <option value="<?= $key; ?>" <?= $selType; ?>><?= $label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="fichier_examen" class="form-label fw-semibold small">Ajouter de nouveaux fichiers (Multiples)</label>
                                <input type="file" class="form-control shadow-none bg-light" id="fichier_examen" name="fichiers_examens[]" multiple>
                                <div class="form-text small text-muted mt-1">Les fichiers sélectionnés s'ajouteront aux documents existants.</div>
                            </div>
                        </div>

                        <!-- Bouton de Soumission -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary rounded-pill py-3 fw-bold shadow-sm">
                                <i class="bi bi-check-circle-fill me-2"></i> Mettre à jour la Consultation
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