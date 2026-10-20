<?php 
$pageTitle = "Nouvelle Admission - Jàmm Santé";
$page = 'hospitalisations';

$patient = $patient ?? null;
$patients = $patients ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle; ?></title>
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

    <!-- Inclusion de la Sidebar -->
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>

    <div class="main-content">
        <!-- Inclusion de la Topbar dynamique -->
        <?php include __DIR__ . '/../layouts/topbar.php'; ?>

        <div class="container-fluid px-0" style="max-width: 850px;">
            
            <!-- Fil d'Ariane et Bouton Retour -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index.php?action=hospitalisations" class="text-decoration-none">Hospitalisations</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Nouvelle Admission</li>
                    </ol>
                </nav>
                <a href="index.php?action=hospitalisations" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour
                </a>
            </div>

            <!-- Carte principale avec effet Wow -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                <div class="bg-primary bg-opacity-10 p-4 p-md-5 border-bottom border-light">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary text-white p-3 rounded-4 shadow-sm">
                            <i class="fas fa-procedures fa-2x"></i>
                        </div>
                        <div>
                            <h2 class="fw-bold text-dark mb-1">Admission d'un Patient</h2>
                            <p class="text-muted mb-0" style="font-size: 0.95rem;">Renseignez l'affectation de lit, de chambre et le motif clinique.</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    <form action="index.php?action=hospitalisations_store" method="POST">
                        
                        <!-- Sélection dynamique du patient -->
                        <div class="mb-4 position-relative">
                            <label for="patient_search" class="form-label fw-semibold text-secondary">Rechercher le Patient (Nom, Prénom ou Code) <span class="text-danger">*</span></label>
                            <?php if ($patient): ?>
                                <input type="hidden" name="patient_id" value="<?= $patient['id'] ?>">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-primary ps-3"><i class="fas fa-user-check"></i></span>
                                    <input type="text" class="form-control bg-light fw-bold text-primary border-start-0 ps-0 shadow-none" value="<?= htmlspecialchars(($patient['code_patient'] ?? 'SANTE-000') . ' - ' . $patient['prenom'] . ' ' . $patient['nom']) ?>" readonly>
                                </div>
                            <?php else: ?>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fas fa-search"></i></span>
                                    <input type="text" id="patient_search" class="form-control bg-light border-start-0 ps-0 shadow-none" placeholder="Tapez un nom, prénom ou code patient (ex: 23092026/001)..." autocomplete="off">
                                </div>
                                <input type="hidden" name="patient_id" id="patient_id" required>
                                
                                <!-- Liste des résultats de recherche -->
                                <div id="patient-search-results" class="card shadow border-0 rounded-4 mt-2 bg-white" style="display: none; position: absolute; z-index: 1000; width: 100%; max-height: 220px; overflow-y: auto;">
                                    <div class="list-group list-group-flush rounded-4">
                                        <?php foreach ($patients as $p): ?>
                                            <a href="#" class="list-group-item list-group-item-action patient-item py-3 px-3 border-light" 
                                               data-id="<?= $p['id'] ?>" 
                                               data-text="<?= htmlspecialchars(($p['code_patient'] ?? '---') . ' - ' . $p['prenom'] . ' ' . $p['nom'] . ' (' . $p['telephone'] . ')') ?>">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="fw-bold text-dark"><?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?></span>
                                                        <span class="badge bg-primary bg-opacity-10 text-primary ms-2"><?= htmlspecialchars($p['code_patient'] ?? 'Sans code') ?></span>
                                                    </div>
                                                    <small class="text-muted"><i class="fas fa-phone-alt me-1"></i><?= htmlspecialchars($p['telephone']) ?></small>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Chambre et Lit -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="numero_chambre" class="form-label fw-semibold text-secondary">Numéro de Chambre <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-door-open"></i></span>
                                    <input type="text" class="form-control border-start-0 ps-0 shadow-none" id="numero_chambre" name="numero_chambre" placeholder="Ex: Chambre 102" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="numero_lit" class="form-label fw-semibold text-secondary">Numéro de Lit <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-bed"></i></span>
                                    <input type="text" class="form-control border-start-0 ps-0 shadow-none" id="numero_lit" name="numero_lit" placeholder="Ex: Lit A" required>
                                </div>
                            </div>
                        </div>

                        <!-- Date et Heure d'entrée -->
                        <div class="mb-4">
                            <label for="date_entree" class="form-label fw-semibold text-secondary">Date et Heure d'entrée <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-calendar-alt"></i></span>
                                <input type="datetime-local" class="form-control border-start-0 ps-0 shadow-none" id="date_entree" name="date_entree" value="<?= date('Y-m-d\TH:i') ?>" required>
                            </div>
                        </div>

                        <!-- Motif de l'hospitalisation -->
                        <div class="mb-4">
                            <label for="motif_hospitalisation" class="form-label fw-semibold text-secondary">Motif de l'hospitalisation <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted align-items-start pt-3"><i class="fas fa-notes-medical"></i></span>
                                <textarea class="form-control border-start-0 ps-0 shadow-none" id="motif_hospitalisation" name="motif_hospitalisation" rows="4" placeholder="Précisez les raisons cliniques de l'admission..." required></textarea>
                            </div>
                        </div>

                        <!-- Bouton de validation -->
                        <div class="d-flex justify-content-end gap-3 pt-4 border-top">
                            <a href="index.php?action=hospitalisations" class="btn btn-light px-4 py-2 rounded-pill fw-semibold text-secondary shadow-sm">Annuler</a>
                            <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill fw-bold shadow-sm">
                                <i class="fas fa-check-circle me-2"></i> Valider l'Admission
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Script pour la recherche dynamique interactive -->
    <script>
        const searchInput = document.getElementById('patient_search');
        const resultsBox = document.getElementById('patient-search-results');
        const hiddenPatientId = document.getElementById('patient_id');

        if (searchInput && resultsBox) {
            searchInput.addEventListener('focus', () => {
                resultsBox.style.display = 'block';
            });

            searchInput.addEventListener('input', function() {
                const filter = this.value.toLowerCase();
                const items = resultsBox.querySelectorAll('.patient-item');
                resultsBox.style.display = 'block';

                items.forEach(item => {
                    const text = item.getAttribute('data-text').toLowerCase();
                    if (text.includes(filter)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });

            document.querySelectorAll('.patient-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    searchInput.value = this.getAttribute('data-text');
                    hiddenPatientId.value = this.getAttribute('data-id');
                    resultsBox.style.display = 'none';
                });
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                    resultsBox.style.display = 'none';
                }
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>