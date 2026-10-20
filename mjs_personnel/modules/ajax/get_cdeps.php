<?php
require_once __DIR__ . '/../../config/bd.php';

$region_id = $_GET['region_id'] ?? 0;

// Requête pour chercher les CDEPS (type_structure = 'CDEPS') 
// liés à la région (id_parent)
$stmt = $pdo->prepare("SELECT id_structure, nom_structure FROM structures 
                       WHERE type_structure = 'CDEPS' AND id_parent = ?");
$stmt->execute([$region_id]);
$cdeps = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($cdeps);