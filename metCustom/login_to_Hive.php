<?php

/*
 * Checks whether the current user is logged in and paid up
 * Generates a URL containing the user's details (email, name, etc.)
 * Redirects to the Hive (runs the file sso.php on the Hive)
*/

require "../classes/generalUtils.php";
require "../includes/load_main_components.inc.php";

//check that user is logged in and paid up
if(!isset($_SESSION["met_user"]) || $_SESSION["met_user"]["pagado"] === 0) {

    generalUtils::redirigir(CURRENT_DOMAIN);

} else {

    //if all OK, go ahead and generate URL
    $met_user_email = $_SESSION["met_user"]["email"];
    $met_user_first_name = $_SESSION["met_user"]["name"];
    $met_user_last_name = $_SESSION["met_user"]["lastname"];
    $user_registered = date('Y-m-d H:i:s');

    //$hive_url = 'https://hive.metmeetings.org';

    if ($met_user_email != '') {

        //encrypt the email address
        $email_encoded = rtrim(strtr(base64_encode($met_user_email), '+/', '-_'), '=');

        //redirect to the Hive
        header('Location: https://hive.metmeetings.org/sso.php?email=' . $email_encoded . '&fname=' . $met_user_first_name . '&lname=' . $met_user_last_name . '&date=' . $user_registered);
        exit;

    }
}

