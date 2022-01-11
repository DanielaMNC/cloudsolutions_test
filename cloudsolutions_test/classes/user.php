<?php

require_once 'database.php';

function validaPhone($phone){
  if(strlen($phone) == 11){
    return true;
  }else{
    return false;
  }
}

function validaDate($date){
  return (bool)preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/", trim($date));
}

function validaEmail($e){
  return (bool)preg_match("`^[a-z0-9!#$%&'*+\/=?^_\`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_\`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$`i", trim($e));
}

require_once 'classes/user.php';

$objUser = new User();
// GET
if(isset($_GET['edit_id'])){
  $id = $_GET['edit_id'];
  $stmt = $objUser->runQuery("SELECT u.*, a.address, a.number, a.complement, a.district, a.city, a.state FROM users u LEFT JOIN address a on u.id=a.id WHERE u.id=:id");
  $stmt->execute(array(":id" => $id));
  $rowUser = $stmt->fetch(PDO::FETCH_ASSOC);
}else{
  $id = null;
  $rowUser = null;
}



// Função que valida o CPF
function validaCPF($cpf)
{	// Verifiva se o número digitado contém todos os digitos
    $cpf = str_pad(preg_replace('[^0-9]', '', $cpf), 11, '0', STR_PAD_LEFT);
	
	// Verifica se nenhuma das sequências abaixo foi digitada, caso seja, retorna falso
    if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999')
	{
	return false;
    }
	else
	{   // Calcula os números para verificar se o CPF é verdadeiro
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf{$c} * (($t + 1) - $c);
            }
 
            $d = ((10 * $d) % 11) % 10;
 
            if ($cpf{$c} != $d) {
                return false;
            }
        }
 
        return true;
    }
}

function formata_cpf($cpf){
  /*
      Pega qualquer CPF eformata

      CPF: 000.000.000-00
  */

  ## Retirando tudo que não for número.
  $cpf = preg_replace("/[^0-9]/", "", $cpf);
  $tipo_dado = NULL;
  if(strlen($cpf)==11){
      $tipo_dado = "cpf";
  }
  if(strlen($cpf)==14){
      $tipo_dado = "cnpj";
  }
  switch($tipo_dado){
      default:
          $cpf_formatado = "Não foi possível definir tipo de dado";
      break;

      case "cpf":
          $bloco_1 = substr($cpf,0,3);
          $bloco_2 = substr($cpf,3,3);
          $bloco_3 = substr($cpf,6,3);
          $dig_verificador = substr($cpf,-2);
          $cpf_formatado = $bloco_1.".".$bloco_2.".".$bloco_3."-".$dig_verificador;
      break;

  }
  return $cpf_formatado;
}

class User {
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

    // Insert user
    public function insert($name, $email, $phone, $cpf, $birth_date){
      try{

        $stmt = $this->conn->prepare("INSERT INTO users (name, email, phone, cpf, birth_date) VALUES(:name, :email, :phone, :cpf, :birth_date)");
        $stmt->bindparam(":name", $name);
        $stmt->bindparam(":email", $email);
        $stmt->bindparam(":phone", $phone);
        $stmt->bindparam(":cpf", $cpf);
        $stmt->bindparam(":birth_date", $birth_date);
        $stmt->execute();

        return $stmt;
      }catch(PDOException $e){
        echo $e->getMessage();
      }
    }

    // Insert address
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


    // Update user
    public function update($name, $email, $phone, $cpf, $birth_date, $id){
        try{
          $stmt = $this->conn->prepare("UPDATE users SET name = :name, email = :email, phone = :phone, cpf = :cpf, birth_date = :birth_date WHERE id = :id");
          $stmt->bindparam(":name", $name);
          $stmt->bindparam(":email", $email);
          $stmt->bindparam(":phone", $phone);
          $stmt->bindparam(":cpf", $cpf);
          $stmt->bindparam(":birth_date", $birth_date);
          $stmt->bindparam(":id", $id);
          $stmt->execute();
          return $stmt;
        }catch(PDOException $e){
          echo $e->getMessage();
        }
    }

    // Update address
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


    // Delete user
    public function delete($id){
      try{
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindparam(":id", $id);
        $stmt->execute();
        return $stmt;
      }catch(PDOException $e){
          echo $e->getMessage();
      }
    }

    // Delete address
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
