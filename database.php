<?php
class Database {
    private $server = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "cpcStud_db"; // Make sure this matches your database name

    protected $conn;
    protected $errMsg;
    protected $state;

    public function __construct() {
        try {
            $this->conn = new PDO("mysql:host=" . $this->server . ";dbname=" . $this->dbname, $this->username, $this->password);
            $this->conn->exec('set names utf8');
            $this->errMsg = "Connected";
            $this->state = true;
        } catch (PDOException $e) {
            $this->state = false;
            $this->errMsg = "Error: " . $e->getMessage();
        }
    }
    

    protected function getState() {
        return $this->state;
    }

    protected function getErrMsg() {
        return $this->errMsg;
    }

    protected function getDb() {
        return $this->conn;
    }
}
?>
