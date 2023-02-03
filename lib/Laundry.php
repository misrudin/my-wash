<?php

class Laundry extends Connection
{
    private $conn;
    private $admin;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->admin = $_COOKIE['cookie_admin'];
    }

    public function get()
    {
      $query = $this->conn->prepare("SELECT * FROM laundry where admin_id = ? order by id desc");
      $query->execute([$this->admin]);
      $data = $query->fetchAll();
      return $data;
    }

    public function create($data)
    {
      $name = $data['name'];
      $price = $data['price'];
      $type = $data['type'];
      $data = $this->conn->prepare('INSERT INTO laundry (name, price, type, admin_id) VALUES (?, ?, ?, ?)');
      $data->bindParam(1, $name);
      $data->bindParam(2, $price);
      $data->bindParam(3, $type);
      $data->bindParam(4, $this->admin);

      $data->execute();
      return $data->rowCount();
    }

    public function update($data)
    {
      $id = $data['id'];
      $name = $data['name'];
      $price = $data['price'];
      $type = $data['type'];

      $query = $this->conn->prepare("UPDATE laundry SET name=?, price=?, type=? WHERE id = ". (int)$id);

      $query->bindParam(1, $name);
      $query->bindParam(2, $price);
      $query->bindParam(3, $type);

      $query->execute();
      return $query->rowCount();
    }

    public function delete($id)
    {
        $query = $this->conn->prepare("DELETE FROM laundry where id = ". (int)$id);
        $query->execute();
        return $query->rowCount();
    }
}