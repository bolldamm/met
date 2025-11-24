<?php
	/**
	 * 
	 * Pagina de inicio desde donde se inicia el portal
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */

	require "includes/load_main_components.inc.php";
	//require_once "environment_config.php"; //load file for toggling PHP error reporting
	
	// Instanciamos la clase Xtemplate con la plantilla base
	$plantilla = new XTemplate("html/index.html");
	
	// Instanciamos la clase Xtemplate con la plantilla que va a contener la información del apartado
	$subPlantilla = new XTemplate("html/remember_password.html");
	
	/**
	 * Asignamos el CSS que corresponde a este apartado
	 */
	$plantilla->assign("SECTION_FILE_CSS", "remember_password.css");
			
	/**
	 * Cargamos la información que ha de aparecer siempre como la cabecera, el pie u otro contenido
	 */
	;
	require "includes/load_structure.inc.php";
	
	/**** INICIO: breadcrumb ****/
	$breadCrumbUrlDetalle = $_SERVER["REQUEST_URI"];
	$breadCrumbDescripcionDetalle = STATIC_REMEMBER_PASSWORD_TITLE;
	/**** FINAL: breadcrumb ****/


	//Cargamos el breadcrumb
	$idMenu=null;
	require "includes/load_breadcrumb.inc.php";
	
	//Captcha
	$subPlantilla->assign("SESSION_ID", md5(uniqid(time())));

	
	$plantilla->parse("contenido_principal.control_superior");
	$plantilla->parse("contenido_principal.validar_remember_password");
	
	/**
	 * 
	 * En muchos casos nos encntramos en que hay algun script o estilo css que no deaseamos
	 * ejecturar en una pagina determinada.
	 * Dese aquí parsearemos todos aquellos ficheros que se encuentren dentro del tag <header>
	 * que deseamos que se ejecuten.
	 * 
	 */
	
	$subPlantilla->parse("contenido_principal");
	//Exportamos plantilla secundaria a la principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));

    //Parse inner page content with lefthand menu
    $plantilla->parse("contenido_principal.menu_left");

    //Parseamos y sacamos informacion por pantalla
	$plantilla->parse("contenido_principal");
	$plantilla->out("contenido_principal");
?>