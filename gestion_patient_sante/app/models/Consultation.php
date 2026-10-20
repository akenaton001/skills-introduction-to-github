<?php
require_once __DIR__ . '/../config/Database.php';

class Consultation {
    private ?PDO $conn;
    private string $table = "consultations";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Créer une consultation
    public function create(array $data) {
        $query = "INSERT INTO " . $this->table . " 
                (patient_id, medecin_id, date_consultation, evolution, motif, symptomes, diagnostic, prescription, examens_prescrits, date_prochain_rv) 
                VALUES (:patient_id, :medecin_id, :date_consultation, :evolution, :motif, :symptomes, :diagnostic, :prescription, :examens_prescrits, :date_prochain_rv)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':patient_id' => $data['patient_id'],
            ':medecin_id' => $data['medecin_id'],
            ':date_consultation' => $data['date_consultation'],
            ':evolution' => $data['evolution'],
            ':motif' => $data['motif'],
            ':symptomes' => $data['symptomes'],
            ':diagnostic' => $data['diagnostic'],
            ':prescription' => $data['prescription'],
            ':examens_prescrits' => $data['examens_prescrits'],
            ':date_prochain_rv' => $data['date_prochain_rv']
        ]);
        return $this->conn->lastInsertId();
    }

    // Récupérer une consultation par son ID
    public function getById(int $id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Mettre à jour une consultation
    public function update(int $id, array $data) {
        $query = "UPDATE " . $this->table . " SET 
                    date_consultation = :date_consultation,
                    evolution = :evolution,
                    motif = :motif,
                    symptomes = :symptomes,
                    diagnostic = :diagnostic,
                    prescription = :prescription,
                    examens_prescrits = :examens_prescrits,
                    date_prochain_rv = :date_prochain_rv
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':id' => $id,
            ':date_consultation' => $data['date_consultation'],
            ':evolution' => $data['evolution'],
            ':motif' => $data['motif'],
            ':symptomes' => $data['symptomes'],
            ':diagnostic' => $data['diagnostic'],
            ':prescription' => $data['prescription'],
            ':examens_prescrits' => $data['examens_prescrits'],
            ':date_prochain_rv' => $data['date_prochain_rv']
        ]);
    }

    // Ajouter une pièce jointe (examen/fichier) liée à la consultation
    public function addItemPieceJointe(array $data) {
        $query = "INSERT INTO examens_pieces_jointes (patient_id, consultation_id, titre, type_examen, fichier_path, date_examen) 
                  VALUES (:patient_id, :consultation_id, :titre, :type_examen, :fichier_path, NOW())";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':patient_id' => $data['patient_id'],
            ':consultation_id' => $data['consultation_id'],
            ':titre' => $data['titre'],
            ':type_examen' => $data['type_examen'],
            ':fichier_path' => $data['fichier_path']
        ]);
    }

    // Récupérer les pièces jointes d'une consultation spécifique
    public function getPiecesJointesByConsultationId(int $consultationId) {
        $query = "SELECT * FROM examens_pieces_jointes WHERE consultation_id = :consultation_id ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':consultation_id', $consultationId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer toutes les consultations avec les informations des patients (et filtre optionnel par date)
    public function getAllConsultations(?string $date = null) {
        $query = "SELECT c.*, p.prenom, p.nom, p.telephone, p.sexe, p.groupe_sanguin 
                  FROM " . $this->table . " c
                  JOIN patients p ON c.patient_id = p.id";
        
        if ($date) {
            $query .= " WHERE DATE(c.date_consultation) = :date_consultation";
        }
        
        $query .= " ORDER BY c.date_consultation DESC";

        $stmt = $this->conn->prepare($query);
        
        if ($date) {
            $stmt->execute([':date_consultation' => $date]);
        } else {
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupère et unifie les Consultations et les Rendez-vous pour une date donnée ou globale
    public function getAllConsultationsEtRendezVous(?string $date = null) {
        $query = "
            SELECT 
                c.id,
                c.patient_id,
                c.medecin_id,
                c.date_consultation AS date_heure,
                c.motif,
                c.diagnostic,
                c.prescription,
                c.evolution,
                c.symptomes,
                'consultation' AS type_source,
                p.prenom,
                p.nom,
                p.telephone,
                p.sexe,
                p.groupe_sanguin,
                m.prenom AS medecin_prenom,
                m.nom AS medecin_nom
            FROM consultations c
            JOIN patients p ON c.patient_id = p.id
            LEFT JOIN utilisateurs m ON c.medecin_id = m.id
            " . ($date ? "WHERE DATE(c.date_consultation) = :date_c" : "") . "

            UNION

            SELECT 
                r.id,
                r.patient_id,
                r.medecin_id,
                r.date_heure_rdv AS date_heure,
                r.notes AS motif,
                'Rendez-vous planifié' AS diagnostic,
                NULL AS prescription,
                NULL AS evolution,
                NULL AS symptomes,
                'rendez_vous' AS type_source,
                p.prenom,
                p.nom,
                p.telephone,
                p.sexe,
                p.groupe_sanguin,
                m.prenom AS medecin_prenom,
                m.nom AS medecin_nom
            FROM rendez_vous r
            JOIN patients p ON r.patient_id = p.id
            LEFT JOIN utilisateurs m ON r.medecin_id = m.id
            " . ($date ? "WHERE DATE(r.date_heure_rdv) = :date_r" : "") . "

            ORDER BY date_heure DESC
        ";

        $stmt = $this->conn->prepare($query);

        if ($date) {
            $stmt->execute([
                ':date_c' => $date,
                ':date_r' => $date
            ]);
        } else {
            $stmt->execute();
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}