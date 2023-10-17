<?php
namespace Database;
include_once 'DBConfig.php';
use PDOException;
use PDO;

class DBConnector
{
    private string $host = DB_HOST_NAME;
    private string $dbName = DB_NAME;
    private string $username = DB_NAME;
    private string $password = DB_PASSWORD;
    public $conn;

    public function getConnection(): ?PDO
    {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbName, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
