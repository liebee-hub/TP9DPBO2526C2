<?php
class DB {
    private PDO $conn;
    private ?PDOStatement $stmt = null;

    public function __construct(
        string $host = 'localhost',
        string $name = 'mvp_kandidat_db',
        string $user = 'root',
        string $pass = ''
    ) {
        $this->conn = new PDO("mysql:host=$host;dbname=$name;charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    public function executeQuery(string $query, array $params = []): bool {
        $this->stmt = $this->conn->prepare($query);
        return $this->stmt->execute($params);
    }

    public function getAllResult(): array {
        return $this->stmt ? $this->stmt->fetchAll() : [];
    }
}
