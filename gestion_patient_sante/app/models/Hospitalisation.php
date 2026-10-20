<?php
require_once __DIR__ . '/../config/database.php';

class Hospitalisation {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Récupérer toutes les hospitalisations (avec les infos du patient)
    public function getAll() {
        $sql = "SELECT h.*, p.nom, p.prenom, p.telephone 
                FROM hospitalisations h 
                JOIN patients p ON h.patient_id = p.id 
                ORDER BY h.date_entree DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les hospitalisations d'un patient spécifique
    public function getByPatientId($patient_id) {
        $sql = "SELECT * FROM hospitalisations WHERE patient_id = ? ORDER BY date_entree DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$patient_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer une hospitalisation par son ID
    public function getById($id) {
        $sql = "SELECT h.*, p.nom, p.prenom, p.telephone, p.sexe, p.date_naissance 
                FROM hospitalisations h 
                JOIN patients p ON h.patient_id = p.id 
                WHERE h.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Enregistrer une nouvelle hospitalisation (Admission)
    public function create($data) {
        $sql = "INSERT INTO hospitalisations (patient_id, numero_chambre, numero_lit, date_entree, motif_hospitalisation, statut) 
                VALUES (:patient_id, :numero_chambre, :numero_lit, :date_entree, :motif_hospitalisation, 'en_cours')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'patient_id' => $data['patient_id'],
            'numero_chambre' => $data['numero_chambre'],
            'numero_lit' => $data['numero_lit'],
            'date_entree' => $data['date_entree'] ?? date('Y-m-d H:i:s'),
            'motif_hospitalisation' => $data['motif_hospitalisation']
        ]);
    }

    // Mettre fin à une hospitalisation (Sortie du patient)
    public function discharge($id, $date_sortie) {
        $sql = "UPDATE hospitalisations SET date_sortie = :date_sortie, statut = 'termine' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'date_sortie' => $date_sortie ?? date('Y-m-d H:i:s')
        ]);
    }

    // Supprimer une hospitalisation
    public function delete($id) {
        $sql = "DELETE FROM hospitalisations WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}