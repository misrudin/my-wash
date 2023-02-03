<?php

class Admin extends Connection
{
    private $conn;
    private $table = 'admin';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function login($username, $password)
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE username = :username AND password = :password';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);

        $stmt->execute();

        return $stmt;
    }

    public function findByUsername($username)
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE username = :username';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':username', $username);

        $stmt->execute();

        return $stmt;
    }

    public function register($name, $username, $password)
    {
      $data = $this->conn->prepare('INSERT INTO admin (name, username, password) VALUES (?, ?, ?)');
      $data->bindParam(1, $name);
      $data->bindParam(2, $username);
      $data->bindParam(3, $password);

      $data->execute();
      return $data->rowCount();
    }
}