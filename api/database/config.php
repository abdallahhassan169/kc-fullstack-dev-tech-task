<?php
class Database {
    private $host = "localhost";
    private $db_name = "dc";
    private $username = "root";
    private $password = "";
    public $conn;
 
 public function getConnection() {
        $this->conn = null;

        try {
            // Change DSN for MySQL
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
