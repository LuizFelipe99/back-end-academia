<?php
include_once('connection.php');
$config = new Config();

$con = new mysqli($config->host, $config->user, $config->password, $config->db);
$con->set_charset("UTF8");

if ($con == '') {
  $message['connection'] = 'Erro ao se conectar com banco de dados.';
  echo json_encode($message);
  die();
}
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
  $data = $_POST;
}
// recebendo dados do front e do servidor
$username = $_SERVER['PHP_AUTH_USER'];
$password = $_SERVER['PHP_AUTH_PW'];

$tipo = $data['tipo'];
$user_id = $data['user_id'];
$user = $data['user'];

$name = $data['name'];
$age = $data['age'];
$email = $data['email'];
$phone = $data['phone'];
$adress = $data['adress'];
$level = $data['level'];
$instructor_id = $data['instructor_id'];
$active = $data['active'];
$id_student = $data['id_student'];
$contract_id = $data['contract_id'];

$user = $data['user'];
$password = $data['password'];


$name_edit = $data['name_edit'];
$age_edit = $data['age_edit'];
$email_edit = $data['email_edit'];
$phone_edit = $data['phone_edit'];
$adress_edit = $data['adress_edit'];
$level_edit = $data['level_edit'];

// if ($username && $password){
//   $query = "SELECT username, password FROM users WHERE username = '$username' AND password = '$password'";
//   $result = mysqli_query($con, $query);
//   $users = $result->fetch_all(MYSQLI_ASSOC);
// }else{
//   if($user){}else{
//     $message['result'] = 'acesso negado.';
//     echo json_encode($message);
//   die();
//   }
// }
// após validado cai aqui
// verifica qual tipo de ação quer ser tomado, login / listar / procurar
if ($tipo == '') {
  echo json_encode('nenhum metodo foi passado');
  die;
}

if ($tipo == 'list') {

  if ($name){
    if ($where == ""){
      $where = " WHERE s.name LIKE '%$name%' ";
    }else{
      $where .= " AND s.name LIKE '%$name%' ";
    }
  }

  if ($email){
    if ($where == ""){
      $where = " WHERE s.email LIKE '%$email%' ";
    }else{
      $where .= " AND s.email LIKE '%$email%' ";
    }
  }
  
  if($active != "" && $active <= 1 ){
    if($where == ""){
      $where = " WHERE s.active = '$active' ";
    }else{
      $where .= " AND s.active = '$active' ";
    }
  }

  if($level != "" && $level <=3 ){
    if($where == ""){
      $where = " WHERE l.id = '$level' ";
    }else{
      $where .= " AND l.id = '$level' ";
    }
  }

  if($instructor_id > 0 ){
    if($where == ""){
      $where = " WHERE s.instructor_id = '$instructor_id' ";
    }else{
      $where .= " AND s.instructor_id = '$instructor_id' ";
    }
  }

  if($id_student){
    if($where == ""){
      $where = " WHERE s.id = '$id_student' ";
    }else{
      $where .= " AND s.id = '$id_student' ";
    }
  }

  $query = "SELECT
    	s.id,
      s.name,
      s.age,
      s.email,
      s.phone,
      s.adress,
      l.description as level,
      i.name AS instructor,
      l.id as id_instructor,
      case 
        when s.active = '0' then 'INATIVO'
        when s.active  = '1' then 'ATIVO'
	    end as 'active'
    FROM
      student s
    INNER JOIN `level` l ON l.id = s.`level` 
    INNER JOIN instructor i ON s.instructor_id = i.id ".$where;

  $total_results = "SELECT COUNT(*) AS total FROM ($query) results";
  $total = mysqli_query($con, $total_results);
  $rows['total'] = $total->fetch_all(MYSQLI_ASSOC)[0]['total'];

  $result = mysqli_query($con, $query);
  $rows['dados'] = $result->fetch_all(MYSQLI_ASSOC);
  echo json_encode($rows);
  die();
}

if ($tipo == 'insert') {
  $query = "INSERT INTO student
      (id, name, age, email, phone, adress, level, instructor_id, active, contract_id, user, password)
      VALUES(NULL, '$name', '$age', '$email', '$phone', '$adress', '$level', '$instructor_id', '$active', '$contract_id', '$user', '$password')
    ";
  $result = mysqli_query($con, $query);
  if ($result == true) {
    $message['status'] = 'true';
    $message['description'] = 'aluno inserido com sucesso';
    echo json_encode($message);
  } else {
    $message['status'] = 'false';
    $message['description'] = 'falha oa inserir.';
    echo json_encode($message);
  }
}

if ($tipo == 'update') {
  $query = "UPDATE student SET
                name = '$name_edit',
                age = '$age_edit',
                email = '$email_edit',
                phone = '$phone_edit',
                adress = '$adress_edit',
                level = '$level_edit'
              WHERE 
                id = '$id_student'
              ";
  $result = mysqli_query($con, $query);
  if ($result == true) {
    $message['status'] = 'true';
    $message['description'] = 'aluno atualizado com sucesso';
    echo json_encode($message);
  } else {
    $message['status'] = 'false';
    $message['description'] = 'falha oa atualizar.';
    echo json_encode($message);
  }
}

if ($tipo == 'deactivate') {
  $query = "UPDATE student SET active = '$active' WHERE id = '$id_student'";
  $result = mysqli_query($con, $query);

  if ($result == true) {
    if ($active == 0) {
      $message['status'] = 'true';
      $message['description'] = 'aluno desativado com sucesso';
      echo json_encode($message);
    } else {
      $message['status'] = 'true';
      $message['description'] = 'aluno ativo com sucesso';
      echo json_encode($message);
    }
  } else {
    $message['status'] = 'false';
    $message['description'] = 'falha oa desativar.';
    echo json_encode($message);
  }
}

if ($tipo == 'listForInstructor'){
  $query = "SELECT id, name, instructor_id, active FROM student WHERE instructor_id != $instructor_id AND name LIKE '%$user%'";
  $result = mysqli_query($con, $query);
  $rows['dados'] = $result->fetch_all(MYSQLI_ASSOC);
  echo json_encode($rows);
  die();
}

if ($tipo == 'assingStudent') {
  $query = "UPDATE student SET instructor_id = $instructor_id WHERE id = $id_student";
  $result = mysqli_query($con, $query);
  echo json_encode("adicionado com sucesso");
}

if ($tipo == 'removeStudent') {
  $query = "UPDATE student SET instructor_id = 1 WHERE id = $id_student";
  $result = mysqli_query($con, $query);
  echo json_encode("removido com sucesso");
}

if ($tipo == 'countStudent') {
  $query = "SELECT count(*) as total FROM student";
  $result = mysqli_query($con, $query);
  $rows['dados'] = $result->fetch_all(MYSQLI_ASSOC);
  echo json_encode($rows);
  die();
}

if ($tipo == 'countStudentActive') {
  $query = "SELECT count(*) as total FROM student WHERE active = 1";
  $result = mysqli_query($con, $query);
  $rows['dados'] = $result->fetch_all(MYSQLI_ASSOC);
  echo json_encode($rows);
  die();
}

if ($tipo == 'countStudentInactive') {
  $query = "SELECT count(*) as total FROM student WHERE active = 0";
  $result = mysqli_query($con, $query);
  $rows['dados'] = $result->fetch_all(MYSQLI_ASSOC);
  echo json_encode($rows);
  die();
}

?>