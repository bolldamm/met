<?php
	/**
	 * 
	 * Script que muestra y realiza la edicion de un menu
	 * @Author eData
	 * 
	 */
	
	//Si hemos enviado el formulario...
	if(count($_POST)){
		//Datos generales del menu
		//Datos generales del movimiento
		if(isset($_POST["hdnPagado"])){
			$pagado=$_POST["hdnPagado"];
		}else{
			$pagado=0;
		}
		
		
		//<<<07/12/2016>>>
		if(isset($_POST["hdnNonTaxable"])){
			$taxable=$_POST["hdnNonTaxable"];
		}else{
			$taxable=0;
		}
		
		
		
		$idMovimiento=$_POST["hdnIdMovimiento"];
		
		//Iniciar transaccion
		$db->startTransaction();
				
		//Insercion movimiento
		$descripcion=generalUtils::escaparCadena($_POST["txtaDescripcion"]);
		

		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_movimiento_editar(".$idMovimiento.",".$_POST["cmbTipo"].",".$_POST["cmbConcepto"].",".$taxable.",".$_POST["cmbTipoPago"].",'".generalUtils::escaparCadena($_POST["txtConceptoPersonalizado"])."','".generalUtils::escaparCadena($_POST["txtPersona"])."','".generalUtils::conversionFechaFormato($_POST["txtFecha"])."','".$descripcion."','".$_POST["txtImporte"]."',".$pagado.")");		

		//Si el concepto es membership fee, es decir inscripcion o rene, y decimos que esta pagado el movimiento...
		if($_POST["cmbConcepto"]==2 && $pagado==1){
			//Miramos si la inscripcion estaba pagada y con el email de activacio enviada...
			$resultadoMovimientoInscripcion=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_movimiento_inscripcion_obtener_concreta(".$idMovimiento.")");
			$datoMovimientoInscripcion=$db->getData($resultadoMovimientoInscripcion);
			
			//Si aun no se habia enviado el email..., debemos enviarlo, y poner que tanto la inscripcion ha sido pagada y que el email ha sido enviado...
			if($datoMovimientoInscripcion["es_mail_enviado"]==0){
				$esMailEnviado=1;
				$idInscripcion=$datoMovimientoInscripcion["id_inscripcion"];

				require "../includes/load_mailer.inc.php";
				
				$mail->FromName = STATIC_MAIL_FROM;
				$mail->Subject = STATIC_INSCRIPTIONS_EMAIL_SUBJECT;
							
				$plantilla=new XTemplate("../html/mail/mail_index.html");

				switch($datoMovimientoInscripcion["id_tipo_usuario_web"]){
					case MODALIDAD_USUARIO_INDIVIDUAL:
						$subPlantillaMail=new XTemplate("../html/mail/mail_individual_member_payment_confirmed.html");	
						$usuarioNombreCompleto=$datoMovimientoInscripcion["nombre"]." ".$datoMovimientoInscripcion["apellidos"];	
						break;
					case MODALIDAD_USUARIO_INSTITUTIONAL:
						$subPlantillaMail=new XTemplate("../html/mail/mail_institutional_member_payment_confirmed.html");
						$usuarioNombreCompleto=$datoMovimientoInscripcion["nombre_representante"]." ".$datoMovimientoInscripcion["apellidos_representante"];
						$subPlantillaMail->assign("INSTITUCION_NOMBRE",$datoMovimientoInscripcion["institucion"]);
						break;
				}
				
				$subPlantillaMail->assign("USUARIO_NOMBRE_COMPLETO",$usuarioNombreCompleto);

				$subPlantillaMail->parse("contenido_principal");
				
				//Exportamos subPlantilla a plantilla
				$plantilla->assign("CONTENIDO",$subPlantillaMail->text("contenido_principal"));
				
				$plantilla->parse("contenido_principal");
				
				//Establecemos cuerpo del mensaje
				$mail->Body=$plantilla->text("contenido_principal");
				
				//Establecemos destinatario
				$mail->AddAddress($datoMovimientoInscripcion["correo_electronico"]);
				
				
				//Enviamos correo
				if($mail->Send()){
					
					/****** Guardamos el log del correo electronico ******/
					$idUsuarioWebCorreo=$datoMovimientoInscripcion["id_usuario_web"];
			
					//Tipo correo electronico
					$idTipoCorreoElectronico=EMAIL_TYPE_INSCRIPTION_ACTIVATED;
			
			
					//Destinatario
					$vectorDestinatario=Array();
					array_push($vectorDestinatario,$datoMovimientoInscripcion["correo_electronico"]);
			
					//Asunto
					$asunto=STATIC_INSCRIPTIONS_EMAIL_SUBJECT;
					$cuerpo=$mail->Body;
			
					require "../includes/load_log_email.inc.php";
					
					$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_inscripcion_usuario_web_actualizar(".$idInscripcion.",1,".$esMailEnviado.")");
					
					
				}//end if
			}//end if
		}//end if

		
		//Cerrar transaccion
		$db->endTransaction();
		
		//Volvemos a listar menu
		if($_POST["hdnVolver"]==0){
			generalUtils::redirigir("main_app.php?section=movement&action=view");
		}else{
			generalUtils::redirigir("main_app.php?section=movement&action=edit&id_movimiento=".$_GET["id_movimiento"]);
		}
	}

	

	if(!isset($_GET["id_movimiento"]) || !is_numeric($_GET["id_movimiento"])){
		generalUtils::redirigir("main_app.php?section=movement&action=view");
	}
	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/movement/manage_movement.html");
		
	//Sacamos la informacion del menu en cada idioma
	$resultadoMovimiento=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_movimiento_obtener_concreta(".$_GET["id_movimiento"].",".$_SESSION["user"]["language_id"].")");
	$datoMovimiento=$db->getData($resultadoMovimiento);
	$idTipo=$datoMovimiento["id_tipo_movimiento"];
	$idConcepto=$datoMovimiento["id_concepto_movimiento"];
	$idTipoPago=$datoMovimiento["id_tipo_pago_movimiento"];
	
	//Combo tipo
	$subPlantilla->assign("COMBO_TIPO",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_tipo_movimiento_obtener_combo(".$_SESSION["user"]["language_id"].")","cmbTipo","cmbTipo",$idTipo,"nombre","id_tipo_movimiento","",0,""));
	
	//Combo concepto
	$subPlantilla->assign("COMBO_CONCEPTO",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_concepto_movimiento_obtener_combo(".$_SESSION["user"]["language_id"].")","cmbConcepto","cmbConcepto",$idConcepto,"nombre","id_concepto_movimiento",STATIC_GLOBAL_COMBO_DEFAULT,0,""));
	
	
	
	
	//<<<07/12/2016>>>
	if($datoMovimiento["non_taxable"]==1){
		$subPlantilla->assign("TAXABLE_CLASE","checked");
	}else{
		$subPlantilla->assign("TAXABLE_CLASE","unChecked");
	}
	
	
	
	
	//Combo tipo pago
	$subPlantilla->assign("COMBO_TIPO_PAGO",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_tipo_pago_movimiento_obtener_combo(".$_SESSION["user"]["language_id"].")","cmbTipoPago","cmbTipoPago",$idTipoPago,"nombre","id_tipo_pago_movimiento",STATIC_GLOBAL_COMBO_DEFAULT,0,""));

	
	//La primera vez, miramos si la noticia esta activo o no
	if($datoMovimiento["es_pagado"]==1){
		$subPlantilla->assign("PAGADO_CLASE","checked");
	}else{
		$subPlantilla->assign("PAGADO_CLASE","unChecked");
	}
	
	//<<<07/12/2016>>>
	$subPlantilla->assign("MOVIMIENTO_TAXABLE",$datoMovimiento["non_taxable"]);
	
	$subPlantilla->assign("MOVIMIENTO_CONCEPTO_PERSONALIZADO",$datoMovimiento["concepto_personalizado"]);
	$subPlantilla->assign("MOVIMIENTO_PERSONA",$datoMovimiento["nombre_persona"]);
	$subPlantilla->assign("MOVIMIENTO_IMPORTE",$datoMovimiento["importe"]);
	$subPlantilla->assign("MOVIMIENTO_PAGADO",$datoMovimiento["es_pagado"]);
	$subPlantilla->assign("MOVIMIENTO_FECHA",generalUtils::conversionFechaFormato($datoMovimiento["fecha_movimiento"]));

	$subPlantilla->assign("MOVIMIENTO_DESCRIPCION",$datoMovimiento["mas_informacion"]);
		
	
	$plantilla->assign("TEXTAREA_ID","txtaDescripcion");
	$plantilla->assign("TEXTAREA_TOOLBAR","Minimo");
	$plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");
	
	$plantilla->parse("contenido_principal.carga_inicial.editor_finder");
		
		
	
		
	
	$plantilla->parse("contenido_principal.carga_inicial.editor_finder");
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_MOVEMENT_VIEW_MOVEMENT_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_MOVEMENT_VIEW_MOVEMENT_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_MOVEMENT_EDIT_MOVEMENT_LINK."&id_movimiento=".$_GET["id_movimiento"];
	$vectorMigas[2]["texto"]=$datoMovimiento["concepto"];

	
	require "includes/load_breadcumb.inc.php";
	
	
	//Parametros hidden formulario
	$subPlantilla->assign("ID_MOVIMIENTO",$_GET["id_movimiento"]);
	$subPlantilla->assign("ACTION","edit");

	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
	
	$subPlantilla->parse("contenido_principal.item_button_close");
	
	//Date pickers
	$plantilla->assign("INPUT_ID","txtFecha");
	$plantilla->parse("contenido_principal.carga_inicial.autoload_datepicker");
	
	//Incluimos proceso boton factura
	$subPlantilla->parse("contenido_principal.boton_factura");
	
		
	//Incluimos script editor
	$plantilla->parse("contenido_principal.editor_script");
	
	//Incluimos proceso onload
	$plantilla->parse("contenido_principal.carga_inicial");
	
	
	//Submenu
	$subPlantilla->parse("contenido_principal.item_submenu");
	
	
	//Contruimos plantilla secundaria
	$subPlantilla->parse("contenido_principal");
	
	//Exportamos plantilla secundaria a la plantilla principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
	//Construimos plantilla principal
	$plantilla->parse("contenido_principal");
	
	//Mostramos plantilla principal por pantalla
	$plantilla->out("contenido_principal");
?>