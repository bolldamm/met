<?php
	$absolutePath=dirname(__FILE__);


	//Clase phpmailer
	require $absolutePath."/load_mailer.inc.php";
	
	require $absolutePath."/load_format_date.inc.php";
	
	
	//El dia que haya multidioma ya lo tocaremos(ingles a saco, es por paypal temas)
	$idIdioma=3;
	//Get conference data
	$resultConference=$db->callProcedure("CALL ed_sp_web_conferencia_actual()");
	$dataConference=$db->getData($resultConference);
	//Conference prices
	$priceExtraWorkshop=generalUtils::escaparCadena($dataConference["price_extra_workshop"]);
	$priceExtraMinisession=generalUtils::escaparCadena($dataConference["price_extra_minisession"]);
	$priceDinnerGuest=generalUtils::escaparCadena($dataConference["price_dinner_guest"]);
	$priceDinnerOptoutDiscount=generalUtils::escaparCadena($dataConference["price_dinner_optout_discount"]);
	$priceWineReceptionGuest=generalUtils::escaparCadena($dataConference["price_wine_reception_guest"]);
    //$priceOtherExtra=generalUtils::escaparCadena($dataConference["price_other_extra"]);

	//Conference email texts
/*	$emailToMet=generalUtils::escaparCadena($dataConference["email_to_met"]);
    $emailToUserPaypalIntro=generalUtils::escaparCadena($dataConference["email_to_user_paypal_intro"]);
    $emailToUserTransferIntro=generalUtils::escaparCadena($dataConference["email_to_user_transfer_intro"]);
    $emailToUserBody=generalUtils::escaparCadena($dataConference["email_to_user_body"]);
    $emailToUserSignoff=generalUtils::escaparCadena($dataConference["email_to_user_signoff"]);
*/
	$emailToMet=$dataConference["email_to_met"];
    $emailToUserPaypalIntro=$dataConference["email_to_user_paypal_intro"];
    $emailToUserTransferIntro=$dataConference["email_to_user_transfer_intro"];
    $emailToUserBody=$dataConference["email_to_user_body"];
    $emailToUserSignoff=$dataConference["email_to_user_signoff"];
      
	$resultadoConferencia=$db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_obtener_concreta(".$idInscripcion.",".$idIdioma.")");
	$datoConferencia=$db->getData($resultadoConferencia);

	$esFactura=1;
				
	//Billing information
	$nifFactura=generalUtils::escaparCadena($datoConferencia["nif_cliente_factura"]);
	$nombreClienteFactura=generalUtils::escaparCadena($datoConferencia["nombre_cliente_factura"]);
	$nombreEmpresaFactura=generalUtils::escaparCadena($datoConferencia["nombre_empresa_factura"]);
	$direccionFactura=generalUtils::escaparCadena($datoConferencia["direccion_factura"]);
	$codigoPostalFactura=generalUtils::escaparCadena($datoConferencia["codigo_postal_factura"]);
	$ciudadFactura=generalUtils::escaparCadena($datoConferencia["ciudad_factura"]);
	$provinciaFactura=generalUtils::escaparCadena($datoConferencia["provincia_factura"]);
	$paisFactura=generalUtils::escaparCadena($datoConferencia["pais_factura"]);
	$emailClienteFactura=generalUtils::escaparCadena($datoConferencia["correo_electronico"]);
	$firstName=generalUtils::escaparCadena($datoConferencia["nombre"]);

	//Plantilla principal
	$plantillaMail=new XTemplate($absolutePath."/../html/mail/mail_index_white_background.html");
		
	//Subplantilla  MET
	$subPlantillaMail=new XTemplate($absolutePath."/../html/mail/mail_conference_to_met.html");
	
	//Subplantilla  USER
	$subPlantillaMailUser=new XTemplate($absolutePath."/../html/mail/mail_conference_to_user.html");

	if($metodoPago==INSCRIPCION_TIPO_PAGO_TRANSFERENCIA){
		$metodoPagoDescripcion=INSCRIPCION_TIPO_PAGO_TRANSFERENCIA_DESCRIPTION;
		$tipoPagoMovimiento=MOVIMIENTO_TIPO_PAGO_TRANSFERENCIA;
		$pagado=0;
      
		//$subPlantillaMailUser->assign("MAIL_INSCRIPTION_TO_USER_INTRO_VALUE",STATIC_MAIL_INSCRIPCION_CONFERENCE_TO_USER_BANK_TRANSFER);
		$subPlantillaMailUser->assign("EMAIL_TO_USER_INTRO_VALUE",$emailToUserTransferIntro);
	}else if($metodoPago==INSCRIPCION_TIPO_PAGO_PAYPAL)	{
		$metodoPagoDescripcion=INSCRIPCION_TIPO_PAGO_PAYPAL_DESCRIPTION;
		$tipoPagoMovimiento=MOVIMIENTO_TIPO_PAGO_PAYPAL;
		$pagado=1;
		
		//$subPlantillaMailUser->assign("MAIL_INSCRIPTION_TO_USER_INTRO_VALUE",STATIC_MAIL_INSCRIPCION_CONFERENCE_TO_USER_PAYPAL);
		$subPlantillaMailUser->assign("EMAIL_TO_USER_INTRO_VALUE",$emailToUserPaypalIntro);
	}

	
	//Assign email-to-MET text to placeholder
    $subPlantillaMail->assign("EMAIL_TO_MET",$emailToMet);
    $subPlantillaMailUser->assign("EMAIL_TO_MET",$emailToMet);
	
	
	
	$firstName=$datoConferencia["nombre"];
	$nombre="";
	if($datoConferencia["tratamiento"]!=""){
		$nombre.=$datoConferencia["tratamiento"]." ";
	}
	$nombre.=$datoConferencia["nombre"]." ".$datoConferencia["apellidos"];

	$subPlantillaMail->assign("MAIL_INSCRIPTION_USER_FULL_NAME",$nombre);
	$subPlantillaMailUser->assign("MAIL_INSCRIPTION_USER_FIRST_NAME",$firstName);
	$subPlantillaMail->assign("MAIL_INSCRIPTION_TO_MET_EMAIL_VALUE",$datoConferencia["correo_electronico"]);
	
	if($datoConferencia["telefono"]){
		$subPlantillaMail->assign("MAIL_INSCRIPTION_TO_MET_PHONE_VALUE",$datoConferencia["telefono"]);
		$subPlantillaMail->parse("contenido_principal.bloque_phone");
	}
	
	if($datoConferencia["descripcion"]){
		$subPlantillaMail->assign("MAIL_INSCRIPTION_TO_MET_SISTER_ASSOCIATION_VALUE",$datoConferencia["descripcion"]);
		$subPlantillaMail->parse("contenido_principal.bloque_association");
		$subPlantillaMailUser->assign("MAIL_INSCRIPTION_TO_MET_SISTER_ASSOCIATION_VALUE",$datoConferencia["descripcion"]);
		$subPlantillaMailUser->parse("contenido_principal.bloque_association");
	}
	
	if($datoConferencia["speaker"]==1){
		$subPlantillaMail->parse("contenido_principal.bloque_speaker");
		$subPlantillaMailUser->parse("contenido_principal.bloque_speaker");
	}
	
	$esDinnerOptout=false;
	$dinnerOptoutDiscount = 0;
	if($datoConferencia["es_dinner"]==1){
		$subPlantillaMail->assign("MAIL_HAS_DINNER_VALUE",STATIC_GLOBAL_BUTTON_NO);
		$subPlantillaMailUser->assign("MAIL_HAS_DINNER_VALUE",STATIC_GLOBAL_BUTTON_NO);
		$subPlantillaMail->parse("contenido_principal.bloque_dinner");
		$subPlantillaMailUser->parse("contenido_principal.bloque_dinner");
		$esDinnerOptout=true;
		$dinnerOptoutDiscount = $priceDinnerOptoutDiscount;
	}else{
		$subPlantillaMail->assign("MAIL_HAS_DINNER_VALUE",STATIC_GLOBAL_BUTTON_YES);
		$subPlantillaMailUser->assign("MAIL_HAS_DINNER_VALUE",STATIC_GLOBAL_BUTTON_YES);
		$subPlantillaMail->parse("contenido_principal.bloque_dinner");
		$subPlantillaMailUser->parse("contenido_principal.bloque_dinner");
	}
	
	if($datoConferencia["email_permiso"]==1){
		$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_EMAIL_PERMISSION_VALUE",STATIC_GLOBAL_BUTTON_NO);
		
	}else{
		$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_EMAIL_PERMISSION_VALUE",STATIC_GLOBAL_BUTTON_YES);
	}
	
	if($datoConferencia["es_certificado"]==1){
		$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_CERTIFICATE_VALUE",STATIC_GLOBAL_BUTTON_YES);
	}else{
		$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_CERTIFICATE_VALUE",STATIC_GLOBAL_BUTTON_NO);
	}
	
	
	$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_BADGE_VALUE",$datoConferencia["conference_badge"]);
	
	//talleres
	$resultadoTallerListado=$db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_linea_obtener(".$idInscripcion.",".$idIdioma.")");
	
	require "load_format_date.inc.php";
	

	//List workshop choices
	$talleresNormales=0;
	$talleresMini=0;
	while($datoTallerListado=$db->getData($resultadoTallerListado)){
		$fechaActual=$datoTallerListado["fecha"];
		$fechaTaller = generalUtils::conversionFechaFormato($fechaActual, "-", "/");
		$mesTaller = explode("/", $fechaTaller);
	
		//Proceso para obtener dia de la semana
		$fechaTrozeada=explode("-",$fechaActual);
		
		if($datoTallerListado["es_mini"]==1){
			$talleresMini++;
		}else{
			$talleresNormales++;
		}//end else
		
		$fechaTimeStamp=mktime(0,0,0,$mesTaller[1],$mesTaller[0],$mesTaller[2]);
		$diaSemana=$vectorSemana[date("N",$fechaTimeStamp)];
		
		$fechaFormateada= $diaSemana.", ".intval($fechaTrozeada[2])." ".$vectorMes[$fechaTrozeada[1]];
		
		$subPlantillaMail->assign("WORKSHOP_FECHA",$fechaFormateada);
		$subPlantillaMail->assign("WORKSHOP_NOMBRE",$datoTallerListado["nombre"]);
		
		//Para user
		$subPlantillaMailUser->assign("WORKSHOP_NOMBRE",$datoTallerListado["nombre"]);
		$subPlantillaMailUser->assign("WORKSHOP_FECHA", $fechaFormateada);
		
		
		$subPlantillaMail->parse("contenido_principal.item_workshop");
		$subPlantillaMailUser->parse("contenido_principal.item_workshop");
	}
	
	
	/*********** INICIO: extra **************/
	
	$resultadoConferenciaExtra=$db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_extra_obtener_concreta(".$idInscripcion.")");
	$hayExcursion=false;
	$esDinnerGuest=false;
	$esWineReceptionGuest=false;
        $dinnerGuestCost=0;
        $wineReceptionGuestCost=0;
	while($datoConferenciaExtra=$db->getData($resultadoConferenciaExtra)){
		$valor=$datoConferenciaExtra["valor"];
		switch($datoConferenciaExtra["id_conferencia_extra"]){
			case 1:
				if($valor==1){
					$hayExcursion=true;
					$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_GUIDE_POBLET_VALUE",STATIC_GLOBAL_BUTTON_YES);
					$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_GUIDE_POBLET_VALUE",STATIC_GLOBAL_BUTTON_YES);
				}else{
					$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_GUIDE_POBLET_VALUE",STATIC_GLOBAL_BUTTON_NO);
					$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_GUIDE_POBLET_VALUE",STATIC_GLOBAL_BUTTON_NO);
				}
				
				break;
			case 2:
				if($valor>0){
					$esDinnerGuest=true;
				        $dinnerGuestCost = number_format(($priceDinnerGuest*$valor),2);

				        $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_DINNER_GUESTS_VALUE",$valor);
					$subPlantillaMail->parse("contenido_principal.bloque_dinner_guest");
					
					$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_DINNER_GUESTS_VALUE",$valor);
					$subPlantillaMailUser->parse("contenido_principal.bloque_dinner_guest");
				}

				break;
			case 3:
				if($valor==1){
					$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_PHOTO_PORTRAIT_VALUE",STATIC_GLOBAL_BUTTON_YES);
					$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_PHOTO_PORTRAIT_VALUE",STATIC_GLOBAL_BUTTON_YES);
				}else{
					$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_PHOTO_PORTRAIT_VALUE",STATIC_GLOBAL_BUTTON_NO);
					$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_PHOTO_PORTRAIT_VALUE",STATIC_GLOBAL_BUTTON_NO);
				}
				
				break;
			case 4:
				if($valor==1){
					$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_MORNING_WALK_VALUE",STATIC_GLOBAL_BUTTON_YES);
					$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_MORNING_WALK_VALUE",STATIC_GLOBAL_BUTTON_YES);
				}else{
					$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_MORNING_WALK_VALUE",STATIC_GLOBAL_BUTTON_NO);
					$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_MORNING_WALK_VALUE",STATIC_GLOBAL_BUTTON_NO);
				}
				
				break;
			case 5:
				if($valor==1){
					$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_WINE_VALUE",STATIC_GLOBAL_BUTTON_YES);
					$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_WINE_VALUE",STATIC_GLOBAL_BUTTON_YES);
				}else{
					$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_WINE_VALUE",STATIC_GLOBAL_BUTTON_NO);
					$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_WINE_VALUE",STATIC_GLOBAL_BUTTON_NO);
				}
				
				break;
			case 6:
				if($valor==1){
					$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_MORNING_WALK_2_VALUE",STATIC_GLOBAL_BUTTON_YES);
					$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_MORNING_WALK_2_VALUE",STATIC_GLOBAL_BUTTON_YES);
				}else{
					$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_MORNING_WALK_2_VALUE",STATIC_GLOBAL_BUTTON_NO);
					$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_MORNING_WALK_2_VALUE",STATIC_GLOBAL_BUTTON_NO);
				}
				
				break;
			case 7:
				if($valor>0){
					$esWineReceptionGuest=true;
                  		$wineReceptionGuestCost = number_format(($priceWineReceptionGuest*$valor),2);

				        $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_WINE_RECEPTION_GUESTS_VALUE",$valor);
					$subPlantillaMail->parse("contenido_principal.bloque_wine_reception_guest");
					
					$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_WINE_RECEPTION_GUESTS_VALUE",$valor);
					$subPlantillaMailUser->parse("contenido_principal.bloque_wine_reception_guest");
				}

				break;
		}
	}
	
	/*********** FIN: extra **************/

	$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_PAYMENT_METHOD_VALUE",$metodoPagoDescripcion);
	$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_PAYMENT_METHOD_VALUE",$metodoPagoDescripcion);

	$esExtraWorkshop=false;
	$extraWorkshopAmount = 0;	
	if($talleresNormales==2){
		$esExtraWorkshop=true;		
		$extraWorkshopAmount=$priceExtraWorkshop;
	}else if($talleresNormales==1){
		if($talleresMini==1){
		$esExtraWorkshop=true;	
		$extraWorkshopAmount=$priceExtraMinisession;
		}else if($talleresMini==2){
		$esExtraWorkshop=true;	
		$extraWorkshopAmount=$priceExtraWorkshop;
		}
	}else if($talleresMini==3){
		$esExtraWorkshop=true;
		$extraWorkshopAmount=$priceExtraMinisession;
	}else if($talleresMini==4){
		$esExtraWorkshop=true;
		$extraWorkshopAmount=$priceExtraWorkshop;
	}//end else

	$totalPrice=$datoConferencia["importe_total"];
	
	$basicConferenceFee = number_format(($totalPrice - $extraWorkshopAmount - $dinnerGuestCost - $wineReceptionGuestCost + $dinnerOptoutDiscount),2);
	
	$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_FEE_VALUE","&euro;".$basicConferenceFee);
	$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_FEE_VALUE","&euro;".$basicConferenceFee);
		
	if($esExtraWorkshop){
		$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_EXTRA_WORKSHOP_FEE_VALUE","&euro;".$extraWorkshopAmount);
		$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_EXTRA_WORKSHOP_FEE_VALUE","&euro;".$extraWorkshopAmount);
		$subPlantillaMail->parse("contenido_principal.bloque_extra_workshop_fee");
		$subPlantillaMailUser->parse("contenido_principal.bloque_extra_workshop_fee");
	}

	if($esDinnerGuest){
		$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_DINNER_GUEST_COST_VALUE","&euro;".$dinnerGuestCost);
		$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_DINNER_GUEST_COST_VALUE","&euro;".$dinnerGuestCost);
		$subPlantillaMail->parse("contenido_principal.bloque_dinner_guest_cost");
		$subPlantillaMailUser->parse("contenido_principal.bloque_dinner_guest_cost");
	}

	if($esWineReceptionGuest){
		$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_WINE_RECEPTION_GUEST_COST_VALUE","&euro;".$wineReceptionGuestCost);
		$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_WINE_RECEPTION_GUEST_COST_VALUE","&euro;".$wineReceptionGuestCost);
		$subPlantillaMail->parse("contenido_principal.bloque_wine_reception_guest_cost");
		$subPlantillaMailUser->parse("contenido_principal.bloque_wine_reception_guest_cost");
	}

	if($esDinnerOptout){
		$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_DINNER_OPTOUT_COST_VALUE","- &euro;".$dinnerOptoutDiscount);
		$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_DINNER_OPTOUT_COST_VALUE","- &euro;".$dinnerOptoutDiscount);
		$subPlantillaMail->parse("contenido_principal.bloque_dinner_optout_cost");
		$subPlantillaMailUser->parse("contenido_principal.bloque_dinner_optout_cost");
	}

	$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_AMOUNT_PAYABLE_VALUE","&euro;".$totalPrice);
	$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_AMOUNT_PAYABLE_VALUE","&euro;".$totalPrice);
	
	if($datoConferencia["comentarios"]){
		$subPlantillaMail->assign("MAIL_INSCRIPTION_TO_MET_COMMENTS_VALUE",$datoConferencia["comentarios"]);
		$subPlantillaMail->parse("contenido_principal.bloque_comment");
		$subPlantillaMailUser->assign("MAIL_INSCRIPTION_TO_MET_COMMENTS_VALUE",$datoConferencia["comentarios"]);
		$subPlantillaMailUser->parse("contenido_principal.bloque_comment");
	}

	$nonTaxable = 1; //conference signups are non-taxable, right?
	
	//Insertamos un movimiento si estamos haciendo una nueva reserva
	$resultadoMovimiento=$db->callProcedure("CALL ed_sp_web_movimiento_insertar(".MOVIMIENTO_TIPO_ENTRADA.",".$idConceptoMovimiento.",".$nonTaxable.",".$tipoPagoMovimiento.",null,'".generalUtils::escaparCadena($nombre)."','".date("Y-m-d")."','".STATIC_MOVEMENT_NEW_CONFERENCE_DESCRIPTION."','".$totalPrice."',".$pagado.")");
	$datoMovimiento = $db->getData($resultadoMovimiento);
	$idMovimiento = $datoMovimiento["id_movimiento"];
	
	//Insertamos movimiento-inscripcion
	$db->callProcedure("CALL ed_sp_web_movimiento_inscripcion_conferencia_insertar(".$idMovimiento.",".$idInscripcion.")");
	
	
	if(isset($comision)){
		$resultadoMovimientoComision=$db->callProcedure("CALL ed_sp_web_movimiento_insertar(".MOVIMIENTO_TIPO_SALIDA.",".MOVIMIENTO_CONCEPTO_FEE_GENERAL_BANKING.",".$tipoPagoMovimiento.",null,'".STATIC_GLOBAL_PAYPAL_FEE."','".date("Y-m-d")."','".generalUtils::escaparCadena($nombrePersonaCompleto)."','".$comision."',".$pagado.")");		
		$datoMovimientoComision = $db->getData($resultadoMovimientoComision);
		$idMovimientoComision = $datoMovimientoComision["id_movimiento"];
		
		//Insertamos movimiento-inscripcion
		$db->callProcedure("CALL ed_sp_web_movimiento_inscripcion_conferencia_insertar(".$idMovimientoComision.",".$idInscripcion.")");
	
	}
	
	
	// Commented out by Stephen because invoice is required always		
	//if($esFactura==1){		
		$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_BILLING_NAME_VALUE",$nombreClienteFactura);
		//$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_INVOICE_REQUIRED_TO_USER_VALUE",STATIC_GLOBAL_BUTTON_YES);
	
		if($nombreEmpresaFactura!=""){
			$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_BILLING_COMPANY_NAME_VALUE",$nombreEmpresaFactura);
			$subPlantillaMail->parse("contenido_principal.bloque_invoice.bloque_invoice_company_name");
		}
		$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_BILLING_ADDRESS_VALUE",$direccionFactura);
		$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_BILLING_ZIPCODE_VALUE",$codigoPostalFactura);
		$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_BILLING_CITY_VALUE",$ciudadFactura);
		
		if($provinciaFactura!=""){
			$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_BILLING_BILLING_PROVINCE_VALUE",$provinciaFactura);
			$subPlantillaMail->parse("contenido_principal.bloque_invoice.bloque_invoice_province");
		}

		$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_BILLING_BILLING_COUNTRY_VALUE",$paisFactura);


		//Ponemos factura en el e-mail
		$subPlantillaMail->parse("contenido_principal.bloque_invoice");
	//}else{
	//	$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_INVOICE_REQUIRED_TO_USER_VALUE",STATIC_GLOBAL_BUTTON_NO);
	//}
	
		//Insertamos factura		

		$resultadoFactura=$db->callProcedure("CALL ed_sp_web_factura_insertar('".$nifFactura."','".$nombreClienteFactura."','".$nombreEmpresaFactura."','".$direccionFactura."','".$codigoPostalFactura."','".$ciudadFactura."','".$provinciaFactura."','".$paisFactura."','".$emailClienteFactura."','".$firstName."')");		

		$datoFactura=$db->getData($resultadoFactura);
		$idFactura=$datoFactura["id_factura"];
		
		//Insertamos linea factura
		$db->callProcedure("CALL ed_sp_web_linea_factura_insertar(".$idFactura.",".$idMovimiento.")");

        //Establecemos cuerpo del mensaje
        $subPlantillaMailUser->assign("EMAIL_TO_USER_BODY",$emailToUserBody);
		$subPlantillaMailUser->assign("EMAIL_TO_USER_SIGNOFF",$emailToUserSignoff);


	
	$subPlantillaMail->parse("contenido_principal");

	$subPlantillaMailUser->parse("contenido_principal");

	//Primero mail a MET
	
	$mail->Body=$subPlantillaMail->text("contenido_principal");

	/**
	 * 
	 * Incluimos la configuracion del componente phpmailer
	 * 
	 */
	
	$mail->AddAddress(STATIC_MAIL_TO_METM_REG_FORM);


	$mail->FromName = STATIC_MAIL_FROM;
	//$mail->Subject = STATIC_MAIL_INSCRIPTION_CONFERENCE_SUBJECT.": ".$nombre;
	$mail->Subject = $emailToMet.": ".$nombre;
	

	//Enviamos correo
	if($mail->Send()){
		/****** Guardamos el log del correo electronico ******/
		$idUsuarioWebCorreo="null";
		
		//Tipo correo electronico
		$idTipoCorreoElectronico=EMAIL_TYPE_CONFERENCE_FORM_TO_MET;
		
		
		//Destinatario
		$vectorDestinatario=Array();

		array_push($vectorDestinatario,STATIC_MAIL_TO_METM_REG_FORM);
		
		
		//Asunto
		$asunto=$mail->Subject;
		$cuerpo=$mail->Body;
		
		require $absolutePath."/load_log_email.inc.php";
		
		
		
		//Pasemos al email que se le envia al usuario
		
		//Mail al usuario
		$mail->ClearAllRecipients();
		$mail->AddAddress($datoConferencia["correo_electronico"]);
        $mail->AddBCC("metm_registration@metmeetings.org");
		$plantillaMail->assign("CONTENIDO",$subPlantillaMailUser->text("contenido_principal"));
		
		$plantillaMail->parse("contenido_principal");
		
		$mail->Subject = STATIC_MAIL_INSCRIPTION_CONFERENCE_SUBJECT;
		$mail->Body=$plantillaMail->text("contenido_principal");
		$mail->Send();
		
		//Tipo correo electronico
		$idTipoCorreoElectronico=EMAIL_TYPE_CONFERENCE_FORM_TO_USER;
		
		$vectorDestinatario=Array();
		array_push($vectorDestinatario,$datoConferencia["correo_electronico"]);
		
		//Asunto
		$asunto=$mail->Subject;
		$cuerpo=$mail->Body;
		
		require $absolutePath."/load_log_email.inc.php";
	}
?>