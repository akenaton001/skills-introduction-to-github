<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: /mjs_personnel/index.php');
    exit();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm sticky-top mb-4">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="/mjs_personnel/views/dashboard.php">
            <img src="/mjs_personnel/public/images/logo-mjs.png" alt="Logo MJS" class="logo-ministere" style="height: 30px; margin-right: 10px;">
            <span class="fw-bold text-white" style="font-size: 1.1rem; letter-spacing: 0.5px;">MJS - GESTION RH</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="topNavbar">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="/mjs_personnel/views/dashboard.php"><i class="fa-solid fa-gauge me-1"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="/mjs_personnel/views/profil.php"><i class="fa-solid fa-user me-1"></i> Profil</a></li>
                <li class="nav-item"><a class="nav-link" href="/mjs_personnel/views/formulaire_conge.php"><i class="fa-solid fa-calendar-plus me-1"></i> Congés</a></li>
                
                <?php if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['SuperAdmin', 'DAGE_RH'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="fa-solid fa-shield-halved me-1"></i> Administration</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/mjs_personnel/views/agents_liste.php"><i class="fa-solid fa-users me-2"></i> Registre Personnel</a></li>
                            <li><a class="dropdown-item" href="/mjs_personnel/views/gestion_conges_rh.php"><i class="fa-solid fa-plane-departure me-2"></i> Validation Congés</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav align-items-center">
                <li class="nav-item me-3 text-lg-end">
                    <span class="navbar-text text-white fw-semibold d-block p-0 small">
                        <i class="fa-solid fa-circle-user me-1"></i> <?= htmlspecialchars($_SESSION['user_nom_complet'] ?? 'Utilisateur'); ?>
                    </span>
                    <span class="badge bg-light text-dark font-monospace" style="font-size: 0.65rem; padding: 2px 6px;">
                        <?= htmlspecialchars($_SESSION['user_role'] ?? 'Agent'); ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-light btn-sm px-3" href="/mjs_personnel/modules/auth/deconnexion.php" onclick="return confirm('Voulez-vous vous déconnecter ?');" style="border-radius: 8px;">
                        <i class="fa-solid fa-power-off me-1"></i> Quitter
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>