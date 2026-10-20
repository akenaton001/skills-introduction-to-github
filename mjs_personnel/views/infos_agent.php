<?php
// views/infos_agent.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/bd.php';

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: agents_liste.php'); exit(); }

// Chargement complet de toutes les données de l'agent
$stmt = $pdo->prepare("
    SELECT a.*, s.nom_structure AS structure_nom 
    FROM agents a 
    LEFT JOIN structures s ON a.id_structure_actuelle = s.id_structure 
    WHERE a.id_agent = ?
");
$stmt->execute([$id]);
$agent = $stmt->fetch();

if (!$agent) { die("Agent introuvable."); }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MJS - Dossier Agent : <?= htmlspecialchars($agent['prenom'] . ' ' . $agent['nom']) ?></title>
    <link href="https://fonts.cdnfonts.com/css/gilroy-bold" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Application globale de la police Gilroy */
        body {
            font-family: 'Gilroy', sans-serif;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            color: #1e293b;
            min-height: 100vh;
        }

        :root {
            --sn-green: #008751;
            --sn-yellow: #FCD116;
            --sn-red: #E31B23;
        }

        .card { 
            border: none; 
            border-radius: 1.5rem; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); 
            background-color: #fff;
        }
        .info-label { 
            font-size: 0.75rem; 
            text-transform: uppercase; 
            /* Changé de #94a3b8 à #64748b pour un meilleur contraste */
            color: #64748b; 
            font-weight: 700; 
            letter-spacing: 0.05em; 
        }
        .info-value { 
            font-size: 0.95rem; 
            /* Changé de #0f172a à #1e293b (plus sombre/noir) pour une lecture claire */
            color: #1e293b; 
            font-weight: 600; 
            margin-bottom: 1.2rem; 
        }
        .section-title { 
            font-size: 1.1rem; 
            color: var(--sn-green); 
            font-weight: 700; 
            margin-bottom: 1.5rem; 
            border-bottom: 2px solid #e2e8f0; 
            padding-bottom: 0.5rem; 
        }
        .btn-retour {
            background: #fff;
            color: #1e293b;
            font-weight: 600;
            border: none;
            transition: all 0.2s ease;
        }
        .btn-retour:hover {
            background: var(--sn-green);
            color: #fff;
        }
    </style>
</head>
<body>

    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="container mt-4 pb-5">
        <div class="mb-4">
            <a href="agents_liste.php" class="btn btn-retour shadow-sm rounded-pill px-4">
                <i class="fa-solid fa-arrow-left me-2"></i> Retour à la liste
            </a>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3">
                <div class="card p-4 text-center">
                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem; font-weight: 700;">
                        <?= strtoupper(substr($agent['prenom'], 0, 1) . substr($agent['nom'], 0, 1)) ?>
                    </div>
                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($agent['prenom'] . ' ' . $agent['nom']) ?></h5>
                    <p class="text-muted small mb-3">Matricule : <span class="fw-bold text-dark"><?= htmlspecialchars($agent['matricule_solde'] ?? 'N/A') ?></span></p>
                    <div class="d-grid">
                        <a href="agent_modifier.php?id=<?= $id ?>" class="btn btn-outline-success rounded-pill fw-bold">
                            <i class="fa-solid fa-pen me-2"></i>Modifier le dossier
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="card p-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="section-title"><i class="fa-solid fa-user me-2"></i>État Civil</div>
                            <div class="info-label">Date de naissance</div>
                            <div class="info-value"><?= !empty($agent['date_naissance']) ? date('d/m/Y', strtotime($agent['date_naissance'])) : 'Non renseignée' ?></div>
                            <div class="info-label">Lieu de naissance</div>
                            <div class="info-value"><?= htmlspecialchars($agent['lieu_naissance'] ?? 'Non renseigné') ?></div>
                            <div class="info-label">Sexe</div>
                            <div class="info-value"><?= $agent['sexe'] === 'M' ? 'Masculin' : 'Féminin' ?></div>
                            <div class="info-label">Situation Matrimoniale</div>
                            <div class="info-value"><?= htmlspecialchars($agent['situation_matrimoniale'] ?? 'Non renseignée') ?></div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="section-title"><i class="fa-solid fa-building me-2"></i>Administratif</div>
                            <div class="info-label">Statut de l'agent</div>
                            <div class="info-value"><?= htmlspecialchars($agent['statut_agent'] ?? 'Non spécifié') ?></div>
                            <div class="info-label">Direction / Service</div>
                            <div class="info-value"><?= htmlspecialchars($agent['direction_service'] ?? 'Non spécifiée') ?></div>
                            <div class="info-label">Structure Actuelle</div>
                            <div class="info-value"><?= htmlspecialchars($agent['structure_nom'] ?? 'Aucune structure affectée') ?></div>
                            <div class="info-label">Corps de l'agent</div>
                            <div class="info-value"><?= htmlspecialchars($agent['corps_agent'] ?? 'Non renseigné') ?></div>
                            <div class="info-label">Hiérarchie</div>
                            <div class="info-value"><?= htmlspecialchars($agent['hierarchie'] ?? 'Non spécifiée') ?></div>
                        </div>

                        <div class="col-md-4">
                            <div class="section-title"><i class="fa-solid fa-briefcase me-2"></i>Carrière & Famille</div>
                            <div class="info-label">Date Entrée Ministère</div>
                            <div class="info-value"><?= !empty($agent['date_entree_ministere']) ? date('d/m/Y', strtotime($agent['date_entree_ministere'])) : 'Non renseignée' ?></div>
                            <div class="info-label">Date Prise de Service</div>
                            <div class="info-value"><?= !empty($agent['date_prise_service']) ? date('d/m/Y', strtotime($agent['date_prise_service'])) : 'Non renseignée' ?></div>
                            <div class="info-label">Situation Militaire</div>
                            <div class="info-value"><?= htmlspecialchars($agent['situation_militaire'] ?? 'N/A') ?></div>
                            <div class="info-label">Nombre d'enfants</div>
                            <div class="info-value"><?= htmlspecialchars($agent['nombre_enfants'] ?? '0') ?></div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="section-title"><i class="fa-solid fa-address-book me-2"></i>Contacts & Adresse</div>
                            <div class="info-label">Numéro de Téléphone</div>
                            <div class="info-value"><?= htmlspecialchars($agent['telephone'] ?? 'Non renseigné') ?></div>
                            <div class="info-label">Adresse Résidentielle</div>
                            <div class="info-value"><?= htmlspecialchars($agent['adresse'] ?? 'Non renseignée') ?></div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="section-title"><i class="fa-solid fa-credit-card me-2"></i>Identifiants & Banque</div>
                            <div class="info-label">Numéro CNI</div>
                            <div class="info-value"><?= htmlspecialchars($agent['cni'] ?? 'Non renseigné') ?></div>
                            <div class="info-label">Compte Bancaire (RIB)</div>
                            <div class="info-value"><?= htmlspecialchars($agent['numero_compte_bancaire'] ?? 'Non renseigné') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>