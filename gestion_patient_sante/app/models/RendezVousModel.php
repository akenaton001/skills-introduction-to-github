<?php
class RendezVousModel {
    private $db;

    public function __construct($db = null) {
        if ($db !== null) {
            $this->db = $db;
        } else {
            // Chemin corrigé vers le dossier config
            require_once __DIR__ . '/../config/database.php';
            
            $database = new Database();
            $this->db = $database->getConnection();
        }
    }

    // Récupérer tous les rendez-vous avec les informations des patients et médecins
    public function tousLesRendezVous() {
        $query = "SELECT r.*, p.nom AS patient_nom, p.prenom AS patient_prenom, p.code_patient, p.telephone, 
                         u.nom AS medecin_nom, u.prenom AS medecin_prenom 
                  FROM rendez_vous r 
                  JOIN patients p ON r.patient_id = p.id 
                  LEFT JOIN utilisateurs u ON r.medecin_id = u.id 
                  ORDER BY r.date_heure_rdv ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer la liste de tous les patients pour alimenter le menu déroulant
    public function getTousLesPatients() {
        $query = "SELECT id, code_patient, nom, prenom FROM patients ORDER BY nom ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer la liste des médecins/utilisateurs
    public function getMedecins() {
        $query = "SELECT id, nom, prenom FROM utilisateurs WHERE role IN ('medecin', 'administrateur')";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Insérer un nouveau rendez-vous
    public function insererRendezVous($data) {
        $query = "INSERT INTO rendez_vous (patient_id, medecin_id, date_heure_rdv, statut, notes) 
                  VALUES (:patient_id, :medecin_id, :date_heure_rdv, :statut, :notes)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'patient_id' => $data['patient_id'],
            'medecin_id' => !empty($data['medecin_id']) ? $data['medecin_id'] : null,
            'date_heure_rdv' => $data['date_heure_rdv'],
            'statut' => $data['statut'] ?? 'planifie',
            'notes' => !empty($data['notes']) ? $data['notes'] : null
        ]);
    }

    // --- Récupérer et Mettre à jour un rendez-vous ---

    public function getRendezVousById(int $id) {
        $query = "SELECT * FROM rendez_vous WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateRendezVous(int $id, array $data) {
        $query = "UPDATE rendez_vous SET 
                    patient_id = :patient_id, 
                    medecin_id = :medecin_id, 
                    date_heure_rdv = :date_heure_rdv, 
                    statut = :statut, 
                    notes = :notes 
                  WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'id' => $id,
            'patient_id' => $data['patient_id'],
            'medecin_id' => !empty($data['medecin_id']) ? $data['medecin_id'] : null,
            'date_heure_rdv' => $data['date_heure_rdv'],
            'statut' => $data['statut'] ?? 'planifie',
            'notes' => !empty($data['notes']) ? $data['notes'] : null
        ]);
    }

    // --- NOUVEAUTÉS : Mettre à jour uniquement le statut et Supprimer ---

    public function updateStatut(int $id, string $statut) {
        $query = "UPDATE rendez_vous SET statut = :statut WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'id' => $id,
            'statut' => $statut
        ]);
    }

    public function supprimerRendezVous(int $id) {
        $query = "DELETE FROM rendez_vous WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'id' => $id
        ]);
    }
}
?>