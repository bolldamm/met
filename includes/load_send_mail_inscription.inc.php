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
		
		
		//Insertamos otra inscripcion para cada usuario de institucion
		$resultadoInscripcionInstitucion=$db->callProcedure("CALL ed_sp_web_inscripcion_asociados_institucion_insertar(".$idEstadoInscripcion.",".$metodoPago.",".$idUsuarioWeb.",'".$importe."','".$fechaInscripcion."','".$fechaFinalizacion."',".$esFactura.")");
		
	}
	
	//Edited by Stephen to use only first name in email to member (rather than full name)
	//$subPlantillaMail->assign("EMAIL_USUARIO_WEB_NOMBRE_COMPLETO",$nombrePersonaCompleto);
	$subPlantillaMail->assign("EMAIL_USUARIO_WEB_NOMBRE_COMPLETO",$nombreUsuario);
	
	$subPlantillaMail->parse("contenido_principal");
	
	//Exportamos subPlantilla a plantilla
	$plantillaMail->assign("CONTENIDO",$subPlantillaMail->text("contenido_principal"));
	
	$plantillaMail->parse("contenido_principal");
	
	//Establecemos cuerpo del mensaje
	$mail->Body=$subPlantillaMail->text("contenido_principal");

   // If signup is after 30 September, set sub-account to Prepaid
    $fechaInscripcion=date("Y-m-d G:i:s");
    $fechaHoraDesglosada=explode(" ", $fechaInscripcion);
    $fechaDesglosada=explode("-",$fechaHoraDesglosada[0]);
	$nextYear=$fechaDesglosada[0] + 1;
	$dummyDate=$nextYear . "-01-02";
    if($fechaDesglosada[1]>9){
        $idConceptoMovimiento=MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP_PREPAID;
        } 


	//Insertamos un movimiento si estamos haciendo una nueva reserva
	//set movement to be non-taxable
	$nonTaxable = 1;
	$resultadoMovimiento=$db->callProcedure("CALL ed_sp_web_movimiento_insertar(".MOVIMIENTO_TIPO_ENTRADA.",".$idConceptoMovimiento.",".$nonTaxable.",".$tipoPagoMovimiento.",".$idUsuarioWeb.",'".generalUtils::escaparCadena($nombrePersonaCompleto)."','".date("Y-m-d")."','".STATIC_MOVEMENT_NEW_MEMBERSHIP_DESCRIPTION."','".$importe."',".$pagado.")");

	$datoMovimiento = $db->getData($resultadoMovimiento);
	$idMovimiento = $datoMovimiento["id_movimiento"];
	
	//Insertamos movimiento-inscripcion
	$db->callProcedure("CALL ed_sp_web_movimiento_inscripcion_insertar(".$idMovimiento.",".$idInscripcion.")");

    // If signup is after September, create two extra movements (positive & negative) dated 2 Jan the following year
    if($fechaDesglosada[1]>0){
        $db->callProcedure("CALL ed_sp_web_movimiento_prepaid_positive_insertar(".MOVIMIENTO_TIPO_ENTRADA.",".$idConceptoMovimiento.",".$nonTaxable.",".$tipoPagoMovimiento.",".$idUsuarioWeb.",'".generalUtils::escaparCadena($nombrePersonaCompleto)."',".$dummyDate.",'".STATIC_MOVEMENT_NEW_MEMBERSHIP_DESCRIPTION."','".$importe."',".$pagado.")");
        $db->callProcedure("CALL ed_sp_web_movimiento_prepaid_negative_insertar(".MOVIMIENTO_TIPO_ENTRADA.",".$idConceptoMovimiento.",".$nonTaxable.",".$tipoPagoMovimiento.",".$idUsuarioWeb.",'".generalUtils::escaparCadena($nombrePersonaCompleto)."',".$dummyDate.",'".STATIC_MOVEMENT_NEW_MEMBERSHIP_DESCRIPTION."','".-$importe."',".$pagado.")");
    }
	
	if(isset($comision)){
		$resultadoMovimientoComision=$db->callProcedure("CALL ed_sp_web_movimiento_insertar(".MOVIMIENTO_TIPO_SALIDA.",".MOVIMIENTO_CONCEPTO_FEE_GENERAL_BANKING.",".$nonTaxable.",".$tipoPagoMovimiento.",".$idUsuarioWeb.",'".STATIC_GLOBAL_PAYPAL_FEE."','".date("Y-m-d")."','".generalUtils::escaparCadena($nombrePersonaCompleto)."','".$comision."',".$pagado.")");		
		$datoMovimientoComision = $db->getData($resultadoMovimientoComision);
		$idMovimientoComision = $datoMovimientoComision["id_movimiento"];
		
		//Insertamos movimiento-inscripcion
		$db->callProcedure("CALL ed_sp_web_movimiento_inscripcion_insertar(".$idMovimientoComision.",".$idInscripcion.")");
	
	}


	//if($esFactura==1){
		//Insertamos factura		
		$resultadoFactura=$db->callProcedure("CALL ed_sp_web_factura_insertar('".$nifFactura."','".$nombreClienteFactura."','".$nombreEmpresaFactura."','".$direccionFactura."','".$codigoPostalFactura."','".$ciudadFactura."','".$provinciaFactura."','".$paisFactura."','".$emailUsuario."','".$nombreUsuario."')");		
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
	$mail->AddBCC(STATIC_MAIL_TO_MEMBERSHIP_REG_FORM);
	//$mail->AddBCC(STATIC_MAIL_TO_TREASURER);
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
		array_push($vectorDestinatario,STATIC_MAIL_TO_MEMBERSHIP_REG_FORM);
		//array_push($vectorDestinatario,STATIC_MAIL_TO_TREASURER);
		
		//Asunto
		$asunto=STATIC_MAIL_INSCRIPCION_SUBJECT;
		$cuerpo=$mail->Body;
		
		require $absolutePath."/load_log_email.inc.php";
				
	}

	
			
	

	//Exporta
	$plantillaMail->assign("CONTENIDO",$subPlantillaMail->text("contenido_principal"));
	$plantillaMail->parse("contenido_principal");
?>