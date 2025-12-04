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
	$subPlantilla = new XTemplate("html/events_list.html");
	
	/**
	 * Asignamos el CSS que corresponde a este apartado
	 */
	$plantilla->assign("SECTION_FILE_CSS", "news_list.css");
	
	require "includes/load_structure.inc.php";
	
	
	
	
		
	if(isset($_GET["filtro"]) && $_GET["filtro"]!=""){
		//El primer campo pertenece a la query y el segndo a la fecha
		$camposFiltro=explode("_",$_GET["filtro"]);
		if($camposFiltro[0]!=""){
			$_GET["q"]=$camposFiltro[0];
		}
		if($camposFiltro[1]!=""){
			$_GET["d"]=$camposFiltro[1];
		}
		if($camposFiltro[2]!=""){
			$_GET["t"]=$camposFiltro[2];
		}
	}
		
	/**
	 * Información del buscador
	 */
	$descripcionBuscador = "";
	$urlBusqueda = "";
	$fechaBusqueda = '';
	$fechaBusquedaAux = "";
	$idTematica = 0;
	$filtro="#q_#d_#t";
	$esQuery=false;
	$esFecha=false;
	$esTematica=false;
	
	//Descripcion
	if(isset($_GET["q"]) && $_GET["q"] != STATIC_FORM_EVENT_SEARCH_TITLE_CONTENT_EVENTS) {
		$descripcionBuscador = generalUtils::escaparCadena($_GET["q"]);
		$subPlantilla->assign("QUERY_VALUE", generalUtils::reeamplazarAntiBarras(htmlspecialchars($_GET["q"])));
		if(trim($_GET["q"]) != "") {
			$urlBusqueda .= "&q=".$_GET["q"];
			$esQuery=true;
		}
	}else{
		$subPlantilla->assign("QUERY_VALUE", STATIC_FORM_EVENT_SEARCH_TITLE_CONTENT_EVENTS);
	}
	
	//Fecha
	if(isset($_GET["d"]) && $_GET["d"] != STATIC_FORM_NEW_SEARCH_DATE) {
		$fechaBusquedaAux=$_GET["d"];
		$fechaBusqueda = generalUtils::conversionFechaFormato($_GET["d"]);
		$subPlantilla->assign("FECHA_VALUE", $_GET["d"]);
		$urlBusqueda .= "&d=".$_GET["d"];
		$esFecha=true;
	}else{
		$fechaBusquedaAux="";
		$subPlantilla->assign("FECHA_VALUE", STATIC_FORM_NEW_SEARCH_DATE);
	}
	
	//Tematica
	if(isset($_GET["t"]) && is_numeric($_GET["t"])) {
		$idTematica = $_GET["t"];
		$subPlantilla->assign("TEMATICA_VALUE", $idTematica);
		$urlBusqueda .= "&t=".$idTematica;
		$esTematica=true;
	}
	
	//Reemplazar filtro por valores reales
	if($esQuery || $esFecha || $esTematica){
		$filtro=str_replace("#q",$descripcionBuscador,$filtro);
		$filtro=str_replace("#d",$fechaBusquedaAux,$filtro);
		$filtro=str_replace("#t",$idTematica,$filtro);
	}else{
		$filtro="";
	}
	
	//Obtenemos la url asociada a eventos
	$resultadoMenuSeo=$db->callProcedure("CALL ed_sp_web_menu_seo_obtener(".$idMenu.",".$_SESSION["id_idioma"].")");
	$datoMenuSeo=$db->getData($resultadoMenuSeo);
	$vectorAtributosMenu["idioma"]=$_SESSION["siglas"];
	$vectorAtributosMenu["id_menu"]=$idMenu;
	$vectorAtributosMenu["seo_url"]=$datoMenuSeo["seo_url"];
	$urlActualAux=generalUtils::generarUrlAmigableMenu($vectorAtributosMenu);
	
	//Si somos archive...
	if($idMenu==603){
		$idHistorico=1;
		$subPlantilla->parse("contenido_principal.bloque_buscador");
		$direccionOrden="DESC";
	}else{
		$idHistorico=0;
		$direccionOrden="ASC";
	}

	$subPlantilla->assign("EVENTO_SECCION_TITULO",$datoMenuSeo["titulo"]);
	
	//Listamos las noticias ordenadas por fecha más actual
	$codeProcedure = "CALL ed_sp_web_agenda_obtener_listado(".$idHistorico.",".$_SESSION["id_idioma"].", '".$descripcionBuscador."', '".$fechaBusqueda."', ".$idTematica.",'".$direccionOrden."', ";
	
	$totalItemsPagina = 10;
	$totalPaginasMostrar = 8;
	
	if($filtro!=""){
		$urlBusqueda="-".$filtro;
	}
	
	$urlActual= $urlActualAux."-";
	
	//$urlActual = "news_list.php.php?menu=".$idMenu.$urlBusqueda."&";
	require "includes/load_paginator.php";
	
	$resultEventos = $db->callProcedure($codeProcedure);
	if($db->getNumberRows($resultEventos) > 0) {
		while($dataEvento = $db->getData($resultEventos)) {
			$filtro="#q_#d_#t";
			$subPlantilla->assign("ITEM_EVENTO_TITULO", $dataEvento["titulo"]);
			$subPlantilla->assign("ITEM_EVENTO_FECHA", generalUtils::conversionFechaFormato($dataEvento["fecha"]));
			$subPlantilla->assign("ITEM_EVENTO_DESCRIPCION", $dataEvento["descripcion_previa"]);
			
			//Url detalle
			$vectorAtributosDetalle["idioma"]=$_SESSION["siglas"];
			$vectorAtributosDetalle["id_menu"]=$_GET["menu"];
			$vectorAtributosDetalle["id_detalle"]=$dataEvento["id_agenda"];
			$vectorAtributosDetalle["seo_url"]=$dataEvento["titulo"];
			$subPlantilla->assign("ITEM_EVENTO_URL", generalUtils::generarUrlAmigableDetalle($vectorAtributosDetalle));
			
			
			//$subPlantilla->assign("ITEM_EVENTO_URL", "events_detail.php?menu=".$idMenu."&event=".$dataEvento["id_agenda"]);
			$subPlantilla->assign("ITEM_EVENTO_TEMATICA_NOMBRE", $dataEvento["tematica"]);
			
			
			$filtro=str_replace("#q",$descripcionBuscador,$filtro);
			$filtro=str_replace("#d",$_GET["d"],$filtro);
			$filtro=str_replace("#t",$dataEvento["id_tematica"],$filtro);
			$urlBusqueda=$filtro;


			$subPlantilla->assign("ITEM_EVENTO_TEMATICA_URL", $urlActual."1".$urlBusqueda);
			
			//$subPlantilla->assign("ITEM_EVENTO_TEMATICA_URL", "events_list.php?menu=".$idMenu.$urlBusqueda."&t=".$dataEvento["id_tematica"]);
			$subPlantilla->parse("contenido_principal.listado_eventos.item_evento");
		}
		$subPlantilla->parse("contenido_principal.listado_eventos");
	}else{
		$subPlantilla->parse("contenido_principal.no_eventos");
	}
	
	$subPlantilla->assign("MENU_VALUE", $idMenu);
	
	//Cargamos el breadcrumb
	require "includes/load_breadcrumb.inc.php";
	
	//Cargamos los menus hijos del lateral derecho
	require "includes/load_menu_left.inc.php";
	
	//Ancho news
	if($totalMenuPadre==0){
		$subPlantilla->assign("ANCHO_EVENTS","style='width:769px;'");
	}
	
	//Combo tematica
	$subPlantilla->assign("COMBO_TEMATICA", generalUtils::construirCombo($db, "CALL ed_sp_web_tematica_agenda_obtener_combo(".$_SESSION["id_idioma"].")", "t", "cmbTematica", $idTematica, "nombre", "id_tematica", STATIC_NEWS_COMBO_TOPIC_TITLE, 0, "class='inputText left' style='width:142px;'"));
		
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