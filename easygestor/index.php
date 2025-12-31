<?php
	/**
	 * 
	 * Scripts que presenta el formulario de autenticacion al gestor
	 * @author eData
	 * @version 4.0
	 * 
	 */
	require "includes/load_main_components.inc.php";


	//Si el usuario no existe, entonces redirigimos a la pagina principal
	// if(isset($_SESSION["user"])){
	//	generalUtils::redirigir(CURRENT_DOMAIN);
	//}
	
	require "config/dictionary/default.php";
	
	/**
	 * 
	 * Instancia de XTemplate de la pantalla principal, previa a la autentication en el gestor
	 * @var object
	 * 
	 */
	$plantilla=new XTemplate("html/index.html");
	
	//Establecemos titulo
	$plantilla->assign("WEB_TITLE",STATIC_LOGIN_WEB_TITLE);
	
	/**
	 * 
	 * Instancia de XTemplate de la pantalla login
	 * @var object
	 * 
	 */
	$subPlantilla=new XTemplate("html/login.html");
	
	//Construimos bloque $subPlantilla
	$subPlantilla->parse("contenido_principal");
	
	//Exportamos $subPlantilla a $plantilla
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
	if(isset($_GET["code"]) && $_GET["code"]==1){
		//Easy notify
		$plantilla->assign("ITEM_EASYNOTIFY",STATIC_LOGIN_INVALID);
		$plantilla->assign("TYPE_EASYNOTIFY",1);
		$plantilla->parse("contenido_principal.carga_inicial.autoload_easynotify");
		$plantilla->parse("contenido_principal.carga_inicial");
	}
	
	//Construimos bloque $plantilla
	$plantilla->parse("contenido_principal");
	
	//Mostramos bloque $plantilla
	$plantilla->out("contenido_principal");
?>