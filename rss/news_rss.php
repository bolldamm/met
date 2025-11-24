<?php
	session_start();
	header("Content-Type: text/xml; charset=utf-8");
	/**
	 * 
	 * Indicamos la ruta absoluta en donde esta alojado el fichero.
	 * @var string
	 */
	
	$absolutePath=dirname(__FILE__);
	
	require $absolutePath."/../classes/databaseConnection.php";
	require $absolutePath."/../classes/generalUtils.php";
	require $absolutePath."/../database/connection.php";
	require $absolutePath."/../includes/load_template.inc.php";	
	require $absolutePath."/../includes/load_language.inc.php";
	require $absolutePath."/../config/dictionary/".$_SESSION["diccio"];
	require $absolutePath."/../config/constants.php";
	
	$plantilla=new XTemplate("../html/rss/news_rss.xml");
	
	//Sacamos de base de datos las noticias
	$resultadoNoticia=$db->callProcedure("CALL ed_sp_web_noticia_obtener_listado(".$_SESSION["id_idioma"].",'',0,-1,'')");
	
	
	while($datoNoticia=$db->getData($resultadoNoticia)){
		$fechaTrozeada=explode(" ",$datoNoticia["fecha"]);
		$horaPartido=explode(".",$fechaTrozeada[1]);
		

		$plantilla->assign("NOTICIA_TITULO",$datoNoticia["titulo"]);
		$vectorAtributosDetalle["idioma"]=$_SESSION["siglas"];
		$vectorAtributosDetalle["id_detalle"]=$datoNoticia["id_noticia"];
		$vectorAtributosDetalle["seo_url"]=$datoNoticia["titulo"];
		
		
		
		//$plantilla->assign("NOTICIA_ENLACE",CURRENT_DOMAIN."/".generalUtils::generarUrlAmigableNoticiaDetalle($vectorAtributosDetalle));
		$plantilla->assign("NOTICIA_ENLACE",CURRENT_DOMAIN."/news_detail.php?menu=1&amp;new=".$datoNoticia["id_noticia"]);
		$plantilla->assign("NOTICIA_FECHA",date("r", strtotime($fechaTrozeada[0]."T00:00:00".$horaPartido[0])));
		$plantilla->assign("NOTICIA_CATEGORIA",$datoNoticia["tematica"]);
		$plantilla->assign("NOTICIA_DESCRIPCION",str_replace("/documentacion/","https://".CURRENT_DOMAIN."/documentacion/",$datoNoticia["descripcion_previa"]));
		
		//Parseamos fila		
		$plantilla->parse("contenido_principal.item_noticia");
	}
	

	$plantilla->parse("contenido_principal");
	$plantilla->out("contenido_principal");
?>