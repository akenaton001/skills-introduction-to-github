<?php
// views/ajouter_agent.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- MODIFICATION : Inclusion de la BDD ---
// Le chemin utilise $_SERVER['DOCUMENT_ROOT'] pour pointer vers la racine du serveur
// et 'bd.php' avec un 'd' comme indiqué dans votre arborescence.
require_once $_SERVER['DOCUMENT_ROOT'] . '/mjs_personnel/config/bd.php';
// ------------------------------------------

// Sécurité : Seuls SuperAdmin et DAGE_RH ont accès à la saisie
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['SuperAdmin', 'DAGE_RH'])) {
    header('Location: /mjs_personnel/index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MJS - Ajouter un Agent</title>
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
        .navbar-custom {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 3px solid var(--sn-green);
            padding: 0.5rem 1.5rem;
        }
        
        .avatar-wrapper {
            position: relative;
            width: 110px;
            height: 110px;
            margin: 0 auto 1rem;
            cursor: pointer;
            overflow: hidden;
            border-radius: 50%;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            border: 3px solid #fff;
        }
        .avatar-image, .avatar-initials {
            width: 100%; height: 100%; object-fit: cover;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.5rem; font-weight: bold; color: white;
            background-color: var(--sn-green); transition: transform 0.3s ease;
        }
        .avatar-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6); display: flex; flex-direction: column;
            align-items: center; justify-content: center; color: white;
            opacity: 0; transition: opacity 0.3s ease; font-size: 0.75rem;
        }
        .avatar-wrapper:hover .avatar-overlay { opacity: 1; }
        
        .nav-pills .nav-link {
            color: #475569;
            font-weight: 500;
            padding: 0.6rem 1.2rem;
            border-radius: 30px;
            transition: all 0.2s ease;
        }
        .nav-pills .nav-link.active, .nav-pills .nav-link:hover {
            background-color: var(--sn-green) !important;
            color: white !important;
        }
        .info-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            font-weight: 600;
        }
        .info-value {
            font-size: 1rem;
            color: #0f172a;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="container mt-4 pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <div class="d-flex align-items-center mb-4 bg-white p-3 rounded-4 shadow-sm">
                    <a href="agents_liste.php" class="btn btn-light rounded-circle me-3 border" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-arrow-left text-muted"></i>
                    </a>
                    <div>
                        <h2 class="h4 text-dark fw-bold mb-0">Ajouter un nouvel agent</h2>
                        <p class="text-muted small mb-0">Enregistrement initial dans le fichier global du personnel.</p>
                    </div>
                </div>

                <?php if (isset($_SESSION['erreur'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= $_SESSION['erreur']; unset($_SESSION['erreur']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['succes'])): ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3" role="alert">
                        <i class="fa-solid fa-circle-check me-2"></i> <?= $_SESSION['succes']; unset($_SESSION['succes']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="../modules/agents/ajouter_action.php" method="POST" class="needs-validation" novalidate>
                    
                    <div class="card shadow-sm rounded-4 bg-white p-4 mb-4">
                        <div class="border-bottom pb-3 mb-4">
                            <h3 class="h5 text-dark fw-bold mb-0">
                                <i class="fa-solid fa-id-card text-success me-2"></i>1. Identité et État Civil
                            </h3>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label info-label">Matricule Solde <span class="text-danger">*</span></label>
                                <input type="text" name="matricule_solde" class="form-control text-uppercase" placeholder="Ex: 7755720/F" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label info-label">Numéro CNI</label>
                                <input type="text" name="cni" class="form-control" placeholder="13 chiffres" maxlength="13">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label info-label">Sexe <span class="text-danger">*</span></label>
                                <select name="sexe" class="form-select" required>
                                    <option value="M" selected>Masculin</option>
                                    <option value="F">Féminin</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label info-label">Prénom <span class="text-danger">*</span></label>
                                <input type="text" name="prenom" class="form-control" placeholder="Ex: Masse" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label info-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" name="nom" class="form-control text-uppercase" placeholder="Ex: Fall" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label info-label">Date de Naissance</label>
                                <input type="date" name="date_naissance" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label info-label">Lieu de Naissance</label>
                                <input type="text" name="lieu_naissance" class="form-control" placeholder="Ex: Dakar">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label info-label">Situation Matrimoniale</label>
                                <select name="situation_matrimoniale" class="form-select">
                                    <option value="Célibataire">Célibataire</option>
                                    <option value="Marié(e)" selected>Marié(e)</option>
                                    <option value="Divorcé(e)">Divorcé(e)</option>
                                    <option value="Veuf(ve)">Veuf(ve)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label info-label">Situation Militaire</label>
                                <select name="situation_militaire" class="form-select">
                                    <option value="Non effectué" selected>Non effectué</option>
                                    <option value="Libéré">Libéré (Service National)</option>
                                    <option value="Exempté">Exempté</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label info-label">Nombre d'enfants à charge</label>
                                <input type="number" name="nombre_enfants" class="form-control" min="0" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm rounded-4 bg-white p-4 mb-4">
                        <div class="border-bottom pb-3 mb-4">
                            <h3 class="h5 text-dark fw-bold mb-0">
                                <i class="fa-solid fa-address-book text-success me-2"></i>2. Coordonnées de Contact
                            </h3>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label info-label">Téléphone Portable <span class="text-danger">*</span></label>
                                <input type="tel" name="telephone" class="form-control" placeholder="Ex: 775572030" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label info-label">Adresse E-mail Professionnelle</label>
                                <input type="email" name="email_professionnel" class="form-control" placeholder="Ex: prenom.nom@sport.gouv.sn">
                            </div>
                            <div class="col-12">
                                <label class="form-label info-label">Adresse Résidentielle</label>
                                <input type="text" name="adresse" class="form-control" placeholder="Ex: Cité Douanes golf sud">
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm rounded-4 bg-white p-4 mb-4">
                        <div class="border-bottom pb-3 mb-4">
                            <h3 class="h5 text-dark fw-bold mb-0">
                                <i class="fa-solid fa-briefcase text-success me-2"></i>3. Cadre Administratif et Financier
                            </h3>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label info-label">Statut de l'Agent</label>
                                <select name="statut_agent" class="form-select">
                                    <option value="Fonctionnaire" selected>Fonctionnaire Titulaire</option>
                                    <option value="Contractuel">Contractuel de l'État</option>
                                    <option value="Stagiaire">Stagiaire / Prestataire</option>
                                    <option value="Détaché">En Détachement</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label info-label">Direction / Service Affectation <span class="text-danger">*</span></label>
                                <select name="direction_service" class="form-select" required>
                                    <option value="" disabled selected>Sélectionner...</option>
                                    <option value="DAGE">Direction de l’Administration Générale et de l’Équipement (DAGE)</option>
                                    <optgroup label="Direction Générale des Sports">
                                        <option value="DAPS">Direction des Activités Physiques et Sportives (DAPS)</option>
                                        <option value="DFDS">Direction de la Formation et du Développement Sportif (DFDS)</option>
                                        <option value="DIS">Direction des Infrastructures Sportives (DIS)</option>
                                        <option value="DLS">Direction des Loisirs Sportifs (DLS)</option>
                                        <option value="DSHN">Direction du Sport de Haut Niveau (DSHN)</option>
                                    </optgroup>
                                    <optgroup label="Direction Générale de la Jeunesse">
                                        <option value="DASEVA">Direction des Activités Socio-Éducatives et de la Vie Associative</option>
                                        <option value="DPSJ">Direction de la Protection Sociale des Jeunes</option>
                                        <option value="DEP">Direction de l’Éducation Populaire</option>
                                    </optgroup>
                                    <optgroup label="Services Déconcentrés">
                                        <option value="DRJS">Directions Régionales de la Jeunesse et des Sports</option>
                                    </optgroup>
                                    <option value="Cabinet">Cabinet du Ministre / Autre</option>
                                </select>
                            </div>
							<div class="col-md-4">
								<label class="form-label info-label">Affectation CDEPS (Départemental)</label>
								<select name="id_structure_actuelle" class="form-select">
									<option value="">Sélectionner un CDEPS...</option>
									<?php
									$regions = $pdo->query("SELECT id_structure, nom_structure FROM structures WHERE type_structure = 'Service régional'");
									foreach ($regions as $region) {
										echo "<optgroup label='".htmlspecialchars($region['nom_structure'])."'>";
										$stmt = $pdo->prepare("SELECT id_structure, nom_structure FROM structures WHERE type_structure = 'CDEPS' AND id_parent = ?");
										$stmt->execute([$region['id_structure']]);
										while ($cdeps = $stmt->fetch()) {
											echo "<option value='{$cdeps['id_structure']}'>".htmlspecialchars($cdeps['nom_structure'])."</option>";
										}
										echo "</optgroup>";
									}
									?>
								</select>
							</div>
                            <div class="col-md-4">
                                <label class="form-label info-label">Corps d'appartenance</label>
                                <input type="text" name="corps_agent" class="form-control" placeholder="Ex: Administrateur Civil">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label info-label">Hiérarchie</label>
                                <select name="hierarchie" class="form-select">
                                    <option value="">Sélectionner...</option>
                                    <option value="A1">A1</option>
                                    <option value="A2">A2</option>
                                    <option value="A3">A3</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                    <option value="E">E</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label info-label">Date d'Entrée au Ministère</label>
                                <input type="date" name="date_entree_ministere" class="form-control" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label info-label">Date de Prise de Service</label>
                                <input type="date" name="date_prise_service" class="form-control" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label info-label">Numéro de Compte Bancaire</label>
                                <input type="text" name="numero_compte_bancaire" class="form-control" placeholder="Ex: SN123 01001 12345678901 23">
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm rounded-4 bg-white p-4 mb-4" style="border-left: 4px solid var(--sn-yellow) !important;">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="creer_compte_acces" id="switchCompte" value="1">
                            <label class="form-check-label fw-bold text-dark" for="switchCompte">
                                <i class="fa-solid fa-key text-warning me-1"></i> Créer automatiquement un compte utilisateur pour cet agent
                            </label>
                        </div>
                        <p class="text-muted small ms-4 mb-3">L'identifiant sera son matricule. Le mot de passe initial sera le matricule nettoyé en minuscules.</p>
                        
                        <div id="roleSelectionZone" class="ms-4 w-50" style="display: none;">
                            <label class="form-label info-label">Rôle applicatif à attribuer :</label>
                            <select name="role_compte" class="form-select form-select-sm">
                                <option value="Agent" selected>Agent (Consultation uniquement)</option>
                                <option value="Secrétaire">Secrétaire (Saisie basique)</option>
                                <option value="DAGE_RH">DAGE / RH (Gestion complète des agents)</option>
                                <option value="SuperAdmin">SuperAdmin (Contrôle Système)</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-end shadow-sm p-3 bg-white rounded-4 mb-5 border d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-light border rounded-pill px-4 btn-sm fw-medium">Réinitialiser</button>
                        <button type="submit" class="btn btn-success px-5 btn-sm rounded-pill fw-bold shadow-sm" style="background-color: var(--sn-green); border:0;">
                            <i class="fa-solid fa-save me-1"></i> Enregistrer l'Agent
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const switchCompte = document.getElementById('switchCompte');
        const zoneRole = document.getElementById('roleSelectionZone');
        
        switchCompte.addEventListener('change', function() {
            zoneRole.style.display = this.checked ? 'block' : 'none';
        });

        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>