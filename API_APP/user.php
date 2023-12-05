<?php
include_once('conexao.php'); 

$dadosrecebidos = json_decode(file_get_contents("php://input"),true);
$user = addslashes($dadosrecebidos['user']);
$password = addslashes($dadosrecebidos['password']);

	
	$query = "
	select s.id, s.name, s.email, s.phone, s.active from student s where s.user = '$user'and s.password = '$password'"; 
	// SELECT username, user_group, active, avatar FROM users WHERE username = '$user' AND password = '$password' AND active != 0";

  $res = $pdo->query( $query );
	$rows = $res->fetchAll(PDO::FETCH_ASSOC );



	  // $rows['dados'] = $result->fetch_assoc();

	echo json_encode($rows);	 	 

?>