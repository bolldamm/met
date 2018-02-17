<?php
	/**
	  * Enviamos un correo electrónico con la mejora aportada por el usuario
	  * @author eData
	  * @version 4.0
	  */

	$noGuardar=true;
	require "../includes/load_main_components.inc.php";

	//Comprobamos que ha introducido bien el captcha
	include("../classes/secureimage/securimage.php");
  	$img = new Securimage();

  	$valid = $img->check($_POST["txtCaptcha"],false);
	if($valid){
	  	require "../includes/load_mailer.inc.php";
		$mailPlantilla=new XTemplate("../html/mail/mail_metm_transport.html");
		
		$_POST["txtNombre"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_METM_RIDE_NAME,$_POST["txtNombre"]));
		$_POST["txtEmail"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_EMAIL,$_POST["txtEmail"]));
		
		//Contenido mail
		$mailPlantilla->assign("CONTACTO_NOMBRE", $_POST["txtNombre"]);
		$mailPlantilla->assign("CONTACTO_EMAIL", $_POST["txtEmail"]);
		$mailPlantilla->assign("CONTACTO_OPCION",$_POST["rdRideOption"]);
				
			
		$mailPlantilla->parse("contenido_principal");
			
		//Establecemos cuerpo del mensaje
		$mail->Body=$mailPlantilla->text("contenido_principal");
			
		/**
		 * Incluimos la configuracion del componente phpmailer
		 */	
		$mail->FromName = STATIC_MAIL_FROM;
		$mail->Subject = STATIC_METM_RIDE_MAIL_SUBJECT;
		$mail->AddAddress(STATIC_MAIL_TO_SECRETARY);
		//$mail->AddAddress(STATIC_MAIL_TO_SECRETARY);

		//Enviamos correo
		if($mail->Send()){
			/****** Guardamos el log del correo electronico ******/
			$idUsuarioWebCorreo="null";
			
			//Tipo correo electronico
			$idTipoCorreoElectronico=EMAIL_TYPE_FORM_METM_TRANSPORT;
			
			//Destinatario
			$vectorDestinatario=Array();
			array_push($vectorDestinatario,STATIC_MAIL_TO_SECRETARY);
			
			//Asunto
			$asunto=STATIC_METM_RIDE_MAIL_SUBJECT;
			$cuerpo=$mail->Body;
			
			$db->startTransaction();
			
			require "../includes/load_log_email.inc.php";
			
			$db->endTransaction();
			
			$img->clearCode();
			echo 1;
		}else{
			//Error, correo no enviado
			echo 3;
			echo $mail->ErrorInfo;
		}
	}else{
		//Captcha no valido	
		echo 2;
	}
?>