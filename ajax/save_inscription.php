<?php
	/**
	 * 
	 * Es el script principal para realizar la inscripcion y generar el tpv
	 * 
	 */

	require "../includes/load_main_components.inc.php";
	
	$esValido=true;
	$mensajeError="";
	
	
	//Proceso donde nos indica si todo ha ido bien...
	$plantilla=new XTemplate("../html/ajax/process_form.html");
	
	
	//Miembro individual
	if($_POST["hdnIdModalidad"]==MODALIDAD_USUARIO_INDIVIDUAL){
			
		if($_POST["chkSex"]==1){
			$esHombre=1;
		}else{
			$esHombre=0;
		}
		
		if($_POST["cmbAnyos"]==-1){
			$_POST["cmbAnyos"]="null";
		}
		
		
		$_POST["txtEmailUser"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_EMAIL_USER,$_POST["txtEmailUser"]));
		$_POST["txtNombre"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_FIRST_NAME,$_POST["txtNombre"]));
		$_POST["txtApellidos"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_LAST_NAMES,$_POST["txtApellidos"]));
		$_POST["txtNacionalidad"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_NATIONALITY,$_POST["txtNacionalidad"]));
		$_POST["txtDireccion1"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_STREET_1,$_POST["txtDireccion1"]));
		$_POST["txtDireccion2"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_STREET_2,$_POST["txtDireccion2"]));
		$_POST["txtCiudad"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_TOWN_CITY,$_POST["txtCiudad"]));
		$_POST["txtProvincia"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_PROVINCE,$_POST["txtProvincia"]));
		$_POST["txtCp"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_POSTCODE,$_POST["txtCp"]));
		$_POST["txtEmail"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_EMAIL,$_POST["txtEmail"]));
		$_POST["txtTelefonoCasa"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_HOME_PHONE,$_POST["txtTelefonoCasa"]));
		$_POST["txtEmailAlternativo"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_ALTERNATIVE_EMAIL,$_POST["txtEmailAlternativo"]));
		$_POST["txtTelefonoTrabajo"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_WORK_PHONE,$_POST["txtTelefonoTrabajo"]));
		$_POST["txtFax"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_FAX,$_POST["txtFax"]));
		$_POST["txtTelefonoMobil"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_MOBILE_PHONE,$_POST["txtTelefonoMobil"]));
		$_POST["txtSpecifyOther"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_IF_OTHER_SPECIFY,$_POST["txtSpecifyOther"]));
		$_POST["txtSpecifyStudy"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_IF_STUDENT_SUBJECT,$_POST["txtSpecifyStudy"]));
		$_POST["txtProfesionQualificacion"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_DEGREES_QUALIFICATIONS,$_POST["txtProfesionQualificacion"]));
		$_POST["txtSobreMet"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_HOW_DID_YOU_HEAR_ABOUT_MET,$_POST["txtSobreMet"]));
		$_POST["txtOtherSpecification"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_IF_OTHER_SPECIFY,$_POST["txtOtherSpecification"]));
		$_POST["txtStudySpecification"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_IF_STUDENT_SUBJECT,$_POST["txtStudySpecification"]));
	}else if($_POST["hdnIdModalidad"]==MODALIDAD_USUARIO_INSTITUTIONAL){	
		$_POST["txtEmailUser"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_EMAIL_USER,$_POST["txtEmailUser"]));
		$_POST["txtNombreInstitucion"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_INSTIT_NAME_INSTITUTION,$_POST["txtNombreInstitucion"]));
		$_POST["txtDepartamento"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_INSTIT_DEPARTMENT_IF_APPLICABLE,$_POST["txtDepartamento"]));
		$_POST["txtDireccion1"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_STREET_1,$_POST["txtDireccion1"]));
		$_POST["txtDireccion2"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_STREET_2,$_POST["txtDireccion2"]));
		$_POST["txtCp"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_POSTCODE,$_POST["txtCp"]));
		$_POST["txtCiudad"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_TOWN_CITY,$_POST["txtCiudad"]));
		$_POST["txtProvincia"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_PROVINCE,$_POST["txtProvincia"]));
		$_POST["txtTelefono"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_INSTIT_PHONE_NO,$_POST["txtTelefono"]));
		$_POST["txtFax"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_INSTIT_FAX_NO,$_POST["txtFax"]));
		$_POST["txtEmail"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_INSTIT_EMAIL_ADDRESS,$_POST["txtEmail"]));
		$_POST["txtNombre"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_FIRST_NAME,$_POST["txtNombre"]));
		$_POST["txtApellidos"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_LAST_NAMES,$_POST["txtApellidos"]));
		
		$_POST["txtEstado"]="";
		//$_POST["txtEstado"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_INSTIT_IF_OTHER_PLEASE_STATE,$_POST["txtEstado"]));
		$_POST["txtEmailUsuario"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_INSTIT_EMAIL_TO_USER,$_POST["txtEmailUsuario"]));
		$_POST["txtEmailUsuarioAlternativo"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_INSTIT_ALTERNATIVE_EMAIL,$_POST["txtEmailUsuarioAlternativo"]));
		$_POST["txtTelefonoTrabajo"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_WORK_PHONE,$_POST["txtTelefonoTrabajo"]));
		$_POST["txtTelefonoMovil"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_MOBILE_PHONE,$_POST["txtTelefonoMovil"]));

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
	if(!isset($_POST["chkPrivacy"])){
		$esValido=false;
        $privacy=0;
	} else {
        $privacy=1;
    }

	
	
	//Email usuario
	$emailUsuario=$_POST["txtEmailUser"];
	$nombreUsuario=$_POST["txtNombre"];
	$apellidosUsuario=$_POST["txtApellidos"];
	
	
	//Establecemos el tipo de pago
	$vectorMetodoPago=Array(INSCRIPCION_TIPO_PAGO_TRANSFERENCIA,INSCRIPCION_TIPO_PAGO_PAYPAL,INSCRIPCION_TIPO_PAGO_DEBIT);
	$metodoPago=$_POST["rdMetodoPago"];
	
	//Quitamos los espacios extras de todos los campos
	foreach($_POST as $clave=>$valor){
		$_POST[$clave]=trim($valor);
	}
		
	//Miramos si existe usuario con este correo y que no este borrado
	$resultadoCorreo=$db->callProcedure("CALL ed_sp_web_usuario_web_existe_registrado('".$_POST["txtEmailUser"]."')");
	$esValido=$db->getNumberRows($resultadoCorreo)==0;
	$mensajeError=STATIC_FORM_MEMBERSHIP_EMAIL_REPEAT;

	
	if(!in_array($metodoPago, $vectorMetodoPago)){
		$esValido=false;
	}
	
	
	$vectorActividadProfesional=array_filter(explode(",",$_POST["hdnIdActividadProfesional"]));
	if($_POST["hdnIdModalidad"]==MODALIDAD_USUARIO_INDIVIDUAL && count($vectorActividadProfesional)==0){
		$esValido=false;
		$mensajeError=STATIC_FORM_MEMBERSHIP_ERROR_PROFESSION;
	}

	//Si todas las validaciones son correctas
	if($esValido){		
	
		//Datos usuario...
		$publico=0;
		$activo=0;
		$borrado=0;
		$esFactura=1;
		
				
		if($_POST["cmbPais"]==-1){
			$_POST["cmbPais"]="null";
		}
		
		if($_POST["cmbTitulo"]==-1){
			$_POST["cmbTitulo"]="null";
		}
		
		//if(isset($_POST["chkInvoice"])){
		//	$esFactura=1;
		//}
		
		//Por defecto entendemos que el concepto del movimiento asociado sera el de un usuario individual normal
		$idConceptoMovimiento=MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP;
		
		$db->startTransaction();
		
		

		if($metodoPago==INSCRIPCION_TIPO_PAGO_PAYPAL){
			//Si estamos pagando por paypal o tpv... el estado es pendiente hasta que se haga el pago
			$idEstadoInscripcion=INSCRIPCION_ESTADO_INSCRIPCION_PENDIENTE;
		}else{
			$pagado=0;
			$idEstadoInscripcion=INSCRIPCION_ESTADO_INSCRIPCION_CONFIRMADA;
		}
		
		//Insertamos usuario
		$resultadoUsuarioWeb=$db->callProcedure("CALL ed_sp_web_usuario_web_insertar(".TIPO_USUARIO_SOCIO.",".$_POST["hdnIdModalidad"].",'".$_POST["txtEmailUser"]."','".$_POST["txtPasswd"]."',".$publico.",'".$_POST["txtFacturacionNifCliente"]."','".$_POST["txtFacturacionNombreCliente"]."','".$_POST["txtFacturacionNombreEmpresa"]."','".$_POST["txtFacturacionDireccion"]."','".$_POST["txtFacturacionCodigoPostal"]."','".$_POST["txtFacturacionCiudad"]."','".$_POST["txtFacturacionProvincia"]."','".$_POST["txtFacturacionPais"]."',".$privacy.",".$activo.",".$borrado.")");
		$datoUsuarioWeb=$db->getData($resultadoUsuarioWeb);
		$idUsuarioWeb=$datoUsuarioWeb["id_usuario_web"];

        $fechaInscripcion=date("Y-m-d G:i:s");
        $fechaHoraDesglosada=explode(" ", $fechaInscripcion);
        $fechaDesglosada=explode("-",$fechaHoraDesglosada[0]);

        // If signup is after 30 September, set expiry to end of following year
        if($fechaDesglosada[1]>9){
            $fechaFinalizacion=($fechaDesglosada[0]+1)."-12-31";
        }else{
            $fechaFinalizacion=($fechaDesglosada[0])."-12-31";
        }

		
		if(isset($_POST["hdnIdModalidad"]) && $_POST["hdnIdModalidad"]==MODALIDAD_USUARIO_INDIVIDUAL){
			$idSituacionAdicional="null";
			$vectorSituacionAdicional=Array(SITUACION_ADICIONAL_JUBILADO,SITUACION_ADICIONAL_ESTUDIANTE);
			
			$importe=PRECIO_MODALIDAD_USUARIO_INDIVIDUAL;
			
			//Si somos jubilados o estudiantes...
			if(in_array($_POST["cmbSituacionAdicional"],$vectorSituacionAdicional)){
				$idSituacionAdicional=$_POST["cmbSituacionAdicional"];
				$importe=PRECIO_MODALIDAD_USUARIO_INDIVIDUAL_DESCUENTO;
                if($fechaDesglosada[1]<10){
                    //Si somos retirados entonces...
                    if($idSituacionAdicional==SITUACION_ADICIONAL_JUBILADO){
                        $idConceptoMovimiento=MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP_RETIRED;
                    }else if($idSituacionAdicional==SITUACION_ADICIONAL_ESTUDIANTE){
                        $idConceptoMovimiento=MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP_STUDENT;
                    }
			    } else {
                    //Si somos retirados entonces...
                    if($idSituacionAdicional==SITUACION_ADICIONAL_JUBILADO){
                        $idConceptoMovimiento=MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP_PREPAID;
                    }else if($idSituacionAdicional==SITUACION_ADICIONAL_ESTUDIANTE){
                        $idConceptoMovimiento=MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP_PREPAID;
                    }
                }
            }

            //Insertamos usuario
			$resultadoUsuarioWebIndividual=$db->callProcedure("CALL ed_sp_web_usuario_web_individual_insertar(".$idUsuarioWeb.",".$_POST["cmbTitulo"].",".$_POST["cmbAnyos"].",".$_POST["cmbPais"].",".$idSituacionAdicional.",'".$_POST["txtNombre"]."','".$_POST["txtApellidos"]."','".$_POST["txtNacionalidad"]."','".$_POST["txtDireccion1"]."','".$_POST["txtDireccion2"]."','".$_POST["txtCiudad"]."','".$_POST["txtProvincia"]."','".$_POST["txtCp"]."','".$_POST["txtEmail"]."','".$_POST["txtEmailAlternativo"]."','".$_POST["txtTelefonoCasa"]."','".$_POST["txtTelefonoTrabajo"]."','".$_POST["txtFax"]."','".$_POST["txtTelefonoMobil"]."','".$esHombre."','".$_POST["txtSobreMet"]."','".$_POST["txtProfesionQualificacion"]."')");			

			//Insertamos actividades web
			foreach($vectorActividadProfesional as $valor){
				$descripcion="";
				if($valor==7){
					//estudio
					$descripcion=$_POST["txtStudySpecification"];
				}else if($valor==8){
					//other
					$descripcion=$_POST["txtOtherSpecification"];
				}
				
				$db->callProcedure("CALL ed_sp_web_usuario_web_actividad_profesional_insertar(".$idUsuarioWeb.",".$valor.",'".$descripcion."')");
			}
						
			
			//Situacion laboral
			$vectorSituacionLaboral=array_filter(explode(",",$_POST["hdnIdSituacionLaboral"]));
			foreach($vectorSituacionLaboral as $valor){				
				$db->callProcedure("CALL ed_sp_web_usuario_web_situacion_laboral_insertar(".$idUsuarioWeb.",".$valor.")");
			}
			
			
		}else if($_POST["hdnIdModalidad"]==MODALIDAD_USUARIO_INSTITUTIONAL){
			$importe=PRECIO_MODALIDAD_USUARIO_INSTITUTIONAL;
			$idConceptoMovimiento=MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP_INSTITUTIONAL;
			
			//Insertamos usuario
			$resultadoUsuarioWebInstitucion=$db->callProcedure("CALL ed_sp_web_usuario_web_institucion_insertar(".$idUsuarioWeb.",".$_POST["cmbTitulo"].",".$_POST["cmbPais"].",'".$_POST["txtNombreInstitucion"]."','".$_POST["txtDepartamento"]."','".$_POST["txtDireccion1"]."','".$_POST["txtDireccion2"]."','".$_POST["txtCp"]."','".$_POST["txtCiudad"]."','".$_POST["txtProvincia"]."','".$_POST["txtTelefono"]."','".$_POST["txtFax"]."','".$_POST["txtEmail"]."','".$_POST["txtNombre"]."','".$_POST["txtApellidos"]."','".$_POST["txtEstado"]."','".$_POST["txtEmailUsuario"]."','".$_POST["txtEmailUsuarioAlternativo"]."','".$_POST["txtTelefonoTrabajo"]."','".$_POST["txtTelefonoMovil"]."')");
		}


        //Insertamos inscripcion
		$resultadoInscripcion=$db->callProcedure("CALL ed_sp_web_inscripcion_insertar(".$idEstadoInscripcion.",".$metodoPago.",".$idUsuarioWeb.",'".$importe."','".$fechaInscripcion."','".$fechaFinalizacion."',".$esFactura.")");
		$datoInscripcion=$db->getData($resultadoInscripcion);


        //Si estamos trabajando con tpv entonces...
		$plantilla->assign("TIPO_RESULTADO_INSCRIPCION",$metodoPago);
		switch($metodoPago){
			case INSCRIPCION_TIPO_PAGO_PAYPAL:
				$subPlantilla=new XTemplate("../html/ajax/paypal_form.html");
				$serverActual="http://www.metmeetings.org/";
				
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
	    

			    $form["return"]=$serverActual."inscripcion_finalizada.php?tipo=2";
			    $form["notify_url"]=$serverActual."ajax/last_step_inscription_paypal.php";
			    $form["return_cancel"]=$serverActual."inscripcion_finalizada.php?tipo=0";
			    $form["rb"]="2";
			    $form["bn"]="MET";
			    $form["upload"]="1";
			    $form["business"]="metmember@gmail.com";
			    $form["currency_code"]="EUR";
			   	$form["custom"]=$datoInscripcion["numero_inscripcion"]."-".$datoInscripcion["codigo"]."-1";


				//Encriptacion
			   	$subPlantilla->assign("ENCRYPT",$paypal->encrypt($form));
				
				
				//Parseamos toda la info
				$subPlantilla->parse("contenido_principal");
				
				//Exportamos a la plantilla principal
				$plantilla->assign("FORMULARIO_ADICIONAL",$subPlantilla->text("contenido_principal"));
				break;
			case INSCRIPCION_TIPO_PAGO_TRANSFERENCIA:
				$idUsuarioWebCorreo=$idUsuarioWeb;
				$numeroInscripcion=$datoInscripcion["numero_inscripcion"];
				$tipoInscripcion=1;
				$idInscripcion=$datoInscripcion["codigo"];
				require "../includes/load_send_mail_inscription.inc.php";
				break;
			case INSCRIPCION_TIPO_PAGO_DEBIT:
				$idUsuarioWebCorreo=$idUsuarioWeb;
				$numeroInscripcion=$datoInscripcion["numero_inscripcion"];
				$tipoInscripcion=1;
				$idInscripcion=$datoInscripcion["codigo"];
				require "../includes/load_send_mail_inscription.inc.php";
				break;
		}
		
		$db->endTransaction();
		
	}else{
		$plantilla->assign("TIPO_RESULTADO_INSCRIPCION",$mensajeError);
	}//end else
	
	
	$plantilla->parse("contenido_principal");
	$plantilla->out("contenido_principal");
?>