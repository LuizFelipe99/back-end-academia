<?php
include_once('conexao.php'); 

$dadosrecebidos = json_decode(file_get_contents("php://input"),true);
$idUser = addslashes($dadosrecebidos['idUser']);
// $password = addslashes($dadosrecebidos['password']);
// $request = addslashes($dadosrecebidos['request']);

// if($request == 'login'){
	
	$query = "SELECT stu.name,
							stu.age,
							stu.age,
							stu.email,
							stu.phone,
							stu.adress,
							stu.active,
							ct.name as 'contract',
							inst.name AS 'instructor',
							inst.description,
							lvl.description as 'level'
						FROM student stu
						INNER JOIN instructor inst ON inst.id = stu.instructor_id
						INNER JOIN level lvl on lvl.id = stu.level
						INNER JOIN contract ct on ct.id_contract = stu.contract_id
						WHERE stu.id = $idUser";


  $res = $pdo->query( $query );
	$rows = $res->fetchAll(PDO::FETCH_ASSOC );

	  // $rows['dados'] = $result->fetch_assoc();

	echo json_encode($rows);	 	 

// }
?>