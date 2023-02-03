<?php
class Connection {
    private $host = 'localhost';
    private $dbname = 'my-wash';
    private $username = 'root';
    private $password = '';
    private $conn = '';

    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->dbname, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Koneksi Error: ' . $e->getMessage();
        }
        return $this->conn;
    }
}

require_once('Admin.php');
require_once('Laundry.php');
require_once('Service.php');
require_once('Transaction.php');
require_once('Customer.php');