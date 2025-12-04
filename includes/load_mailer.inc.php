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
	require_once $absolutePath."/../includes/settings.php";
	
	//Datos configuracion
	$mail = new PHPMailer();
 	$mail->PluginDir = $absolutePath."/../classes/phpmailer/";
  	$mail->Mailer = "smtp";
  	$mail->Host = "ssl.s701.sureserver.com";  // Actual server hostname (matches SSL certificate)
  	$mail->Port = 587;
  	$mail->SMTPSecure = 'tls';
  	$mail->SMTPAuth = true;
  	$mail->Username = "noreply@metmeetings.org";
  	$mail->Password = STATIC_EMAIL_PASSWORD;
   	$mail->Timeout=30;
  	$mail->IsHTML(true);

    if (defined('MET_ENV') && constant('MET_ENV') == 'LOCAL') {
        define("STATIC_MAIL_FROM", "noreply@metmeetings.org");
        define("STATIC_MAIL_TO", "swaller111@gmail.com");
        define("STATIC_MAIL_TO_TREASURER", "swaller111@gmail.com");
        define("STATIC_MAIL_FORM_SUBMIT_TO", "swaller111@gmail.com");
        define("STATIC_MAIL_FORM_NEWS_SUBMIT_TO", "swaller111@gmail.com");

        define("STATIC_MAIL_TO_METM_REG_FORM", "swaller111@gmail.com");
        define("STATIC_MAIL_TO_WORKSHOP_REG_FORM", "swaller111@gmail.com");
        define("STATIC_MAIL_TO_MEMBERSHIP_REG_FORM", "swaller111@gmail.com");

    } else {
        define("STATIC_MAIL_FROM", "noreply@metmeetings.org");
        define("STATIC_MAIL_TO", "membership@metmeetings.org");
        define("STATIC_MAIL_TO_TREASURER", "treasurer@metmeetings.org");
        define("STATIC_MAIL_FORM_SUBMIT_TO", "membership@metmeetings.org");
        define("STATIC_MAIL_FORM_NEWS_SUBMIT_TO", "membership@metmeetings.org");

        define("STATIC_MAIL_TO_METM_REG_FORM", "metm_registration@metmeetings.org");
        define("STATIC_MAIL_TO_WORKSHOP_REG_FORM", "workshop_registration@metmeetings.org");
        define("STATIC_MAIL_TO_MEMBERSHIP_REG_FORM", "membership_registration@metmeetings.org");
    }

  	
  	$mail->From = STATIC_MAIL_FROM;
  	
?>