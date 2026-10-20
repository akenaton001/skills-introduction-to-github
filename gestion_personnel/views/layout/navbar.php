<!-- Sidebar -->
<aside class="sidebar">
    <div>
        <div class="sidebar-brand">
            <h4>RH GESTION</h4>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="index.php" class="nav-link active">
                    <i class="fa-solid fa-users"></i>
                    <span>Liste Employés</span>
                </a>
            </li>
            <li>
                <a href="index.php?action=create" class="nav-link">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Nouveau Personnel</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="p-3 text-center text-muted border-top" style="font-size: 0.8rem;">
        &copy; 2026 RH System
    </div>
</aside>

<!-- Main Wrapper -->
<div class="main-wrapper">
    <!-- Topbar -->
    <header class="topbar">
        <div class="user-profile">
            <div class="text-end">
                <div class="fw-bold" style="font-size: 0.9rem;">Administrateur</div>
                <small class="text-muted" style="font-size: 0.75rem;">admin@entreprise.sn</small>
            </div>
            <div class="user-avatar">A</div>
        </div>
    </header>
    <!-- Dynamic Content Start -->
    <main class="p-4">
	<!-- Gestion des notifications (Pop-up / Alerte) -->
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-<?= $_SESSION['flash']['type']; ?> alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert" style="border-radius: 12px;">
            <?php if ($_SESSION['flash']['type'] === 'success'): ?>
                <i class="fa-solid fa-circle-check me-2 fs-5"></i>
            <?php else: ?>
                <i class="fa-solid fa-circle-exclamation me-2 fs-5"></i>
            <?php endif; ?>
            
            <div>
                <?= $_SESSION['flash']['message']; ?>
            </div>
            
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>