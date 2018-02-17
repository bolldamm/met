<?php
	/**
	  * 
	  * Enviamos al solicitante un correo con los pasos a seguir para modificar su password
	  * @author eData
	  * @version 4.0
	  * 
	  */

		
	require "../includes/load_main_components.inc.php";
	require "../config/dictionary/default.php";

	if(isset($_GET["txtEmail"]) && trim($_GET["txtEmail"])!="" && generalUtils::validarEmail($_GET["txtEmail"])){
		
		//Comprobamos que ha introducido bien el captcha
		include("../../classes/secureimage/securimage.php");
	  	$img = new Securimage();
	
	  	$valid = $img->check($_GET["txtCode"],false);
		if($valid){
			//Comprobamos que exista el email introducido en el sistema
			$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_usuario_existe_mail(null,'".$_GET["txtEmail"]."')");
			//Si existe entonces...
			if($db->getNumberRows($resultado)==1){
				/**
				 * 
				 * Incluimos la configuracion del componente phpmailer
				 * 
				 */
				require "../../includes/load_mailer.inc.php";
				$mail->From="no-reply@edatasoft.com";
				$mail->FromName="eData";
				$mail->Subject=STATIC_REMEMBER_PASSWORD_MAIL_SUBJECT;
				
				/**
				 * 
				 * aplicamos md5 al total de segundos que han pasado desde 1970 hasta el momento de la ejecucion de este script
				 * @var string
				 * 
				 */
				$hashGenerado=md5(session_id().time().$_GET["txtEmail"]);
				
				$plantilla=new XTemplate("../html/mail/index.html");
				$plantilla->assign("CURRENT_SERVER",$_SERVER["HTTP_HOST"]);
				
				//Contenido mail
				$subPlantilla=new XTemplate("../html/mail/send_remember_password.html");
				$subPlantilla->assign("HASH_GENERADO",$hashGenerado);
				$subPlantilla->parse("contenido_principal");
				
				//Exportamos subPlantilla a plantilla
				$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
				
				$plantilla->parse("contenido_principal");
				
				//Establecemos cuerpo del mensaje
				$mail->Body=$plantilla->text("contenido_principal");
				
				//Establecemos destinatario
				$mail->AddAddress($_GET["txtEmail"]);
				
				//Enviamos correo
				if($mail->Send()){
					//Ok, correo enviado
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_usuario_asignar_hash('".$_GET["txtEmail"]."','".$hashGenerado."')");
					$img->clearCode();
					echo 1;	
				}else{
					//Error, correo no enviado
					echo 2;
				}
			}else{
				echo 3;
			}
		}else{
			echo 4;
		}
	}
?>