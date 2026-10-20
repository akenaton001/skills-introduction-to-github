<?php 
$pageTitle = "Ajouter un examen ou une pièce jointe - Jàmm Santé";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <div class="d-flex align-items-center mb-4">
                <a href="index.php?action=patient_show&id=<?= $patient['id']; ?>" class="btn btn-outline-primary rounded-pill px-3 fw-semibold me-3">
                    <i class="bi bi-arrow-left me-1"></i> Retour
                </a>
                <h2 class="fw-bold text-dark mb-0">
                    <i class="bi bi-file-earmark-medical text-success me-2"></i> Ajouter un examen / Pièce jointe
                </h2>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <!-- Rappel du patient -->
                <div class="alert alert-light border mb-4 rounded-3">
                    <strong>Patient :</strong> <?= htmlspecialchars(($patient['prenom'] ?? '') . ' ' . ($patient['nom'] ?? '')); ?> 
                    <span class="text-muted ms-2">(Réf : <?= htmlspecialchars($patient['code_patient'] ?? ''); ?>)</span>
                </div>

                <form action="index.php?action=examens_store" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="patient_id" value="<?= $patient['id']; ?>">
                    
                    <!-- Si une consultation est rattachée, on peut stocker son ID s'il existe dans l'URL -->
                    <?php if (!empty($_GET['consultation_id'])): ?>
                        <input type="hidden" name="consultation_id" value="<?= htmlspecialchars($_GET['consultation_id']); ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Titre du document / de l'examen</label>
                        <input type="text" class="form-control rounded-3" name="titre" placeholder="Ex: Hémogramme complet, Radio pulmonaire..." required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Type d'examen</label>
                            <select class="form-select rounded-3" name="type_examen" required>
                                <option value="radio">Radio</option>
                                <option value="biologie">Biologie</option>
                                <option value="scanner">Scanner</option>
                                <option value="irm">IRM</option>
                                <option value="autre" selected>Autre</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Date de l'examen</label>
                            <input type="date" class="form-control rounded-3" name="date_examen" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Résultats / Paramètres biologiques (Optionnel)</label>
                        <textarea class="form-control rounded-3" name="resultats" rows="3" placeholder="Saisir les résultats ou valeurs mesurées..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Compte-rendu médical (Optionnel)</label>
                        <textarea class="form-control rounded-3" name="compte_rendu" rows="3" placeholder="Saisir le compte-rendu..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Fichier (PDF, Image, etc.)</label>
                        <input type="file" class="form-control rounded-3" name="fichier">
                        <small class="text-muted mt-1 d-block">Formats acceptés : PDF, JPG, PNG, DOC, DOCX</small>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold text-white">
                            <i class="bi bi-check-circle me-1"></i> Enregistrer l'examen
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>