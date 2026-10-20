<?php
require_once __DIR__ . '/../config/Database.php';

class User {
    private ?PDO $conn;
    private string $table = "utilisateurs";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function findByEmail(string $email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }
}