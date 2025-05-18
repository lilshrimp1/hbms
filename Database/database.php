<?php

class database
{
    private $host = 'localhost';
    private $username = 'root';
    private $password = 'qwerty';
    private $database = 'hbms';
    public $conn;

    public function __construct()
    {
        try {
            $db = "mysql:host=$this->host;dbname=$this->database;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->conn = new PDO(
                $db,
                $this->username,
                $this->password,
                $options
            );
        } catch (PDOException $e) {
            // Log the error to a file (recommended for production)
            error_log("Database Connection Error: " . $e->getMessage());
            // Or, you could use a more sophisticated logging mechanism

            // Display a user-friendly error message
            echo "A database error occurred. Please try again later.";
            die(); // Stop script execution
        }
    }

    public function setConnection($conn)
    {
        $this->conn = $conn;
        return $this; // Added return
    }

    public function getConnection()
    {
        return $this->conn;
    }

    public function __destruct()
    {
        $this->conn = null;
    }
}
?>