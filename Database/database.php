<?php

class database{
    private $host = 'localhost';
    private $username = 'root';
    private $password = 'Leah100m_1thv';
    private $database = 'hbms';
    public $conn;

    public function __construct(){
        try{
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
        }
        catch(PDOException $e){
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function setConnection($conn){
        return $this->conn = $conn;
    }

    public function getConnection(){
        return $this->conn ;
    }

    public function __destruct(){
        $this->conn = null;
    }
}