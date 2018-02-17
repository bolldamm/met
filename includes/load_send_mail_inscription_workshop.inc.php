<?php

/*
 * Generates and sends signup emails, inserts associated movement and logs the emails
 */

	$absolutePath=dirname(__FILE__);


	//Clase phpmailer
	require $absolutePath."/load_mailer.inc.php";
	
	require $absolutePath."/load_format_date.inc.php";
	
	
	//El dia que haya multidioma ya lo tocaremos(ingles a saco, es por paypal temas)
	$idIdioma=3;
	//Resultado workshop
	$resultadoTallerConcreto=$db->callProcedure("CALL ed_sp_web_inscripcion_taller_obtener_concreta(".$idInscripcion.",".$idIdioma.")");
	$datoTallerConcreto=$db->getData($resultadoTallerConcreto);

	$esFactura=1;
	//$esFactura=$datoTallerConcreto["es_factura"];
				
	//Billing information
	$nifFactura=generalUtils::escaparCadena($datoTallerConcreto["nif_cliente_factura"]);
	$nombreClienteFactura=generalUtils::escaparCadena($datoTallerConcreto["nombre_cliente_factura"]);
	$nombreEmpresaFactura=generalUtils::escaparCadena($datoTallerConcreto["nombre_empresa_factura"]);
	$direccionFactura=generalUtils::escaparCadena($datoTallerConcreto["direccion_factura"]);
	$codigoPostalFactura=generalUtils::escaparCadena($datoTallerConcreto["codigo_postal_factura"]);
	$ciudadFactura=generalUtils::escaparCadena($datoTallerConcreto["ciudad_factura"]);
	$provinciaFactura=generalUtils::escaparCadena($datoTallerConcreto["provincia_factura"]);
	$paisFactura=generalUtils::escaparCadena($datoTallerConcreto["pais_factura"]);
	$emailClienteFactura=generalUtils::escaparCadena($datoTallerConcreto["correo_electronico"]);
	$firstName=generalUtils::escaparCadena($datoTallerConcreto["nombre"]);
	
	//Plantilla principal
	$plantillaMail=new XTemplate($absolutePath."/../html/mail/mail_index.html");
	
	if($metodoPago==INSCRIPCION_TIPO_PAGO_TRANSFERENCIA){
		$tipoPagoMovimiento=MOVIMIENTO_TIPO_PAGO_TRANSFERENCIA;
		if(!isset($pagado)){
			$pagado=0;
		}
		
	}else if($metodoPago==INSCRIPCION_TIPO_PAGO_PAYPAL)	{
		$tipoPagoMovimiento=MOVIMIENTO_TIPO_PAGO_PAYPAL;
		$pagado=1;
	}
	
	
	
	//Subplantilla  MET
	$subPlantillaMail=new XTemplate($absolutePath."/../html/mail/mail_workshop_to_met.html");
	
	//Subplantilla  USER
	$subPlantillaMailUser=new XTemplate($absolutePath."/../html/mail/mail_workshop_to_user.html");
	
	$nombre="";
	if($datoTallerConcreto["tratamiento"]!=""){
		$nombre.=$datoTallerConcreto["tratamiento"]." ";
	}
	$nombre.=$datoTallerConcreto["nombre"]." ".$datoTallerConcreto["apellidos"];
	
	$subPlantillaMail->assign("MAIL_INSCRIPTION_TO_MET_NAME_VALUE",$nombre);
	$subPlantillaMailUser->assign("MAIL_INSCRIPTION_TO_USER_NAME_VALUE",$nombre);
	$subPlantillaMail->assign("MAIL_INSCRIPTION_TO_MET_EMAIL_VALUE",$datoTallerConcreto["correo_electronico"]);
	
	if($datoTallerConcreto["telefono"]){
		$subPlantillaMail->assign("MAIL_INSCRIPTION_TO_MET_PHONE_VALUE",$datoTallerConcreto["telefono"]);
		$subPlantillaMail->parse("contenido_principal.bloque_phone");
	}
	
	if($datoTallerConcreto["descripcion"]){
		$subPlantillaMail->assign("MAIL_INSCRIPTION_TO_MET_SISTER_ASSOCIATION_VALUE",$datoTallerConcreto["descripcion"]);
		$subPlantillaMail->parse("contenido_principal.bloque_association");
	}

	//talleres
	$resultadoTallerListado=$db->callProcedure("CALL ed_sp_web_inscripcion_taller_linea_obtener(".$idInscripcion.",".$idIdioma.")");

	
	//Taller listado
	$precio=0;
	while($datoTallerListado=$db->getData($resultadoTallerListado)){
		$subPlantillaMail->assign("WORKSHOP_NOMBRE",$datoTallerListado["nombre"]);
		
		//Para user
		$subPlantillaMailUser->assign("WORKSHOP_NOMBRE",$datoTallerListado["nombre"]);
		
		
		$fechaTaller = generalUtils::conversionFechaFormato($datoTallerListado["fecha"], "-", "/");
		$mesTaller = explode("/", $fechaTaller);

		//Proceso para obtener dia de la semana
		$fechaTrozeada=explode("-",$datoTallerListado["fecha"]);
		$subPlantillaMailUser->assign("WORKSHOP_FECHA", intval($fechaTrozeada[2])." ".$vectorMes[$fechaTrozeada[1]]);
		$subPlantillaMailUser->assign("WORKSHOP_PRECIO",$datoTallerListado["importe"]);
		
		
		$subPlantillaMail->parse("contenido_principal.item_workshop");
		$subPlantillaMailUser->parse("contenido_principal.item_workshop");
		$precio+=$datoTallerListado["importe"];
	}
	
	
	if($datoTallerConcreto["comentarios"]){
		$subPlantillaMail->assign("MAIL_INSCRIPTION_TO_MET_COMMENTS_VALUE",$datoTallerConcreto["comentarios"]);
		$subPlantillaMail->parse("contenido_principal.bloque_comment");
	}

	//Segun seamos(sister association,logueados o no miembors)
	if($datoTallerConcreto["id_usuario_web"]!=""){
		$asuntoTipoInscrito=STATIC_MAIL_INSCRIPTION_WORKSHOP_SUBJECT_MEMBER_TYPE;
		$subPlantillaMailUser->assign("FORM_WORKSHOP_REGISTER_LEGEND_WORKSHOPS_PRICE",STATIC_FORM_WORKSHOP_REGISTER_LEGEND_WORKSHOPS_PRICE);
	}else{
		if($datoTallerConcreto["id_asociacion_hermana"]==""){
			$asuntoTipoInscrito=STATIC_MAIL_INSCRIPTION_WORKSHOP_SUBJECT_NON_MEMBER_TYPE;
			$subPlantillaMailUser->assign("FORM_WORKSHOP_REGISTER_LEGEND_WORKSHOPS_PRICE",STATIC_FORM_WORKSHOP_REGISTER_LEGEND_WORKSHOPS_PRICE_NON_MEMBER);
		}else{
			$asuntoTipoInscrito=STATIC_MAIL_INSCRIPTION_WORKSHOP_SUBJECT_SISTER_ASSOCIATION_TYPE;
			$subPlantillaMailUser->assign("FORM_WORKSHOP_REGISTER_LEGEND_WORKSHOPS_PRICE",STATIC_FORM_WORKSHOP_REGISTER_LEGEND_WORKSHOPS_PRICE_SISTER);
		}
	}

	
	
	$subPlantillaMail->parse("contenido_principal");
	$subPlantillaMailUser->parse("contenido_principal");
	

	
	//Establecemos cuerpo del mensaje
	
	
	//Primero mail a MET
	
	$mail->Body=$subPlantillaMail->text("contenido_principal");

    $nonTaxable = 1; //workshops signups are non-taxable, right?
	
	//Insertamos un movimiento si estamos haciendo una nueva reserva
	$resultadoMovimiento=$db->callProcedure("CALL ed_sp_web_movimiento_insertar(".MOVIMIENTO_TIPO_ENTRADA.",".$idConceptoMovimiento.",".$nonTaxable.",".$tipoPagoMovimiento.",null,'".generalUtils::escaparCadena($nombre)."','".date("Y-m-d")."','".STATIC_MOVEMENT_NEW_WORKSHOP_DESCRIPTION."','".$precio."',".$pagado.")");
	$datoMovimiento = $db->getData($resultadoMovimiento);
	$idMovimiento = $datoMovimiento["id_movimiento"];
	
	//Insertamos movimiento-inscripcion
	$db->callProcedure("CALL ed_sp_web_movimiento_inscripcion_taller_insertar(".$idMovimiento.",".$idInscripcion.")");
	
	
	if(isset($comision)){
		$resultadoMovimientoComision=$db->callProcedure("CALL ed_sp_web_movimiento_insertar(".MOVIMIENTO_TIPO_SALIDA.",".MOVIMIENTO_CONCEPTO_FEE_GENERAL_BANKING.",".$tipoPagoMovimiento.",null,'".STATIC_GLOBAL_PAYPAL_FEE."','".date("Y-m-d")."','".generalUtils::escaparCadena($nombrePersonaCompleto)."','".$comision."',".$pagado.")");		
		$datoMovimientoComision = $db->getData($resultadoMovimientoComision);
		$idMovimientoComision = $datoMovimientoComision["id_movimiento"];
		
		//Insertamos movimiento-inscripcion
		$db->callProcedure("CALL ed_sp_web_movimiento_inscripcion_taller_insertar(".$idMovimientoComision.",".$idInscripcion.")");
	
	}
	
		//Insertamos factura		
		$resultadoFactura=$db->callProcedure("CALL ed_sp_web_factura_insertar('".$nifFactura."','".$nombreClienteFactura."','".$nombreEmpresaFactura."','".$direccionFactura."','".$codigoPostalFactura."','".$ciudadFactura."','".$provinciaFactura."','".$paisFactura."','".$emailClienteFactura."','".$firstName."')");

		$datoFactura=$db->getData($resultadoFactura);
		$idFactura=$datoFactura["id_factura"];
		
		//Insertamos linea factura
		$db->callProcedure("CALL ed_sp_web_linea_factura_insertar(".$idFactura.",".$idMovimiento.")");


	/**
	 * 
	 * Incluimos la configuracion del componente phpmailer
	 * 
	 */
	
	$mail->AddAddress("drntyt@gmail.com");
	/*$mail->AddBCC(STATIC_MAIL_TO);
	$mail->AddBCC(STATIC_MAIL_TO_TREASURER);*/
	$mail->FromName = STATIC_MAIL_FROM;
	$mail->Subject = STATIC_MAIL_INSCRIPTION_WORKSHOP_SUBJECT.": ".$nombre." (".$asuntoTipoInscrito.")";
	
	
	//Enviamos correo
	if($mail->Send()){
		/****** Guardamos el log del correo electronico ******/
		$idUsuarioWebCorreo="null";
		
		//Tipo correo electronico
		$idTipoCorreoElectronico=EMAIL_TYPE_WORKSHOP_FORM_TO_MET;

		//define("EMAIL_TYPE_WORKSHOP_FORM_TO_USER",16);
		
		
		//Destinatario
		$vectorDestinatario=Array();

		array_push($vectorDestinatario,STATIC_MAIL_TO_WORKSHOP_REG_FORM);
		//array_push($vectorDestinatario,STATIC_MAIL_DEVELOPMENT_TO);
		
		//Asunto
		$asunto=$mail->Subject;
		$cuerpo=$mail->Body;
		
		require $absolutePath."/load_log_email.inc.php";
		
		
		
		//Pasemos al email que se le envia al usuario
		
		//Mail al usuario
		$mail->ClearAllRecipients();
		$mail->AddAddress($datoTallerConcreto["correo_electronico"]);
		$plantillaMail->assign("CONTENIDO",$subPlantillaMailUser->text("contenido_principal"));
		
		$plantillaMail->parse("contenido_principal");
		
		$mail->Subject = STATIC_MAIL_INSCRIPTION_WORKSHOP_SUBJECT;
		$mail->Body=$plantillaMail->text("contenido_principal");
		$mail->Send();
		
		//Tipo correo electronico
		$idTipoCorreoElectronico=EMAIL_TYPE_WORKSHOP_FORM_TO_USER;
		
		$vectorDestinatario=Array();
		array_push($vectorDestinatario,$datoTallerConcreto["correo_electronico"]);
		
		//Asunto
		$asunto=$mail->Subject;
		$cuerpo=$mail->Body;
		
		require $absolutePath."/load_log_email.inc.php";
	}
?>