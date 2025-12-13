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
	
	$plantilla=new XTemplate("../html/rss/events_rss.xml");
	
	//Sacamos de base de datos las noticias
	$resultadoEvento=$db->callProcedure("CALL ed_sp_web_agenda_obtener_listado(0,".$_SESSION["id_idioma"].",'',-1,'','DESC','')");
	
	
	while($datoEvento=$db->getData($resultadoEvento)){
		$fechaTrozeada=explode(" ",$datoEvento["fecha"]);
		$horaPartido=explode(".",$fechaTrozeada[1]);
		

		$plantilla->assign("EVENTO_TITULO",$datoEvento["titulo"]);
		$vectorAtributosDetalle["idioma"]=$_SESSION["siglas"];
		$vectorAtributosDetalle["id_detalle"]=$datoEvento["id_agenda"];
		$vectorAtributosDetalle["seo_url"]=$datoEvento["titulo"];
		
		
		
		//$plantilla->assign("NOTICIA_ENLACE",CURRENT_DOMAIN."/".generalUtils::generarUrlAmigableNoticiaDetalle($vectorAtributosDetalle));
		$plantilla->assign("EVENTO_ENLACE",CURRENT_DOMAIN."/events_detail.php?menu=49&amp;event=".$datoEvento["id_agenda"]);
		$plantilla->assign("EVENTO_FECHA",date("r", strtotime($fechaTrozeada[0]."T00:00:00".$horaPartido[0])));
		$plantilla->assign("EVENTO_CATEGORIA",$datoEvento["tematica"]);
		$plantilla->assign("EVENTO_DESCRIPCION",str_replace("/documentacion/","https://".CURRENT_DOMAIN."/documentacion/",$datoEvento["descripcion_previa"]));
		
		//Parseamos fila		
		$plantilla->parse("contenido_principal.item_evento");
	}
	

	$plantilla->parse("contenido_principal");
	$plantilla->out("contenido_principal");
?>