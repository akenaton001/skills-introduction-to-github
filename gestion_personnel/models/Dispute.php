<?php

class Dispute {
    private $pdo;
    private $db;

    public function __construct($pdo = null) {
        if ($pdo instanceof PDO) {
            $this->pdo = $pdo;
            $this->db = $pdo;
        } else {
            // Intégration pour architecture avec classe de connexion Database
            require_once __DIR__ . '/../config/database.php';
            $database = new Database();
            $this->pdo = $database->getConnection();
            $this->db = $this->pdo;
        }
    }

    // Récupérer tous les contentieux (avec support des filtres)
    public function getAll($filters = []) {
        $sql = "SELECT d.*, e.nom, e.prenom, e.matricule 
                FROM disputes d 
                LEFT JOIN employees e ON d.employee_id = e.id 
                WHERE 1=1";
        $params = [];

        // Filtre par Statut
        if (!empty($filters['statut'])) {
            $sql .= " AND d.statut = :statut";
            $params[':statut'] = $filters['statut'];
        }

        // Filtre par Gravité
        if (!empty($filters['gravite'])) {
            $sql .= " AND d.gravite = :gravite";
            $params[':gravite'] = $filters['gravite'];
        }

        // Filtre par Type (Interne / Externe)
        if (!empty($filters['type'])) {
            $sql .= " AND d.type = :type";
            $params[':type'] = $filters['type'];
        }

        // Recherche par mot-clé (Objet, Description, Tiers, Nom/Prénom/Matricule)
        if (!empty($filters['search'])) {
            $sql .= " AND (d.objet LIKE :search 
                        OR d.description LIKE :search 
                        OR d.tiers_impliques LIKE :search 
                        OR e.nom LIKE :search 
                        OR e.prenom LIKE :search 
                        OR e.matricule LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY d.date_ouverture DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les contentieux d'un employé spécifique
    public function getByEmployeeId($employee_id) {
        $sql = "SELECT * FROM disputes WHERE employee_id = :employee_id ORDER BY date_ouverture DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':employee_id' => $employee_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un contentieux par son ID (alias find & getById)
    public function getById($id) {
        $sql = "SELECT d.*, e.nom, e.prenom, e.matricule 
                FROM disputes d 
                LEFT JOIN employees e ON d.employee_id = e.id 
                WHERE d.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        return $this->getById($id);
    }

    // --- HISTORIQUE DES DÉNOUEMENTS & ÉVOLUTIONS ---

    // Récupérer l'historique des dénouements d'un dossier
    public function getUpdatesByDisputeId($disputeId) {
        $sql = "SELECT * FROM dispute_updates WHERE dispute_id = :dispute_id ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':dispute_id' => $disputeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ajouter une évolution / dénouement et mettre à jour le statut du dossier
    public function addUpdate($disputeId, $statut, $decision, $fichierJoint = null) {
        // 1. Insertion de la note d'évolution et du fichier dans la table d'historique
        $sql = "INSERT INTO dispute_updates (dispute_id, statut, decision, fichier_joint) 
                VALUES (:dispute_id, :statut, :decision, :fichier_joint)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':dispute_id'   => $disputeId,
            ':statut'       => $statut,
            ':decision'     => $decision,
            ':fichier_joint' => $fichierJoint
        ]);

        // 2. Mise à jour du statut global du dossier principal (et clôture si résolu)
        $dateCloture = in_array($statut, ['Résolu', 'Classé sans suite']) ? date('Y-m-d H:i:s') : null;
        
        $sqlDispute = "UPDATE disputes SET statut = :statut, date_cloture = :date_cloture WHERE id = :id";
        $stmtDispute = $this->pdo->prepare($sqlDispute);
        return $stmtDispute->execute([
            ':statut'       => $statut,
            ':date_cloture' => $dateCloture,
            ':id'           => $disputeId
        ]);
    }

    // --- MANIPULATION DES DOSSIERS ---

    // Créer un nouveau contentieux
    public function create($data) {
        $sql = "INSERT INTO disputes (
                    employee_id, type, categorie, gravite, objet, description, 
                    tiers_impliques, date_ouverture, date_limite_reponse, statut, 
                    decision, impact_financier, fichier_joint
                ) VALUES (
                    :employee_id, :type, :categorie, :gravite, :objet, :description, 
                    :tiers_impliques, :date_ouverture, :date_limite_reponse, :statut, 
                    :decision, :impact_financier, :fichier_joint
                )";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':employee_id'        => !empty($data['employee_id']) ? $data['employee_id'] : null,
            ':type'               => $data['type'],
            ':categorie'          => $data['categorie'],
            ':gravite'            => $data['gravite'],
            ':objet'              => $data['objet'],
            ':description'        => $data['description'] ?? null,
            ':tiers_impliques'    => $data['tiers_impliques'] ?? null,
            ':date_ouverture'     => $data['date_ouverture'],
            ':date_limite_reponse'=> !empty($data['date_limite_reponse']) ? $data['date_limite_reponse'] : null,
            ':statut'             => $data['statut'],
            ':decision'           => $data['decision'] ?? null,
            ':impact_financier'   => !empty($data['impact_financier']) ? $data['impact_financier'] : 0,
            ':fichier_joint'      => $data['fichier_joint'] ?? null
        ]);
    }

    // Modifier un contentieux complet
    public function update($id, $data) {
        $sql = "UPDATE disputes SET 
                    employee_id = :employee_id,
                    type = :type,
                    categorie = :categorie,
                    gravite = :gravite,
                    objet = :objet,
                    description = :description,
                    tiers_impliques = :tiers_impliques,
                    date_ouverture = :date_ouverture,
                    date_limite_reponse = :date_limite_reponse,
                    date_cloture = :date_cloture,
                    statut = :statut,
                    decision = :decision,
                    impact_financier = :impact_financier";
        
        if (!empty($data['fichier_joint'])) {
            $sql .= ", fichier_joint = :fichier_joint";
        }

        $sql .= " WHERE id = :id";

        $params = [
            ':id'                  => $id,
            ':employee_id'         => !empty($data['employee_id']) ? $data['employee_id'] : null,
            ':type'                => $data['type'],
            ':categorie'           => $data['categorie'],
            ':gravite'             => $data['gravite'],
            ':objet'               => $data['objet'],
            ':description'         => $data['description'] ?? null,
            ':tiers_impliques'     => $data['tiers_impliques'] ?? null,
            ':date_ouverture'      => $data['date_ouverture'],
            ':date_limite_reponse' => !empty($data['date_limite_reponse']) ? $data['date_limite_reponse'] : null,
            ':date_cloture'        => !empty($data['date_cloture']) ? $data['date_cloture'] : null,
            ':statut'              => $data['statut'],
            ':decision'            => $data['decision'] ?? null,
            ':impact_financier'    => !empty($data['impact_financier']) ? $data['impact_financier'] : 0
        ];

        if (!empty($data['fichier_joint'])) {
            $params[':fichier_joint'] = $data['fichier_joint'];
        }

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    // Mettre à jour la décision (Rétrocompatibilité)
    public function updateDecision($data) {
        return $this->addUpdate($data['id'], $data['statut'], $data['decision'] ?? '', $data['fichier_joint'] ?? null);
    }

    // Mettre à jour uniquement le fichier principal du dossier
    public function updateDocument($id, $fileName) {
        $sql = "UPDATE disputes SET fichier_joint = :fichier_joint WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id'            => $id,
            ':fichier_joint' => $fileName
        ]);
    }

    // Supprimer un contentieux
    public function delete($id) {
        $sql = "DELETE FROM disputes WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}