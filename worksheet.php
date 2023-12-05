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


  $where = "";

  

// criando função para criar uma nova agenda, mas antes tem que verificar se ja existe
  if ($tipo == 'insert') {
    $query = "SELECT id FROM schedule WHERE id_instructor = '$id_instructor' AND `date` = '$date' AND `time` = '$time' ";
    $result = mysqli_query($con, $query);
    $result = $result->fetch_assoc();
    // caso nao encontre nada, segue o fluxo de inserir novo agendamento
    if ($result == false){
      $query = "INSERT INTO schedule 
        (id, id_user_created, id_student, id_instructor, date, time, status)
        VALUES(
        NULL,
        '$id_user_created',
        '$id_student',
        '$id_instructor',
        '$date',
        '$time',
        '1')";
      $result = mysqli_query($con, $query);
      $message['status'] = true;
      $message['message'] = "Agendado com sucesso";
      echo json_encode($message);
      die();
    }else{ // mas se ecnontrar algum resultado ele não poderá agendar horario
      $message['status'] = false;
      $message['message'] = "Horario ocupado";
      echo json_encode($message);
      die();
    }
  }

  //listando as ultimas 10 agendas
  if ($tipo == 'list') {
    $query = "SELECT 
      s.name 'student', 
      s.instructor_id,
      i.name 'instructor',
      d.num_ficha
      from datasheet d 
          inner join student s on s.id = d.id_student 
          inner join instructor i on i.id = s.instructor_id 
      GROUP by s.id";

    $total_results = "SELECT COUNT(*) AS total FROM ($query) results";
    $total = mysqli_query($con, $total_results);
    $rows['total'] = $total->fetch_all(MYSQLI_ASSOC)[0]['total'];

    $result = mysqli_query($con, $query);
    $rows['dados'] = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($rows);
  }
?>