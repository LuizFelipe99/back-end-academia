<?php
//Botão logout, starta a sessao, apaga as variaveis nome e nivel e redireciona para index.
	session_start();
  session_destroy();
	header('location:../academia/index.php');
?>