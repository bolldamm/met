<?php
	/**
	 * 
	 * Si no estamos logueado, enviamos al usuario fuera del gestor de manera inmediata.
	 * 
	 */

	if(!isset($_SESSION["user"])){
		if(isset($esAjax)){
			exit();	
		}else{
			generalUtils::redirigir("index.php");
		}
	}
?>