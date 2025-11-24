<?php
	/******
	 * Insertamos el correo electronico enviado
	 */	
	$resultadoCorreoElectronico=$db->callProcedure("CALL ed_sp_web_correo_electronico_insertar(".$idTipoCorreoElectronico.",".$idUsuarioWebCorreo.",'".generalUtils::escaparCadena($asunto)."','".generalUtils::escaparCadena($cuerpo)."')");
	$datoCorreoElectronico=$db->getData($resultadoCorreoElectronico);

	//Id del correo
	$idCorreoElectronico=$datoCorreoElectronico["id_correo_electronico"];
	
	
	/****** insercion del destinatario/destinatarios ******/
	foreach($vectorDestinatario as $destinatario){
		$db->callProcedure("CALL ed_sp_web_correo_electronico_destinatario_insertar(".$idCorreoElectronico.",'".generalUtils::escaparCadena($destinatario)."')");
	}
	
	//Segun sea seccion...
	switch($idTipoCorreoElectronico){
		case EMAIL_TYPE_NEW_FORM:
			$db->callProcedure("CALL ed_sp_web_correo_electronico_noticia_insertar(".$idCorreoElectronico.",".$idNoticia.")");
			break;
		case EMAIL_TYPE_EVENT_FORM:
			$db->callProcedure("CALL ed_sp_web_correo_electronico_agenda_insertar(".$idCorreoElectronico.",".$idAgenda.")");
			break;
		case EMAIL_TYPE_EXPENSE_FORM:
			$db->callProcedure("CALL ed_sp_web_correo_electronico_movimiento_insertar(".$idCorreoElectronico.",".$idMovimiento.")");
			break;
		case EMAIL_TYPE_JOB_FORM:
			$db->callProcedure("CALL ed_sp_web_correo_electronico_oferta_trabajo_insertar(".$idCorreoElectronico.",".$idOferta.")");
			break;
		case EMAIL_TYPE_INSCRIPTION_FORM:
		case EMAIL_TYPE_INSCRIPTION_RENEW_FORM:
			$db->callProcedure("CALL ed_sp_web_correo_electronico_inscripcion_insertar(".$idCorreoElectronico.",".$idInscripcion.")");
			break;
		case EMAIL_TYPE_JOB_FORM_REQUEST:
			$db->callProcedure("CALL ed_sp_web_correo_electronico_oferta_trabajo_solicitud_insertar(".$idCorreoElectronico.",".$idJob.")");
			break;
		case EMAIL_TYPE_INSCRIPTION_ACTIVATED:
			$db->callProcedure("CALL ed_sp_web_correo_electronico_inscripcion_activa_insertar(".$idCorreoElectronico.",".$idInscripcion.")");
			break;
		case EMAIL_TYPE_WORKSHOP_FORM_TO_MET:
		case EMAIL_TYPE_WORKSHOP_FORM_TO_USER:
			$db->callProcedure("CALL ed_sp_web_correo_electronico_inscripcion_taller_insertar(".$idCorreoElectronico.",".$idInscripcion.")");
			break;
		case EMAIL_TYPE_WORKSHOP_FORM_TO_USER_ACTIVATION:
			$db->callProcedure("CALL ed_sp_web_correo_electronico_inscripcion_taller_activa_insertar(".$idCorreoElectronico.",".$idInscripcion.")");
			break;
		case EMAIL_TYPE_INVOICE_SENT:
			$db->callProcedure("CALL ed_sp_web_correo_electronico_factura_insertar(".$idCorreoElectronico.",".$idFactura.")");
			break;
		case EMAIL_TYPE_CONFERENCE_FORM_TO_MET:
		case EMAIL_TYPE_CONFERENCE_FORM_TO_USER:
			$db->callProcedure("CALL ed_sp_web_correo_electronico_inscripcion_conferencia_insertar(".$idCorreoElectronico.",".$idInscripcion.")");
			break;
		case EMAIL_TYPE_CONFERENCE_FORM_TO_USER_ACTIVATION:
			$db->callProcedure("CALL ed_sp_web_correo_electronico_inscripcion_conferencia_activa_ins(".$idCorreoElectronico.",".$idInscripcion.")");
			break;
        case "22":
			$db->callProcedure("CALL ed_sp_web_correo_electronico_certificado_insertar(".$idCorreoElectronico.",".$id_inscripcion.")");
			break;
        case "23":
			$db->callProcedure("CALL ed_sp_web_correo_electronico_certificado_metm_insertar(".$idCorreoElectronico.",".$id_inscripcion.")");
			break;
	}

?>