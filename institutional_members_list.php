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
	$subPlantilla = new XTemplate("html/institutional_members_list.html");
	
	/**
	 * Asignamos el CSS que corresponde a este apartado
	 */
	$plantilla->assign("SECTION_FILE_CSS", "institutional_members_list.css");
	
	require "includes/load_structure.inc.php";
	
	if(isset($_GET["filtro"]) && $_GET["filtro"]!=""){
		//El primer campo pertenece a la query y el segndo a la fecha
		$camposFiltro=explode("_",$_GET["filtro"]);
		if($camposFiltro[0]!=""){
			$_GET["q"]=$camposFiltro[0];
		}
	}
	
	
	/**
	 * Información del buscador
	 */
	$descripcionBuscador = "";
	$urlBusqueda = "";
	$filtro="#q";
	$esQuery=false;
	//Descripcion
	if(isset($_GET["q"]) && $_GET["q"] != STATIC_FORM_INSTITUTIONAL_MEMBER_SEARCH_TITLE_CONTENT) {
		$descripcionBuscador = generalUtils::escaparCadena($_GET["q"]);
		$subPlantilla->assign("QUERY_VALUE", generalUtils::reeamplazarAntiBarras(htmlspecialchars($_GET["q"])));
		if(trim($_GET["q"]) != "") {
			$urlBusqueda = $_GET["q"];
			$esQuery=true;
		}
	}else{
		$subPlantilla->assign("QUERY_VALUE", STATIC_FORM_INSTITUTIONAL_MEMBER_SEARCH_TITLE_CONTENT);
	}
	
	
	//Reemplazar filtro por valores reales
	if($esQuery){
		$filtro=str_replace("#q",$descripcionBuscador,$filtro);
	}else{
		$filtro="";
	}
	
	
	//Cargamos los miembros institucionales
	$codeProcedure = "CALL ed_sp_web_usuario_web_institucion_obtener_listado(";
	
	$totalItemsPagina = 5;
	$totalPaginasMostrar = 8;
	
	if($filtro!=""){
		$urlBusqueda="-".$filtro;
	}
	
	//Obtenemos la url asociada a noticias
	$resultadoMenuSeo=$db->callProcedure("CALL ed_sp_web_menu_seo_obtener(".$idMenu.",".$_SESSION["id_idioma"].")");
	$datoMenuSeo=$db->getData($resultadoMenuSeo);
	$vectorAtributosMenu["idioma"]=$_SESSION["siglas"];
	$vectorAtributosMenu["id_menu"]=$idMenu;
	$vectorAtributosMenu["seo_url"]=$datoMenuSeo["seo_url"];
	$urlActualAux=generalUtils::generarUrlAmigableMenu($vectorAtributosMenu);
	
	$urlActual= $urlActualAux."-";
	

	
	//$urlActual = "institutional_members_list.php?menu=".$idMenu.$urlBusqueda."&";
	require "includes/load_paginator.php";
	
	$resultMiembrosInstitucionales = $db->callProcedure($codeProcedure);
	if($db->getNumberRows($resultMiembrosInstitucionales) > 0) {
		$cont = 1;
		while($dataMiembrosInstitucionales = $db->getData($resultMiembrosInstitucionales)) {
			$subPlantilla->assign("ITEM_MIEMBRO_INSTITUCIONAL_TITULO", $dataMiembrosInstitucionales["nombre"]);
			$subPlantilla->assign("ITEM_MIEMBRO_INSTITUCIONAL_DESCRIPCION", $dataMiembrosInstitucionales["descripcion_previa"]);
			
			
			//Url detalle
			$vectorAtributosDetalle["idioma"]=$_SESSION["siglas"];
			$vectorAtributosDetalle["id_menu"]=$_GET["menu"];
			$vectorAtributosDetalle["id_detalle"]=$dataMiembrosInstitucionales["id_usuario_web"];
			$vectorAtributosDetalle["seo_url"]=$dataMiembrosInstitucionales["nombre"];
			$subPlantilla->assign("ITEM_MIEMBRO_INSTITUCIONAL_URL", generalUtils::generarUrlAmigableDetalle($vectorAtributosDetalle));
			
			
			//$subPlantilla->assign("ITEM_MIEMBRO_INSTITUCIONAL_URL", "institutional_member_detail.php?menu=".$idMenu."&institutional_member=".$dataMiembrosInstitucionales["id_caso_exito"]);
			
			if($dataMiembrosInstitucionales["imagen"] != "") {
				$subPlantilla->assign("ITEM_MIEMBRO_INSTITUCIONAL_IMAGEN", "files/members/thumb/".$dataMiembrosInstitucionales["imagen"]);
				if($cont % 2 == 0) {
					$subPlantilla->parse("contenido_principal.listado_miembros_institucionales.item_miembro_institucional.imagen_derecha");
				}else{
					$subPlantilla->parse("contenido_principal.listado_miembros_institucionales.item_miembro_institucional.imagen_izquierda");
				}
			}
			
			$subPlantilla->assign("ITEM_MIEMBRO_INSTITUCIONAL_IMAGEN", $dataMiembrosInstitucionales["nombre"]);
			
			$subPlantilla->parse("contenido_principal.listado_miembros_institucionales.item_miembro_institucional");
			$cont ++;
		}
		$subPlantilla->parse("contenido_principal.listado_miembros_institucionales");
	}else{
		$subPlantilla->parse("contenido_principal.no_miembros");
	}
	
	$subPlantilla->assign("MENU_ID", $idMenu);
	
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
	$plantilla->parse("contenido_principal.control_superior");
	$plantilla->parse("contenido_principal.bloque_ready");
	
	
	//Exportamos plantilla secundaria a la principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));

    //Parse inner page content with lefthand menu
    $plantilla->parse("contenido_principal.menu_left");

    //Parseamos y sacamos informacion por pantalla
	$plantilla->parse("contenido_principal");
	$plantilla->out("contenido_principal");
?>