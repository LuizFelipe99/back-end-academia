<?php

class Config{
	public $host = 'localhost';
	public $user = 'gymdev68_felipe';
	public $password = 'TKV.#REh8hB[';
	// public $password = 'CqjuG@{X&9t+';
	public $db = 'gymdev68_gym';
}
$config = new Config();
$con = new mysqli($config->host, $config->user, $config->password, $config->db);
$con->set_charset("UTF8");

// $con=mysqli_connect(
	// 	"localhost", //host
	// 	"root", //usser
	// 	"felipe84221635", //password
	// 	"academia" //database
	// );
?>