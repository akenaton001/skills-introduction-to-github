<?php
session_start();
require_once __DIR__ . '/../../config/bd.php';

// Vérification de sécurité
if (!isset($_SESSION['id_agent'])) {
    die("Erreur : Session invalide. Veuillez vous reconnecter.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_agent = $_SESSION['id_agent'];
    $type = $_POST['type_conge'];
    $debut = $_POST['date_debut'];
    $fin = $_POST['date_fin'];
    $motif = $_POST['motif'];

    // Calcul du nombre de jours
    $d1 = new DateTime($debut);
    $d2 = new DateTime($fin);
    $diff = $d1->diff($d2)->days + 1;

    // Insertion sécurisée dans la table 'conges'
    $stmt = $pdo->prepare("INSERT INTO conges (id_agent, type_conge, date_debut, date_fin, nombre_jours, motif, statut) 
                           VALUES (?, ?, ?, ?, ?, ?, 'En attente')");
    $stmt->execute([$id_agent, $type, $debut, $fin, $diff, $motif]);

    $_SESSION['message'] = "Votre demande de congé a été enregistrée avec succès.";
    header('Location: ../../views/formulaire_conge.php');
    exit();
}
?>