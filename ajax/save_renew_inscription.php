<?php
	/**
	 * 
	 * Es el script principal para realizar la inscripcion y generar el tpv
	 * 
	 */

	require "../includes/load_main_components.inc.php";
	require "../includes/load_validate_user_web.inc.php";
	
	$esValido=true;
	$mensajeError="";
	
	if($_SESSION["met_user"]["pagado"]==0){
		//No podemos hacer renew si tenemos pagado a 0 en la actual inscripcion
		die();
	}
	
	//Proceso donde nos indica si todo ha ido bien...
	$plantilla=new XTemplate("../html/ajax/process_form.html");
	
	
	if($_SESSION["met_user"]["id_modalidad"]==MODALIDAD_USUARIO_INDIVIDUAL){
		$importe=PRECIO_MODALIDAD_USUARIO_INDIVIDUAL;
		$idConceptoMovimiento=MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP;
		
		$idSituacionAdicional="null";
		$vectorSituacionAdicional=Array(SITUACION_ADICIONAL_JUBILADO,SITUACION_ADICIONAL_ESTUDIANTE);
		
		if(in_array($_POST["cmbSituacionAdicional"],$vectorSituacionAdicional)){
			$idSituacionAdicional=$_POST["cmbSituacionAdicional"];
			$importe=PRECIO_MODALIDAD_USUARIO_INDIVIDUAL_DESCUENTO;
			
			//Si somos retirados entonces...
			if($idSituacionAdicional==SITUACION_ADICIONAL_JUBILADO){
				$idConceptoMovimiento=MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP_RETIRED;
			}else if($idSituacionAdicional==SITUACION_ADICIONAL_ESTUDIANTE){
				$idConceptoMovimiento=MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP_STUDENT;
			}
			
			$db->callProcedure("CALL ed_sp_web_usuario_web_individual_situacion_adicional_editar(".$_SESSION["met_user"]["id"].",".$idSituacionAdicional.")");
		}
	
	}else if($_SESSION["met_user"]["id_modalidad"]==MODALIDAD_USUARIO_INSTITUTIONAL){
		$importe=PRECIO_MODALIDAD_USUARIO_INSTITUTIONAL;
		$idConceptoMovimiento=MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP_INSTITUTIONAL;
	}


	//Datos billing
	$_POST["txtFacturacionNifCliente"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_CUSTOMER_NIF,$_POST["txtFacturacionNifCliente"]));
	$_POST["txtFacturacionNombreCliente"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_NAME_CUSTOMER,$_POST["txtFacturacionNombreCliente"]));
	$_POST["txtFacturacionNombreEmpresa"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_NAME_COMPANY,$_POST["txtFacturacionNombreEmpresa"]));
	$_POST["txtFacturacionDireccion"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_ADDRESS,$_POST["txtFacturacionDireccion"]));
	$_POST["txtFacturacionCodigoPostal"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_ZIPCODE,$_POST["txtFacturacionCodigoPostal"]));
	$_POST["txtFacturacionCiudad"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_CITY,$_POST["txtFacturacionCiudad"]));
	$_POST["txtFacturacionProvincia"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_PROVINCE,$_POST["txtFacturacionProvincia"]));
	$_POST["txtFacturacionPais"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_COUNTRY,$_POST["txtFacturacionPais"]));
	
	//Para guardar en base de datos en un proceso posterior
	$nifFactura=$_POST["txtFacturacionNifCliente"];
	$nombreClienteFactura=$_POST["txtFacturacionNombreCliente"];
	$nombreEmpresaFactura=$_POST["txtFacturacionNombreEmpresa"];
	$direccionFactura=$_POST["txtFacturacionDireccion"];
	$codigoPostalFactura=$_POST["txtFacturacionCodigoPostal"];
	$ciudadFactura=$_POST["txtFacturacionCiudad"];
	$provinciaFactura=$_POST["txtFacturacionProvincia"];
	$paisFactura=$_POST["txtFacturacionPais"];
	
	
	//Establecemos el tipo de pago
	$metodoPago=$_POST["rdMetodoPago"];
	$vectorMetodoPago=Array(INSCRIPCION_TIPO_PAGO_TRANSFERENCIA,INSCRIPCION_TIPO_PAGO_PAYPAL,INSCRIPCION_TIPO_PAGO_DEBIT);
	
	if(!in_array($metodoPago, $vectorMetodoPago)){
		$esValido=false;
	}
	
	//Si todas las validaciones son correctas
	if($esValido){						
		$db->startTransaction();
		
		$esFactura=1;
		if($metodoPago==INSCRIPCION_TIPO_PAGO_PAYPAL){
			//Si estamos pagando por paypal o tpv... el estado es pendiente hasta que se haga el pago
			$idEstadoInscripcion=INSCRIPCION_ESTADO_INSCRIPCION_PENDIENTE;
		}else{
			$pagado=0;
			$idEstadoInscripcion=INSCRIPCION_ESTADO_INSCRIPCION_CONFIRMADA;
		}
		
		$fechaInscripcion=date("Y-m-d G:i:s");
		$fechaHoraDesglosada=explode(" ", $fechaInscripcion);
		$fechaDesglosada=explode("-",$fechaHoraDesglosada[0]);
		$idUsuarioWeb=$_SESSION["met_user"]["id"];
		
		// Commented out by Stephen and set $esFactura=1 (above) in case billing details error is
		// due to browser not reading value of chkInvoice because checkbox is hidden
		//Si queremos factura...
		//if(isset($_POST["chkInvoice"])){
		//	$esFactura=1;
			$db->callProcedure("CALL ed_sp_web_usuario_web_actualizar(".$idUsuarioWeb.",'".$_POST["txtFacturacionNifCliente"]."','".$_POST["txtFacturacionNombreCliente"]."','".$_POST["txtFacturacionNombreEmpresa"]."','".$_POST["txtFacturacionDireccion"]."','".$_POST["txtFacturacionCodigoPostal"]."','".$_POST["txtFacturacionCiudad"]."','".$_POST["txtFacturacionProvincia"]."','".$_POST["txtFacturacionPais"]."')");
		//}
		



		//Obtenemos la fecha finalizacion de la ultima inscripcion
		$resultadoInscripcionPrevia=$db->callProcedure("CALL ed_sp_web_inscripcion_previa(".$idUsuarioWeb.")");
		$datoInscripcionPrevia=$db->getData($resultadoInscripcionPrevia);
		$fechaActual=date("Y-m-d");
		$fechaPrevia=$datoInscripcionPrevia["fecha_finalizacion"];

		$fechaPreviaHoraDesglosada=explode(" ", $fechaPrevia);
		$fechaPreviaDesglosada=explode("-",$fechaPreviaHoraDesglosada[0]);
		
		
		$fechaActualAux=generalUtils::conversionFechaFormato($fechaActual,"-","/");
		$fechaPreviaAux=generalUtils::conversionFechaFormato($fechaPrevia,"-","/");

		//Si aun falta para le fecha de finalizacion..., hacemos que caduque para el año siguiente
		if(generalUtils::compararFechas($fechaActualAux,$fechaPreviaAux)<=2){
			$nuevoAnyo=$fechaPreviaDesglosada[0]+1;
			$fechaFinalizacion=$nuevoAnyo."-12-31";
		}else{
			//Si entramos aqui, es que estamos renovando cuando esta caducado
			
			//If renewing after 30 September, set expiry to 31/12 of following year
			if($fechaDesglosada[1]>9){
				$fechaFinalizacion=($fechaDesglosada[0]+1)."-12-31";
			}else{
				$fechaFinalizacion=($fechaDesglosada[0])."-12-31";
			}
			
		}

	
		//Insertamos inscripcion
		$resultadoInscripcion=$db->callProcedure("CALL ed_sp_web_inscripcion_insertar(".$idEstadoInscripcion.",".$metodoPago.",".$idUsuarioWeb.",'".$importe."','".$fechaInscripcion."','".$fechaFinalizacion."',".$esFactura.")");
		$datoInscripcion=$db->getData($resultadoInscripcion);
		
		//Si estamos trabajando con tpv entonces...
		$plantilla->assign("TIPO_RESULTADO_INSCRIPCION",$metodoPago);
		switch($metodoPago){
			case INSCRIPCION_TIPO_PAGO_PAYPAL:
				$subPlantilla=new XTemplate("../html/ajax/paypal_form.html");
				$serverActual="http://www.metmeetings.org";
				
				//Generamos encriptacion		
				require "../classes/paypal/clase.paypal.php";
				$config = array(
		            "cert_id" => "PR3V5CJ4DN4BA",
		            "business" => "metmember@gmail.com",
		            "openssl" => "/usr/bin/openssl",
		            "my_cert" => "../classes/paypal/certificados/8708ec99f0a753dddea0757b7d35297d-pubcert.pem",
		            "my_key" => "../classes/paypal/certificados/8708ec99f0a753dddea0757b7d35297d-prvkey.pem",
		            "paypal_cert" => "../classes/paypal/certificados/paypal_cert.pem"
		    	);
    	
    	
		
				//Informacion para paypal
				$form["charset"]="UTF-8";	
				$form["cmd"]="_xclick";

	
		
				
				//Variables paypal
				$form["item_name"]="MET";
				$form["amount"]=$importe;
		

			    $paypal = new PayPal($config);
	    

			    $form["return"]=$serverActual."/inscripcion_finalizada.php?tipo=2";
			    $form["notify_url"]=$serverActual."/ajax/last_step_inscription_paypal.php";
			    $form["return_cancel"]=$serverActual."/inscripcion_finalizada.php?tipo=0";
			    $form["rb"]="2";
			    $form["bn"]="MET";
			    $form["upload"]="1";
			    $form["business"]="metmember@gmail.com";
			    $form["currency_code"]="EUR";
			   	$form["custom"]=$datoInscripcion["numero_inscripcion"]."-".$datoInscripcion["codigo"]."-2";


				//Encriptacion
			   	$subPlantilla->assign("ENCRYPT",$paypal->encrypt($form));
				
				
				//Parseamos toda la info
				$subPlantilla->parse("contenido_principal");
				
				//Exportamos a la plantilla principal
				$plantilla->assign("FORMULARIO_ADICIONAL",$subPlantilla->text("contenido_principal"));
				break;
			case INSCRIPCION_TIPO_PAGO_TRANSFERENCIA:
				$numeroInscripcion=$datoInscripcion["numero_inscripcion"];
				$tipoInscripcion=2;
				$nombreUsuario=$_SESSION["met_user"]["name"];
				$apellidosUsuario=$_SESSION["met_user"]["lastname"];
				$emailUsuario=$_SESSION["met_user"]["email"];
				$idInscripcion=$datoInscripcion["codigo"];
				$idUsuarioWebCorreo=$idUsuarioWeb;
				require "../includes/load_send_mail_inscription.inc.php";
				break;
			case INSCRIPCION_TIPO_PAGO_DEBIT:
				$numeroInscripcion=$datoInscripcion["numero_inscripcion"];
				$tipoInscripcion=2;
				$nombreUsuario=$_SESSION["met_user"]["name"];
				$apellidosUsuario=$_SESSION["met_user"]["lastname"];
				$emailUsuario=$_SESSION["met_user"]["email"];
				$idInscripcion=$datoInscripcion["codigo"];
				$idUsuarioWebCorreo=$idUsuarioWeb;
				require "../includes/load_send_mail_inscription.inc.php";
				break;
		}
		
		
		$db->endTransaction();
		
	}else{
		$plantilla->assign("TIPO_RESULTADO_INSCRIPCION",STATIC_FORM_MEMBERSHIP_EMAIL_REPEAT);
	}//end else
	
	
	$plantilla->parse("contenido_principal");
	$plantilla->out("contenido_principal");
?>