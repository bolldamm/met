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
	$subPlantilla = new XTemplate("html/jobs_list.html");
	
	/**
	 * Asignamos el CSS que corresponde a este apartado
	 */
	// $plantilla->assign("SECTION_FILE_CSS", "jobs_list.css");
	$plantilla->assign("SECTION_FILE_CSS", "openbox.css");
	
	require "includes/load_structure.inc.php";

	//Obtenemos la url asociada a noticias
	$resultadoMenuSeo=$db->callProcedure("CALL ed_sp_web_menu_seo_obtener(".$idMenu.",".$_SESSION["id_idioma"].")");
	$datoMenuSeo=$db->getData($resultadoMenuSeo);
	$vectorAtributosMenu["idioma"]=$_SESSION["siglas"];
	$vectorAtributosMenu["id_menu"]=$idMenu;
	$vectorAtributosMenu["seo_url"]=$datoMenuSeo["seo_url"];
	$urlActualAux=generalUtils::generarUrlAmigableMenu($vectorAtributosMenu);
	
		
	//Listamos todas las ofertas de trabajo activas y ordenadas por fecha
	$codeProcedure = "CALL ed_sp_web_oferta_trabajo_obtener_listado( ";
	
	$totalItemsPagina = 6;
	$totalPaginasMostrar = 8;
	$urlActual= $urlActualAux."-";
	//$urlActual = "jobs_list.php.php?menu=".$idMenu."&";
	require "includes/load_paginator.php";
	
	$resultJobs = $db->callProcedure($codeProcedure);
	
	if($db->getNumberRows($resultJobs) > 0) {
		while($dataJob = $db->getData($resultJobs)) {
			$subPlantilla->assign("ITEM_OFERTA_TITULO", $dataJob["titulo"]);
			$subPlantilla->assign("ITEM_OFERTA_FECHA", generalUtils::conversionFechaFormato($dataJob["fecha"]));
			$subPlantilla->assign("ITEM_OFERTA_DESCRIPCION", $dataJob["descripcion_previa"]);
			
			//Url amigable
			$vectorAtributosDetalle["idioma"]=$_SESSION["siglas"];
			$vectorAtributosDetalle["id_menu"]=$_GET["menu"];
			$vectorAtributosDetalle["id_detalle"]=$dataJob["id_oferta_trabajo"];
			$vectorAtributosDetalle["seo_url"]=$dataJob["titulo"];
			$subPlantilla->assign("ITEM_OFERTA_URL", generalUtils::generarUrlAmigableDetalle($vectorAtributosDetalle));
			
			//$subPlantilla->assign("ITEM_OFERTA_URL", "job_detail.php?menu=".$idMenu."&job=".$dataJob["id_oferta_trabajo"]);
			
			$subPlantilla->parse("contenido_principal.item_oferta_trabajo");
		}
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