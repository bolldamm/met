<?php

	/**
	 * 
	 * Creates a link that calls a URL containing
	 * thedetails of the currently logged in user,
	 * email address, first name, last name and date
	 * Ties in with the file sso.php on the Hive
	 */


if(!isset($_GET["menu"]) && is_numeric($_GET["menu"])) 
{
    generalUtils::redirigir(CURRENT_DOMAIN); //redirect to home page if page URL not found

} else { 
	;
    if(!isset($_SESSION["met_user"]) || $_SESSION["met_user"]["pagado"] === 0) {

	generalUtils::redirigir(CURRENT_DOMAIN); //redirect to home page if not logged in or logged in but not paid up

    } else {

      	$met_user_email = $_SESSION["met_user"]["email"];
    	$met_user_first_name = $_SESSION["met_user"]["name"];
    	$met_user_last_name = $_SESSION["met_user"]["lastname"];
    	$user_registered = date('Y-m-d H:i:s');

    	$hive_url = 'https://hive.metmeetings.org'; 
 
    	if ($met_user_email != '') {
	    	$email_encoded = rtrim(strtr(base64_encode($met_user_email), '+/', '-_'), '='); //email encryption
            //$password_encoded = rtrim(strtr(base64_encode($met_user_password), '+/', '-_'), '='); //password encryption
            $link = '<a href="'.$hive_url.'/sso.php?email='.$email_encoded.'&fname='.$met_user_first_name.'&lname='.$met_user_last_name.'&date='.$user_registered.'" target="_blank">'.STATIC_HIVE_LOGIN_LINK_TEXT.'</a>';
    	}
    }
}

$plantillaFormulario->assign("HIVE_LOGIN_LINK", $link);

?> 

