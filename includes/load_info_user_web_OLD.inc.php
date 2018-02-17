<?php
	//Obtenemos detalle concreto del usuario
	if($_SESSION["met_user"]["id_modalidad"]==MODALIDAD_USUARIO_INDIVIDUAL){
		$resultadoUsuario=$db->callProcedure("CALL ed_sp_web_usuario_web_individual_obtener_concreto(".$idUsuarioWeb.")");
		$datoUsuario=$db->getData($resultadoUsuario);
		

		//Datos del usuario
		$nombre=$datoUsuario["nombre"];
		$apellidos=$datoUsuario["apellidos"];
		$email=$datoUsuario["correo_electronico"];
	}else if($_SESSION["met_user"]["id_modalidad"]==MODALIDAD_USUARIO_INSTITUTIONAL){
		$resultadoUsuario=$db->callProcedure("CALL ed_sp_web_usuario_web_institucion_obtener_concreto(".$idUsuarioWeb.")");
		$datoUsuario=$db->getData($resultadoUsuario);

		//Datos del usuario
		$nombre=$datoUsuario["nombre_representante"];
		$apellidos=$datoUsuario["apellidos_representante"];
		$email=$datoUsuario["correo_electronico_representante"];
	}


	//Nombre usuario
	$urlMiembroDetalle=CURRENT_DOMAIN."/easygestor/main_app.php?section=member&action=edit&id_miembro=".$idUsuarioWeb;
	
	$plantillaInfoUsuario=new XTemplate("html/mail/includes/info_user_web.inc.html");
	$plantillaInfoUsuario->assign("MIEMBRO_CORREO_ELECTRONICO",$email);
	$plantillaInfoUsuario->assign("MIEMBRO_NOMBRE",$nombre." ".$apellidos);
	$plantillaInfoUsuario->assign("MIEMBRO_URL",$urlMiembroDetalle);
	
	//Parseamos contenido
	$plantillaInfoUsuario->parse("contenido_principal");
	
	
	//Exportamos a la subplantilla mail
	$mailSubPlantilla->assign("DETALLE_MIEMBRO",$plantillaInfoUsuario->text("contenido_principal"));
?>