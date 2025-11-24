<?php
	/**
	 * 
	 * Pagina de inicio desde donde se inicia el portal
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */


	require "includes/load_main_components.inc.php";
	
	
	
	// Instanciamos la clase Xtemplate con la plantilla base
	$plantilla = new XTemplate("html/index.html");
	
	$plantilla->assign("TITULO_WEB", STATIC_404_TITLE);
	
	
	
	// Instanciamos la clase Xtemplate con la plantilla que va a contener la información del apartado
	$subPlantilla = new XTemplate("html/error404.html");
		
	/**
	 * Asignamos el CSS que corresponde a este apartado
	 */
	$plantilla->assign("SECTION_FILE_CSS", "openbox.css");
	
	$plantilla->assign("ID_VARIACION_CSS", "styleFull_".$_SESSION["id_idioma"]);
	
	

	//Cargamos la información que debe salir diempre
	require "includes/load_structure.inc.php";
	
	
	
	/**
	 * Realizamos todos los parse relacionados con este apartado
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