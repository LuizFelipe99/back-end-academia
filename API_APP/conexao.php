<?php
 
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('America/Sao_Paulo');
 
$HostName = "localhost";
$DatabaseName = "gymdev68_gym";
$User = "gymdev68_felipe";
$Password = "TKV.#REh8hB[";
// $Password = "CqjuG@{X&9t+";

try{
	$pdo = new PDO("mysql:host=$HostName;dbname=$DatabaseName", "$User", "$Password");
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e){
	echo 'Erro ao conectar:'.$e;
}
?>