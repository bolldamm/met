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
	  	
	  	//Index principal
	  	$mailPrincipal=new XTemplate("../html/mail/mail_index.html");
	  	
	  	//User mail
		$mailPlantilla=new XTemplate("../html/mail/mail_accommodation_user.html");
		
		$esShareRoom=false;
		$esShareBedroom=false;
		$esSharePartner=false;
		$esTwinRoom=false;
		$esSuitableTwinRoom=false;
		$esComentario=false;
		
		$_POST["txtNombre"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_METM_RIDE_NAME,$_POST["txtNombre"]));
		$_POST["txtEmail"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_EMAIL,$_POST["txtEmail"]));
		
		if($_POST["txtShareRoom"]!=""){
			$esShareRoom=true;
		}
		if($_POST["txtShareBedroom"]!=""){
			$esShareBedroom=true;
		}
		if($_POST["txtSharePartner"]!=""){
			$esSharePartner=true;
		}
		if(isset($_POST["chkTwinRoom"])){
			$esSuitableTwinRoom=true;
		}
		if($_POST["txtContenido"]!=""){
			$esComentario=true;
		}

		//Contenido mail USUARIO
		$mailPlantilla->assign("FORM_ACCOMMODATION_NAME_VALUE", $_POST["txtNombre"]);
		$mailPlantilla->assign("FORM_ACCOMMODATION_ROOM_VALUE", $_POST["rdHotelOption"]);
		$mailPlantilla->assign("FORM_ACCOMMODATION_ARRIVAL_VALUE", $_POST["txtFechaLlegada"]);
		$mailPlantilla->assign("FORM_ACCOMMODATION_DEPARTURE_VALUE", $_POST["txtFechaSalida"]);
		
		
		if($esShareRoom){
			$mailPlantilla->assign("FORM_ACCOMMODATION_MAIL_USER_SHARE_ROOM_VALUE", $_POST["txtShareRoom"]);
			$mailPlantilla->parse("contenido_principal.bloque_share_room");
		}
		
		if($esShareBedroom){
			$mailPlantilla->assign("FORM_ACCOMMODATION_MAIL_USER_SHARE_BEDROOM_VALUE", $_POST["txtShareBedroom"]);
			$mailPlantilla->parse("contenido_principal.bloque_share_bedroom");
		}
		
		if($esSharePartner){
			$mailPlantilla->assign("FORM_ACCOMMODATION_MAIL_USER_SHARE_PARTNER_VALUE", $_POST["txtSharePartner"]);
			$mailPlantilla->parse("contenido_principal.bloque_share_partner");
		}
		
		if($esSuitableTwinRoom){
			$mailPlantilla->parse("contenido_principal.bloque_share_suitable");
		}
		
		if($esComentario){
			$mailPlantilla->assign("FORM_ACCOMMODATION_MAIL_USER_COMMENT_VALUE", $_POST["txtContenido"]);
			$mailPlantilla->parse("contenido_principal.bloque_share_comment");
		}

				
			
		$mailPlantilla->parse("contenido_principal");
		
		$mailPrincipal->assign("CONTENIDO",$mailPlantilla->text("contenido_principal"));
		$mailPrincipal->parse("contenido_principal");
		
			
		//Establecemos cuerpo del mensaje
		$mail->Body=$mailPrincipal->text("contenido_principal");
			
		/**
		 * Incluimos la configuracion del componente phpmailer
		 */	
		$mail->FromName = STATIC_MAIL_FROM;
		$mail->Subject = STATIC_FORM_ACCOMMODATION_MAIL_USER_SUBJECT;
		$mail->AddAddress($_POST["txtEmail"]);

		//Enviamos correo
		if($mail->Send()){
			/****** Guardamos el log del correo electronico ******/
			$idUsuarioWebCorreo="null";
			
			//Tipo correo electronico
			$idTipoCorreoElectronico=EMAIL_TYPE_ACCOMMODATION_FORM_USER;
			
			//Destinatario
			$vectorDestinatario=Array();
			array_push($vectorDestinatario,$_POST["txtEmail"]);
			
			//Asunto
			$asunto=STATIC_FORM_ACCOMMODATION_MAIL_USER_SUBJECT;
			$cuerpo=$mail->Body;

			$db->startTransaction();
			
			require "../includes/load_log_email.inc.php";
			
			$db->endTransaction();

			//Ahora enviamos a MET
			$mailPlantilla=new XTemplate("../html/mail/mail_accommodation.html");
			
			//Contenido mail MET
			$mailPlantilla->assign("FORM_ACCOMMODATION_MAIL_DETAIL_NAME_VALUE", $_POST["txtNombre"]);
			$mailPlantilla->assign("FORM_ACCOMMODATION_MAIL_DETAIL_EMAIL_VALUE", $_POST["txtEmail"]);
			$mailPlantilla->assign("FORM_ACCOMMODATION_ROOM_VALUE", $_POST["rdHotelOption"]);
			$mailPlantilla->assign("FORM_ACCOMMODATION_ARRIVAL_VALUE", $_POST["txtFechaLlegada"]);
			$mailPlantilla->assign("FORM_ACCOMMODATION_DEPARTURE_VALUE", $_POST["txtFechaSalida"]);
			
			if($esShareRoom){
				$mailPlantilla->assign("FORM_ACCOMMODATION_MAIL_SHARE_ROOM_VALUE", $_POST["txtShareRoom"]);
				$mailPlantilla->parse("contenido_principal.bloque_share_room");
			}
			
			if($esShareBedroom){
				$mailPlantilla->assign("FORM_ACCOMMODATION_MAIL_SHARE_BEDROOM_VALUE", $_POST["txtShareBedroom"]);
				$mailPlantilla->parse("contenido_principal.bloque_share_bedroom");
			}
			
			if($esSharePartner){
				$mailPlantilla->assign("FORM_ACCOMMODATION_MAIL_SHARE_PARTNER_VALUE", $_POST["txtSharePartner"]);
				$mailPlantilla->parse("contenido_principal.bloque_share_partner");
			}
			
			if($esSuitableTwinRoom){
				$mailPlantilla->parse("contenido_principal.bloque_share_suitable");
			}
			
			if($esComentario){
				$mailPlantilla->assign("FORM_ACCOMMODATION_MAIL_COMMENT_VALUE", $_POST["txtContenido"]);
				$mailPlantilla->parse("contenido_principal.bloque_share_comment");
			}
			
			/**
			 * Incluimos la configuracion del componente phpmailer
			 */	
			$mail->FromName = STATIC_MAIL_FROM;
			$mail->Subject = STATIC_FORM_ACCOMMODATION_MAIL_SUBJECT.": ".$_POST["txtNombre"];
			$mail->ClearAllRecipients();
			$mail->AddAddress(STATIC_MAIL_TO_ACCOMMODATION);
				
			$mailPlantilla->parse("contenido_principal");
	
			//Establecemos cuerpo del mensaje
			$mail->Body=$mailPlantilla->text("contenido_principal");
				
			//Enviamos correo
			if($mail->Send()){
				/****** Guardamos el log del correo electronico ******/
				$idUsuarioWebCorreo="null";
			
				//Tipo correo electronico
				$idTipoCorreoElectronico=EMAIL_TYPE_ACCOMMODATION_FORM;
				
				//Destinatario
				$vectorDestinatario=Array();
				array_push($vectorDestinatario,STATIC_MAIL_TO_ACCOMMODATION);
				
				//Asunto
				$asunto=$mail->Subject;
				$cuerpo=$mail->Body;
	
				$db->startTransaction();
				
				require "../includes/load_log_email.inc.php";
				
				$db->endTransaction();
			}
			
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