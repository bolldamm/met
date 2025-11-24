<?php
	/**
	  * 
	  * Enviamos al solicitante un correo con los pasos a seguir para modificar su password
	  * @author eData
	  * @version 4.0
	  * 
	  */

	$noGuardar=true;
	require "../includes/load_main_components.inc.php";
	//require_once "../environment_config.php"; //load file for toggling PHP error reporting
	

	if(isset($_GET["mail"]) && trim($_GET["mail"])!="" && generalUtils::validarEmail($_GET["mail"])){
		
		//Comprobamos que ha introducido bien el captcha
		include("../classes/securimage/securimage.php");
	  	$img = new Securimage();
	
	  	$valid = $img->check($_GET["captcha"],false);
		if($valid){
			//Comprobamos que exista el email introducido en el sistema
			$resultado=$db->callProcedure("CALL ed_sp_web_usuario_web_existe ('".$_GET["mail"]."')");

			
			//Si existe entonces...
			if($db->getNumberRows($resultado)==1){
				/**
				 * 
				 * aplicamos md5 al total de segundos que han pasado desde 1970 hasta el momento de la ejecucion de este script
				 * @var string
				 * 
				 */
				require "../includes/load_mailer.inc.php";
				
				$hashGenerado=md5(session_id().time().$_GET["mail"]);
				
				$mailPlantilla=new XTemplate("../html/mail/mail_index.html");
				
				//Contenido mail
				$mailSubPlantilla=new XTemplate("../html/mail/mail_remember_password.html");
				$mailSubPlantilla->assign("URL_RESET_PASSWORD", CURRENT_DOMAIN."/reset_password.php?c=".$hashGenerado);
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
				$mail->AddAddress($_GET["mail"]);
				$mail->FromName = STATIC_MAIL_FROM;
				$mail->Subject = STATIC_REMEMBER_PASSWORD_MAIL_SUBJECT;

				//Enviamos correo
				if($mail->Send()){
					//Ok, correo enviado
					$resultado=$db->callProcedure("CALL ed_sp_web_usuario_web_asignar_hash('".$_GET["mail"]."','".$hashGenerado."')");
					
					/****** Guardamos el log del correo electronico ******/
					$idUsuarioWebCorreo="null";
					
					//Tipo correo electronico
					$idTipoCorreoElectronico=EMAIL_TYPE_REMEMBER_PASSWORD_FORM;
					
					
					//Destinatario
					$vectorDestinatario=Array();
					array_push($vectorDestinatario,$_GET["mail"]);
					
					//Asunto
					$asunto=STATIC_REMEMBER_PASSWORD_MAIL_SUBJECT;
					$cuerpo=$mail->Body;
					
					$db->startTransaction();
					
					require "../includes/load_log_email.inc.php";
					
					$db->endTransaction();
					
					
					//$img->clearCode();
					echo 1;	
				}else{
					//Error, correo no enviado
					echo 2;
				}
			}else{
				//Usuario con este correo no existe
				echo 3;
			}
		}else{
			//Captcha no valido	
			echo 4;
		}
	}else{
		//Error interno
		echo 4;
	}
?>