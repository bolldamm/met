<?php
	/**
	  * 
	  * Send email to user with password reset instructions
	  * @author eData
	  * @version 4.0
	  * 
	  */

		
	require "../includes/load_main_components.inc.php";
	require "../config/dictionary/default.php";

	if(isset($_GET["txtEmail"]) && trim($_GET["txtEmail"])!="" && generalUtils::validarEmail($_GET["txtEmail"])){
		
		//Generate captcha
		include("../../classes/securimage/securimage.php");
	  	$img = new Securimage();
	
	  	$valid = $img->check($_GET["txtCode"],false);
		if($valid){
			//Check the email address exists in database
			$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_usuario_existe_mail(null,'".$_GET["txtEmail"]."')");
			//Si existe entonces...
			if($db->getNumberRows($resultado)==1){
				/**
				 * 
				 * Include phpmailer class
				 * 
				 */
				require "../../includes/load_mailer.inc.php";
				$mail->From="no-reply@edatasoft.com";
				$mail->FromName="eData";
				$mail->Subject=STATIC_REMEMBER_PASSWORD_MAIL_SUBJECT;
				
				/**
				 * 
				 * Generate encrypted timestamp
				 * @var string
				 * 
				 */
				$hashGenerado=md5(session_id().time().$_GET["txtEmail"]);
				
				$plantilla=new XTemplate("../html/mail/index.html");
				$plantilla->assign("CURRENT_SERVER",$_SERVER["HTTP_HOST"]);
				
				//Email content
				$subPlantilla=new XTemplate("../html/mail/send_remember_password.html");
				$subPlantilla->assign("HASH_GENERADO",$hashGenerado);
				$subPlantilla->parse("contenido_principal");
				
				//Export sub-template to main template
				$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
				
				$plantilla->parse("contenido_principal");
				
				//Set email body
				$mail->Body=$plantilla->text("contenido_principal");
				
				//Set recipient address
				$mail->AddAddress($_GET["txtEmail"]);
				
				/*
				 * Send email
				 * 1 = Email sent OK
				 * 2 = Email not sent
				 * 3 = No user with that email address
				 * 4 = Captcha is invalid
				 */
				if($mail->Send()){
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_usuario_asignar_hash('".$_GET["txtEmail"]."','".$hashGenerado."')");
					$img->clearCode();
					echo 1;	
				}else{
					echo 2;
				}
			}else{
				echo 3;
			}
		}else{
			echo 4;
		}
	}
