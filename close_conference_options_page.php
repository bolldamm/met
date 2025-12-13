<?php
	session_start();
	require "classes/generalUtils.php";
	unset($_SESSION["conference_user"]);
	
	//Redirect to home page
	generalUtils::redirigir("index.php");
?>