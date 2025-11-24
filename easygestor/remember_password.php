<?php
	/**
	 * 
	 * Scripts que presenta el formulario de recordar password
	 * @author eData
	 * @version 4.0
	 * 
	 */
	require "includes/load_main_components.inc.php";
	require "config/dictionary/default.php";



	/**
	 * 
	 * Instancia de XTemplate de la pantalla principal, previa a la autentication en el gestor
	 * @var object
	 */
	$plantilla=new XTemplate("html/index.html");
	
	//Establecemos titulo
	$plantilla->assign("WEB_TITLE",STATIC_REMEMBER_PASSWORD_WEB_TITLE);
	
	/**
	 * 
	 * Instancia de XTemplate de la pantalla remember password
	 * @var object
	 */
	$subPlantilla=new XTemplate("html/remember_password.html");
	
	$subPlantilla->assign("SESSION_ID",md5(uniqid(time())));
	
	//Construimos bloque $subPlantilla
	$subPlantilla->parse("contenido_principal");
	
	//Exportamos $subPlantilla a $plantilla
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
	
	//Construimos bloque $plantilla
	$plantilla->parse("contenido_principal");
	
	//Mostramos bloque $plantilla
	$plantilla->out("contenido_principal");
?>