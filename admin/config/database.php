<?php
class Database {
    private $host = 'localhost';
    private $username = 'blusolv_db';
    private $password = 'blusolv_db';
    private $database = 'blusolv_db';
    public $connection;
    
    public function __construct() {
        $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database);
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }
    
    public function getConnection() {
        return $this->connection;
    }
}


?>