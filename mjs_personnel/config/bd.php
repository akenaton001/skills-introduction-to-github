<?php
// ============================================================================
// CONFIGURATION DE LA BASE DE DONNÉES (MJS)
// ============================================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Vide par défaut sur XAMPP
define('DB_NAME', 'mjs_personnel_db');
define('DB_CHARSET', 'utf8mb4');

// ============================================================================
// CONNEXION VIA PDO AVEC SÉCURITÉ TRY/CATCH
// ============================================================================
try {
    // Version simplifiée et directe avec vos valeurs
    $dsn = "mysql:host=localhost;dbname=mjs_personnel_db;charset=utf8mb4";
    
    // Options de sécurité et de rapport d'erreurs
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // Création de la connexion globale $pdo
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

} catch (PDOException $e) {
    // En cas de panne (mauvais mot de passe, serveur éteint...), on affiche un message propre
    die("Désolé, une erreur technique est survenue lors de la connexion à la base de données.");
}
?>