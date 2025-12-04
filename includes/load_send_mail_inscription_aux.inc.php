<?php
	$absolutePath=dirname(__FILE__);


	//Clase phpmailer
	require $absolutePath."/load_mailer.inc.php";
	
	//Plantilla principal
	$plantillaMail=new XTemplate($absolutePath."/../html/mail/mail_index.html");
	
	//Subplantilla
	$subPlantillaMail=new XTemplate($absolutePath."/../html/mail/mail_inscription.html");


	
	if($metodoPago==INSCRIPCION_TIPO_PAGO_TRANSFERENCIA){
		$tipoPagoMovimiento=MOVIMIENTO_TIPO_PAGO_TRANSFERENCIA;
		$pagado=0;
		if($tipoInscripcion==1){
			//New membership
			$subPlantillaMail->assign("EMAIL_TEXTO",STATIC_MAIL_INSCRIPTION_TRANSFER_PAYMENT_FIRST_STEP);
		}else{
			//Renew membership
			$subPlantillaMail->assign("EMAIL_TEXTO",STATIC_MAIL_RENEW_INSCRIPTION_TRANSFER_PAYMENT_FIRST_STEP);
		}
		
	}else if($metodoPago==INSCRIPCION_TIPO_PAGO_PAYPAL)	{
		$tipoPagoMovimiento=MOVIMIENTO_TIPO_PAGO_PAYPAL;
		$pagado=1;
		if($tipoInscripcion==1){
			$subPlantillaMail->assign("EMAIL_TEXTO",STATIC_MAIL_INSCRIPTION_PAYPAL_PAYMENT_FIRST_STEP);	
		}else{
			$subPlantillaMail->assign("EMAIL_TEXTO",STATIC_MAIL_INSCRIPTION_PAYPAL_PAYMENT_FIRST_STEP);	
		}
	}else if($metodoPago==INSCRIPCION_TIPO_PAGO_DEBIT){
		//En si es un pago bancario tambien
		$tipoPagoMovimiento=MOVIMIENTO_TIPO_PAGO_DEBIT;
		$pagado=0;
		if($tipoInscripcion==1){
			//New membership
			$subPlantillaMail->assign("EMAIL_TEXTO",STATIC_MAIL_INSCRIPTION_DEBIT_PAYMENT_FIRST_STEP);
		}else{
			//Renew membership
			$subPlantillaMail->assign("EMAIL_TEXTO",STATIC_MAIL_RENEW_INSCRIPTION_DEBIT_PAYMENT_FIRST_STEP);
		}
	}

	$nombrePersonaCompleto=$nombreUsuario." ".$apellidosUsuario;
	
	

	//Si estamos en renew...
	if($tipoInscripcion==2 && $_SESSION["met_user"]["id_modalidad"]==MODALIDAD_USUARIO_INSTITUTIONAL){
		$nombrePersonaCompleto=$_SESSION["met_user"]["institution_name"];
	}
	
	//$subPlantillaMail->assign("EMAIL_USUARIO_WEB_NOMBRE_COMPLETO",$nombrePersonaCompleto);
	$subPlantillaMail->assign("EMAIL_USUARIO_WEB_NOMBRE_COMPLETO",$nombreUsuario);
	
	$subPlantillaMail->parse("contenido_principal");
	
	//Exportamos subPlantilla a plantilla
	$plantillaMail->assign("CONTENIDO",$subPlantillaMail->text("contenido_principal"));
	
	$plantillaMail->parse("contenido_principal");
	
	//Establecemos cuerpo del mensaje
	$mail->Body=$subPlantillaMail->text("contenido_principal");
	

	
	
	
	//Insertamos un movimiento si estamos haciendo una nueva reserva
	$resultadoMovimiento=$db->callProcedure("CALL ed_sp_web_movimiento_insertar(".MOVIMIENTO_TIPO_ENTRADA.",".$idConceptoMovimiento.",".$tipoPagoMovimiento.",".$idUsuarioWeb.",'".generalUtils::escaparCadena($nombrePersonaCompleto)."','".date("Y-m-d")."','".STATIC_MOVEMENT_NEW_MEMBERSHIP_DESCRIPTION."','".$importe."',".$pagado.")");		
	$datoMovimiento = $db->getData($resultadoMovimiento);
	$idMovimiento = $datoMovimiento["id_movimiento"];
	
	//Insertamos movimiento-inscripcion
	$db->callProcedure("CALL ed_sp_web_movimiento_inscripcion_insertar(".$idMovimiento.",".$idInscripcion.")");
	

	//if($esFactura==1){
		//Insertamos factura (with tax ID fields for Verifactu)
		$taxIdCountry = isset($_SESSION["taxIdCountry"]) ? $_SESSION["taxIdCountry"] : "";
		$taxIdType = isset($_SESSION["taxIdType"]) ? $_SESSION["taxIdType"] : "";
		$taxIdNumber = isset($_SESSION["taxIdNumber"]) ? $_SESSION["taxIdNumber"] : "";
		$tipoFacturaVerifactu = isset($_SESSION["tipoFacturaVerifactu"]) ? $_SESSION["tipoFacturaVerifactu"] : "F1";

		$resultadoFactura=$db->callProcedure("CALL ed_sp_web_factura_insertar('".$nifFactura."','".$nombreClienteFactura."','".$nombreEmpresaFactura."','".$direccionFactura."','".$codigoPostalFactura."','".$ciudadFactura."','".$provinciaFactura."','".$paisFactura."','".$emailUsuario."','".$nombreUsuario."','".$taxIdCountry."','".$taxIdType."','".$taxIdNumber."','".$tipoFacturaVerifactu."')");
		$datoFactura=$db->getData($resultadoFactura);
		$idFactura=$datoFactura["id_factura"];
		
		//Insertamos linea factura
		$db->callProcedure("CALL ed_sp_web_linea_factura_insertar(".$idFactura.",".$idMovimiento.")");
	//}
	
	
	
	
	/**
	 * 
	 * Incluimos la configuracion del componente phpmailer
	 * 
	 */
	$mail->AddAddress($emailUsuario);
	$mail->AddBCC(STATIC_MAIL_TO);
	$mail->AddBCC(STATIC_MAIL_TO_TREASURER);
	$mail->FromName = STATIC_MAIL_FROM;
	$mail->Subject = STATIC_MAIL_INSCRIPCION_SUBJECT;
	

	//Enviamos correo
	if($mail->Send()){
		/****** Guardamos el log del correo electronico ******/
		
		//Tipo correo electronico
		if($tipoInscripcion==1){
			$idTipoCorreoElectronico=EMAIL_TYPE_INSCRIPTION_FORM;
		}else{
			$idTipoCorreoElectronico=EMAIL_TYPE_INSCRIPTION_RENEW_FORM;
		}
		
		
		//Destinatario
		$vectorDestinatario=Array();
		array_push($vectorDestinatario,$emailUsuario);
		array_push($vectorDestinatario,STATIC_MAIL_TO);
		array_push($vectorDestinatario,STATIC_MAIL_TO_TREASURER);
		
		//Asunto
		$asunto=STATIC_MAIL_INSCRIPCION_SUBJECT;
		$cuerpo=$mail->Body;
		
		require $absolutePath."/load_log_email.inc.php";
				
	}
			
	

	//Exporta
	$plantillaMail->assign("CONTENIDO",$subPlantillaMail->text("contenido_principal"));
	$plantillaMail->parse("contenido_principal");
?>