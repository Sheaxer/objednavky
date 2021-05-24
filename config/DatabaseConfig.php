<?php


class DatabaseConfig
{
    private string $host="localhost";
    private string $db_name="tss_zadanie";
    private string $username = "tss";
    private string $password = "test";
    private string $port = "3306";
    public ?PDO $conn = null;

    public function getConnection(): ?PDO
    {
        try {
            $this->conn = new PDO("mysql:host=$this->host; dbname=$this->db_name; port=$this->port",
            $this->username, $this->password);
            $this->conn->exec("set names utf8");
        }catch (PDOException $exception)
        {
            echo "Connection error ". $exception->getMessage();
            return null;
        }
        return  $this->conn;
    }
}
