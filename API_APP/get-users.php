<?php
include_once('conexao.php'); 

$dadosrecebidos = json_decode(file_get_contents("php://input"),true);

	
	$query = "
	select s.id, s.name, s.email, s.phone, s.active from student s"; 
	
  $res = $pdo->query( $query );
	$rows = $res->fetchAll(PDO::FETCH_ASSOC );



	  // $rows['dados'] = $result->fetch_assoc();

	echo json_encode($rows);	 	 

?>