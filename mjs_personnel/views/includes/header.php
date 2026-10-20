<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { header('Location: /mjs_personnel/index.php'); exit(); }

$role_actuel = $_SESSION['user_role'] ?? 'Agent';

// Calcul du nombre de congés en attente
$nb_alertes = 0;
if (in_array($role_actuel, ['SuperAdmin', 'DAGE_RH'])) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM conges WHERE statut = 'En attente'");
        $nb_alertes = $stmt->fetchColumn();
    } catch (Exception $e) { $nb_alertes = 0; }
}
?>

<style>
    .navbar-custom { padding: 0.25rem 1rem !important; border-bottom: 2px solid var(--sn-green) !important; background-color: #0f172a; }
    .navbar-brand { font-size: 0.95rem !important; }
    .logo-container img { height: 45px !important; width: auto; }
    .logo-container .text-uppercase { font-size: 0.6rem !important; }
    .navbar-dark .navbar-nav .nav-link { color: rgba(255,255,255,0.85) !important; font-weight: 500; padding: 0.5rem 0.8rem !important; font-size: 0.9rem; }
    .btn-circle { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; border-radius: 50% !important; }

    /* Animation et style moderne */
    .dropdown-menu { border: none !important; border-radius: 12px !important; box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important; display: none; margin-top: 5px !important; }
    .dropdown.show .dropdown-menu { display: block !important; animation: slideIn 0.3s ease-out; }
    @keyframes slideIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .dropdown-item { padding: 10px 15px !important; border-radius: 8px !important; transition: 0.3s; }
    .dropdown-item:hover { background-color: #f1f5f9 !important; padding-left: 20px !important; }
	
	/* 1. S'assurer que la barre de navigation est bien au-dessus */
    .navbar { 
        position: relative; 
        z-index: 2000 !important; 
    }

    /* 2. S'assurer que le menu déroulant est détaché du flux normal */
    .dropdown-menu {
        position: absolute !important; /* Force le positionnement hors du parent */
        z-index: 3000 !important;      /* Plus élevé que la navbar */
        margin-top: 10px !important;
    }

    /* 3. Optionnel : Empêcher les autres éléments de la page de "monter" au-dessus */
    .main-content, .card, .table-responsive {
        position: relative;
        z-index: 1; /* Inférieur au menu */
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm mb-4">
    <div class="container-fluid">
        <div class="logo-container d-flex align-items-center">
            <a href="dashboard.php" class="text-decoration-none d-flex align-items-center">
                <img src="../public/images/logo.png" alt="Logo MJS">
                <div class="ms-2">
                    <span class="navbar-brand mb-0 h1 fw-bold text-white" style="font-size: 1.1rem;">MINISTÈRE DE LA JEUNESSE</span>
                    <span class="d-block text-uppercase fw-semibold" style="font-size: 0.65rem; color: var(--sn-yellow);">République du Sénégal</span>
                </div>
            </a>
        </div>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fa-solid fa-gauge me-1"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="formulaire_conge.php"><i class="fa-solid fa-calendar-days me-1"></i> Mes Congés</a></li>
                
                <?php if (in_array($role_actuel, ['SuperAdmin', 'DAGE_RH'])): ?>
                    <li class="nav-item"><a class="nav-link" href="/mjs_personnel/views/cartographie.php"><i class="fa-solid fa-map-location-dot"></i> Cartographie</a></li>
                    <li class="nav-item"><a class="nav-link" href="gestion_conges_rh.php"><i class="fa-solid fa-envelope"></i> <span class="badge bg-danger rounded-pill"><?= $nb_alertes ?></span></a></li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDrop">Admin RH</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="agents_liste.php"><i class="fa-solid fa-users me-2"></i> Registre Personnel</a></li>
                            <?php if ($role_actuel === 'SuperAdmin'): ?>
                                <li><a class="dropdown-item" href="utilisateurs_liste.php"><i class="fa-solid fa-users-gear me-2"></i> Registre Utilisateurs</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="agent_ajouter.php"><i class="fa-solid fa-user-plus me-2"></i> Enregistrer un agent</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="gestion_conges_rh.php"><i class="fa-solid fa-envelope-open-text me-2"></i> Validation Congés</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="profil.php" class="btn btn-sm btn-light rounded-pill px-3"><i class="fa-solid fa-user-gear me-1"></i> Profil</a>
            <a href="../modules/auth/deconnexion.php" class="btn btn-sm btn-outline-danger btn-circle" title="Déconnexion"><i class="fa-solid fa-power-off"></i></a>
        </div>
    </div>
</nav>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const dropdown = document.querySelector('.dropdown');
        if(dropdown) {
            dropdown.querySelector('.dropdown-toggle').addEventListener('click', (e) => {
                e.preventDefault();
                dropdown.classList.toggle('show');
            });
            document.querySelectorAll('.dropdown-item').forEach(item => {
                item.addEventListener('click', () => dropdown.classList.remove('show'));
            });
            document.addEventListener('click', (e) => {
                if (!dropdown.contains(e.target)) dropdown.classList.remove('show');
            });
        }
    });
</script>