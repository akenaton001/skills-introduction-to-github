<?php
// 1. SÉCURITÉ : Chargement des configurations
require_once __DIR__ . '/../config/bd.php';
require_once __DIR__ . '/../config/securite.php';

// Sécurité stricte : Seuls l'Admin et les RH peuvent insérer des données dans la table agents
restreindre_acces_a(['SuperAdmin', 'DAGE_RH']);

// 2. LOGIQUE REQUÊTE SQL (Exécutée uniquement si le formulaire a été soumis en POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Nettoyage des données reçues pour éviter les failles XSS
    $matricule = trim($_POST['matricule']);
    $prenom    = trim($_POST['prenom']);
    $nom       = trim($_POST['nom']);
    $structure = intval($_POST['id_structure']); 

    // REQUÊTE PRÉPARÉE (Le code SQL et les données sont séparés : 100% sécurisé)
    try {
        $sql = "INSERT INTO agents (matricule_solde, prenom, nom, id_structure_actuelle, date_entree_ministere) 
                VALUES (:matricule, :prenom, :nom, :structure, NOW())";
                
        $stmt = $pdo->prepare($sql);
        
        // Liaison sécurisée des variables (bindValue protège contre les injections SQL)
        $stmt->bindValue(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->bindValue(':prenom', $prenom, PDO::PARAM_STR);
        $stmt->bindValue(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindValue(':structure', $structure, PDO::PARAM_INT);
        
        $stmt->execute();
        
        // Message de succès stocké en session pour la page suivante
        $_SESSION['succes'] = "L'agent a été enregistré avec succès au Ministère.";
        
    } catch (PDOException $e) {
        error_log("Échec de l'ajout de l'agent : " . $e->getMessage());
        $_SESSION['erreur'] = "Erreur lors de l'enregistrement. Le matricule existe peut-être déjà.";
    }
    
    // 3. REDIRECTION IMMÉDIATE (Évite de renvoyer le formulaire en rafraîchissant la page)
    header('Location: /mjs_personnel/views/agents_liste.php');
    exit();
} else {
    // Si quelqu'un essaie d'accéder au fichier directement sans formulaire, on le dégage
    header('Location: /mjs_personnel/views/dashboard.php');
    exit();
}
?>