<?php
session_start();
require_once __DIR__ . '/../../config/bd.php';

if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['SuperAdmin', 'DAGE_RH'])) {
    echo json_encode(['success' => false, 'message' => 'Accès refusé.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['action_type'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    try {
        if ($type === 'acces') {
            $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id_utilisateur = :id");
            $stmt->execute([':id' => $id]);
            echo json_encode(['success' => true, 'message' => 'Accès supprimé.']);
        } elseif ($type === 'agent') {
            $pdo->beginTransaction();
            $pdo->prepare("DELETE FROM utilisateurs WHERE id_agent = :id")->execute([':id' => $id]);
            $pdo->prepare("DELETE FROM agents WHERE id_agent = :id")->execute([':id' => $id]);
            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Agent et accès supprimés.']);
        }
    } catch (PDOException $e) {
        if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Erreur SQL : Impossible de supprimer (historique existant).']);
    }
}