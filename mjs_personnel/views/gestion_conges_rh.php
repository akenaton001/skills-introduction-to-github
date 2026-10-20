<?php
// views/gestion_conges_rh.php
session_start();
require_once __DIR__ . '/../config/bd.php';

// 1. SÉCURITÉ
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['SuperAdmin', 'DAGE_RH'])) {
    header('Location: /mjs_personnel/views/dashboard.php');
    exit();
}

// 2. LOGIQUE ACTIONS ET NOTIFICATION PAR E-MAIL
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id_conge = $_GET['id'];
    $statut = ($_GET['action'] == 'valider') ? 'Validé' : 'Rejeté';

    // 1. Mettre à jour le statut en base
    $stmt = $pdo->prepare("UPDATE conges SET statut = ? WHERE id_conge = ?");
    $stmt->execute([$statut, $id_conge]);

    // 2. Récupérer l'e-mail professionnel de l'agent
    $stmt_email = $pdo->prepare("SELECT a.email_professionnel, a.prenom FROM agents a JOIN conges c ON a.id_agent = c.id_agent WHERE c.id_conge = ?");
    $stmt_email->execute([$id_conge]);
    $agent = $stmt_email->fetch();

    if ($agent && !empty($agent['email_professionnel'])) {
        $to = $agent['email_professionnel'];
        $subject = "SIGP - Mise à jour de votre demande de congé";
        $message = "Bonjour " . $agent['prenom'] . ",\n\n" .
                   "Votre demande de congé vient d'être traitée par le service RH.\n" .
                   "Nouveau statut : " . $statut . ".\n\n" .
                   "Cordialement,\nLe Service DAGE";
        
        // Envoi de l'e-mail
        @mail($to, $subject, $message, "From: noreply@mjs.sn");
    }
    
    header('Location: gestion_conges_rh.php?success=1');
    exit();
}

// Récupération des demandes en attente (Triées par date d'arrivée)
$stmt = $pdo->query("SELECT c.*, a.prenom, a.nom 
                     FROM conges c 
                     JOIN agents a ON c.id_agent = a.id_agent 
                     WHERE c.statut = 'En attente' 
                     ORDER BY c.date_debut ASC");
$demandes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MJS - Validation Congés</title>
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
        
        /* Style du conteneur de recherche */
        .search-wrapper {
            background: #ffffff;
            border-radius: 12px;
            padding: 5px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        .search-wrapper:focus-within {
            border-color: var(--sn-green);
            box-shadow: 0 0 0 4px rgba(0, 135, 81, 0.1);
        }
        .search-input {
            border: none !important;
            background: transparent !important;
            padding: 0.6rem 1rem !important;
            font-size: 0.95rem;
        }
        .search-input:focus {
            outline: none !important;
            box-shadow: none !important;
        }
        .search-icon {
            padding-left: 15px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3"><i class="fa-solid fa-list-check text-success me-2"></i>Traitement des demandes en attente</h2>
            <a href="dashboard.php" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Retour Dashboard</a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> L'action a été effectuée avec succès.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="text-uppercase small text-muted">
                            <tr>
                                <th class="ps-4">Agent</th>
                                <th>Type</th>
                                <th>Période</th>
                                <th class="text-center">Durée</th>
                                <th>Motif</th>
                                <th class="pe-4 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($demandes) > 0): foreach ($demandes as $d): ?>
                                <tr>
                                    <td class="ps-4 fw-bold"><?= htmlspecialchars($d['prenom'] . ' ' . $d['nom']) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($d['type_conge']) ?></span></td>
                                    <td><?= date('d/m/Y', strtotime($d['date_debut'])) ?> au <?= date('d/m/Y', strtotime($d['date_fin'])) ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary rounded-pill"><?= htmlspecialchars($d['nombre_jours']) ?> jour(s)</span>
                                    </td>
                                    <td class="text-muted small"><?= htmlspecialchars($d['motif']) ?></td>
                                    <td class="pe-4 text-end">
                                        <a href="?action=valider&id=<?= $d['id_conge'] ?>" 
                                           class="btn btn-sm btn-outline-success btn-action me-1" 
                                           title="Valider" 
                                           onclick="return confirm('Confirmez-vous la VALIDATION de cette demande ?')">
                                           <i class="fa-solid fa-check"></i>
                                        </a>
                                        <a href="?action=rejeter&id=<?= $d['id_conge'] ?>" 
                                           class="btn btn-sm btn-outline-danger btn-action" 
                                           title="Rejeter" 
                                           onclick="return confirm('ATTENTION : Confirmez-vous le REJET de cette demande ?')">
                                           <i class="fa-solid fa-xmark"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-clipboard-check fa-2x mb-3 d-block"></i>
                                        Aucune demande en attente actuellement.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>