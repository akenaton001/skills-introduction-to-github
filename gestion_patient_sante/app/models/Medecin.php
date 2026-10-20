<?php
class Medecin {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // Récupérer uniquement les utilisateurs qui ont le rôle 'medecin'
    public function getAllMedecins() {
        $query = "SELECT * FROM utilisateurs WHERE role = 'medecin' ORDER BY nom ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un médecin par son ID
    public function getMedecinById($id) {
        $query = "SELECT * FROM utilisateurs WHERE id = :id AND role = 'medecin' LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Ajouter un médecin (création d'un utilisateur avec le rôle 'medecin')
    public function createMedecin($data) {
        $query = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (:nom, :prenom, :email, :mot_de_passe, 'medecin')";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'mot_de_passe' => password_hash($data['mot_de_passe'], PASSWORD_DEFAULT)
        ]);
    }

    // Mettre à jour un médecin
    public function updateMedecin($id, $data) {
        if (!empty($data['mot_de_passe'])) {
            $query = "UPDATE utilisateurs SET nom = :nom, prenom = :prenom, email = :email, mot_de_passe = :mot_de_passe WHERE id = :id AND role = 'medecin'";
            $params = [
                'id' => $id,
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'mot_de_passe' => password_hash($data['mot_de_passe'], PASSWORD_DEFAULT)
            ];
        } else {
            $query = "UPDATE utilisateurs SET nom = :nom, prenom = :prenom, email = :email WHERE id = :id AND role = 'medecin'";
            $params = [
                'id' => $id,
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email']
            ];
        }
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    // Supprimer un médecin
    public function deleteMedecin($id) {
        $query = "DELETE FROM utilisateurs WHERE id = :id AND role = 'medecin'";
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['id' => $id]);
    }
}