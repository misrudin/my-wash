<?php

class Transaction extends Connection
{
    private $conn;
    private $admin;

    private function placeholders($text, $count=0, $separator=","){
      $result = array();
      if($count > 0){
          for($x=0; $x<$count; $x++){
              $result[] = $text;
          }
      }

      return implode($separator, $result);
  }

    public function __construct($db)
    {
        $this->conn = $db;
        $this->admin = $_COOKIE['cookie_admin'];
    }

    public function get()
    {
      $query = $this->conn->prepare("
        SELECT t.id, t.no_kwitansi, t.created_at, t.status, s.name as service,
        ADDTIME(t.created_at, CONCAT(s.estimate, ':00:00')) as tanggal_ambil, s.estimate,
        ((SELECT SUM(td.price * td.qty) from transaction_detail td WHERE td.transaction_id = t.id) + s.price) as price
        FROM transaction t
        left join service s on s.id = t.service
        where t.admin_id = ?
        order by created_at desc
      ");
      $query->execute([$this->admin]);
      $data = $query->fetchAll();
      return $data;
    }

    public function detail($data)
    {
      $no_kwitansi = $data['no_kwitansi'];
      $query = $this->conn->prepare("
        SELECT t.id, t.no_kwitansi, t.created_at, t.status, s.name as service, s.price as service_price,
        ADDTIME(t.created_at, CONCAT(s.estimate, ':00:00')) as tanggal_ambil, s.estimate,
        ((SELECT SUM(td.price * td.qty) from transaction_detail td WHERE td.transaction_id = t.id) + s.price) as price
        FROM transaction t
        left join service s on s.id = t.service
        where t.admin_id = ? and no_kwitansi = ?
        order by created_at desc
      ");
      $query->execute([$this->admin, $no_kwitansi]);
      $data = $query->fetch();
      return $data;
    }

    public function detailListCucian($data)
    {
      $id = $data['id'];
      $query = $this->conn->prepare("
        SELECT td.transaction_id, td.laundry_id, td.qty, td.price, l.name, (td.qty * td.price) as total_price FROM transaction_detail td
        LEFT JOIN laundry l on l.id = td.laundry_id
        WHERE td.transaction_id = ?
      ");
      $query->execute([$id]);
      $data = $query->fetchAll();
      return $data;
    }

    public function create($data)
    {
      // data customer
      $phone = $data['customer_phone'];
      $name = $data['customer_name'];
      $address = $data['customer_address'];

      $this->conn->beginTransaction();
      $query = $this->conn->prepare("SELECT * FROM customer where phone_number=?");
      $query->execute([
        $phone
      ]);
      $id_pelanggan = null;
      $data_pelanggan = $query->fetch();
      if(empty($data_pelanggan)) {
        $plg = $this->conn->prepare('INSERT INTO customer (phone_number, name, address, admin_id) VALUES (?, ?, ?, ?)');
        $plg->bindParam(1, $phone);
        $plg->bindParam(2, $name);
        $plg->bindParam(3, $address);
        $plg->bindParam(4, $this->admin);

        $plg->execute();
        $id_pelanggan = $this->conn->lastInsertId();
      } else {
        $id_pelanggan = $data_pelanggan['id'];
      }

      // create transaction
      $no_kwitansi = $data['no_kwitansi'];
      $id_serevice = $data['id_service'];
      $status = "PROCCESS";


      $transaksi = $this->conn->prepare('INSERT INTO transaction (no_kwitansi, service, status, customer_id, admin_id) VALUES (?, ?, ?, ?, ?)');
      $transaksi->bindParam(1, $no_kwitansi);
      $transaksi->bindParam(2, $id_serevice);
      $transaksi->bindParam(3, $status);
      $transaksi->bindParam(4, $id_pelanggan);
      $transaksi->bindParam(5, $this->admin);

      $transaksi->execute();
      $id_transaksi = $this->conn->lastInsertId();

      // insert detail transaksi
      $datafields = array('transaction_id', 'laundry_id', 'price', 'qty');
      $body = [];
      foreach($data['laundry'] as $value) {
        array_push($body, [
          'transaction_id'=> $id_transaksi,
          'laundry_id'=> $value['id'],
          'price'=> $value['price'],
          'qty'=> $value['qty'],
        ]);
      }

      $insert_values = array();
      foreach($body as $d){
          $question_marks[] = '('  . $this->placeholders('?', sizeof($d)) . ')';
          $insert_values = array_merge($insert_values, array_values($d));
      }

      $sql = "INSERT INTO transaction_detail (" . implode(",", $datafields ) . ") VALUES " .
            implode(',', $question_marks);

      $stmt = $this->conn->prepare ($sql);
      $stmt->execute($insert_values);

      $this->conn->commit();
      return $transaksi->rowCount();
    }

    public function setToDone($data)
    {
      $id = $data['id'];
      $query = $this->conn->prepare("UPDATE transaction SET status='DONE' WHERE id = ". (int)$id);
      $query->execute();
      return $query->rowCount();
    }

    private function getTotalByStatus($status)
    {
      $query = $this->conn->prepare("
        SELECT count(t.id) as total
        FROM transaction t
        where t.admin_id = ? and t.status = ?
        order by created_at desc;
      ");
      $query->execute([$this->admin, $status]);
      $data = $query->fetch();
      return $data['total'];
    }

    public function getDataDashboard()
    {
      $query = $this->conn->prepare("
        SELECT count(t.id) as total_transaksi,
        SUM(((SELECT SUM(td.price * td.qty) from transaction_detail td WHERE td.transaction_id = t.id) + s.price)) as price
        FROM transaction t
        left join service s on s.id = t.service
        where t.admin_id = ?
        order by created_at desc
      ");
      $query->execute([$this->admin]);
      $data = $query->fetch();

      $dataDashboarad = [
        'total_transaksi'=> $data['total_transaksi'],
        'total_pendapatan'=> $data['price'],
        'total_process' => $this->getTotalByStatus('PROCCESS'),
        'total_done' => $this->getTotalByStatus('DONE'),
      ];
      return $dataDashboarad;
    }
}