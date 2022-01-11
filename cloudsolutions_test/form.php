<?php
// Show PHP errors
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

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

// POST
if(isset($_POST['btn_save'])){
  $name   = strip_tags($_POST['name']);
  $email  = strip_tags($_POST['email']);
  $phone   = strip_tags($_POST['phone']);
  $cpf   = strip_tags($_POST['cpf']);
  $birth_date   = strip_tags($_POST['birth_date']);
  $address   = strip_tags($_POST['address']);
  $number   = strip_tags($_POST['number']);
  $complement   = strip_tags($_POST['complement']);
  $district   = strip_tags($_POST['district']);
  $city   = strip_tags($_POST['city']);
  $state   = strip_tags($_POST['state']);

  if(validaCPF($cpf) == true){
    if(validaEmail($email)){
      if(validaPhone($phone)){
        if(validaDate($birth_date)){
          try{
            if($id != null){
              if($objUser->update($name, $email, $phone, $cpf, $birth_date, $id)){
                if($objUser->update_address($address, $number, $complement, $district, $city, $state, $id)){
                  $objUser->redirect('index.php?updated');
                }
              }
            }else{
              if($objUser->insert($name, $email, $phone, $cpf, $birth_date)){
                if($objUser->insert_address($address, $number, $complement, $district, $city, $state)){
                  $objUser->redirect('index.php?inserted');
                }else{
                  $objUser->redirect('index.php?error');
                }
              }else{
                $objUser->redirect('index.php?error');
              }
            }
          }catch(PDOException $e){
            echo $e->getMessage();
          }
        }else{
          echo "<script>alert('Invalid date!');</script>";}
      }else{
        echo "<script>alert('Invalid phone!');</script>";}
    }else{
      echo "<script>alert('invalid e-mail!');</script>";}
  }else{
    echo "<script>alert('invalid cpf!');</script>";
  }

}

?>

<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript">	
		
  $(document).ready(function () {
  
    $.getJSON('database/estados_cidades.json', function (data) {

      var items = [];
      var options = '<option value="">escolha um estado</option>';	

      $.each(data, function (key, val) {
        options += '<option value="' + val.nome + '">' + val.nome + '</option>';
      });					
      $("#state").html(options);				
      
      $("#state").change(function () {				
      
        var options_cidades = '';
        var str = "";					
        
        $("#state option:selected").each(function () {
          str += $(this).text();
        });
        
        $.each(data, function (key, val) {
          if(val.nome == str) {							
            $.each(val.cidades, function (key_city, val_city) {
              options_cidades += '<option value="' + val_city + '">' + val_city + '</option>';
            });							
          }
        });

        $("#city").html(options_cidades);
        
      }).change();		
    
    });
  
  });
		
</script>

<!doctype html>
<html lang="pt-br">
    <head>
        <!-- Head metas, css, and title -->
        <?php require_once 'includes/head.php'; ?>
    </head>
    <body>
        <!-- Header banner -->
        <?php require_once 'includes/header.php'; ?>
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar menu -->
                <?php require_once 'includes/sidebar.php'; ?>
                <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                  <h1 style="margin-top: 10px">Add / Edit Users</h1>
                  <p>Required fields are in (*)</p>
                  <form  method="post">
                    <div class="form-group">
                        <label for="name">Name *</label>
                        <input  class="form-control" type="text" name="name" id="name" placeholder="First Name and Last Name" value="<?php isset($rowUser['name']) ? print($rowUser['name']) : ((isset($_POST['name']))?print($_POST['name']):0);?>" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input  class="form-control" type="text" name="email" id="email" placeholder="type your e-mail" value="<?php isset($rowUser['email']) ? print($rowUser['email']) : ((isset($_POST['email']))?print($_POST['email']):0);?>" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="phone">phone *</label>
                        <input  class="form-control" type="text" name="phone" id="phone" placeholder="put only numbers. Please, include the dd code and 9 before your number" value="<?php isset($rowUser['phone']) ? print($rowUser['phone']) : ((isset($_POST['phone']))?print($_POST['phone']):0);?>" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="cpf">CPF *</label>
                        <input  class="form-control" type="text" name="cpf" id="cpf" placeholder="put only numbers" value="<?php isset($rowUser['cpf']) ? print($rowUser['cpf']) : ((isset($_POST['cpf']))?print($_POST['cpf']):0);?>" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="birth_date">Birth date *</label>
                        <input  class="form-control" type="text" name="birth_date" id="birth_date" placeholder="the date is accept in the following format: XXXX-XX-XX (Year-Month-day)" value="<?php isset($rowUser['birth_date']) ? print($rowUser['birth_date']) : ((isset($_POST['birth_date']))?print($_POST['birth_date']):0);?>" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="address">Address *</label>
                        <input  class="form-control" type="text" name="address" id="address" placeholder="" value="<?php isset($rowUser['address']) ? print($rowUser['address']) : ((isset($_POST['address']))?print($_POST['address']):0);?>" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="number">Number *</label>
                        <input  class="form-control" type="text" name="number" id="number" placeholder="" value="<?php isset($rowUser['number']) ? print($rowUser['number']) : ((isset($_POST['number']))?print($_POST['number']):0);?>" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="complement">Complement *</label>
                        <input  class="form-control" type="text" name="complement" id="complement" placeholder="" value="<?php isset($rowUser['complement']) ? print($rowUser['complement']) : ((isset($_POST['complement']))?print($_POST['complement']):0);?>" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="district">District *</label>
                        <input  class="form-control" type="text" name="district" id="district" placeholder="" value="<?php isset($rowUser['district']) ? print($rowUser['district']) : ((isset($_POST['district']))?print($_POST['district']):0);?>" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="state">Estado *</label>
                        <select id="state" name="state" class="form-control" required maxlength="100">
                          <option value="<?php isset($rowUser['state']) ? print($rowUser['state']) : ((isset($_POST['state']))?print($_POST['state']):0);?>"></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="city">Cidade *</label>
                        <select id="city" name="city" class="form-control" required maxlength="100">
                          <option value="<?php isset($rowUser['city']) ? print($rowUser['city']) : ((isset($_POST['city']))?print($_POST['city']):0);?>"></option>
                        </select>
                    </div>
                    <input class="btn btn-primary mb-2" type="submit" name="btn_save" value="Save">
                  </form>
                </main>
            </div>
        </div>
        <!-- Footer scripts, and functions -->
        <?php require_once 'includes/footer.php'; ?>
    </body>
</html>
