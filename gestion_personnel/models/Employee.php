<?php
class Employee {
    private $conn;
    private $table = "employees";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function search($search = null, $contrat = null, $poste = null) {
        $query = "SELECT * FROM " . $this->table . " WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $query .= " AND (nom LIKE :search OR prenom LIKE :search OR cni LIKE :search OR telephone LIKE :search OR matricule LIKE :search OR entreprise LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if (!empty($contrat)) {
            $query .= " AND type_contrat = :contrat";
            $params[':contrat'] = $contrat;
        }

        if (!empty($poste)) {
            $query .= " AND intitule_poste = :poste";
            $params[':poste'] = $poste;
        }

        $query .= " ORDER BY id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getPostes() {
        $query = "SELECT DISTINCT intitule_poste FROM " . $this->table . " ORDER BY intitule_poste ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Récupère le prochain ID de la table pour générer le matricule sur 6 chiffres
    public function getNextAutoIncrementId() {
        $query = "SHOW TABLE STATUS LIKE '" . $this->table . "'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch();
        
        if ($row && isset($row['Auto_increment'])) {
            return $row['Auto_increment'];
        }
        
        // Solution de secours si SHOW TABLE STATUS échoue
        $queryMax = "SELECT MAX(id) as max_id FROM " . $this->table;
        $stmtMax = $this->conn->prepare($queryMax);
        $stmtMax->execute();
        $res = $stmtMax->fetch();
        return ($res['max_id'] ?? 0) + 1;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (matricule, entreprise, nom, prenom, etat_civil, adresse, telephone, cni, email, intitule_poste, type_contrat, date_embauche, numero_permis, categories_permis, date_expiration_permis, contrat_pdf) 
                  VALUES (:matricule, :entreprise, :nom, :prenom, :etat_civil, :adresse, :telephone, :cni, :email, :intitule_poste, :type_contrat, :date_embauche, :numero_permis, :categories_permis, :date_expiration_permis, :contrat_pdf)";

        $stmt = $this->conn->prepare($query);
        $this->bindParameters($stmt, $data);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " SET 
                    entreprise = :entreprise,
                    nom = :nom,
                    prenom = :prenom,
                    etat_civil = :etat_civil,
                    adresse = :adresse,
                    telephone = :telephone,
                    cni = :cni,
                    email = :email,
                    intitule_poste = :intitule_poste,
                    type_contrat = :type_contrat,
                    date_embauche = :date_embauche,
                    numero_permis = :numero_permis,
                    categories_permis = :categories_permis,
                    date_expiration_permis = :date_expiration_permis,
                    contrat_pdf = :contrat_pdf
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $this->bindParameters($stmt, $data);

        return $stmt->execute();
    }

    public function delete($id) {
        $query = "SELECT contrat_pdf FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $employee = $stmt->fetch();

        if ($employee && !empty($employee['contrat_pdf'])) {
            $filePath = __DIR__ . '/../uploads/contracts/' . $employee['contrat_pdf'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $deleteQuery = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmtDelete = $this->conn->prepare($deleteQuery);
        $stmtDelete->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmtDelete->execute();
    }

    public function importOrUpdate($data) {
        $query = "SELECT id, contrat_pdf FROM " . $this->table . " WHERE matricule = :matricule LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':matricule', $data['matricule']);
        $stmt->execute();
        $existing = $stmt->fetch();

        if ($existing) {
            $data['contrat_pdf'] = $data['contrat_pdf'] ?? $existing['contrat_pdf'];
            return $this->update($existing['id'], $data);
        } else {
            return $this->create($data);
        }
    }

    private function bindParameters($stmt, $data) {
        $fields = [
            'matricule', 'entreprise', 'nom', 'prenom', 'etat_civil', 'adresse', 
            'telephone', 'cni', 'email', 'intitule_poste', 'type_contrat', 
            'date_embauche', 'numero_permis', 'categories_permis', 
            'date_expiration_permis', 'contrat_pdf'
        ];

        foreach ($fields as $field) {
            $val = $data[$field] ?? null;
            if ($val === null || $val === '') {
                $stmt->bindValue(':' . $field, null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':' . $field, $val);
            }
        }
    }
}