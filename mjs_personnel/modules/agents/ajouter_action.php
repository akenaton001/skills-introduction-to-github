<?php
// modules/agents/ajouter_action.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/bd.php';

// Sécurité : Seuls les rôles autorisés (SuperAdmin et DAGE_RH) peuvent ajouter un agent
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['SuperAdmin', 'DAGE_RH'])) {
    $_SESSION['erreur'] = "Accès refusé. Vous n'avez pas les droits nécessaires.";
    header('Location: ../../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Récupération et nettoyage des données
    $matricule = isset($_POST['matricule_solde']) ? trim(strtoupper($_POST['matricule_solde'])) : '';
    $cni       = isset($_POST['cni']) ? trim($_POST['cni']) : '';
    $prenom    = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
    $nom       = isset($_POST['nom']) ? trim(strtoupper($_POST['nom'])) : '';
    $date_naiss= isset($_POST['date_naissance']) ? trim($_POST['date_naissance']) : '';
    $lieu_naiss= isset($_POST['lieu_naissance']) ? trim($_POST['lieu_naissance']) : '';
    $sexe      = isset($_POST['sexe']) ? trim($_POST['sexe']) : '';
    $sit_matri = isset($_POST['situation_matrimoniale']) ? trim($_POST['situation_matrimoniale']) : '';
    $telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : '';
    $email     = isset($_POST['email_professionnel']) ? trim(strtolower($_POST['email_professionnel'])) : '';
    $adresse   = isset($_POST['adresse']) ? trim($_POST['adresse']) : '';
    $statut    = isset($_POST['statut_agent']) ? trim($_POST['statut_agent']) : '';
    $direction = isset($_POST['direction_service']) ? trim($_POST['direction_service']) : '';
    $date_entree = isset($_POST['date_entree_ministere']) ? trim($_POST['date_entree_ministere']) : '';
	$situation_militaire = $_POST['situation_militaire'] ?? NULL;
	$nombre_enfants      = !empty($_POST['nombre_enfants']) ? $_POST['nombre_enfants'] : 0;
	$rib = !empty($_POST['numero_compte_bancaire']) ? trim($_POST['numero_compte_bancaire']) : NULL;
	$corps_agent = !empty($_POST['corps_agent']) ? trim($_POST['corps_agent']) : NULL;
	$hierarchie = !empty($_POST['hierarchie']) ? trim($_POST['hierarchie']) : NULL;
	$date_prise_service  = !empty($_POST['date_prise_service']) ? $_POST['date_prise_service'] : NULL;
	$id_structure        = !empty($_POST['id_structure_actuelle']) ? $_POST['id_structure_actuelle'] : NULL;

    // 2. Validation stricte des champs obligatoires
    if (empty($matricule) || empty($prenom) || empty($nom) || empty($telephone) || 
        empty($date_naiss) || empty($lieu_naiss) || empty($sexe) || empty($direction)) {
        
        $_SESSION['erreur'] = "Erreur : Tous les champs obligatoires (*) doivent être remplis.";
        header('Location: ../../views/agent_ajouter.php');
        exit();
    }

    try {
        // 3. Sécurité : Vérifier si le matricule existe déjà
        $checkStmt = $pdo->prepare("SELECT id_agent FROM agents WHERE matricule_solde = :matricule");
        $checkStmt->execute([':matricule' => $matricule]);
        if ($checkStmt->fetch()) {
            $_SESSION['erreur'] = "Erreur : Un agent possède déjà ce matricule ($matricule) dans le système.";
            header('Location: ../../views/agent_ajouter.php');
            exit();
        }

        // 4. Insertion de l'agent
        $sql = "INSERT INTO agents (
				matricule_solde, cni, prenom, nom, date_naissance, lieu_naissance, sexe, 
				situation_matrimoniale, telephone, adresse, statut_agent, direction_service, 
				date_entree_ministere, situation_militaire, nombre_enfants, numero_compte_bancaire, 
				id_structure_actuelle, corps_agent, hierarchie, date_prise_service
			) VALUES (
				:matricule, :cni, :prenom, :nom, :date_naiss, :lieu_naiss, :sexe, 
				:sit_matri, :tel, :adresse, :statut, :service, 
				:date_entree, :sit_militaire, :nb_enfants, :rib, 
				:id_structure, :corps, :hierarchie, :date_prise
			)";
        
        $stmt = $pdo->prepare($sql);
		$stmt->execute([
			':matricule'   => $matricule,
			':cni'         => $cni,
			':prenom'      => $prenom,
			':nom'         => $nom,
			':date_naiss'  => $date_naiss,
			':lieu_naiss'  => $lieu_naiss,
			':sexe'        => $sexe,
			':sit_matri'   => $sit_matri,
			':tel'         => $telephone,
			':adresse'     => $adresse,
			':statut'      => $statut,
			':service'     => $service,
			':date_entree' => $date_entree,
			':sit_militaire'=> $situation_militaire,
			':nb_enfants'  => $nombre_enfants,
			':rib'         => $rib,
			':id_structure'=> $id_structure,
			':corps'       => $corps_agent,
			':hierarchie'  => $hierarchie,
			':date_prise'  => $date_prise_service
		]);

        $id_nouvel_agent = $pdo->lastInsertId();

        // 5. Optionnel : Création du compte utilisateur
        if (isset($_POST['creer_compte_acces']) && $_POST['creer_compte_acces'] === '1') {
            $role_compte = isset($_POST['role_compte']) ? trim($_POST['role_compte']) : 'Agent';
            $password_initial = strtolower(str_replace('/', '', $matricule));
            $hash = password_hash($password_initial, PASSWORD_BCRYPT);

            $userSql = "INSERT INTO utilisateurs (username, password_hash, role, id_agent, actif) 
                        VALUES (:username, :hash, :role, :id_agent, 1)";
            $userStmt = $pdo->prepare($userSql);
            $userStmt->execute([
                ':username' => $matricule,
                ':hash'     => $hash,
                ':role'     => $role_compte,
                ':id_agent' => $id_nouvel_agent
            ]);
            
            $_SESSION['succes'] = "L'agent a été ajouté et son compte d'accès créé.";
        } else {
            $_SESSION['succes'] = "La fiche de l'agent $prenom $nom a été enregistrée avec succès.";
        }

        header('Location: ../../views/agent_ajouter.php');
        exit();

    } catch (PDOException $e) {
        $_SESSION['erreur'] = "Une erreur technique s'est produite : " . $e->getMessage();
        header('Location: ../../views/agent_ajouter.php');
        exit();
    }
}