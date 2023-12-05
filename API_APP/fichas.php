<?php
include_once('conexao.php'); 

$dadosrecebidos = json_decode(file_get_contents("php://input"),true);
$idUser = addslashes($dadosrecebidos['idUser']);
// $password = addslashes($dadosrecebidos['password']);
// $request = addslashes($dadosrecebidos['request']);

// if($request == 'login'){
	
	$query = "SELECT 
	ds.num_ficha,
	ds.dt_created,
	ds.focus,
	CASE
			WHEN s.level = 1 THEN 'INICIANTE'
			WHEN s.level = 2 THEN 'MEDIANO'
			WHEN s.level = 3 THEN 'AVANÇADO'
	END AS 'level',
	st.name AS 'instructor'
FROM datasheet ds
INNER JOIN exercices ex ON ex.id = ds.id_exercice
INNER JOIN student s ON s.id = ds.id_student
INNER JOIN instructor st ON st.id = s.instructor_id
WHERE ds.id_student = $idUser
GROUP BY ds.num_ficha";


  $res = $pdo->query( $query );
	$rows = $res->fetchAll(PDO::FETCH_ASSOC );

	  // $rows['dados'] = $result->fetch_assoc();

	echo json_encode($rows);	 	 

// }
?>