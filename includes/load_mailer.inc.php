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
	require $absolutePath."/../includes/settings.php";
	
	//Datos configuracion
	$mail = new PHPMailer();
 	$mail->PluginDir = $absolutePath."/../classes/phpmailer/";
  	$mail->Mailer = "smtp";
  	$mail->Host = "smtp.metmeetings.org";
  	$mail->SMTPAuth = true;
  	$mail->Username = "noreply@metmeetings.org"; 
//  	$mail->Password = "QvA292qm&O&mLl%$&jt";
  	$mail->Password = STATIC_EMAIL_PASSWORD;
   	$mail->Timeout=30;
  	$mail->IsHTML(true);

    if (defined('MET_ENV') && constant('MET_ENV') == 'LOCAL') {
        define("STATIC_MAIL_FROM", "noreply@metmeetings.org");
        define("STATIC_MAIL_TO", "webmaster@metmeetings.org");
        define("STATIC_MAIL_TO_TREASURER", "webmaster@metmeetings.org");
        define("STATIC_MAIL_FORM_SUBMIT_TO", "webmaster@metmeetings.org");
        define("STATIC_MAIL_FORM_NEWS_SUBMIT_TO", "webmaster@metmeetings.org");

        define("STATIC_MAIL_TO_METM_REG_FORM", "webmaster@metmeetings.org");
        define("STATIC_MAIL_TO_WORKSHOP_REG_FORM", "webmaster@metmeetings.org");
        define("STATIC_MAIL_TO_MEMBERSHIP_REG_FORM", "webmaster@metmeetings.org");

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