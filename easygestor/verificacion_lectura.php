<?php
	require "includes/load_main_components.inc.php";
	require "config/constants.php";
	require "config/sections.php";
	
	$db->callProcedure("CALL ed_sp_eletter_envio_aumentar_contador('".generalUtils::escaparCadena(generalUtils::filtrarInyeccionSQL($_GET["id"]))."')");
	
	header("Content-type: image/gif");
	readfile("images/pictures/tracker.gif");
	die();
?>