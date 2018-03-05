<?php
	/**
	 * 
	 * Incluimos los ficheros y los datos de configuracion necesareos para el funcionamiento de phpmailer
	 * @author eData S.L.
	 * @version 4.0
	 * 
	 */

	/**
	 * 
	 * Indicamos la ruta absoluta en donde esta alojado el fichero.
	 * @var string
	 * 
	 */
	$absolutePath=dirname(__FILE__);
	
	//Clase phpmailer
	require $absolutePath."/../classes/phpmailer/class.phpmailer.php";
	
	//Datos configuracion
	$mail = new PHPMailer();
 	$mail->PluginDir = $absolutePath."/../classes/phpmailer/";
  	$mail->Mailer = "smtp";
  	$mail->Host = "smtp.metmeetings.org";
  	$mail->SMTPAuth = true;
  	$mail->Username = "noreply@metmeetings.org"; 
  	$mail->Password = "z4r#MEDUF$3v";
   	$mail->Timeout=30;
  	$mail->IsHTML(true);
  
  	define("STATIC_MAIL_FROM", "noreply@metmeetings.org");
  	define("STATIC_MAIL_TO", "swaller111@gmail.com");
  	define("STATIC_MAIL_TO_TREASURER", "swaller111@gmail.com");
  	define("STATIC_MAIL_FORM_SUBMIT_TO","swaller111@gmail.com");
  	define("STATIC_MAIL_FORM_NEWS_SUBMIT_TO","swaller111@gmail.com");
  	define("STATIC_MAIL_DEVELOPMENT_TO","swaller111@gmail.com");
  	define("STATIC_MAIL_TO_SECRETARY","swaller111@gmail.com");
  	define("STATIC_MAIL_TO_ACCOMMODATION","swaller111@gmail.com");
  	
  	//Email que servira como "enrutador" a otros-conference
  	define("STATIC_MAIL_TO_METM_REG_FORM","swaller111@gmail.com");
  	
    //Email que servira como "enrutador" a otros-workshop
  	define("STATIC_MAIL_TO_WORKSHOP_REG_FORM","swaller111@gmail.com");
  	
    //Email que servira como "enrutador" a otros-membership
  	define("STATIC_MAIL_TO_MEMBERSHIP_REG_FORM","swaller111@gmail.com");
  	
  	$mail->From = STATIC_MAIL_FROM;
  	
?>