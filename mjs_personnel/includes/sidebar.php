<?php
// On s'assure que le fichier de sécurité est bien chargé pour utiliser la fonction a_le_role()
require_once __DIR__ . '/../config/securite.php';

// Variable optionnelle pour mettre en surbrillance l'onglet actif (à définir sur vos pages vues)
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar p-3">
    <p class="text-muted small text-uppercase fw-bold mb-2 px-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Navigation</p>
    <div class="nav nav-pills flex-column">
        
        <!-- Onglet 1 : Tableau de bord (Accessible à TOUT LE MONDE) -->
        <a href="/mjs_personnel/views/dashboard.php" class="nav-link text-dark mb-1 <?= ($current_page == 'dashboard.php') ? 'active text-white bg-primary' : ''; ?>">
            <i class="bi bi-speedometer2 me-2"></i> Tableau de bord
        </a>

        <!-- Onglet 2 : Gestion des Agents (Uniquement SuperAdmin, DAGE_RH et Consultant) -->
        <?php if (a_le_role('SuperAdmin') || a_le_role('DAGE_RH') || a_le_role('Consultant')): ?>
            <a href="/mjs_personnel/views/agents_liste.php" class="nav-link text-dark mb-1 <?= (strpos($current_page, 'agents_') !== false) ? 'active text-white bg-primary' : ''; ?>">
                <i class="bi bi-people me-2"></i> Personnel (Agents)
            </a>
        <?php endif; ?>

        <!-- Onglet 3 : Gestion des Congés (Accessible à tout le monde, mais l'affichage interne différera) -->
        <a href="/mjs_personnel/views/conges_demandes.php" class="nav-link text-dark mb-1 <?= (strpos($current_page, 'conges_') !== false) ? 'active text-white bg-primary' : ''; ?>">
            <i class="bi bi-calendar-check me-2"></i> Demandes de congés
        </a>

        <!-- Séparateur pour les accès d'Administration de haut niveau -->
        <?php if (a_le_role('SuperAdmin') || a_le_role('DAGE_RH')): ?>
            <hr class="text-muted my-2">
            <p class="text-muted small text-uppercase fw-bold mb-2 px-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Administration DAGE</p>
            
            <!-- Onglet 4 : Gestion des Structures / Organigramme -->
            <a href="/mjs_personnel/views/structures_liste.php" class="nav-link text-dark mb-1 <?= ($current_page == 'structures_liste.php') ? 'active text-white bg-primary' : ''; ?>">
                <i class="bi bi-building me-2"></i> Structures & CDEPS
            </a>
        <?php endif; ?>

        <!-- Onglet 5 : Paramètres Système avancés (Réservé UNIQUEMENT au SuperAdmin) -->
        <?php if (a_le_role('SuperAdmin')): ?>
            <a href="/mjs_personnel/views/utilisateurs_gestion.php" class="nav-link text-dark mb-1 <?= ($current_page == 'utilisateurs_gestion.php') ? 'active text-white bg-primary' : ''; ?>">
                <i class="bi bi-shield-lock me-2"></i> Comptes Utilisateurs
            </a>
            <a href="/mjs_personnel/views/configuration.php" class="nav-link text-dark mb-1 <?= ($current_page == 'configuration.php') ? 'active text-white bg-primary' : ''; ?>">
                <i class="bi bi-gear me-2"></i> Configuration Système
            </a>
        <?php endif; ?>

    </div>
</div>