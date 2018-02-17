<?php
	/**
	 * 
	 * Realizamos la desconexion del gestor
	 * @author eData
	 * @version 4.0
	 * 
	 */

	session_start();
	require "classes/generalUtils.php";
	unset($_SESSION["met_user"]);
	
	//Redirigimos al login
	generalUtils::redirigir("index.php");
?>