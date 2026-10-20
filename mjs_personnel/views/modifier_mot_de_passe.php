<?php
// views/modifier_mot_de_passe.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sécurité : L'utilisateur doit être connecté pour accéder à cette page
if (!isset($_SESSION['user_id'])) {
    header('Location: /mjs_personnel/index.php');
    exit();
}

// On détecte si c'est un changement obligatoire (première connexion)
$est_premiere_connexion = isset($_GET['premiere_connexion']) && $_GET['premiere_connexion'] == 1;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MJS - Modifier le mot de passe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sn-green: #008751;
            --sn-yellow: #FCD116;
            --sn-red: #E31B23;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            color: #1e293b;
            min-height: 100vh;
        }
        .info-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="container mt-5 pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                
                <div class="d-flex align-items-center mb-4 bg-white p-3 rounded-4 shadow-sm">
                    <?php if (!$est_premiere_connexion): ?>
                        <a href="profil.php" class="btn btn-light rounded-circle me-3 border" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-arrow-left text-muted"></i>
                        </a>
                    <?php else: ?>
                        <div class="btn btn-light rounded-circle me-3 border bg-warning-subtle border-warning" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-lock text-warning"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h2 class="h4 text-dark fw-bold mb-0">Sécuriser votre compte</h2>
                        <p class="text-muted small mb-0">Modification du mot de passe applicatif.</p>
                    </div>
                </div>

                <?php if ($est_premiere_connexion): ?>
                    <div class="alert alert-warning shadow-sm rounded-4 border-0 p-3 mb-4" role="alert">
                        <div class="d-flex">
                            <i class="fa-solid fa-shield-halved fs-4 me-3 text-warning"></i>
                            <div>
                                <h6 class="alert-heading fw-bold mb-1">Changement de mot de passe obligatoire</h6>
                                <p class="small mb-0 text-dark">Pour des raisons de sécurité, vous devez remplacer le mot de passe initial généré par le système avant de continuer.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div id="errorAlert" class="alert alert-danger d-none rounded-3 shadow-sm" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> <span id="errorMessage"></span>
                </div>

                <form action="../modules/agents/modifier_mon_mdp_agent.php" method="POST" class="needs-validation" novalidate>
                    
                    <input type="hidden" name="premiere_connexion" value="<?= $est_premiere_connexion ? '1' : '0' ?>">

                    <div class="card shadow-sm rounded-4 bg-white p-4 mb-4">
                        <div class="row g-3">
                            
                            <?php if (!$est_premiere_connexion): ?>
                                <div class="col-12">
                                    <label class="form-label info-label">Mot de passe actuel <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-key"></i></span>
                                        <input type="password" name="ancien_mdp" id="ancien_mdp" class="form-control border-start-0" placeholder="Entrez votre mot de passe actuel">
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="col-12">
                                <label class="form-label info-label">Nouveau mot de passe <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                                    <input type="password" name="nouveau_mdp" id="nouveau_mdp" class="form-control border-start-0" placeholder="Minimum 6 caractères" minlength="6" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label info-label">Confirmer le nouveau mot de passe <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-check-double"></i></span>
                                    <input type="password" name="confirmer_mdp" id="confirmer_mdp" class="form-control border-start-0" placeholder="Répétez le mot de passe" required>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="shadow-sm p-3 bg-white rounded-4 border">
                        <button type="submit" class="btn btn-success w-100 py-2 rounded-pill fw-bold shadow-sm" style="background-color: var(--sn-green); border:0;">
                            <i class="fa-solid fa-shield-check me-2"></i> Mettre à jour le mot de passe
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.needs-validation');
        const nouveauMdp = document.getElementById('nouveau_mdp');
        const confirmerMdp = document.getElementById('confirmer_mdp');
        const errorAlert = document.getElementById('errorAlert');
        const errorMessage = document.getElementById('errorMessage');

        form.addEventListener('submit', function(event) {
            event.preventDefault(); 
            errorAlert.classList.add('d-none'); 

            // Correspondance des mots de passe
            if (nouveauMdp.value !== confirmerMdp.value) {
                confirmerMdp.setCustomValidity("Invalide");
                errorMessage.textContent = "Les nouveaux mots de passe ne correspondent pas.";
                errorAlert.classList.remove('d-none');
                form.classList.add('was-validated');
                return;
            } else {
                confirmerMdp.setCustomValidity("");
            }

            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirection après succès
                    window.location.href = '/mjs_personnel/views/dashboard.php';
                } else {
                    errorMessage.textContent = data.message;
                    errorAlert.classList.remove('d-none');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                errorMessage.textContent = 'Une erreur de communication avec le serveur est survenue.';
                errorAlert.classList.remove('d-none');
            });
        }, false);

        confirmerMdp.addEventListener('input', function() {
            if (nouveauMdp.value === confirmerMdp.value) {
                confirmerMdp.setCustomValidity("");
            }
        });
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>