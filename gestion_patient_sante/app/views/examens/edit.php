<?php 
$pageTitle = "Modifier la pièce jointe - Jàmm Santé";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <div class="d-flex align-items-center mb-4">
                <a href="index.php?action=patient_show&id=<?= $examen['patient_id']; ?>" class="btn btn-outline-primary rounded-pill px-3 fw-semibold me-3">
                    <i class="bi bi-arrow-left me-1"></i> Retour
                </a>
                <h2 class="fw-bold text-dark mb-0">
                    <i class="bi bi-pencil text-info me-2"></i> Modifier la pièce jointe
                </h2>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <form action="index.php?action=examens_update" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $examen['id']; ?>">
                    <input type="hidden" name="patient_id" value="<?= $examen['patient_id']; ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Titre du document / de l'examen</label>
                        <input type="text" class="form-control rounded-3" name="titre" value="<?= htmlspecialchars($examen['titre'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Type d'examen</label>
                            <select class="form-select rounded-3" name="type_examen" required>
                                <?php foreach (['radio', 'biologie', 'scanner', 'irm', 'autre'] as $type): ?>
                                    <option value="<?= $type; ?>" <?= (($examen['type_examen'] ?? '') === $type) ? 'selected' : ''; ?>><?= ucfirst($type); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Date de l'examen</label>
                            <input type="date" class="form-control rounded-3" name="date_examen" value="<?= !empty($examen['date_examen']) ? date('Y-m-d', strtotime($examen['date_examen'])) : date('Y-m-d'); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Résultats / Paramètres biologiques</label>
                        <textarea class="form-control rounded-3" name="resultats" rows="4" placeholder="Saisir les résultats..."><?= htmlspecialchars($examen['resultats'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Compte-rendu médical</label>
                        <textarea class="form-control rounded-3" name="compte_rendu" rows="4" placeholder="Saisir le compte-rendu..."><?= htmlspecialchars($examen['compte_rendu'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Remplacer le fichier (optionnel)</label>
                        <input type="file" class="form-control rounded-3" name="fichier">
                        <?php if (!empty($examen['fichier_path'])): ?>
                            <small class="text-muted mt-1 d-block">Fichier actuel : <a href="public/<?= htmlspecialchars($examen['fichier_path']); ?>" target="_blank">Voir le document</a></small>
                        <?php endif; ?>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-info rounded-pill px-4 fw-semibold text-white">
                            <i class="bi bi-check-circle me-1"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>