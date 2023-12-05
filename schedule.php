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
  $id_user_created = $data['id_user_created'];
  $id_student = $data['id_student'];
  $id_instructor = $data['id_instructor'];
  $date = $data['date'];
  $time = $data['time'];
  $status = $data['status'];
  $limit = $data['limit'];
  $name_student = $data['name_student'];

  $where = "";

  if($limit == "sim"){
    $where = "WHERE s.status not in (0, 2) and s.date >= '2023-07-05' order by s.date ASC LIMIT 10";
  }else{
    $limit = "";
  }

  

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
    
  if($name_student){
    if($where == ""){
      $where = " WHERE st.name LIKE '%$name_student%'";
    }else{
      $where .= " AND st.name LIKE '%$name_student%'";
    }
  }
  if($status && $status <=2 ){
    if($where == ""){
      $where = " WHERE s.status = $status";
    }else{
      $where .= " AND s.status = $status";
    }
  }
  if($id_instructor){
    if($where == ""){
      $where = " WHERE it.id = $id_instructor";
    }else{
      $where .= " AND it.id = $id_instructor";
    }
  }

    $query = "SELECT s.id, st.name, s.date, s.time, it.name 'instructor',
    CASE 
    WHEN s.status = 0 then 'CANCELADO'
    WHEN s.status = 1 then 'PENDENTE'
    WHEN s.status = 2 then 'CONCLUÍDO'
    end as 'status'
    from schedule s 
    inner join student st on st.id = s.id_student 
    inner join instructor it on it.id = s.id_instructor
    $where";

    $total_results = "SELECT COUNT(*) AS total FROM ($query) results";
    $total = mysqli_query($con, $total_results);
    $rows['total'] = $total->fetch_all(MYSQLI_ASSOC)[0]['total'];

    $result = mysqli_query($con, $query);
    $rows['dados'] = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($rows);
  }
?>