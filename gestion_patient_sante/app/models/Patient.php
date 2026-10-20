<?php
require_once __DIR__ . '/../config/Database.php';

class Patient {
    private ?PDO $conn;
    private string $table = "patients";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Récupérer tous les patients (du plus récent au plus ancien)
    public function getAllPatients() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Rechercher des patients par nom, prénom, code ou téléphone
    public function rechercherParMotCle(string $terme) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE nom LIKE :t1 
                     OR prenom LIKE :t2 
                     OR code_patient LIKE :t3 
                     OR telephone LIKE :t4 
                  ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $searchTerm = "%" . $terme . "%";
        
        $stmt->execute([
            ':t1' => $searchTerm,
            ':t2' => $searchTerm,
            ':t3' => $searchTerm,
            ':t4' => $searchTerm
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Trouver un patient par son ID (pour le Dossier Patient Unique)
    public function getPatientById(int $id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Compter le nombre de patients inscrits à une date donnée (pour le code patient)
    public function countPatientsByDate(string $date) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE DATE(date_premiere_contact) = :date";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // Enregistrer un nouveau patient
    public function createPatient(array $data) {
        $query = "INSERT INTO " . $this->table . " 
                    (code_patient, nom, prenom, date_naissance, sexe, telephone, adresse, personne_prevenir, telephone_prevenir, groupe_sanguin, antecedents, date_premiere_contact) 
                    VALUES (:code_patient, :nom, :prenom, :date_naissance, :sexe, :telephone, :adresse, :personne_prevenir, :telephone_prevenir, :groupe_sanguin, :antecedents, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            ':code_patient' => $data['code_patient'],
            ':nom' => $data['nom'],
            ':prenom' => $data['prenom'],
            ':date_naissance' => $data['date_naissance'],
            ':sexe' => $data['sexe'],
            ':telephone' => $data['telephone'],
            ':adresse' => $data['adresse'],
            ':personne_prevenir' => $data['personne_prevenir'] ?? null,
            ':telephone_prevenir' => $data['telephone_prevenir'] ?? null,
            ':groupe_sanguin' => $data['groupe_sanguin'] ?? null,
            ':antecedents' => $data['antecedents'] ?? null
        ]);
    }

    // Mettre à jour les informations d'un patient (le code_patient est préservé)
    public function updatePatient(int $id, array $data) {
        $query = "UPDATE " . $this->table . " SET 
                    nom = :nom, 
                    prenom = :prenom, 
                    date_naissance = :date_naissance, 
                    sexe = :sexe, 
                    telephone = :telephone, 
                    adresse = :adresse, 
                    personne_prevenir = :personne_prevenir, 
                    telephone_prevenir = :telephone_prevenir, 
                    groupe_sanguin = :groupe_sanguin, 
                    antecedents = :antecedents 
                    WHERE id = :id";
                
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            ':id' => $id,
            ':nom' => $data['nom'],
            ':prenom' => $data['prenom'],
            ':date_naissance' => $data['date_naissance'],
            ':sexe' => $data['sexe'],
            ':telephone' => $data['telephone'],
            ':adresse' => $data['adresse'],
            ':personne_prevenir' => $data['personne_prevenir'] ?? null,
            ':telephone_prevenir' => $data['telephone_prevenir'] ?? null,
            ':groupe_sanguin' => $data['groupe_sanguin'] ?? null,
            ':antecedents' => $data['antecedents'] ?? null
        ]);
    }

    // Récupérer l'historique des consultations d'un patient
    public function getConsultationsByPatientId(int $patientId) {
        $query = "SELECT c.*, u.nom as medecin_nom, u.prenom as medecin_prenom 
                    FROM consultations c 
                    JOIN utilisateurs u ON c.medecin_id = u.id 
                    WHERE c.patient_id = :patient_id 
                    ORDER BY c.date_consultation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patientId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les pièces jointes (analyses, radios, scans) d'un patient
    public function getExamensByPatientId(int $patientId) {
        $query = "SELECT * FROM examens_pieces_jointes WHERE patient_id = :patient_id ORDER BY date_examen DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patientId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Vérifier si le patient est actuellement hospitalisé
    public function getActiveHospitalisationByPatientId(int $patientId) {
        $query = "SELECT * FROM hospitalisations WHERE patient_id = :patient_id AND statut = 'en_cours' LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patientId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}