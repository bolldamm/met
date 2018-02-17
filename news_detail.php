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
	
	// Instanciamos la clase Xtemplate con la plantilla que va a contener la información del apartado
	$subPlantilla = new XTemplate("html/new_detail.html");
	
	/**
	 * Asignamos el CSS que corresponde a este apartado
	 */
	$plantilla->assign("SECTION_FILE_CSS", "new_detail.css");
	
	if(isset($_GET["elemento"])){
		$_GET["new"]=$_GET["elemento"];
	}
	
	require "includes/load_structure.inc.php";
	
		
	//Comprobamos si existe la noticia y la presentamos
	if(isset($_GET["new"]) && is_numeric($_GET["new"])) {
		$resultNoticia = $db->callProcedure("CALL ed_sp_web_noticia_obtener(".$_SESSION["id_idioma"].", ".$_GET["new"].")");
		if($db->getNumberRows($resultNoticia) > 0) {
			$dataNoticia =  $db->getData($resultNoticia);
			
			$subPlantilla->assign("ITEM_NOTICIA_TITULO", $dataNoticia["titulo"]);
			$subPlantilla->assign("ITEM_NOTICIA_DESCRIPCION", $dataNoticia["descripcion_completa"]);
			$subPlantilla->assign("ITEM_NOTICIA_FECHA", generalUtils::conversionFechaFormato($dataNoticia["fecha"]));
			
			/**** INICIO: breadcrumb ****/
			$vectorAtributosDetalle["idioma"] = $_SESSION["siglas"];
			$vectorAtributosDetalle["id_menu"] = $idMenu;
			$vectorAtributosDetalle["id_detalle"] = $dataNoticia["id_noticia"];
			$vectorAtributosDetalle["seo_url"] = $dataNoticia["titulo"];
			$breadCrumbUrlDetalle = generalUtils::generarUrlAmigableDetalle($vectorAtributosDetalle);
			$breadCrumbDescripcionDetalle = $dataNoticia["titulo"];
			/**** FINAL: breadcrumb ****/
		}else{
			generalUtils::redirigir(CURRENT_DOMAIN);
		}
	}else{
		generalUtils::redirigir(CURRENT_DOMAIN);
	}
	
	//Cargamos el breadcrumb
	require "includes/load_breadcrumb.inc.php";
		
	//Cargamos los menus hijos del lateral derecho
	require "includes/load_menu_left.inc.php";
	
	//Cargamos el slider en caso de que tenga imagenes
	require "includes/load_slider.inc.php";
	
	$subPlantilla->parse("contenido_principal");
	
	/**
	 * Realizamos todos los parse realcionados con este apartado
	 */
	$plantilla->parse("contenido_principal.css_form");
	$plantilla->parse("contenido_principal.script_calendar");
	$plantilla->parse("contenido_principal.bloque_ready.calendario");
	$plantilla->parse("contenido_principal.bloque_ready");
	$plantilla->parse("contenido_principal.control_superior");
	
	
	//Exportamos plantilla secundaria a la principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));

    //Parse inner page content with lefthand menu
    $plantilla->parse("contenido_principal.menu_left");

    //Parseamos y sacamos informacion por pantalla
	$plantilla->parse("contenido_principal");
	$plantilla->out("contenido_principal");
?>