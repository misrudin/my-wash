<?php

class Customer extends Connection
{
    private $conn;
    private $table = 'customer';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function findByPhone($phone)
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE phone_number = :phone';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':phone', $phone);

        $stmt->execute();

        return $stmt->fetch();
    }
}