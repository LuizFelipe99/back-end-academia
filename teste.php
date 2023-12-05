


<?php
include_once('conexao_teste.php'); 

$dadosrecebidos = json_decode(file_get_contents("php://input"),true);
$user = addslashes($dadosrecebidos['user']);
$password = addslashes($dadosrecebidos['password']);

	
 $query = "SELECT id_instructor, username, user_group, active, avatar FROM users WHERE username = '$user' AND password = '$password' AND active != 0";
 $res = $pdo->query( $query );
 $rows = $res->fetchAll(PDO::FETCH_ASSOC );

    $login_user = $rows['dados']['username'];
    $user_group = $rows['dados']['user_group'];
    $id_instructor = $rows['dados']['id_instructor'];
    $avatar = $rows['dados']['avatar'];
    if ($rows){
      echo json_encode($rows);
      session_start();
      $_SESSION['username'] = $login_user;
      $_SESSION['user_group'] = $user_group;
      $_SESSION['avatar'] = $avatar;
      $_SESSION['id_instructor'] = $id_instructor;
    }else{
      $message['result'] = 'credenciais invalidas.';
      echo json_encode($message);
      die();
    }
// 	echo json_encode($rows);	 	 

?>