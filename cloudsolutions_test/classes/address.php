<?php

require_once 'database.php';
require_once 'classes/user.php';
require_once 'classes/address.php';

$objUser = new Address();
// GET
if(isset($_GET['edit_id'])){
  $id = $_GET['edit_id'];
  $stmt = $objUser->runQuery("SELECT * FROM address WHERE id=:id");
  $stmt->execute(array(":id" => $id));
  $rowUser = $stmt->fetch(PDO::FETCH_ASSOC);
}else{
  $id = null;
  $rowUser = null;
}


class Address {
    private $conn;

    // Constructor
    public function __construct(){
      $database = new Database();
      $db = $database->dbConnection();
      $this->conn = $db;
    }


    // Execute queries SQL
    public function runQuery($sql){
      $stmt = $this->conn->prepare($sql);
      return $stmt;
    }

    // Insert
    public function insert_address($address, $number, $complement, $district, $city, $state){
      try{
        $stmt = $this->conn->prepare("INSERT INTO address (address, number, complement, district, city, state) VALUES(:address, :number, :complement, :district, :city, :state)");
        $stmt->bindparam(":address", $address);
        $stmt->bindparam(":number", $number);
        $stmt->bindparam(":complement", $complement);
        $stmt->bindparam(":district", $district);
        $stmt->bindparam(":city", $city);
        $stmt->bindparam(":state", $state);
        $stmt->execute();
        return $stmt;
      }catch(PDOException $e){
        echo $e->getMessage();
      }
    }


    // Update
    public function update_address($address, $number, $complement, $district, $city, $state, $id){
        try{
          $stmt = $this->conn->prepare("UPDATE address SET address = :address, number = :number, complement = :complement, district = :district, city = :city, state = :state WHERE id = :id");
          $stmt->bindparam(":address", $address);
          $stmt->bindparam(":number", $number);
          $stmt->bindparam(":complement", $complement);
          $stmt->bindparam(":district", $district);
          $stmt->bindparam(":city", $city);
          $stmt->bindparam(":state", $state);
          $stmt->bindparam(":id", $id);
          $stmt->execute();
          return $stmt;
        }catch(PDOException $e){
          echo $e->getMessage();
        }
    }


    // Delete
    public function delete_address($id){
      try{
        $stmt = $this->conn->prepare("DELETE FROM address WHERE id = :id");
        $stmt->bindparam(":id", $id);
        $stmt->execute();
        return $stmt;
      }catch(PDOException $e){
          echo $e->getMessage();
      }
    }

    // Redirect URL method
    public function redirect($url){
      header("Location: $url");
    }
}
?>
