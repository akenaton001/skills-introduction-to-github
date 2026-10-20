<?php
session_start();
require_once __DIR__ . '/../../config/bd.php';
require_once __DIR__ . '/../../config/securite.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['agent_id'] ?? null;
    if (!$id) { die("ERREUR : Aucun ID."); }

    try {
        $sql = "UPDATE agents SET 
                adresse = :adresse,
                nom = :nom,
                prenom = :prenom,
                matricule_solde = :matricule,
                cni = :cni,
                date_naissance = :date_naissance,
                sexe = :sexe,
                situation_matrimoniale = :sit_matri,
                telephone = :tel,
                statut_agent = :statut,
                lieu_naissance = :lieu_naiss,
                direction_service = :service,
                date_entree_ministere = :entree,
                situation_militaire = :sit_militaire,
                nombre_enfants = :nb_enfants,
                numero_compte_bancaire = :rib,
                corps_agent = :corps,
                hierarchie = :hierarchie,
                id_structure_actuelle = :id_structure,
                date_prise_service = :prise
                WHERE id_agent = :id";

        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            ':adresse'        => $_POST['adresse'],
            ':nom'            => $_POST['nom'],
            ':prenom'         => $_POST['prenom'],
            ':matricule'      => $_POST['matricule_solde'],
            ':cni'            => $_POST['cni'],
            ':date_naissance' => $_POST['date_naissance'],
            ':sexe'           => $_POST['sexe'],
            ':sit_matri'      => $_POST['situation_matrimoniale'],
            ':tel'            => $_POST['telephone'],
            ':statut'         => $_POST['statut_agent'],
            ':lieu_naiss'     => $_POST['lieu_naissance'],
            ':service' => !empty($_POST['direction_service']) ? $_POST['direction_service'] : NULL,
            ':entree'         => $_POST['date_entree_ministere'],
            ':sit_militaire'  => $_POST['situation_militaire'] ?? NULL,
            ':nb_enfants'     => !empty($_POST['nombre_enfants']) ? $_POST['nombre_enfants'] : 0,
            ':rib'            => !empty($_POST['numero_compte_bancaire']) ? $_POST['numero_compte_bancaire'] : NULL,
            ':corps'          => $_POST['corps_agent'] ?? NULL,
            ':hierarchie'     => $_POST['hierarchie'] ?? NULL,
            ':id_structure'   => !empty($_POST['id_structure_actuelle']) ? $_POST['id_structure_actuelle'] : NULL,
            ':prise'          => !empty($_POST['date_prise_service']) ? $_POST['date_prise_service'] : NULL,
            ':id'             => $id
        ]);

        $_SESSION['succes'] = "Mise à jour complète réussie.";
        header('Location: ../../views/agents_liste.php');
        exit();

    } catch (PDOException $e) {
        die("Erreur technique : " . $e->getMessage());
    }
}
?>