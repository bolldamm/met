<?php
	/**
	 * 
	 * Script que muestra y realiza de un evio
	 * @Author eData
	 * 
	 */
	
	

	if(!isset($_GET["id_history_email"])){
		generalUtils::redirigir("index.php");
	}
	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/history_email/manage_history_email.html");
	
	//Obtenemos newsletter concreto
	$resultadoCorreoElectronico=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_correo_electronico_obtener_concreto(".$_GET["id_history_email"].")");
	$datoCorreoElectronico=$db->getData($resultadoCorreoElectronico);
	$idUsuarioWebCorreo=$datoCorreoElectronico["id_usuario_web"];

	//Datos defecto
	$fechaEnvio=explode(" ",$datoCorreoElectronico["fecha_envio"]);
	$subPlantilla->assign("HISTORY_EMAIL_FECHA_ENVIO",generalUtils::conversionFechaFormato($fechaEnvio[0])." ".$fechaEnvio[1]);
	$subPlantilla->assign("HISTORY_EMAIL_ASUNTO",$datoCorreoElectronico["asunto"]);
	$subPlantilla->assign("HISTORY_EMAIL_DESCRIPCION",$datoCorreoElectronico["cuerpo"]);
	


	//Ver historial eletter(destinatarios)
	$resultadoHistoriaCorreoElectronico=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_correo_electronico_envio_obtener(".$_GET["id_history_email"].")");
	while($datoHistorialCorreoElectronico=$db->getData($resultadoHistoriaCorreoElectronico)){
		$subPlantilla->assign("HISTORY_EMAIL_CORREO_ELECTRONICO",$datoHistorialCorreoElectronico["email"].";");
		$subPlantilla->parse("contenido_principal.item_destinatario");
	}

	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_HISTORY_EMAIL_VIEW_HISTORY_EMAIL_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_HISTORY_EMAIL_VIEW_HISTORY_EMAIL_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_HISTORY_EMAIL_EDIT_HISTORY_EMAIL_LINK."&id_history_email=".$_GET["id_history_email"];
	$vectorMigas[2]["texto"]=$datoCorreoElectronico["asunto"];

	require "includes/load_breadcumb.inc.php";
	
	//Editor descripcion previa
	$plantilla->assign("TEXTAREA_ID","txtaCuerpo");
	$plantilla->assign("TEXTAREA_TOOLBAR","Vacio");
	$plantilla->assign("TEXTAREA_TOOLBAR_EXPANDED",",toolbarStartupExpanded:false");
	$plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");

	$masDetalle="";
	switch($datoCorreoElectronico["id_tipo_correo_electronico"]){
		case EMAIL_TYPE_NEW_FORM:
			$resultadoNoticia=$db->callProcedure("CALL ed_sp_correo_electronico_noticia_obtener(".$_GET["id_history_email"].")");
			$datoNoticia=$db->getData($resultadoNoticia);
			$masDetalle=STATIC_MANAGE_HISTORY_EMAIL_CLICK." <a href='main_app.php?section=new&action=edit&id_noticia=".$datoNoticia["id_noticia"]."'>".STATIC_MANAGE_HISTORY_EMAIL_HERE."</a> ".STATIC_MANAGE_HISTORY_EMAIL_NEW;
			break;
		case EMAIL_TYPE_EVENT_FORM:
			$resultadoAgenda=$db->callProcedure("CALL ed_sp_correo_electronico_agenda_obtener(".$_GET["id_history_email"].")");
			$datoAgenda=$db->getData($resultadoAgenda);
			$masDetalle=STATIC_MANAGE_HISTORY_EMAIL_CLICK." <a href='main_app.php?section=diary&action=edit&id_agenda=".$datoAgenda["id_agenda"]."'>".STATIC_MANAGE_HISTORY_EMAIL_HERE."</a> ".STATIC_MANAGE_HISTORY_EMAIL_EVENT;
			break;
		case EMAIL_TYPE_EXPENSE_FORM:
			$resultadoMovimiento=$db->callProcedure("CALL ed_sp_correo_electronico_movimiento_obtener(".$_GET["id_history_email"].")");
			$datoMovimiento=$db->getData($resultadoMovimiento);
			$masDetalle=STATIC_MANAGE_HISTORY_EMAIL_CLICK." <a href='main_app.php?section=movement&action=edit&id_movimiento=".$datoMovimiento["id_movimiento"]."'>".STATIC_MANAGE_HISTORY_EMAIL_HERE."</a> ".STATIC_MANAGE_HISTORY_EMAIL_MOVEMENT;
			break;
		case EMAIL_TYPE_JOB_FORM:
			$resultadoOfertaTrabajo=$db->callProcedure("CALL ed_sp_correo_electronico_oferta_trabajo_obtener(".$_GET["id_history_email"].")");
			$datoOfertaTrabajo=$db->getData($resultadoOfertaTrabajo);
			$masDetalle=STATIC_MANAGE_HISTORY_EMAIL_CLICK." <a href='main_app.php?section=job&action=edit&id_oferta_trabajo=".$datoOfertaTrabajo["id_oferta_trabajo"]."'>".STATIC_MANAGE_HISTORY_EMAIL_HERE."</a> ".STATIC_MANAGE_HISTORY_EMAIL_JOB;
			break;
		case EMAIL_TYPE_INSCRIPTION_FORM:
		case EMAIL_TYPE_INSCRIPTION_RENEW_FORM:
			$resultadoInscripcion=$db->callProcedure("CALL ed_sp_correo_electronico_inscripcion_obtener(".$_GET["id_history_email"].")");
			$datoInscripcion=$db->getData($resultadoInscripcion);
			$masDetalle=STATIC_MANAGE_HISTORY_EMAIL_CLICK." <a href='main_app.php?section=member&action=edit&id_miembro=".$datoInscripcion["id_usuario_web"]."'>".STATIC_MANAGE_HISTORY_EMAIL_HERE."</a> ".STATIC_MANAGE_HISTORY_EMAIL_INSCRIPTION;
			break;
		case EMAIL_TYPE_JOB_FORM_REQUEST:
			$resultadoOfertaTrabajo=$db->callProcedure("CALL ed_sp_correo_electronico_oferta_trabajo_solicitud_obtener(".$_GET["id_history_email"].")");
			$datoOfertaTrabajo=$db->getData($resultadoOfertaTrabajo);
			$masDetalle=STATIC_MANAGE_HISTORY_EMAIL_CLICK." <a href='main_app.php?section=job&action=edit&id_oferta_trabajo=".$datoOfertaTrabajo["id_oferta_trabajo"]."'>".STATIC_MANAGE_HISTORY_EMAIL_HERE."</a> ".STATIC_MANAGE_HISTORY_EMAIL_JOB;
			break;
		case EMAIL_TYPE_INSCRIPTION_ACTIVATED:
			$resultadoInscripcion=$db->callProcedure("CALL ed_sp_correo_electronico_inscripcion_activa_obtener(".$_GET["id_history_email"].")");
			$datoInscripcion=$db->getData($resultadoInscripcion);
			$masDetalle=STATIC_MANAGE_HISTORY_EMAIL_CLICK." <a href='main_app.php?section=member&action=edit&id_miembro=".$datoInscripcion["id_usuario_web"]."'>".STATIC_MANAGE_HISTORY_EMAIL_HERE."</a> ".STATIC_MANAGE_HISTORY_EMAIL_INSCRIPTION;
			break;
		case EMAIL_TYPE_NOMINEE_ACTIVATION:
			$masDetalle=STATIC_MANAGE_HISTORY_EMAIL_CLICK." <a href='main_app.php?section=member&action=edit&id_miembro=".$idUsuarioWebCorreo."'>".STATIC_MANAGE_HISTORY_EMAIL_HERE."</a> ".STATIC_MANAGE_HISTORY_EMAIL_NOMINEE;
			break;
	}
	
	
	$subPlantilla->assign("HISTORY_EMAIL_MORE_DETAIL",$masDetalle);
	
	if($masDetalle!=""){
		$subPlantilla->parse("contenido_principal.bloque_more_detail");
	}
	
	
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