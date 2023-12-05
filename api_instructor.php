<?php
  date_default_timezone_set("America/Fortaleza");
  include_once('connection.php');
  $config = new Config();

  $con = new mysqli($config->host, $config->user, $config->password, $config->db);
  $con->set_charset("UTF8");

  if ($con == ''){
    $message['connection'] = 'Erro ao se conectar com banco de dados.';
    echo json_encode($message);
    die(); 
  }
  $data = json_decode(file_get_contents("php://input"),true);
  if(!$data ){
    $data = $_POST;
  } 
  // recebendo dados do front e do servidor
  $username = $_SERVER['PHP_AUTH_USER'];
  $password = $_SERVER['PHP_AUTH_PW'];

  $tipo = $data['tipo'];

  $name = $data['name'];
  $description = $data['description'];
  $occupation = $data['occupation'];
  $active = $data['active'];
  $id_instructor = $data['id_instructor'];
  $id_user = $data['id_user'];

  $id_instructor_edit = $data['id_instructor_edit'];
  $occupation_edit = $data['occupation_edit'];
  $description_edit = $data['description_edit'];


  if ($tipo == ''){
    echo json_encode('nenhum metodo foi passado');
    die;
  }
  if($tipo == "list"){

    if ($name){
      if ($where == ""){
        $where = " WHERE name LIKE '%$name%' ";
      }else{
        $where .= " AND name LIKE '%$name%' ";
      }
    }

    if ($occupation){
      if ($where == ""){
        $where = " WHERE occupation LIKE '%$occupation%' ";
      }else{
        $where .= " AND occupation LIKE '%$occupation%' ";
      }
    }

    if($active != "" && $active <= 1 ){
      if($where == ""){
        $where = " WHERE active = '$active' ";
      }else{
        $where .= " AND active = '$active' ";
      }
    }

    if($id_instructor){
      if($where == ""){
        $where = " WHERE i.id = '$id_instructor' ";
      }else{
        $where .= " AND i.id = '$id_instructor' ";
      }
    }
  
  
    $query ="SELECT 
              i.id, 
              i.name, 
              i.occupation, 
              i.description,
              CASE 
                WHEN i.active = '0' THEN 'INATIVO'
                WHEN i.active = '1' THEN 'ATIVO'
              END AS 'active'
              FROM instructor i " .$where;
    $result = mysqli_query($con, $query);
    $rows['dados'] = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($rows);
    die();
  }

  if ($tipo == 'listForGroup'){
  // função para listar apenas o nome e  quem pertence ao grupo instrutor
    $query ="SELECT id, username FROM users WHERE user_group = 3 AND active != 0";
    $result = mysqli_query($con, $query);
    $rows['dados'] = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($rows);
    die();
  }

  if($tipo == 'insert'){
    $query = "INSERT INTO instructor
        (id, id_user, name, description, occupation, active) 
        VALUES (NULL,'$id_user', '$name', '$description', '$occupation', '$active')
    ";
    $result = mysqli_query($con, $query);

    if ($result == true) {
      $message['status'] = 'true';
      $message['description'] = 'instrutor inserido com sucesso';
      echo json_encode($message);
    } else {
      $message['status'] = 'false';
      $message['description'] = 'falha oa inserir.';
      echo json_encode($message);
    }
  }

  if($tipo == 'mystudent'){
    $query = "SELECT id, name, instructor_id, active FROM student WHERE instructor_id = $id_instructor ORDER BY active DESC, name";
    $result = mysqli_query($con, $query);
    $rows['dados'] = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($rows);
    die();
  }
  
  if ($tipo == 'countInstructor') {
    $query = "SELECT count(*) as total FROM instructor";
    $result = mysqli_query($con, $query);
    $rows['dados'] = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($rows);
    die();
  }

  if ($tipo == 'countInstructorActive') {
    $query = "SELECT count(*) as total FROM instructor WHERE active = 1";
    $result = mysqli_query($con, $query);
    $rows['dados'] = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($rows);
    die();
  }

  if ($tipo == 'countInstructorInactive') {
    $query = "SELECT count(*) as total FROM instructor WHERE active = 0";
    $result = mysqli_query($con, $query);
    $rows['dados'] = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($rows);
    die();
  }

  if ($tipo == 'update')  {
    $query = "UPDATE instructor SET
                description = '$description_edit',
                occupation = '$occupation_edit'
              WHERE 
                id = '$id_instructor_edit'";
     $result = mysqli_query($con, $query); 
     if ($result == true){
       $message['status'] = 'true';
       $message['description'] = 'instrutor alterado com sucesso.';
       echo json_encode($message);
       die();
     }else{
       $message['status'] = 'false';
       $message['description'] = 'ocorreu um erro ao alterar o instrutor';
       echo json_encode($message);
       die();
     }
  }

  if ($tipo == "deactivate") {
    $query = "UPDATE instructor SET active = '$active' WHERE id = '$id_instructor'";
    $result = mysqli_query($con, $query);
  
    if ($result == true) {
      if ($active == 0) {
        $message['status'] = 'true';
        $message['description'] = 'instrutor desativado com sucesso';
        echo json_encode($message);
      } else {
        $message['status'] = 'true';
        $message['description'] = 'instrutor ativo com sucesso';
        echo json_encode($message);
      }
    } else {
      $message['status'] = 'false';
      $message['description'] = 'falha oa desativar.';
      echo json_encode($message);
    }
  }

?>