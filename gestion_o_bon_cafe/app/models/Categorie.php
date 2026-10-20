<?php
class Categorie {
    private $conn;
    private $table = "categories";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Récupérer toutes les catégories
    public function listerTous() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ajouter une catégorie
    public function ajouter($nom, $description) {
        $query = "INSERT INTO " . $this->table . " (nom, description) VALUES (:nom, :description)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nom", $nom);
        $stmt->bindParam(":description", $description);
        return $stmt->execute();
    }

    // Supprimer une catégorie
    public function supprimer($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}