<?php
	/**
	 * 
	 * Script que muestra y realiza la creacion de un menu
	 * @Author eData
	 * 
	 */


	//Si hemos enviado el formulario...
	if(count($_POST)){
		//Validamos internamente que realmente no este el correo insertado
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_usuario_web_existe_registrado(null,'".$_POST["txtEmail"]."')");
		if($db->getNumberRows($resultado)==0){
			
			if(isset($_POST["hdnPublico"])){
				$publico=$_POST["hdnPublico"];
			}else{
				$publico=0;
			}
			
			$idInstitucion="null";
			$idTipoUsuario=$_POST["cmbTipoMiembro"];
			
			//Si no somos miembros lo que queremos crear forzamos que la modalidad sea individual
			if($idTipoUsuario!=TIPO_USUARIO_SOCIO){
				$idModalidadUsuario=MODALIDAD_USUARIO_INDIVIDUAL;
			}else{
				$idModalidadUsuario=$_POST["cmbModalidadUsuario"];
			}
			
			//Iniciar transaccion
			$db->startTransaction();
			
			//Insertamos datos usuario web genericos
			$resultadoUsuarioWeb=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_usuario_web_insertar(".$idTipoUsuario.",".$idModalidadUsuario.",'".$_POST["txtEmail"]."','".generalUtils::escaparCadena($_POST["txtPassword"])."','".generalUtils::escaparCadena($_POST["txtOtrasActividades"])."','".generalUtils::escaparCadena($_POST["txtOtrasDescripciones"])."','".generalUtils::escaparCadena($_POST["txtOtrasPublicaciones"])."','".generalUtils::escaparCadena($_POST["txtWeb"])."',".$publico.",'".generalUtils::escaparCadena($_POST["txtNif"])."','".generalUtils::escaparCadena($_POST["txtNombreCliente"])."','".generalUtils::escaparCadena($_POST["txtNombreEmpresa"])."','".generalUtils::escaparCadena($_POST["txtDireccion"])."','".generalUtils::escaparCadena($_POST["txtCodigoPostal"])."','".generalUtils::escaparCadena($_POST["txtCiudad"])."','".generalUtils::escaparCadena($_POST["txtProvincia"])."','".generalUtils::escaparCadena($_POST["txtPais"])."','".generalUtils::escaparCadena($_POST["txtObservaciones"])."')");
			$datoUsuarioWeb=$db->getData($resultadoUsuarioWeb);
			$idUsuarioWeb=$datoUsuarioWeb["id_usuario_web"];
			

			//Institucion
			if($idModalidadUsuario==MODALIDAD_USUARIO_INSTITUTIONAL){
				if(isset($_POST["hdnActivoWeb"])){
					$activoWeb=$_POST["hdnActivoWeb"];
				}else{
					$activoWeb=0;
				}
				
				if($_POST["cmbInstitucionPais"]==0){
					$_POST["cmbInstitucionPais"]="null";
				}
				
				if($_POST["cmbInstitucionTitulo"]==0){
					$_POST["cmbInstitucionTitulo"]="null";
				}
				
				$importe=PRECIO_MODALIDAD_USUARIO_INSTITUTIONAL;
			
				//Insertamos usuario
				$resultadoUsuarioWebInstitucion=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_usuario_web_institucion_insertar(".$idUsuarioWeb.",".$_POST["cmbInstitucionTitulo"].",".$_POST["cmbInstitucionPais"].",'".generalUtils::escaparCadena($_POST["txtInstitucionNombreInstitucion"])."','".generalUtils::escaparCadena($_POST["txtInstitucionDepartamento"])."','".generalUtils::escaparCadena($_POST["txtInstitucionDireccion1"])."','".generalUtils::escaparCadena($_POST["txtInstitucionDireccion2"])."','".generalUtils::escaparCadena($_POST["txtInstitucionCp"])."','".generalUtils::escaparCadena($_POST["txtInstitucionCiudad"])."','".generalUtils::escaparCadena($_POST["txtInstitucionProvincia"])."','".generalUtils::escaparCadena($_POST["txtInstitucionTelefono"])."','".generalUtils::escaparCadena($_POST["txtInstitucionFax"])."','".generalUtils::escaparCadena($_POST["txtInstitucionEmail"])."','".generalUtils::escaparCadena($_POST["txtInstitucionNombreRepresentante"])."','".generalUtils::escaparCadena($_POST["txtInstitucionApellidosRepresentante"])."','','".generalUtils::escaparCadena($_POST["txtInstitucionEmailUsuarioRepresentante"])."','".generalUtils::escaparCadena($_POST["txtInstitucionEmailUsuarioAlternativoRepresentante"])."','".generalUtils::escaparCadena($_POST["txtInstitucionTelefonoTrabajoRepresentante"])."','".generalUtils::escaparCadena($_POST["txtInstitucionTelefonoMovilRepresentante"])."','".generalUtils::escaparCadena($_POST["txtaDescripcionPrevia"])."','".generalUtils::escaparCadena($_POST["txtaDescripcion"])."',".$activoWeb.")");
			}else{
				//Individual
				if($_POST["cmbIndividualPais"]==0){
					$_POST["cmbIndividualPais"]="null";
				}
				
				if($_POST["cmbIndividualTitulo"]==0){
					$_POST["cmbIndividualTitulo"]="null";
				}
				
				if($_POST["rdSexo"]==1){
					$esHombre=1;
				}else{
					$esHombre=0;
				}
		
				if($_POST["cmbAnyos"]==0){
					$_POST["cmbAnyos"]="null";
				}
				
				$idSituacionAdicional="null";
				$vectorSituacionAdicional=Array(SITUACION_ADICIONAL_JUBILADO,SITUACION_ADICIONAL_ESTUDIANTE);
			
				$importe=PRECIO_MODALIDAD_USUARIO_INDIVIDUAL;
			
				//Si somos jubilados o estudiantes...
				if(in_array($_POST["cmbSituacionAdicional"],$vectorSituacionAdicional)){
					$idSituacionAdicional=$_POST["cmbSituacionAdicional"];
					$importe=PRECIO_MODALIDAD_USUARIO_INDIVIDUAL_DESCUENTO;
				}
					
				
				if($_POST["cmbInstitucion"]!="0"){
					$idInstitucion=$_POST["cmbInstitucion"];
                    $importe=PRECIO_MODALIDAD_USUARIO_INSTITUTIONAL;
				}
				
				/*if($idTipoUsuario==TIPO_USUARIO_INVITADO){
					$idInstitucion=$_POST["cmbInstitucion"];
				}*/
				
				//Insertamos usuario
				$resultadoUsuarioWebIndividual=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_usuario_web_individual_insertar(".$idUsuarioWeb.",".$idInstitucion.",".$_POST["cmbIndividualTitulo"].",".$_POST["cmbAnyos"].",".$_POST["cmbIndividualPais"].",".$idSituacionAdicional.",'".generalUtils::escaparCadena($_POST["txtIndividualNombre"])."','".generalUtils::escaparCadena($_POST["txtIndividualApellidos"])."','".generalUtils::escaparCadena($_POST["txtIndividualNacionalidad"])."','".generalUtils::escaparCadena($_POST["txtIndividualDireccion1"])."','".generalUtils::escaparCadena($_POST["txtIndividualDireccion2"])."','".generalUtils::escaparCadena($_POST["txtIndividualCiudad"])."','".generalUtils::escaparCadena($_POST["txtIndividualProvincia"])."','".generalUtils::escaparCadena($_POST["txtIndividualCp"])."','".generalUtils::escaparCadena($_POST["txtIndividualEmail"])."','".generalUtils::escaparCadena($_POST["txtIndividualEmailAlternativo"])."','".generalUtils::escaparCadena($_POST["txtIndividualTelefonoCasa"])."','".generalUtils::escaparCadena($_POST["txtIndividualTelefonoTrabajo"])."','".generalUtils::escaparCadena($_POST["txtIndividualFax"])."','".generalUtils::escaparCadena($_POST["txtIndividualTelefonoMovil"])."','".$esHombre."','".generalUtils::escaparCadena($_POST["txtaSobreMet"])."','".generalUtils::escaparCadena($_POST["txtProfesionQualificacion"])."')");			


				//Insertamos actividades web
				$vectorActividadProfesional=array_filter(explode(",",$_POST["hdnIdActividadProfesional"]));

				foreach($vectorActividadProfesional as $valor){
					$descripcion="";
					if($valor==7){
						//estudio
						$descripcion=$_POST["txtStudySpecification"];
					}else if($valor==8){
						//other
						$descripcion=$_POST["txtOtherSpecification"];
					}
					
					$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_usuario_web_actividad_profesional_insertar(".$idUsuarioWeb.",".$valor.",'".$descripcion."')");
				}
							
				
				//Situacion laboral
				$vectorSituacionLaboral=array_filter(explode(",",$_POST["hdnIdSituacionLaboral"]));
				foreach($vectorSituacionLaboral as $valor){				
					$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_usuario_web_situacion_laboral_insertar(".$idUsuarioWeb.",".$valor.")");
				}
				
			}
			
			//Insertamos inscripcion
			$fechaInscripcion=date("Y-m-d G:i:s");
			$fechaHoraDesglosada=explode(" ", $fechaInscripcion);
			$fechaDesglosada=explode("-",$fechaHoraDesglosada[0]);
	
			//Si nos estamos inscribiendo el ultimo dia del año, hacemos que la fecha finalizacion sea el 31/12/XXXX, donde XXXX es el año siguiente
			
			//Si estamos en el ultimo dia del año...
			if($fechaDesglosada[1]==12 && $fechaDesglosada[2]==31){
				$fechaFinalizacion=($fechaDesglosada[0]+1)."-".$fechaDesglosada[1]."-".$fechaDesglosada[2];
			}else{
				$fechaFinalizacion=($fechaDesglosada[0])."-12-31";
			}
	
			//Insertamos inscripcion
			$resultadoInscripcion=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_inscripcion_insertar(".INSCRIPCION_ESTADO_INSCRIPCION_CONFIRMADA.",".INSCRIPCION_TIPO_PAGO_OTROS.",".$idUsuarioWeb.",'".$importe."','".$fechaInscripcion."','".$fechaFinalizacion."',1,1)");
			$datoInscripcion=$db->getData($resultadoInscripcion);
		
			
			//Imagen miembro
			require "image_member.php";
			
			//Si estamos creando un nominee enviaremos un usuario al correo pertinente...
			if($idTipoUsuario==TIPO_USUARIO_INVITADO){
				require "../includes/load_mailer.inc.php";
				$mail->FromName = STATIC_MAIL_FROM;
				$mail->Subject = STATIC_NOMINEE_ASSOCIATED_EMAIL_SUBJECT;		
						
				$plantilla=new XTemplate("../html/mail/mail_index.html");
				$plantilla->assign("CURRENT_SERVER",$_SERVER["HTTP_HOST"]);
					
				//Construimos email para el nominee
				$subPlantillaMail=new XTemplate("../html/mail/mail_member_nominee.html");
				$usuarioNombreCompleto=$_POST["txtIndividualNombre"]." ".$_POST["txtIndividualApellidos"];	

				$subPlantillaMail->assign("USUARIO_NOMBRE_COMPLETO",$usuarioNombreCompleto);
				$subPlantillaMail->parse("contenido_principal");
				
				//Exportamos subPlantilla a plantilla
				$plantilla->assign("CONTENIDO",$subPlantillaMail->text("contenido_principal"));
				
				$plantilla->parse("contenido_principal");
				
				//Establecemos cuerpo del mensaje
				$mail->Body=$plantilla->text("contenido_principal");
				
				//Establecemos destinatario
				$mail->AddAddress($_POST["txtEmail"]);
				
				
				//Enviamos correo
				if($mail->Send()){
					
					/****** Guardamos el log del correo electronico ******/
					$idUsuarioWebCorreo=$idUsuarioWeb;
			
					//Tipo correo electronico
					$idTipoCorreoElectronico=EMAIL_TYPE_NOMINEE_ACTIVATION;
			
			
					//Destinatario
					$vectorDestinatario=Array();
					array_push($vectorDestinatario,$_POST["txtEmail"]);
			
					//Asunto
					$asunto=STATIC_NOMINEE_ASSOCIATED_EMAIL_SUBJECT;
					$cuerpo=$mail->Body;
			
			
					require "../includes/load_log_email.inc.php";
				}
				
			}
			//Cerrar transaccion
			$db->endTransaction();
		}
		
		//Volvemos a listar menu
		generalUtils::redirigir("main_app.php?section=member&action=view");
	}

	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/member/manage_member.html");
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_MEMBER_VIEW_MEMBER_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_MEMBER_VIEW_MEMBER_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_MEMBER_CREATE_MEMBER_LINK;
	$vectorMigas[2]["texto"]=STATIC_BREADCUMB_MEMBER_CREATE_MEMBER_TEXT;
		
	require "includes/load_breadcumb.inc.php";
		
	
	
	$subPlantilla->assign("ACTION","create");
	
	//Atributos del checkbox
	$subPlantilla->assign("MIEMBRO_PUBLICO","0");
	$subPlantilla->assign("PUBLICO_CLASE","unChecked");
	
	//Atributos del checkbox activo web
	$subPlantilla->assign("INSTITUCION_ACTIVO_WEB","0");
	$subPlantilla->assign("ACTIVO_WEB_CLASE","unChecked");

	
	//Combo tipo usuario
	$subPlantilla->assign("COMBO_TIPO_USUARIO",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_tipo_usuario_web_obtener_combo(".$_SESSION["user"]["language_id"].")","cmbTipoMiembro","cmbTipoMiembro",0,"nombre","id_tipo_usuario_web",STATIC_GLOBAL_COMBO_DEFAULT,0,"onchange='obtenerComboModalidadUsuario(this)'"));
	
	//Combo pais
	$subPlantilla->assign("COMBO_PAIS_INSTITUCION",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_pais_obtener_combo()","cmbInstitucionPais","cmbInstitucionPais",0,"nombre_original","id_pais",STATIC_GLOBAL_COMBO_DEFAULT,0,""));
	
	//Combo titulos
	$subPlantilla->assign("COMBO_TITULOS_INSTITUCION", generalUtils::construirCombo($db, "CALL ".OBJECT_DB_ACRONYM."_sp_tratamiento_usuario_web_obtener_combo(".$_SESSION["user"]["language_id"].")", "cmbInstitucionTitulo", "cmbInstitucionTitulo", 0, "nombre", "id_tratamiento_usuario_web", STATIC_GLOBAL_COMBO_DEFAULT, 0,""));

	//Combo pais individual
	$subPlantilla->assign("COMBO_PAIS_INDIVIDUAL",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_pais_obtener_combo()","cmbIndividualPais","cmbIndividualPais",0,"nombre_original","id_pais",STATIC_GLOBAL_COMBO_DEFAULT,0,""));
	
	//Combo titulos individual
	$subPlantilla->assign("COMBO_TITULOS_INDIVIDUAL", generalUtils::construirCombo($db, "CALL ".OBJECT_DB_ACRONYM."_sp_tratamiento_usuario_web_obtener_combo(".$_SESSION["user"]["language_id"].")", "cmbIndividualTitulo", "cmbIndividualTitulo", 0, "nombre", "id_tratamiento_usuario_web", STATIC_GLOBAL_COMBO_DEFAULT, 0,""));
	
	//Combo años
	$subPlantilla->assign("COMBO_ANYOS", generalUtils::construirCombo($db, "CALL ".OBJECT_DB_ACRONYM."_sp_edad_usuario_web_obtener_combo(".$_SESSION["user"]["language_id"].")", "cmbAnyos", "cmbAnyos", -1, "nombre", "id_edad_usuario_web", STATIC_GLOBAL_COMBO_DEFAULT, 0, ""));
	
	//Combo institucion
	$subPlantilla->assign("COMBO_INSTITUCION",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_usuario_web_institucion_obtener_combo()","cmbInstitucion","cmbInstitucion",0,"nombre","id_usuario_web",STATIC_GLOBAL_COMBO_DEFAULT,0,""));

	//Combo situacion adicional
	$subPlantilla->assign("COMBO_SITUACION_ADICIONAL", generalUtils::construirCombo($db, "CALL ed_sp_situacion_adicional_obtener_combo(".$_SESSION["user"]["language_id"].")", "cmbSituacionAdicional", "cmbSituacionAdicional", 0, "nombre", "id_situacion_adicional", STATIC_GLOBAL_COMBO_DEFAULT, -1, "" ));

	//Combo modalidad
	$subPlantilla->assign("DISPLAY_MODALIDAD","none;");
	
	
	//Ponemos ocultos todos los bloques
	$subPlantilla->assign("BLOQUE_EXPANDED_INDIVIDUAL","noDisplayed");
	$subPlantilla->assign("BLOQUE_EXPANDED_INSTITUTIONAL","noDisplayed");
	
	//Ocultamos combo institucion asociada
	$subPlantilla->assign("DISPLAY_INSTITUCION","style='display:none;'");
	
	//Ocultamos combo situacion adicional
	$subPlantilla->assign("DISPLAY_SITUACION_ADICIONAL","style='display:none;'");
	
	//Editor descripcion previa
	$plantilla->assign("TEXTAREA_ID","txtaDescripcionPrevia");
	$plantilla->assign("TEXTAREA_TOOLBAR","Minimo");
	$plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");
	
	//Editor descripcion completa
	$plantilla->assign("TEXTAREA_ID","txtaDescripcion");
	$plantilla->assign("TEXTAREA_TOOLBAR","Minimo");
	$plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");
	
	
	$plantilla->parse("contenido_principal.carga_inicial.editor_finder");
	
	
	//Listado de actividades profesionales
	$resulActividadesProfesionales = $db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_actividad_profesional_obtener_listado(".$_SESSION["user"]["language_id"].")");
	$validacion = "";
	$i=1;
	while($dataActividadProfesional = $db->getData($resulActividadesProfesionales)) {
		$subPlantilla->assign("ITEM_ACTIVIDAD_PROFESIONAL_ID", $dataActividadProfesional["id_actividad_profesional"]);
		$subPlantilla->assign("ITEM_ACTIVIDAD_PROFESIONAL_NOMBRE", $dataActividadProfesional["descripcion"]);
		
		if($i%6==0){
			$subPlantilla->assign("SALTO_LINEA_PROFESION","<br>");
		}else{
			$subPlantilla->assign("SALTO_LINEA_PROFESION","");
		}
		$subPlantilla->parse("contenido_principal.item_actividad_profesional");
		$i++;
	}
	
	//Listamos situaciones laborales
	$resultadoSituacionLaboral = $db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_situacion_laboral_obtener(".$_SESSION["user"]["language_id"].")");
	while($dataSituacionLaboral = $db->getData($resultadoSituacionLaboral)) {
		$subPlantilla->assign("SITUACION_LABORAL_ID", $dataSituacionLaboral["id_situacion_laboral"]);
		$subPlantilla->assign("SITUACION_LABORAL_NOMBRE", $dataSituacionLaboral["nombre"]);
		
		$subPlantilla->parse("contenido_principal.item_situacion_laboral");
	}
	
	
	$subPlantilla->assign("CLASS_INFO_IMAGE_1","class='noDisplayed'");
	$subPlantilla->assign("CLASS_INFO_IMAGE_2","class='noDisplayed'");

	
	
	
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