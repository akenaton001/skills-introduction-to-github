<?php
// modules/agents/modifier_avatar.php
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sécurité 1 : Utilisateur authentifié ?
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Session expirée ou non autorisée.']);
    exit();
}

require_once __DIR__ . '/../../config/bd.php';
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        
        $fileTmpPath = $_FILES['avatar']['tmp_name'];
        $fileName = $_FILES['avatar']['name'];
        $fileSize = $_FILES['avatar']['size'];

        // Sécurité 2 : On inspecte le vrai type MIME binaire
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($fileTmpPath);

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $allowedExts  = ['jpg', 'jpeg', 'png', 'webp'];
        $fileExt      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validation taille (2 Mo max) et format
        if ($fileSize > 2 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'L\'image dépasse la taille maximale autorisée de 2 Mo.']);
            exit();
        }

        if (!in_array($mimeType, $allowedMimes) || !in_array($fileExt, $allowedExts)) {
            echo json_encode(['success' => false, 'message' => 'Format invalide. Autorisé : JPG, PNG, WEBP.']);
            exit();
        }

        // Définition et création automatique du dossier cible
        $uploadDir = __DIR__ . '/../../uploads/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Sécurité 3 : Nettoyage du nom et unicité
        $newFileName = 'avatar_' . $user_id . '_' . time() . '.' . $fileExt;
        $destPath = $uploadDir . $newFileName;

        // Récupération de l'ancien avatar pour le supprimer proprement du stockage
        try {
            $stmtSelect = $pdo->prepare("SELECT avatar FROM utilisateurs WHERE id_utilisateur = ?");
            $stmtSelect->execute([$user_id]);
            $currentAvatar = $stmtSelect->fetchColumn();

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                
                // Nettoyage de l'ancienne image physique si elle existe
                if (!empty($currentAvatar)) {
                    $oldFilePath = $uploadDir . $currentAvatar;
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                // Enregistrement du nouveau nom en base de données
                $stmtUpdate = $pdo->prepare("UPDATE utilisateurs SET avatar = ? WHERE id_utilisateur = ?");
                $stmtUpdate->execute([$newFileName, $user_id]);

                // Mise à jour immédiate de la session
                $_SESSION['user_avatar'] = $newFileName;

                echo json_encode([
                    'success' => true, 
                    'message' => 'Photo de profil mise à jour avec succès.',
                    'avatar' => $newFileName
                ]);
                exit();
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la sauvegarde physique de l\'image.']);
                exit();
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur BDD : ' . $e->getMessage()]);
            exit();
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Aucun fichier reçu ou code d\'erreur upload présent.']);
        exit();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode HTTP non autorisée.']);
    exit();
}