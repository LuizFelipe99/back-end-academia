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
  $user = $data['user'];
  $password = $data['password'];
  $email = $data['email'];
  $user_group = $data['user_group'];
  $user_id = $data['user_id'];
  $adress = $data['adress'];
  $phone = $data['phone'];
  $id_instructor = $data['id_instructor'];
  $avatar = $data['avatar'];
  $active = $data['active'];
  $id_user = $data['id_user'];
  // $begin_date = $data['begin_date'];
  // $end_date = $data['end_date'];

  $create_at = date("Y.m.d-H.i.s");


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
  if ($tipo == ''){
    echo json_encode('nenhum metodo foi passado');
    die;
  }

  // metodo login
  if($tipo == "login"){
    // pegando usergroup
    $query = "SELECT user_group FROM users WHERE username = '$user' AND password = '$password' AND active != 0";
    $result = mysqli_query($con, $query);
    $rows['dados'] = $result->fetch_assoc();

    // se o grupo do usuario for = 3 (instrutor) ele faz processo de login pegando o id_instructor da tabela instructor
    if ($rows['dados']['user_group'] <= 3){
      $query = "SELECT
      u.username,
      u.user_group,
      u.active,
      u.avatar,
      i.id_user 'id_instructor' 
      FROM users u
      inner join instructor i on i.id_user = u.id
      WHERE u.username = '$user' AND u.password = '$password' AND u.active != 0";
  
      $result = mysqli_query($con, $query);
      $rows['dados'] = $result->fetch_assoc();
      
      $login_user = $rows['dados']['username'];
      $user_group = $rows['dados']['user_group'];
      $id_instructor = $rows['dados']['id_instructor'];
      $avatar = $rows['dados']['avatar'];
  
  
      if ($rows['dados']){
        $rows['status'] = true;
        echo json_encode($rows);
        session_start();
        $_SESSION['username'] = $login_user;
        $_SESSION['user_group'] = $user_group;
        $_SESSION['avatar'] = $avatar;
        $_SESSION['id_instructor'] = $id_instructor;
        die();
      }else{
        $message['status'] = false;
        $message['message'] = 'credenciais invalidas. asdasd';
        echo json_encode($message);
        die();
      }
      // senao for igual a 3 ele faz login sem usar tabela instructor
    }else{
      $query = "SELECT
      u.username,
      u.user_group,
      u.active,
      u.avatar
      FROM users u
      WHERE u.username = '$user' AND u.password = '$password' AND u.active != 0";
  
      $result = mysqli_query($con, $query);
      $rows['dados'] = $result->fetch_assoc();
      
      $login_user = $rows['dados']['username'];
      $user_group = $rows['dados']['user_group'];
      $avatar = $rows['dados']['avatar'];
  
      if ($rows['dados']){
        $rows['status'] = true;
        echo json_encode($rows);
        session_start();
        $_SESSION['username'] = $login_user;
        $_SESSION['user_group'] = $user_group;
        $_SESSION['avatar'] = $avatar;
        die();
      }else{
        $message['status'] = false;
        $message['message'] = 'credenciais invalidas. asdasd';
        echo json_encode($message);
        die();
      }
    }


   
  }
  
  // metodo lisar usuarios
  if ($tipo == "list") {

    if ($user){
      if ($where == ""){
        $where = " WHERE u.username LIKE '%$user%' ";
      }else{
        $where .= " AND u.username LIKE '%$user%' ";
      }
    }

    if($email){
      if($where == ""){
        $where = " WHERE u.email LIKE '%$email%' ";
      }else{
        $where .= " AND u.email LIKE '%$email%' ";
      }
    }

    if($active != "" && $active <= 1 ){
      if($where == ""){
        $where = " WHERE u.active = '$active' ";
      }else{
        $where .= " AND u.active = '$active' ";
      }
    }

    if($user_group > 0 ){
      if($where == ""){
        $where = " WHERE u.user_group = '$user_group' ";
      }else{
        $where .= " AND u.user_group = '$user_group' ";
      }
    }

    if($id_instructor > 0 ){
      if($where == ""){
        $where = " WHERE i.id = '$id_instructor' ";
      }else{
        $where .= " AND i.id = '$id_instructor' ";
      }
    }

    if($id_user){
      if($where == ""){
        $where = " WHERE u.id = '$id_user' ";
      }
    }

    $query = "SELECT 
    u.id,
    u.username,
    u.email,
    case 
		when u.active = 1 then 'ATIVO'
        when u.active = 0 then 'INATIVO'
    end as 'active',
	DATE_FORMAT(u.create_at, '%d/%m/%Y - %H:%i') as  'register',
    u.user_group,
    case 
		when u.user_group = 1 then 'ADM'
        when u.user_group = 2 then 'ALUNO'
        when u.user_group = 3 then 'PERSONAL'
    end as 'group',
	ug.description,
    ud.phone,
    ud.adress
FROM users u 
inner join user_detail ud on ud.user_id = u.id
inner join user_groups ug on ug.id = u.user_group " .$where;
    // $total_results = "SELECT COUNT(*) AS total FROM ($query) results";
    // $total = mysqli_query($con, $total_results);
    // $rows['total'] = $total->fetch_all(MYSQLI_ASSOC)[0]['total'];
    
    // echo json_encode($query);
    // die();
    $total_results = "SELECT COUNT(*) AS total FROM ($query) results";
    $total = mysqli_query($con, $total_results);
    $rows['total'] = $total->fetch_all(MYSQLI_ASSOC)[0]['total'];

    $result = mysqli_query($con, $query);
    $rows['dados'] = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($rows);
    die();
  }

  // metodo atualizar usuario
  if($tipo == "update"){
    // primeiro atualiza a tabela user
    $query = "UPDATE users SET
              username = '$user',
              email = '$email',
              user_group = '$user_group'
            WHERE
              id = '$user_id'
            ";
    $result = mysqli_query($con, $query);
  // logo em seguida atualiza a tabela user_detail
    if ($result == true){
      $query = "UPDATE user_detail SET
                  adress = '$adress',
                  phone = '$phone'
                WHERE 
                  user_id = '$user_id'   
                ";
      $result = mysqli_query($con, $query);

      $message['status'] = 'true';
      $message['description'] = 'dados alterados com sucesso.';
      echo json_encode($message);
    }else{
      $message['status'] = 'false';
      $message['description'] = 'erro ao alterar cadastro.';
      echo json_encode($message);
      die();
    }
  }

  // metodo de criar novo usuario
  if($tipo == "insert"){
    $query = "INSERT INTO users
                (id, username, password, email, create_at, avatar, user_group)
              VALUES(NULL,
                '$user',
                '$password',
                '$email',
                '$create_at',
                '$avatar',
                '$user_group')
              ";
    $result = mysqli_query($con, $query);

    // verifica se foi inserido e pega o id de quem foi inserido
    if($result == true){
      $query = "SELECT id from users WHERE email = '$email'";
      $result = mysqli_query($con, $query); 
      $result = $result->fetch_assoc();
      $id_user = $result['id']; // pega o id de quem foi inserido

      if ($result != ""){
        $query = "INSERT INTO user_detail
        (id, user_id, adress, phone)
        VALUES(NULL,
          '$id_user', '$adress', '$phone')";
        $result = mysqli_query($con, $query);
        $message['status'] = 'true';
        $message['description'] = 'usuario inserido com sucesso';
        echo json_encode($message);
      }else{
        $message['status'] = 'false';
        $message['description'] = 'ocorreu uma falha durante o processo';
        echo json_encode($message);
      }
    }
  }

  // metodo de desativar usuario
  if($tipo == "deactivate"){
    //pega o id do usuario
    $query = "SELECT id from users WHERE id = '$user_id'";
    $result = mysqli_query($con, $query); 
    $result = $result->fetch_assoc();
    $id_user = $result['id']; // pega o id de quem foi inserido
    if($result == false){
      $message['status'] = 'false';
      $message['description'] = 'usuario nao encontrado';
      echo json_encode($message);
      die();
    }

     $query = "UPDATE users SET active = '$active' WHERE id = '$id_user'";
    $result = mysqli_query($con, $query); 
    if ($result == true){
      $message['status'] = 'true';
      $message['description'] = 'usuario desativado.';
      echo json_encode($message);
      die();
    }else{
      $message['status'] = 'false';
      $message['description'] = 'ocorreu um erro ao desativar o usuario';
      echo json_encode($message);
      die();
    }
  }

  if ($tipo == 'countUsers') {
    $query = "SELECT count(*) as total FROM users";
    $result = mysqli_query($con, $query);
    $rows['dados'] = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($rows);
    die();
  }

  if ($tipo == 'countUsersActive') {
    $query = "SELECT count(*) as total FROM users WHERE active = 1";
    $result = mysqli_query($con, $query);
    $rows['dados'] = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($rows);
    die();
  }

  if ($tipo == 'countUsersInactive') {
    $query = "SELECT count(*) as total FROM users WHERE active = 0";
    $result = mysqli_query($con, $query);
    $rows['dados'] = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($rows);
    die();
  }
?>