<?php
// views/agents_liste.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/bd.php';

// Contrôle d'accès : Réservé aux profils RH, DAGE ou SuperAdmin
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['SuperAdmin', 'DAGE_RH'])) {
    header('Location: /mjs_personnel/index.php');
    exit();
}

// Récupération de TOUT le personnel
try {
    $stmt = $pdo->query("
        SELECT a.id_agent, a.matricule_solde, a.prenom, a.nom, a.email_professionnel, a.telephone, a.id_structure_actuelle, a.numero_compte_bancaire,
               u.id_utilisateur, u.role, u.actif
        FROM agents a
        LEFT JOIN utilisateurs u ON a.id_agent = u.id_agent
        ORDER BY a.nom ASC, a.prenom ASC
    ");
    $personnel = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erreur de récupération du personnel : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MJS - Registre du Personnel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
       @import url('https://fonts.googleapis.com/css2?family=Gilroy:wght@400;500;600&display=swap');
        
        :root { --sn-green: #198754; --sn-yellow: #FCD116; --sn-red: #E31B23; }
        body { font-family: 'Gilroy', sans-serif; background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); min-height: 100vh; display: flex; flex-direction: column; margin: 0; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); }
        .container { flex: 1; }
    </style>
</head>
<body>

    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="container mt-4">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h2 class="h3"><i class="fa-solid fa-address-book text-success me-2"></i> Registre Global du Personnel</h2>
            </div>
            <div class="col text-end">
                <a href="agent_ajouter.php" class="btn btn-success shadow-sm"><i class="fa-solid fa-user-plus me-1"></i> Enregistrer un agent</a>
            </div>
        </div>

        <div id="alert-container"></div>
        
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="search-wrapper d-flex align-items-center shadow-sm">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" id="searchInput" class="form-control search-input" placeholder="Rechercher un agent (nom, matricule...)" aria-label="Rechercher">
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="personnelTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Agent</th>
                                <th>Matricule</th>
                                <th>Contact & Paiement</th>
                                <th class="text-center">Statut</th>
                                <th class="pe-4 text-end">Actions RH & Sécurité</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($personnel as $agent): ?>
                                <tr>
                                    <td class="ps-4 fw-bold"><?= htmlspecialchars($agent['prenom'] . ' ' . $agent['nom']) ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($agent['matricule_solde'] ?? 'N/A') ?></span></td>
                                    <td>
                                        <small class="d-block"><i class="fa-solid fa-envelope text-muted me-1"></i> <?= htmlspecialchars($agent['email_professionnel'] ?? '-') ?></small>
                                        <small class="d-block"><i class="fa-solid fa-credit-card text-success me-1"></i> <?= htmlspecialchars($agent['numero_compte_bancaire'] ?? 'N/A') ?></small>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($agent['id_utilisateur']): ?>
                                            <span class="badge bg-primary"><i class="fa-solid fa-laptop-code me-1"></i> Actif (<?= $agent['role'] ?>)</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border"><i class="fa-solid fa-user-slash me-1"></i> Sans accès</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="infos_agent.php?id=<?= $agent['id_agent'] ?>" class="btn btn-outline-info" title="Voir dossier complet"><i class="fa-solid fa-eye"></i></a>
                                            <a href="agent_modifier.php?id=<?= $agent['id_agent'] ?>" class="btn btn-outline-secondary" title="Modifier RH"><i class="fa-solid fa-pen"></i></a>
                                            
                                            <?php if ($agent['id_utilisateur']): ?>
                                                <a href="utilisateur_profil.php?id=<?= $agent['id_utilisateur'] ?>" class="btn btn-outline-warning" title="Réinitialiser MDP"><i class="fa-solid fa-key"></i></a>
                                                <button class="btn btn-outline-danger" title="Retirer l'accès" onclick="supprimerDonnee(<?= $agent['id_utilisateur'] ?>, 'acces')"><i class="fa-solid fa-person-circle-minus"></i></button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-outline-primary btn-ouvrir-modal-compte" data-id="<?= $agent['id_agent'] ?>" data-nom="<?= htmlspecialchars($agent['prenom'] . ' ' . $agent['nom']) ?>" title="Créer compte">
                                                    <i class="fa-solid fa-user-plus"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <button class="btn btn-danger" title="Supprimer agent" onclick="supprimerDonnee(<?= $agent['id_agent'] ?>, 'agent')"><i class="fa-solid fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
		
		<div class="mt-4 text-start mb-5">
            <a href="dashboard.php" class="btn btn-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Retour au Tableau de bord</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
	
   <div class="modal fade" id="modalCreationCompte" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="formCreationCompte" class="modal-content" style="border: none; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden;">
      
      <div class="modal-header border-0" style="background: linear-gradient(135deg, #198754, #20c997); padding: 25px;">
        <h5 class="modal-title text-white fw-bold">
          <i class="fa-solid fa-user-shield me-2"></i> Nouvel Accès Système
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4">
        <input type="hidden" name="id_agent" id="inputAgentId">
        <p class="text-muted mb-3">Configuration des droits d'accès pour :</p>
        <div class="bg-light p-3 rounded-3 mb-4 d-flex align-items-center">
            <i class="fa-solid fa-user-circle fa-2x text-success me-3"></i>
            <span id="nomAgentDisplay" class="fw-bold fs-5 text-dark"></span>
        </div>
        
        <label class="form-label fw-semibold text-secondary">Rôle attribué :</label>
        <select name="role" class="form-select form-select-lg border-2" style="border-radius: 12px;" required>
            <option value="Agent">Agent standard</option>
            <option value="DAGE_RH">DAGE / RH</option>
            <option value="SuperAdmin">SuperAdmin</option>
        </select>
      </div>

      <div class="modal-footer border-0 p-4 pt-0">
        <button type="button" class="btn btn-light px-4 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">Annuler</button>
        <button type="submit" class="btn btn-success px-4 py-2 shadow-sm" style="border-radius: 10px; background: #198754;">
            Confirmer la création
        </button>
      </div>
    </form>
  </div>
</div>
	
	    <script>
    // Recherche
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#personnelTable tbody tr');
        rows.forEach(row => { row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none'; });
    });

    // Ouverture Modale
    document.querySelectorAll('.btn-ouvrir-modal-compte').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('inputAgentId').value = this.getAttribute('data-id');
            document.getElementById('nomAgentDisplay').textContent = this.getAttribute('data-nom');
            new bootstrap.Modal(document.getElementById('modalCreationCompte')).show();
        });
    });

    // Suppression
    function supprimerDonnee(id, type) {
        if (!confirm("Confirmez-vous cette action ?")) return;
        const formData = new FormData();
        formData.append('action_type', type);
        formData.append('id', id);
        fetch('../modules/agents/gerer_suppression.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => { alert(data.message); if(data.success) location.reload(); });
    }
	
    // Création compte
    document.getElementById('formCreationCompte').addEventListener('submit', function(e) {
        e.preventDefault();
        fetch('/mjs_personnel/modules/agents/creer_acces_rapide.php', {
            method: 'POST',
            body: new FormData(this)
        })
        .then(r => r.json())
        .then(data => {
            alert(data.message);
            if(data.success) location.reload();
        });
    });
    </script> 
	
</body>
</html>