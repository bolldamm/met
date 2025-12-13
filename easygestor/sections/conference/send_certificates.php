<?php
	/**
	 * 
	 * Script to send METM attendance certificates
	 * @Author Mike
	 * 
	 */
    require "../includes/load_mailer.inc.php";
	//Get certificate details
$idEstadoInscripcion = 2;
$nombre = "";
$idTipoUsuario = 0;
$campoOrden = "ic.id_inscripcion_conferencia";
$direccionOrden = "ASC";

	$codeProcedure = "CALL " . OBJECT_DB_ACRONYM . "_sp_inscripcion_conferencia_listar(" . $_GET["id_conferencia"] . "," . $idEstadoInscripcion . ",'" . $nombre . "','" . $idTipoUsuario . "'," . $_SESSION["user"]["language_id"] . ",'" . $campoOrden . "','" . $direccionOrden . "','')";

	$resultado=$db->callProcedure($codeProcedure);

    //Get details of specified conference
    $resultadoConferencia = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_conferencia_obtener_concreta(" . $_GET["id_conferencia"] . "," . $_SESSION["user"]["language_id"] . ")");
    $datoConferencia = $db->getData($resultadoConferencia);

//    require "../includes/load_mailer.inc.php";

	while($dato=$db->getData($resultado)){
      if($dato["asistido"]) {
      
       $email = $dato['correo_electronico'];
        
	 	$plantilla=new XTemplate("../html/mail/mail_invoice.html");
      
         $enlace=CURRENT_DOMAIN."get_metm_certificate.php?hash=".$dato['hash_generado'];
      
	    	$feedback =$datoConferencia['feedback'];
        
        if ($feedback) {        
        $feedbackBlurb = "<p>Please take a moment to <a href='".$feedback."'>complete this short, anonymous survey</a>. Your feedback is essential for MET to develop and improve content for future workshops and conferences.</p>";
          $feedbackSubject = " and feedback survey";
        } else {
          $feedbackBlurb = "";
          $feedbackSubject = "";
        }
    
	 	$plantilla->assign("CONTENIDO","<p>Dear ".$dato['nombre_inscrito'].",</p><p>Thank you for attending ".$datoConferencia['nombre'].". You can download your <a href='".$enlace."'>certificate of attendance</a> by clicking the link.</p>".$feedbackBlurb."<p>Kind regards,</p><p>".STATIC_CHAIR."<br><a href='mailto:metchair@metmeetings.org'>MET Chair</a><p style='color:#23babd'><small>To learn how MET processes your data, please read our <a href='https://www.metmeetings.org/en/privacy-notice:30'>privacy notice</a>.<br>The information in this email and in any attachments is for you and you alone. Please do not share it with anyone without our consent.</small></p>");
		
	 	$plantilla->parse("contenido_principal");
        
        // BEGIN INCLUDE FOR TESTS ONLY
// Use this for a group of testers        
//        $os = array("info@traduzioni-inglese.it", "emma.goldsmith23@gmail.com", "bmarysavage@gmail.com", "roblunnemail@gmail.com", "roblunn@legalspaintrans.com", "h.hamilton.irun@gmail.com", "helen@helenocleebrown.co.uk", "mfh@marijedejager.eu", "linguaverse@gmail.com");
// Use this for a single tester
//        $os = array("info@traduzioni-inglese.it");
//		if (in_array($email, $os)) {
        // END INCLUDE FOR TESTS ONLY, BUT SEE BELOW
       	$mail->From = STATIC_MAIL_FROM;
	   	$mail->FromName = "MET";
	   	$mail->Subject = "Attendance certificate".$feedbackSubject." for ".$datoConferencia['nombre'];
	   	$mail->Body = $plantilla->text("contenido_principal");
      
       	$vectorDestinatario=Array();

      // Mail a donde enviar
        $mail->addAddress($email, $dato['nombre_inscrito']." ".$dato['apellidos_inscrito']);
      
 	 	if($mail->Send()){		
				//Destinatario
		
	 			array_push($vectorDestinatario,$email);
	
  	 	}
	  		//Limpiamos address
	   		$mail->ClearAllRecipients();
       		unset($plantilla);
	  	
      	/****** Guardamos el log del correo electronico ******/
       	$id_inscripcion = $dato['numero_inscripcion'];
	 	$idUsuarioWebCorreo="null";
	
		//Tipo correo electronico
	 	$idTipoCorreoElectronico="23";
		
		//Asunto
	 	$asunto=$mail->Subject;
	 	$cuerpo=$mail->Body;
      
       	require "../includes/load_log_email.inc.php";	
          
        // BEGIN INCLUDE FOR TESTS ONLY        
//        }
        // END INCLUDE FOR TESTS ONLY, BUT SEE ABOVE
          
      }
     }
		generalUtils::redirigir($_SERVER['HTTP_REFERER']);
?>