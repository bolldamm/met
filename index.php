<?php
	/**
	 * 
	 * Pagina de inicio desde donde se inicia el portal
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */

	require "includes/load_main_components.inc.php";
	
	//Initiate the Xtemplate class with the main template
	$plantilla = new XTemplate("html/index.html");
	
	//Initiate the Xtemplate class with the subtemplate for the requested page
	$subPlantilla = new XTemplate("html/home.html");
	
	//Assign the CSS for this section
	$plantilla->assign("SECTION_FILE_CSS", "home.css");
	
	$esHome=true;
	require "includes/load_structure.inc.php";
	
	$plantilla->assign("TITULO_WEB", STATIC_TITLE_WEB_HOME);
	
	//Load news item summaries (max. 3 items, title and snippet) and assign to placeholders
	$resultNoticias = $db->callProcedure("CALL ed_sp_web_home_noticias_obtener_listado(".$_SESSION["id_idioma"].")");
	if($db->getNumberRows($resultNoticias) > 0) {
		
		while($dataNoticia = $db->getData($resultNoticias)) {
			$subPlantilla->assign("ITEM_NOTICIA_TITULO", $dataNoticia["titulo"]);
			$subPlantilla->assign("ITEM_NOTICIA_DESCRIPCION", $dataNoticia["descripcion_previa"]);
			
			//Generate URL of Details page ("more" link) for each news item and assign to a placeholder
			$vectorAtributosDetalle["idioma"]=$_SESSION["siglas"];
			$vectorAtributosDetalle["id_menu"]=$dataNoticia["id_menu"];
			$vectorAtributosDetalle["id_detalle"]=$dataNoticia["id_noticia"];
			$idMenuNoticia=$dataNoticia["id_menu"];
			$vectorAtributosDetalle["seo_url"]=$dataNoticia["titulo"];
			$subPlantilla->assign("ITEM_NOTICIA_URL", generalUtils::generarUrlAmigableDetalle($vectorAtributosDetalle));
			
			$subPlantilla->parse("contenido_principal.list_noticias.item_noticia");
		}
		$subPlantilla->parse("contenido_principal.list_noticias");
	} else {
		$subPlantilla->parse("contenido_principal.no_noticias");
	}

	//Get URL for "All news" link and assign to a placeholder
	$resultadoMenuSeo=$db->callProcedure("CALL ed_sp_web_menu_seo_obtener(".$idMenu.",".$_SESSION["id_idioma"].")");
	$datoMenuSeo=$db->getData($resultadoMenuSeo);
	$vectorAtributosMenu["idioma"]=$_SESSION["siglas"];
	$vectorAtributosMenu["id_menu"]=$idMenuNoticia;
	$vectorAtributosMenu["seo_url"]=$datoMenuSeo["seo_url"];
	$subPlantilla->assign("NOTICIAS_ENLACE_VER_TODOS",generalUtils::generarUrlAmigableMenu($vectorAtributosMenu));

	//Fetch random image from home_page_headers folder in "files"
	$resultHeaderImage = $db->callProcedure("CALL ed_sp_web_menu_archivo_obtener_concreto_aleatorio(1063)");
	$headerImage = $db->getData($resultHeaderImage);

	//Get home page content (rows) from database
	$resultMenuHome = $db->callProcedure("CALL ed_sp_web_menu_obtener_listado(".$idMenuHome.", ".$idMenuTipo.",".$_SESSION["id_idioma"].", 1)");

    //Assign each row (i.e. each active home page item) to a variable IN EASYGESTOR ORDER
    //$row_image = $resultMenuHome->fetch_assoc();
    $row_A = $resultMenuHome->fetch_assoc();
    $row_B = $resultMenuHome->fetch_assoc();
    $row_C = $resultMenuHome->fetch_assoc();
    $row_D = $resultMenuHome->fetch_assoc();
    $row_E = $resultMenuHome->fetch_assoc();

    //Assign title and content of each home page item to subtemplate placeholders
    $subPlantilla->assign("HEADER_IMAGE", "files/home_page_headers/".$headerImage["nombre"]);
	//$subPlantilla->assign("HOME_PAGE_IMAGE_CAROUSEL", $row_image["descripcion"]);
    $subPlantilla->assign("HOME_PAGE_A_TITLE", $row_A["nombre"]);
    $subPlantilla->assign("HOME_PAGE_A_CONTENT", $row_A["descripcion"]);
    $subPlantilla->assign("HOME_PAGE_B_TITLE", $row_B["nombre"]);
    $subPlantilla->assign("HOME_PAGE_B_CONTENT", $row_B["descripcion"]);
    $subPlantilla->assign("HOME_PAGE_C_TITLE", $row_C["nombre"]);
    $subPlantilla->assign("HOME_PAGE_C_CONTENT", $row_C["descripcion"]);
    $subPlantilla->assign("HOME_PAGE_D_TITLE", $row_D["nombre"]);
    $subPlantilla->assign("HOME_PAGE_D_CONTENT", $row_D["descripcion"]);
    $subPlantilla->assign("HOME_PAGE_E_TITLE", $row_E["nombre"]);
    $subPlantilla->assign("HOME_PAGE_E_CONTENT", $row_E["descripcion"]);

    //Parse subtemplate content
    $subPlantilla->parse("contenido_principal");

	//Parse main template content
    $plantilla->parse("contenido_principal.bloque_ready");

	//Export subtemplate content to main template
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));

    //Parse home page content full-width without lefthand menu
	$plantilla->parse("contenido_principal.home_page");

	//Parse and display complete page content
	$plantilla->parse("contenido_principal");
	$plantilla->out("contenido_principal");
?>