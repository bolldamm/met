<?php
	/**
	 * 
	 * Presentamos por pantalla las migas de pan
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */

	/**
	 * BREADCRUMB
	 */
	$vectorMenuId = Array();
	$vectorMenuNombre = Array();
	$vectorMenuURL = Array();
	//Llamamos al procedimiento que nos devolvera todos los elemntos

	if(isset($idMenu)){
		$resultBreadcrumb = $db->callProcedure("CALL ed_sp_web_menu_breadcrumb(null, ".$idMenu.", ".$_SESSION["id_idioma"].",'','','','','','','','')");
		$datoBreadcrumb = $db->getData($resultBreadcrumb);
	
	
		//Generamos vectores con los id y nombres del menu de los datos separados por >
		$explodeMenuId = explode("~", $datoBreadcrumb["id_menu"]);
		$explodeModuleId = explode("~", $datoBreadcrumb["id_modulo"]);
	
		//$explodeMenuIdCopia = explode("~", $datoBreadcrumb["id_menu_copia"]);
		$explodeMenuNombre = explode("~", $datoBreadcrumb["nombre_menu"]);
		$explodeMenuDescripcion = explode("~", $datoBreadcrumb["descripcion_menu"]);
		$explodeMenuUrl = explode("~", $datoBreadcrumb["url"]);
		$explodeMenuSeoTitle = explode("~", $datoBreadcrumb["seo_title_menu"]);
		$explodeMenuSeoCanonical = explode("~", $datoBreadcrumb["seo_canonical_menu"]);
		$explodeMenuSeoUrl = explode("~", $datoBreadcrumb["seo_url_menu"]);
	
		//Invertimos ambos vectores
		$explodeMenuId = array_reverse($explodeMenuId);
		$explodeModuleId = array_reverse($explodeModuleId);
		//$explodeMenuIdCopia = array_reverse($explodeMenuIdCopia);
		$explodeMenuNombre = array_reverse($explodeMenuNombre);
		$explodeMenuDescripcion = array_reverse($explodeMenuDescripcion);
		$explodeMenuUrl = array_reverse($explodeMenuUrl);
		$explodeMenuSeoTitle = array_reverse($explodeMenuSeoTitle);
		$explodeMenuSeoCanonical = array_reverse($explodeMenuSeoCanonical);
		$explodeMenuSeoUrl = array_reverse($explodeMenuSeoUrl);
		$contMenu = count($explodeMenuId);
		
		$contMigas = 0;
		for($cont = 0; $cont < $contMenu; $cont++) {	
			$vectorMigas[$cont]["descripcion"] = $explodeMenuNombre[$cont];
			//$vectorMigas[$cont]["id_menu_copia"] = $explodeMenuIdCopia[$cont];
			$vectorMigas[$cont]["url"] = $explodeMenuUrl[$cont]."?menu=".$explodeMenuId[$cont];
	
			
			$vectorAtributosMenu["idioma"]=$_SESSION["siglas"];
			$vectorAtributosMenu["id_menu"]=$explodeMenuId[$cont];
			$vectorAtributosMenu["seo_url"]=$explodeMenuSeoUrl[$cont];

			$vectorAtributosMenu=generalUtils::generarUrlMenuContenido($db,$explodeModuleId[$cont],$explodeMenuId[$cont],$explodeMenuDescripcion[$cont],$_SESSION["id_idioma"],$vectorAtributosMenu);
				
	
			
			/**
			 * url amigable parametros
			 */
			$vectorMigas[$cont]["url"] = generalUtils::generarUrlAmigableMenu($vectorAtributosMenu);
			/**
			 * fin url amigable parametros
			 */
			$vectorMigas[$cont]["seo_title"] = $explodeMenuSeoTitle[$cont];
			$vectorMigas[$cont]["seo_canonical"] = $explodeMenuSeoCanonical[$cont];
		
		}
	}
	
	//Instanciamos las plantilla del breadcrumb
	$plantillaBreadcrumb = new XTemplate("html/includes/breadcrumb.html");
		
	//Almacenamos el total de migas a almacenar
	$totalMigas = count($vectorMigas);
	$cont = 0;
	for($cont = 0; $cont < $totalMigas; $cont++) {
		$plantillaBreadcrumb->assign("BREADCRUMB_DESCRIPCION", $vectorMigas[$cont]["descripcion"]);
		$plantillaBreadcrumb->assign("BREADCRUMB_URL", $vectorMigas[$cont]["url"]);
		if($cont != $totalMigas - 1 || ($cont==$totalMigas - 1 && isset($breadCrumbUrlDetalle))) {
			$plantillaBreadcrumb->parse("contenido_principal.item_breadcrumb.separador");
		}
		$plantillaBreadcrumb->parse("contenido_principal.item_breadcrumb");
	}
	
	
	//Si existe detalle...
	if(isset($breadCrumbUrlDetalle)){
		$plantillaBreadcrumb->assign("BREADCRUMB_DESCRIPCION", $breadCrumbDescripcionDetalle);
		//Lo preparamos para title de la web
		$breadCrumbDescripcionDetalle=" - ".$breadCrumbDescripcionDetalle;
		$plantillaBreadcrumb->assign("BREADCRUMB_URL", $breadCrumbUrlDetalle);
		$plantillaBreadcrumb->parse("contenido_principal.item_breadcrumb");
	}else{
		$breadCrumbDescripcionDetalle="";
	}
	
	$plantilla->assign("TITULO_WEB", $vectorMigas[$totalMigas - 1]["seo_title"]);
	
	//Metas opcionales
/*
	if($vectorMigas[$totalMigas - 1]["seo_canonical"]!=""){
		$plantilla->assign("METATAG_OPCIONAL_CANONICAL", $vectorMigas[$totalMigas - 1]["seo_canonical"]);	
		$plantilla->parse("contenido_principal.metatags_opcionales.metatag_canonical");	
	}else if($vectorMigas[$totalMigas - 1]["id_menu_copia"]!=""){
		//Obtenemos url original
		$resultadoMenuSeo=$db->callProcedure("CALL ed_sp_web_menu_seo_obtener(".$vectorMigas[$totalMigas - 1]["id_menu_copia"].",".$_SESSION["id_idioma"].")");
		$datoMenuSeo=$db->getData($resultadoMenuSeo);
		$vectorAtributosMenu["idioma"]=$_SESSION["siglas"];
		$vectorAtributosMenu["seo_url"]=$datoMenuSeo["seo_url"];
		$vectorAtributosMenu["id_menu"]=$vectorMigas[$totalMigas - 1]["id_menu_copia"];
		//Url canonical original
		$plantilla->assign("METATAG_OPCIONAL_CANONICAL", CURRENT_DOMAIN."/".generalUtils::generarUrlAmigableMenu($vectorAtributosMenu));	
		$plantilla->parse("contenido_principal.metatags_opcionales.metatag_canonical");	
	}
	$plantilla->parse("contenido_principal.metatags_opcionales");
*/	
	$plantilla->assign("TITULO_WEB", $vectorMigas[$totalMigas - 1]["seo_title"].$breadCrumbDescripcionDetalle);
	
	$plantillaBreadcrumb->parse("contenido_principal");

	$plantilla->assign("BREADCRUMB", $plantillaBreadcrumb->text("contenido_principal"));
?>