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
	$subPlantilla = new XTemplate("html/home.html");
	
	/**
	 * Asignamos el CSS que corresponde a este apartado
	 */
	$plantilla->assign("SECTION_FILE_CSS", "home.css");
	
	$esHome=true;
	require "includes/load_structure.inc.php";
	
	$plantilla->assign("TITULO_WEB", STATIC_TITLE_WEB_HOME);
	
	//Cargamos las noticias y eventos (3 lementos max.)
	$resultNoticias = $db->callProcedure("CALL ed_sp_web_home_noticias_obtener_listado(".$_SESSION["id_idioma"].")");
	if($db->getNumberRows($resultNoticias) > 0) {
		
		while($dataNoticia = $db->getData($resultNoticias)) {
			$subPlantilla->assign("ITEM_NOTICIA_TITULO", $dataNoticia["titulo"]);
			$subPlantilla->assign("ITEM_NOTICIA_DESCRIPCION", $dataNoticia["descripcion_previa"]);
			
			//Url detalle
			$vectorAtributosDetalle["idioma"]=$_SESSION["siglas"];
			$vectorAtributosDetalle["id_menu"]=$dataNoticia["id_menu"];
			$vectorAtributosDetalle["id_detalle"]=$dataNoticia["id_noticia"];
			$idMenuNoticia=$dataNoticia["id_menu"];
			$vectorAtributosDetalle["seo_url"]=$dataNoticia["titulo"];
			$subPlantilla->assign("ITEM_NOTICIA_URL", generalUtils::generarUrlAmigableDetalle($vectorAtributosDetalle));
			
			//$subPlantilla->assign("ITEM_NOTICIA_URL", "news_detail.php?menu=".$dataNoticia["id_menu"]."&new=".$dataNoticia["id_noticia"]);
			
			$subPlantilla->parse("contenido_principal.lst_noticias.item_noticia");
		}
		$subPlantilla->parse("contenido_principal.lst_noticias");
	} else {
		$subPlantilla->parse("contenido_principal.no_noticias");
	}
	

	//Obtenemos la url asociada a noticias
	$resultadoMenuSeo=$db->callProcedure("CALL ed_sp_web_menu_seo_obtener(".$idMenu.",".$_SESSION["id_idioma"].")");
	$datoMenuSeo=$db->getData($resultadoMenuSeo);
	$vectorAtributosMenu["idioma"]=$_SESSION["siglas"];
	$vectorAtributosMenu["id_menu"]=$idMenuNoticia;
	$vectorAtributosMenu["seo_url"]=$datoMenuSeo["seo_url"];
	$subPlantilla->assign("NOTICIAS_ENLACE_VER_TODOS",generalUtils::generarUrlAmigableMenu($vectorAtributosMenu));
	
	
	
	

	$resultMenuHome = $db->callProcedure("CALL ed_sp_web_menu_obtener_listado(".$idMenuHome.", ".$idMenuTipo.",".$_SESSION["id_idioma"].", 1)");
	while($datoMenuHome=$db->getData($resultMenuHome)){
		$subPlantilla->assign("SUBMENU_HOME_TITLE",$datoMenuHome["nombre"]);
		$subPlantilla->assign("SUBMENU_HOME_CONTENT",$datoMenuHome["descripcion"]);
		if($i<2){
			if($i==1){
				$subPlantilla->assign("ID_CONTENT","id='conference'");
			}
			$subPlantilla->parse("contenido_principal.item_submenu");
		}
		$i++;
	}
	
	$subPlantilla->parse("contenido_principal");
	
	/**
	 * Realizamos todos los parse realcionados con este apartado
	 */
	$plantilla->parse("contenido_principal.bloque_ready");
	
	
	//Exportamos plantilla secundaria a la principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
	//Parseamos y sacamos informacion por pantalla
	$plantilla->parse("contenido_principal");
	$plantilla->out("contenido_principal");
?>