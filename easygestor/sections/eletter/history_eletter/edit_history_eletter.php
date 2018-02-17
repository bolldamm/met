<?php
	/**
	 * 
	 * Script que muestra y realiza de un evio
	 * @Author eData
	 * 
	 */
	
	

	if(!isset($_GET["id_eletter"])){
		generalUtils::redirigir("index.php");
	}
	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/eletter/history_eletter/manage_history_eletter.html");
	
	//Obtenemos newsletter concreto
	$resultadoEletter=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_eletter_obtener_concreto(".$_GET["id_eletter"].")");
	$datoEletter=$db->getData($resultadoEletter);

	//Datos defecto
	$fechaEnvio=explode(" ",$datoEletter["fecha_envio"]);
	$titulo=$datoEletter["titulo"];
	$subPlantilla->assign("ELETTER_FECHA_ENVIO",generalUtils::conversionFechaFormato($fechaEnvio[0])." ".$fechaEnvio[1]);
	$subPlantilla->assign("ELETTER_ASUNTO",$datoEletter["asunto"]);
	$subPlantilla->assign("ELETTER_DESCRIPCION",$datoEletter["descripcion"]);
	$subPlantilla->assign("ELETTER_IDELETTER",$datoEletter["id_novedad"]);
	
	
	//Editor descripcion previa
	$plantilla->assign("TEXTAREA_ID","txtaCuerpo");
	$plantilla->assign("TEXTAREA_TOOLBAR","Vacio");
	$plantilla->assign("TEXTAREA_TOOLBAR_EXPANDED",",toolbarStartupExpanded:false");
	$plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");


	//Ver historial eletter(destinatarios)
	$resultadoHistorialEletter=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_eletter_envio_obtener(".$_GET["id_eletter"].")");
	while($datoHistorialEletter=$db->getData($resultadoHistorialEletter)){
		if($datoHistorialEletter["id_usuario_web"]!=""){
			$subPlantilla->assign("ELETTER_CORREO_ELECTRONICO","<a href='main_app.php?section=member&action=edit&id_miembro=".$datoHistorialEletter["id_usuario_web"]."'>".$datoHistorialEletter["correo_electronico"].";</a>");
		}else{
			$subPlantilla->assign("ELETTER_CORREO_ELECTRONICO",$datoHistorialEletter["correo_electronico"].";");
		}
		$subPlantilla->assign("ELETTER_CORREO_ELECTRONICO_LEIDO"," ".$datoHistorialEletter["total_leido"]);
		$subPlantilla->parse("contenido_principal.item_destinatario");
	}

	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_ELETTER_VIEW_ELETTER_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_ELETTER_VIEW_ELETTER_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_ELETTER_EDIT_ELETTER_LINK."&id_eletter=".$datoEletter["id_novedad"];
	$vectorMigas[2]["texto"]=$titulo;
	$vectorMigas[3]["url"]=STATIC_BREADCUMB_HISTORY_ELETTER_VIEW_HISTORY_ELETTER_LINK."&id_eletter=".$datoEletter["id_novedad"];
	$vectorMigas[3]["texto"]=STATIC_BREADCUMB_HISTORY_ELETTER_VIEW_HISTORY_ELETTER_TEXT;
	$vectorMigas[3]["url"]=STATIC_BREADCUMB_HISTORY_ELETTER_EDIT_HISTORY_ELETTER_LINK."&id_eletter=".$_GET["id_eletter"];
	$vectorMigas[3]["texto"]=$datoEletter["asunto"];

	require "includes/load_breadcumb.inc.php";
	

	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
			
	//Incluimos script editor
	$plantilla->parse("contenido_principal.editor_script");
	
	
	//Incluimos proceso onload
	$plantilla->parse("contenido_principal.carga_inicial");
	
	//Contruimos plantilla secundaria
	$subPlantilla->parse("contenido_principal");
	
	//Exportamos plantilla secundaria a la plantilla principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
	//Construimos plantilla principal
	$plantilla->parse("contenido_principal");
	
	//Mostramos plantilla principal por pantalla
	$plantilla->out("contenido_principal");
?>