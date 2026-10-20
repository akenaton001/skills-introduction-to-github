<?php
require_once __DIR__ . '/../config/database.php';

class Examen {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // ==========================================
    // 1. GESTION DES PIÈCES JOINTES & RADIOS (Dossier Patient)
    // ==========================================

    public function create($data) {
        $sql = "INSERT INTO examens_pieces_jointes (patient_id, consultation_id, titre, type_examen, date_examen, resultats, compte_rendu, fichier_path) 
                VALUES (:patient_id, :consultation_id, :titre, :type_examen, :date_examen, :resultats, :compte_rendu, :fichier_path)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'patient_id' => $data['patient_id'],
            'consultation_id' => $data['consultation_id'] ?? null,
            'titre' => $data['titre'],
            'type_examen' => $data['type_examen'] ?? 'autre',
            'date_examen' => $data['date_examen'] ?? date('Y-m-d'),
            'resultats' => $data['resultats'] ?? '',
            'compte_rendu' => $data['compte_rendu'] ?? '',
            'fichier_path' => $data['fichier_path']
        ]);
        return $this->db->lastInsertId();
    }

    public function getById($id) {
        $sql = "SELECT * FROM examens_pieces_jointes WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer une demande d'examen globale par son ID
    public function getDemandeGlobaleById($id) {
        $sql = "SELECT * FROM examens WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $sql = "UPDATE examens_pieces_jointes 
                SET titre = :titre, 
                    type_examen = :type_examen, 
                    date_examen = :date_examen, 
                    resultats = :resultats, 
                    compte_rendu = :compte_rendu, 
                    fichier_path = :fichier_path 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'titre' => $data['titre'],
            'type_examen' => $data['type_examen'],
            'date_examen' => $data['date_examen'],
            'resultats' => $data['resultats'] ?? '',
            'compte_rendu' => $data['compte_rendu'] ?? '',
            'fichier_path' => $data['fichier_path']
        ]);
    }

    // ==========================================
    // 2. GESTION DU FLUX GLOBAL (Menu "Examens")
    // ==========================================

    public function createDemandeGlobale($data) {
        $sql = "INSERT INTO examens (patient_id, consultation_id, titre, type_examen, date_prescription, statut, medecin_prescripteur) 
                VALUES (:patient_id, :consultation_id, :titre, :type_examen, :date_prescription, :statut, :medecin_prescripteur)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'patient_id' => $data['patient_id'],
            'consultation_id' => $data['consultation_id'] ?? null,
            'titre' => $data['titre'],
            'type_examen' => $data['type_examen'] ?? 'Prescription Médicale',
            'date_prescription' => $data['date_prescription'] ?? date('Y-m-d H:i:s'),
            'statut' => $data['statut'] ?? 'En attente',
            'medecin_prescripteur' => $data['medecin_prescripteur'] ?? 'Médecin traitant'
        ]);
        return $this->db->lastInsertId();
    }

    // Récupérer toutes les demandes avec jointure sur la table utilisateurs pour afficher le vrai nom du médecin
    public function getAllDemandes() {
        $sql = "SELECT e.*, p.nom as patient_nom, p.prenom as patient_prenom, p.code_patient, p.sexe, 
                       u.nom as medecin_nom, u.prenom as medecin_prenom 
                  FROM examens e
                  JOIN patients p ON e.patient_id = p.id
                  LEFT JOIN consultations c ON e.consultation_id = c.id
                  LEFT JOIN utilisateurs u ON c.medecin_id = u.id
                  ORDER BY e.date_prescription DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mettre à jour le statut et les résultats de l'examen global
    public function updateResultatsGlobal($id, $statut, $resultats) {
        $sql = "UPDATE examens 
                  SET statut = :statut, resultats = :resultats 
                  WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'statut' => $statut,
            'resultats' => $resultats
        ]);
    }
}
?>