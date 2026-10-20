<?php
// ============================================================================
// ZONE 1 : SÉCURITÉ & REQUÊTES SQL (Strictement avant tout code HTML)
// ============================================================================
require_once __DIR__ . '/../config/bd.php';       // Connexion $pdo
require_once __DIR__ . '/../config/securite.php'; // Fonctions de sécurité

// Sécurité : Seuls les Admins, RH et Consultants peuvent voir la liste des agents
restreindre_acces_a(['SuperAdmin', 'DAGE_RH', 'Consultant']);

// Requête SQL Sécurisée (Exemple : Récupération des agents)
try {
    $sql = "SELECT a.*, s.nom_structure 
            FROM agents a 
            LEFT JOIN structures s ON a.id_structure_actuelle = s.id_structure
            ORDER BY a.nom, a.prenom";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $agents = $stmt->fetchAll(); // Contient la liste des agents prête à l'affichage
} catch (PDOException $e) {
    error_log("Erreur lors du chargement des agents : " . $e->getMessage());
    $erreur_systeme = "Impossible de charger la liste des agents.";
}

// Inclusion du haut de page Bootstrap (Balises <head>, CSS, barre du haut)
require_once __DIR__ . '/../includes/header.php';
?>

<!-- ============================================================================
     ZONE 2 : INTERFACE GRAPHIQUE BOOTSTRAP (HTML pur + Conditions d'affichage)
     ============================================================================ -->
<div class="container-fluid mt-4">
    <div class="row">
        
        <!-- Inclusion de la barre latérale (Menu) -->
        <div class="col-md-3 col-lg-2">
            <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
        </div>
        
        <!-- Contenu principal de la page -->
        <div class="col-md-9 col-lg-10">
            <h2 class="mb-4">Gestion du Personnel - MJS</h2>
            
            <!-- Affichage d'un message d'erreur SQL si nécessaire -->
            <?php if (isset($erreur_systeme)): ?>
                <div class="alert alert-danger"><?= $erreur_systeme; ?></div>
            <?php endif; ?>

            <!-- TABLEAU BOOTSTRAP RESPONSIVE -->
            <div class="table-responsive bg-white p-3 shadow-sm rounded">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Matricule</th>
                            <th>Nom & Prénom</th>
                            <th>Structure / Service</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($agents as $agent): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($agent['matricule_solde']); ?></strong></td>
                                <td><?= htmlspecialchars($agent['nom'] . ' ' . $agent['prenom']); ?></td>
                                <td><?= htmlspecialchars($agent['nom_structure'] ?? 'Non affecté'); ?></td>
                                
                                <td>
                                    <!-- ADAPTATION BOOTSTRAP SELON LE RÔLE -->
                                    
                                    <!-- Action 1 : Tout le monde (autorisé sur cette page) peut VOIR -->
                                    <a href="agents_fiche.php?id=<?= $agent['id_agent']; ?>" class="btn btn-sm btn-info text-white">
                                        <i class="bi bi-eye"></i> Voir
                                    </a>
                                    
                                    <!-- Action 2 : Seuls les Admins et RH peuvent MODIFIER ou SUPPRIMER -->
                                    <?php if (a_le_role('SuperAdmin') || a_le_role('DAGE_RH')): ?>
                                        <a href="agents_modifier.php?id=<?= $agent['id_agent']; ?>" class="btn btn-sm btn-warning text-white">
                                            <i class="bi bi-pencil"></i> Modifier
                                        </a>
                                        <a href="../modules/agents/supprimer.php?id=<?= $agent['id_agent']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet agent ?');">
                                            <i class="bi bi-trash"></i> Supprimer
                                        </a>
                                    <?php endif; ?>
                                    
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
</div>

<?php
// ============================================================================
// ZONE 3 : PIED DE PAGE (Scripts JavaScript Bootstrap)
// ============================================================================
require_once __DIR__ . '/../includes/footer.php';
?>