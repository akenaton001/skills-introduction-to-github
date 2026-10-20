<?php
session_start();
require_once __DIR__ . '/../config/bd.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /mjs_personnel/index.php');
    exit();
}

if (!isset($_SESSION['id_agent'])) {
    $stmt = $pdo->prepare("SELECT id_agent FROM utilisateurs WHERE id_utilisateur = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    $_SESSION['id_agent'] = $user['id_agent'] ?? null;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MJS - Demande de Congé</title>
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

    <div class="container mt-4">
        <h2 class="h3 mb-4"><i class="fa-solid fa-plane-departure text-success me-2"></i>Mes Congés</h2>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card p-4">
                    <h5 class="fw-bold mb-3">Nouvelle demande</h5>
                    <?php if(isset($_SESSION['message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show small"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
                    <?php endif; ?>
                    
                    <form action="../modules/conge/traiter_demande.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Type de congé</label>
                            <select name="type_conge" class="form-select" required>
                                <option value="Annuel">Congé Annuel</option>
                                <option value="Maladie">Congé Maladie</option>
                                <option value="Exceptionnel">Congé Exceptionnel</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Début</label>
                                <input type="date" name="date_debut" class="form-control" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Fin</label>
                                <input type="date" name="date_fin" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Motif</label>
                            <textarea name="motif" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100 rounded-pill"><i class="fa-solid fa-paper-plane me-2"></i>Envoyer la demande</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Type</th>
                                    <th>Période</th>
                                    <th>Jours</th>
                                    <th class="pe-4">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $pdo->prepare("SELECT * FROM conges WHERE id_agent = ? ORDER BY created_at DESC");
                                $stmt->execute([$_SESSION['id_agent']]);
                                $demandes = $stmt->fetchAll();
                                
                                if(count($demandes) > 0):
                                    foreach ($demandes as $d): ?>
                                        <tr>
                                            <td class="ps-4 fw-medium"><?= htmlspecialchars($d['type_conge']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($d['date_debut'])) ?> - <?= date('d/m/Y', strtotime($d['date_fin'])) ?></td>
                                            <td><?= htmlspecialchars($d['nombre_jours']) ?></td>
                                            <td class="pe-4">
                                                <span class="badge rounded-pill bg-<?= $d['statut'] == 'Validé' ? 'success' : ($d['statut'] == 'Rejeté' ? 'danger' : 'warning') ?>-subtle text-<?= $d['statut'] == 'Validé' ? 'success' : ($d['statut'] == 'Rejeté' ? 'danger' : 'warning') ?>">
                                                    <?= htmlspecialchars($d['statut']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; 
                                else: ?>
                                    <tr><td colspan="4" class="text-center py-4 text-muted">Aucune demande enregistrée.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>