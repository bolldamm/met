<?php

/**
 *
 * Redirects to home page if page error or user not logged in or logged in but not paid up
 *
 */


//if page URL not found, redirect to home page
if(!isset($_GET["menu"]) && is_numeric($_GET["menu"])) {
    generalUtils::redirigir(CURRENT_DOMAIN);
//if user not logged in or not paid up, redirect to home page
} elseif(!isset($_SESSION["met_user"]) || $_SESSION["met_user"]["pagado"] === 0) {
	generalUtils::redirigir(CURRENT_DOMAIN);
}

?>

