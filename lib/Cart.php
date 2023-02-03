<?php

class Cart extends Connection
{
    private $conn;
    private $table = 'cart';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function get($username)
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE order by id asc';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':username', $username);

        $stmt->execute();

        return $stmt;
    }

    public function create($name, $price, $type)
    {
      $data = $this->conn->prepare('INSERT INTO ' . $this->table . ' (name, price, type) VALUES (?, ?, ?)');
      $data->bindParam(1, $name);
      $data->bindParam(2, $price);
      $data->bindParam(3, $type);

      $data->execute();
      return $data->rowCount();
    }

    public function update($id, $name, $price, $type)
    {
      $query = $this->conn->prepare('UPDATE ' . $this->table . ' SET name=?, price=?, type=?, where id=?');

      $query->bindParam(1, $name);
      $query->bindParam(2, $price);
      $query->bindParam(3, $type);
      $query->bindParam(4, $id);

      $query->execute();
      return $query->rowCount();
    }
}