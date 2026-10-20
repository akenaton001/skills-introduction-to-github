<?php
require_once __DIR__ . '/../config/database.php';

class Incident {
    private $db;

    // Accepte une instance PDO transmise par le contrôleur ou en crée une par défaut
    public function __construct($pdo = null) {
        if ($pdo) {
            $this->db = $pdo;
        } else {
            $database = new Database();
            $this->db = $database->getConnection();
        }
    }

    public function getAll() {
        $sql = "SELECT i.*, e.nom, e.prenom, e.matricule 
                FROM incidents i 
                LEFT JOIN employees e ON i.employee_id = e.id 
                ORDER BY i.date_incident DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT i.*, e.nom, e.prenom, e.matricule 
                FROM incidents i 
                LEFT JOIN employees e ON i.employee_id = e.id 
                WHERE i.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $reference = 'INC-' . date('Ymd') . '-' . rand(100, 999);
        $fichier = $data['fichiers_joints'] ?? $data['fichier_joint'] ?? null;

        $sql = "INSERT INTO incidents (
                    reference, employee_id, type_incident, gravite, lieu, 
                    date_incident, titre, description, actions_correctives, 
                    denouement, statut, impact_financier, fichier_joint, fichiers_joints
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $reference,
            !empty($data['employee_id']) ? $data['employee_id'] : null,
            $data['type_incident'] ?? null,
            $data['gravite'] ?? null,
            $data['lieu'] ?? null,
            $data['date_incident'] ?? date('Y-m-d H:i:s'),
            $data['titre'] ?? null,
            $data['description'] ?? null,
            $data['actions_correctives'] ?? null,
            $data['denouement'] ?? null,
            $data['statut'] ?? 'Signalé',
            !empty($data['impact_financier']) ? $data['impact_financier'] : 0.00,
            $fichier,
            $fichier
        ]);
    }

    public function update($id, $data) {
        $fichier = $data['fichiers_joints'] ?? $data['fichier_joint'] ?? null;

        $sql = "UPDATE incidents SET 
                    employee_id = ?, 
                    type_incident = ?, 
                    gravite = ?, 
                    lieu = ?, 
                    date_incident = ?, 
                    titre = ?, 
                    description = ?, 
                    actions_correctives = ?, 
                    denouement = ?, 
                    statut = ?, 
                    impact_financier = ?,
                    fichier_joint = ?,
                    fichiers_joints = ?
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            !empty($data['employee_id']) ? $data['employee_id'] : null,
            $data['type_incident'] ?? null,
            $data['gravite'] ?? null,
            $data['lieu'] ?? null,
            $data['date_incident'] ?? null,
            $data['titre'] ?? null,
            $data['description'] ?? null,
            $data['actions_correctives'] ?? null,
            $data['denouement'] ?? null,
            $data['statut'] ?? 'Signalé',
            !empty($data['impact_financier']) ? $data['impact_financier'] : 0.00,
            $fichier,
            $fichier,
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM incidents WHERE id = ?");
        return $stmt->execute([$id]);
    }
}