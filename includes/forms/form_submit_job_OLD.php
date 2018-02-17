<?php
	/**
	 * 
	 * Presentamos por pantalla este formulario
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */

	$plantillaFormulario->assign("TITLE_FORM", STATIC_FORM_JOB_SUBMIT_TITLE);
	$plantillaFormulario->assign("TITLE_BUTTON_FORM", STATIC_FORM_JOB_SUBMIT_TITLE_BUTTON);
	
	//Insertamos la oferta de trabajo
	if(count($_POST) > 0) {
		$idUsuarioWeb = $_SESSION["met_user"]["id"];
		$txtTitulo = generalUtils::escaparCadena($_POST["txtTitulo"]);
		$idTematica = $_POST["cmbTematica"];
		$txtResumen = generalUtils::escaparCadena($_POST["txtResumen"]);
		$txtComentario = generalUtils::escaparCadena($_POST["txtContenido"]);
		$txtEmail = (trim($_POST["txtEmail"] != "") && $_POST["txtEmail"] != STATIC_FORM_MEMBERSHIP_EMAIL) ? generalUtils::escaparCadena($_POST["txtEmail"]) : "";
		
		if($txtTitulo != "" && $txtTitulo != STATIC_FORM_MEMBERSHIP_TITLE  && $txtResumen != "" && $txtResumen != STATIC_FORM_EVENT_SUBMIT_SUMMARY && $txtComentario != "" && $txtComentario != STATIC_FORM_EVENT_SUBMIT_CONTENT) {
			$resultOferta = $db->callProcedure("CALL ed_sp_web_oferta_trabajo_insertar(".$idUsuarioWeb.",'".$txtTitulo."', '".$txtResumen."', '".$txtComentario."', '".$txtEmail."', 0)");
			$datoOferta = $db->getData($resultOferta);
			$idOferta = $datoOferta["id_oferta_trabajo"];
			
			//Enviamos mail
			require "includes/load_mailer.inc.php";
			
			$mailPlantilla=new XTemplate("html/mail/mail_index.html");
			
			//Contenido mail
			$mailSubPlantilla=new XTemplate("html/mail/mail_submit_job.html");
			
			//Id noticia
			$mailSubPlantilla->assign("OFERTA_ID",$idOferta);
			
			
			
			//Obtenemos los detalles
			require "includes/load_info_user_web_JOB.inc.php";
	
		
			$mailSubPlantilla->parse("contenido_principal");
			
			//Exportamos subPlantilla a plantilla
			$mailPlantilla->assign("CONTENIDO",$mailSubPlantilla->text("contenido_principal"));
			
			$mailPlantilla->parse("contenido_principal");
			
			//Establecemos cuerpo del mensaje
			$mail->Body=$mailSubPlantilla->text("contenido_principal");
			

			
			/**
			 * 
			 * Incluimos la configuracion del componente phpmailer
			 * 
			 */
			//$mail->AddAddress(STATIC_MAIL_FORM_SUBMIT_TO);
			$mail->AddAddress("swaller111@gmail.com");
			$mail->FromName = STATIC_MAIL_FROM;
			$mail->Subject = STATIC_SUBMIT_JOB_MAIL_SUBJECT;
			

			//Enviamos correo
			if($mail->Send()){
				/****** Guardamos el log del correo electronico ******/
				$idUsuarioWebCorreo=$_SESSION["met_user"]["id"];
				
				//Tipo correo electronico
				$idTipoCorreoElectronico=EMAIL_TYPE_JOB_FORM;
				
				
				//Destinatario
				$vectorDestinatario=Array();
				array_push($vectorDestinatario,"swaller111@gmail.com");
				
				//Asunto
				$asunto=STATIC_SUBMIT_JOB_MAIL_SUBJECT;
				$cuerpo=$mail->Body;
				
				$db->startTransaction();
				
				require "includes/load_log_email.inc.php";
				
				$db->endTransaction();	
			}
			
			generalUtils::redirigir(CURRENT_DOMAIN."/openbox.php?menu=".$idMenu."&c=1");
		}else{
			generalUtils::redirigir(CURRENT_DOMAIN."/openbox.php?menu=".$idMenu."&c=2");
		}
	}
	
	if(isset($_GET["c"]) && is_numeric($_GET["c"])) {
		if($_GET["c"] == 1) {
			$plantillaFormulario->assign("MENSAJE_ACCION_PERFIL", STATIC_FORM_JOB_SUBMIT_OK);
			$plantillaFormulario->assign("MENSAJE_ACCION_CLASS", "msgOK");
			$plantillaFormulario->assign("MENSAJE_ACCION_DISPLAY", "");
		}else if($_GET["c"] == 3) {
			$plantillaFormulario->assign("MENSAJE_ACCION_PERFIL", STATIC_FORM_JOB_SUBMIT_KO);
			$plantillaFormulario->assign("MENSAJE_ACCION_CLASS", "msgKO");
			$plantillaFormulario->assign("MENSAJE_ACCION_DISPLAY", "");
		}else{
			$plantillaFormulario->assign("MENSAJE_ACCION_PERFIL", "");
			$plantillaFormulario->assign("MENSAJE_ACCION_CLASS", "msgKO");
			$plantillaFormulario->assign("MENSAJE_ACCION_DISPLAY", "display:none;");
		}
			
		//Hacemos que la pagina baje hacia la parte que le pertoca(donde se esta mostrando el mensaje)
		$plantilla->assign("ELEMENTO_ANCLAR","anclaExito");
		$plantilla->parse("contenido_principal.bloque_ready.bloque_anclar_elemento");
	}
		
	//Editor
	$plantilla->assign("TEXTAREA_ID","txtContenido");
	$plantilla->assign("TEXTAREA_TOOLBAR","Minimo");
	$plantilla->parse("contenido_principal.bloque_ready.inicializar_ckeditor");
	
	
	/**
	 * Realizamos todos los parse realcionados con este apartado
	 */
	$plantilla->parse("contenido_principal.editor_script");
	$plantilla->parse("contenido_principal.script_calendar");
	$plantilla->parse("contenido_principal.bloque_ready.calendario");
	$plantilla->parse("contenido_principal.validar_submit_job");
?>