<?php
class Transaction {
    private $conn;
    private $table = "transactions";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                    (date_transaction, type, libelle, montant, rubrique, beneficiaire, banque, cessionnaire, nombre_terrain, prix_unitaire, commission, type_paiement) 
                  VALUES 
                    (:date_transaction, :type, :libelle, :montant, :rubrique, :beneficiaire, :banque, :cessionnaire, :nombre_terrain, :prix_unitaire, :commission, :type_paiement)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }

    // Récupérer une transaction par son ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Mettre à jour une transaction
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET type = :type, date_transaction = :date_transaction, libelle = :libelle, 
                      rubrique = :rubrique, montant = :montant, banque = :banque, beneficiaire = :beneficiaire,
                      cessionnaire = :cessionnaire, nombre_terrain = :nombre_terrain, 
                      prix_unitaire = :prix_unitaire, commission = :commission, type_paiement = :type_paiement 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    // Supprimer une transaction
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    public function getFiltered($filters = []) {
        $query = "SELECT * FROM " . $this->table . " WHERE 1=1";
        $params = [];

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query .= " AND date_transaction BETWEEN :start_date AND :end_date";
            $params['start_date'] = $filters['start_date'];
            $params['end_date'] = $filters['end_date'];
        }
        if (!empty($filters['type'])) {
            $query .= " AND type = :type";
            $params['type'] = $filters['type'];
        }
        if (!empty($filters['rubrique'])) {
            $query .= " AND rubrique = :rubrique";
            $params['rubrique'] = $filters['rubrique'];
        }
        if (!empty($filters['beneficiaire'])) {
            $query .= " AND beneficiaire = :beneficiaire";
            $params['beneficiaire'] = $filters['beneficiaire'];
        }
        if (!empty($filters['banque'])) {
            $query .= " AND banque = :banque";
            $params['banque'] = $filters['banque'];
        }

        $query .= " ORDER BY date_transaction DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotals() {
        $query = "SELECT 
                    SUM(CASE WHEN type = 'entree' THEN montant ELSE 0 END) as total_entrees,
                    SUM(CASE WHEN type = 'sortie' THEN montant ELSE 0 END) as total_sorties 
                  FROM (
                    SELECT type, montant FROM " . $this->table . "
                    UNION ALL
                    SELECT 'sortie' as type, commission as montant FROM " . $this->table . " WHERE rubrique = 'Vente de terrain' AND commission > 0 AND commission IS NOT NULL
                  ) as combined";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTotalsByBank($bank) {
        $query = "SELECT 
                    SUM(CASE WHEN type = 'entree' THEN montant ELSE 0 END) as entrees,
                    SUM(CASE WHEN type = 'sortie' THEN montant ELSE 0 END) as sorties 
                  FROM (
                    SELECT type, montant, banque FROM " . $this->table . "
                    UNION ALL
                    SELECT 'sortie' as type, commission as montant, banque FROM " . $this->table . " WHERE rubrique = 'Vente de terrain' AND commission > 0 AND commission IS NOT NULL
                  ) as combined WHERE banque = :bank";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['bank' => $bank]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAvailableYears() {
        $query = "SELECT DISTINCT YEAR(date_transaction) as annee FROM " . $this->table . " ORDER BY annee DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $years = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return empty($years) ? [date('Y')] : $years;
    }

    public function getAllRubriques() {
        return [
            'Loyer', 'Coopérative Poste', 'Remboursement prêt', 'Prêt bancaire (Entrant)', 
            'Vente de terrain', 'Perso (Entrée)', 'CCP (Entrée)', 'B9 (Entrée)', 'Retrait', 'Autre revenu',
            'WAK', 'YOFF', 'MCD', 'Transport', 'Entretien',
            'Gardiennage', 'Frais de notaire', 'Niagga', 'Prêt accordé à un tiers (Sortant)',
            'Versement', 'Perso (Extra)', 'Perso (Dépenses)', 'CCP (Sortie)', 'B9 (Sortie)', 'Autre dépense'
        ];
    }

    public function getAllBeneficiaries() {
        return [
            'MARIAMA', 'THIERNO', 'ABD.AZIZ', 'MATY', 'ABDALALAH',
            'MME SECK', 'OUSMANE', 'WOULY', 'BAAY', 'KADOU',
            'BADOU', 'BINETA', 'NGOULOU', 'THIATHIOU', 'HABY'
        ];
    }

    public function getBeneficiaryReport($beneficiary, $year = null, $month = null) {
        $query = "SELECT * FROM " . $this->table . " WHERE beneficiaire = :beneficiary";
        $params = ['beneficiary' => $beneficiary];

        if (!empty($year)) {
            $query .= " AND YEAR(date_transaction) = :year";
            $params['year'] = $year;
        }
        if (!empty($month)) {
            $query .= " AND MONTH(date_transaction) = :month";
            $params['month'] = $month;
        }

        $query .= " ORDER BY date_transaction DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRubriqueSummary($year = null, $month = null) {
        $query = "SELECT type, rubrique, SUM(montant) as total FROM (
                    SELECT type, rubrique, montant, date_transaction FROM " . $this->table . "
                    UNION ALL
                    SELECT 'sortie' as type, 'Commission (Vente terrain)' as rubrique, commission as montant, date_transaction FROM " . $this->table . " WHERE rubrique = 'Vente de terrain' AND commission > 0 AND commission IS NOT NULL
                  ) as combined WHERE 1=1";
        $params = [];

        if (!empty($year)) {
            $query .= " AND YEAR(date_transaction) = :year";
            $params['year'] = $year;
        }
        if (!empty($month)) {
            $query .= " AND MONTH(date_transaction) = :month";
            $params['month'] = $month;
        }

        $query .= " GROUP BY type, rubrique ORDER BY rubrique ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDailyReportByRubrique($year = null, $month = null) {
        $query = "SELECT * FROM " . $this->table . " WHERE 1=1";
        $params = [];

        if (!empty($year)) {
            $query .= " AND YEAR(date_transaction) = :year";
            $params['year'] = $year;
        }
        if (!empty($month)) {
            $query .= " AND MONTH(date_transaction) = :month";
            $params['month'] = $month;
        }

        $query .= " ORDER BY date_transaction DESC, id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>