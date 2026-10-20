<?php
// ============================================================================
// SCRIPT DE CONNEXION SÉCURISÉ (PRODUCTION) - MJS + HISTORIQUE + FORCE MDP
// ============================================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/bd.php';

// Vérification que les données viennent bien du formulaire en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Nettoyage des entrées pour éviter les espaces accidentels au clavier
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (!empty($username) && !empty($password)) {
        try {
            // Requête préparée pour contrer les injections SQL
            $stmt = $pdo->prepare("
                SELECT u.*, a.prenom, a.nom 
                FROM utilisateurs u 
                LEFT JOIN agents a ON u.id_agent = a.id_agent 
                WHERE u.username = :username 
                LIMIT 1
            ");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch();

            // Vérification stricte du mot de passe et du statut actif du compte
            if ($user && (int)$user['actif'] === 1 && password_verify($password, $user['password_hash'])) {
                
                // Régénération de l'ID de session pour contrer la fixation de session
                session_regenerate_id(true);

                // Stockage des informations non sensibles de l'utilisateur connecté
                $_SESSION['user_id']          = (int)$user['id_utilisateur'];
                $_SESSION['username']         = $user['username'];
                $_SESSION['user_role']        = $user['role'];
                $_SESSION['user_nom_complet'] = $user['prenom'] . ' ' . $user['nom'];
                
                // NOUVEAUTÉ : Enregistrement de l'état du changement de mot de passe obligatoire en session
                $_SESSION['doit_changer_mdp'] = (int)$user['doit_changer_mdp'];

                // ============================================================
                // AJOUT : ENREGISTREMENT DANS L'HISTORIQUE DES CONNEXIONS
                // ============================================================
                $ip_adresse = $_SERVER['REMOTE_ADDR'];
                if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip_adresse = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
                }
                
                $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 255) : 'Inconnu';

                $stmt_log = $pdo->prepare("
                    INSERT INTO historique_connexions (id_utilisateur, ip_adresse, user_agent, date_connexion) 
                    VALUES (:id_utilisateur, :ip_adresse, :user_agent, NOW())
                ");
                $stmt_log->execute([
                    ':id_utilisateur' => $_SESSION['user_id'],
                    ':ip_adresse'     => $ip_adresse,
                    ':user_agent'     => $user_agent
                ]);
                // ============================================================

                // NOUVEAUTÉ : INTERCEPTION PREMIÈRE CONNEXION
                // Si l'agent doit changer son mot de passe, on change sa trajectoire de redirection
                if ($_SESSION['doit_changer_mdp'] === 1) {
                    header('Location: /mjs_personnel/modules/agents/modifier_mon_mdp_agent.php?premiere_connexion=1');
                    exit();
                }

                // Si tout est en règle (doit_changer_mdp == 0), redirection normale vers le tableau de bord
                header('Location: /mjs_personnel/views/dashboard.php');
                exit();
            } else {
                // Message d'erreur générique pour ne pas donner d'indices aux hackers
                $_SESSION['erreur'] = "Identifiant ou mot de passe incorrect.";
                header('Location: /mjs_personnel/index.php');
                exit();
            }

        } catch (PDOException $e) {
            // Journalisation interne de l'erreur sans la divulguer sur le web
            error_log("Erreur d'authentification ou d'historique : " . $e->getMessage());
            $_SESSION['erreur'] = "Une erreur technique est survenue. Veuillez réessayer.";
            header('Location: /mjs_personnel/index.php');
            exit();
        }
    } else {
        $_SESSION['erreur'] = "Veuillez remplir tous les champs.";
        header('Location: /mjs_personnel/index.php');
        exit();
    }
} else {
    // Si quelqu'un tente d'accéder au script directement par l'URL
    header('Location: /mjs_personnel/index.php');
    exit();
}