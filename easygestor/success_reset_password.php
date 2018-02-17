<?php
	
	/**
	 * 
	 * Nos indica que hemos restablecido el password correctamente
	 * @author eData
	 * @version 4.0
	 * 
	 */
	require "includes/load_main_components.inc.php";
	require "config/dictionary/default.php";
	
	/**
	 * 
	 * Instancia de XTemplate de la pantalla principal
	 * @var object
	 * 
	 */
	$plantilla=new XTemplate("html/index.html");
	
	//Establecemos titulo
	$plantilla->assign("WEB_TITLE",STATIC_SUCCESS_RESET_PASSWORD_WEB_TITLE);
	
	/**
	 * 
	 * Instancia de XTemplate de la pantalla success_reset_password
	 * @var object
	 * 
	 */
	$subPlantilla=new XTemplate("html/success_reset_password.html");
	
	//Construimos bloque $subPlantilla
	$subPlantilla->parse("contenido_principal");
	
	//Exportamos $subPlantilla a $plantilla
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
	
	//Construimos bloque $plantilla
	$plantilla->parse("contenido_principal");
	
	//Mostramos bloque $plantilla
	$plantilla->out("contenido_principal");
?>