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
	unset($_SESSION["conference_user"]);
	unset($_SESSION["registration_desk"]);
	unset($_SESSION["conference_attendee"]);
	$_SESSION["auth_target"] = "index.php";
	
	//Redirigimos al login
	generalUtils::redirigir("index.php");
?>