<?php
	/**
	 * 
	 * Script to send PDF workshop attendance certificates
	 * @Author Mike
	 * 
	 */
	    require "../includes/load_mailer.inc.php";
	// echo "Send certificates does not work yet<br>";

	//Get certificate details
	$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_pr_get_workshop_certificates('".$_GET['id_taller']."','".$_GET['fecha_taller']."')";
// echo $codeProcedure."<br>";
	$resultado=$db->callProcedure($codeProcedure);
	// $i = 0;

	while($dato=$db->getData($resultado)){

      $email = $dato['correo_electronico'];
//       echo $email."<br>";

		$plantilla=new XTemplate("../html/mail/mail_invoice.html");
      
        $enlace=CURRENT_DOMAIN."get_certificate.php?hash=".$dato['hash_generado'];
      
	   	$plantilla->assign("WORKSHOP_NAME", $dato['nombre_largo']);
        // $plantilla->assign("WORKSHOP_FEEDBACK", $dato['feedback']);
      
      	$feedback = $dato['feedback'];
        
        if ($feedback) {        
        $feedbackBlurb = "<p>Please take a moment to <a href='".$feedback."'>complete this short, anonymous survey</a>. Your feedback is essential for MET to develop and improve content for future workshops and conferences.</p>";
          $feedbackSubject = " and feedback survey";
        } else {
          $feedbackBlurb = "";
          $feedbackSubject = "";
        }
    
		$plantilla->assign("CONTENIDO","<p>Dear ".$dato['nombre'].",</p><p>Thank you for attending <i>".$dato['nombre_largo']."</i>. You can download your <a href='".$enlace."'>certificate of attendance</a> by clicking the link.</p>".$feedbackBlurb."<p>Kind regards,</p><p>".STATIC_CPD."<br><a href='mailto:development@metmeetings.org'>MET CPD chair</a><p style='color:#23babd'><small>To learn how MET processes your data, please read our <a href='https://www.metmeetings.org/en/privacy-notice:30'>privacy notice</a>.<br>The information in this email and in any attachments is for you and you alone. Please do not share it with anyone without our consent.</small></p>");
		
		$plantilla->parse("contenido_principal");
      
      	$mail->From = STATIC_MAIL_FROM;
	  	$mail->FromName = "MET";
	  	$mail->Subject = "Attendance certificate".$feedbackSubject." for a MET event";
	  	$mail->Body = $plantilla->text("contenido_principal");
      
      	$vectorDestinatario=Array();

      // Mail a donde enviar
       $mail->addAddress($email, $dato['nombre']." ".$dato['apellidos']);
      
 		if($mail->Send()){		
				//Destinatario
		
				array_push($vectorDestinatario,$email);
	
  		} 
      
	  		//Limpiamos address
	  		$mail->ClearAllRecipients();
      		// unset($mail);
      		unset($plantilla);
	  	
      	/****** Guardamos el log del correo electronico ******/
      	$id_inscripcion = $dato['numero_inscripcion'];
		$idUsuarioWebCorreo="null";
	
		//Tipo correo electronico
		$idTipoCorreoElectronico="22";
		
		//Asunto
		$asunto=$mail->Subject;
		$cuerpo=$mail->Body;
      
      	require "../includes/load_log_email.inc.php";	
	  	
    }
		generalUtils::redirigir($_SERVER['HTTP_REFERER']);
?>