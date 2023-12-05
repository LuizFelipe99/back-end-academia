<?php
include_once('conexao.php'); 

$dadosrecebidos = json_decode(file_get_contents("php://input"),true);
$num_ficha = addslashes($dadosrecebidos['num_ficha']);
$id_user = addslashes($dadosrecebidos['id_user']);
// $password = addslashes($dadosrecebidos['password']);
// $request = addslashes($dadosrecebidos['request']);

// if($request == 'login'){
	
	$query = "SELECT ds.num_ficha,
	ex.name AS 'exercice',
	ex.description AS 'description',
	ex.level_id,
	s.id,
	ds.serie,
	CASE
			WHEN s.level = 1 THEN 'INICIANTE'
			WHEN s.level = 2 THEN 'MEDIANO'
			WHEN s.level = 3 THEN 'AVANÇADO'
	END AS 'level',
	st.name AS 'instructor',
	ds.weight,
	ds.repetition,
	ds.rest,
	ds.equipment
FROM datasheet ds
INNER JOIN exercices ex ON ex.id = ds.id_exercice
INNER JOIN student s ON s.id = ds.id_student
INNER JOIN instructor st ON st.id = s.instructor_id
WHERE ds.id_student = $id_user
AND ds.num_ficha = $num_ficha";


  $res = $pdo->query( $query );
	$rows = $res->fetchAll(PDO::FETCH_ASSOC );

	  // $rows['dados'] = $result->fetch_assoc();

	echo json_encode($rows);	 	 

// }
?>