<?php

/**
 * Gathers the details required for the payment intent
 * Sets Stripe key (test or live) according to whether working in local or production
 * Creates payment intent on Stripe server
 * Includes client secret and publishable key in the HTML
 * Outputs the payment form to the client
 */

require "includes/load_main_components.inc.php";


$plantilla = new XTemplate("html/index.html");

$subPlantilla = new XTemplate("html/stripe_form.html");

// If ?test=0 or ?test=1 is in the URL, update the session value
if (isset($_GET['test'])) {
    $_SESSION['test'] = $_GET['test'];
}

// $environment = require('/home/metmeetings/private/environment_variables.php');
// $stripeEnv = $environment['STRIPE_ENV'];
require_once('vendor/autoload.php');
$keys = require('/home/metmeetings/private/stripe_keys.php');

/**
 * Get variables needed for Stripe payment intent and Stripe form
 */

$item = $_GET["it"]; //payment item ("concepto") to display on Stripe form
// $item = strtolower($item);
$_SESSION["regType"] = $item; //store in session variable

$price = $_SESSION["amount"]; //price to display on Stripe form (in euros)

//get reg ID, reg number and type (new vs renewal) from "regCode" session variable
//and store them back individually in the session
$regCode = $_SESSION["regCode"];
$datosInscripcion = explode("-", $regCode);
$numeroPedidoInscripcion = $datosInscripcion[0]; //varchar
$_SESSION["numeroPedidoInscripcion"] = $numeroPedidoInscripcion;
$idInscripcion = $datosInscripcion[1]; //integer
$_SESSION["idInscripcion"] = $idInscripcion;
$tipoInscripcion = $datosInscripcion[2];
$_SESSION["tipoInscripcion"] = $tipoInscripcion;

/**
 * IMPORTANT!!
 * Depending on type of registration…
 * determine amount to be charged (get amount from database or session variable)
 * payer’s name for display in Stripe dashboard (“description”)
 * and email address for Stripe receipt
 */

switch ($item) {
    //If payment is for membership or membership renewal…
    case STATIC_FORM_STRIPE_ITEM_MEMBERSHIP:
    case STATIC_FORM_STRIPE_ITEM_MEMBERSHIP_RENEWAL:
        $registrationResult = $db->callProcedure("CALL ed_sp_inscripcion_get_details(" . $idInscripcion . ")");
        $regDetails = $db->getData($registrationResult);
        $fullName = $regDetails['nombre'] . " " . $regDetails['apellidos'];
        $description = $item . ": " . $fullName;
        $amount = $regDetails['importe'] * 100; //price in cents for paymentintent
        $receiptEmail = $regDetails['correo_electronico'];
        break;
    case STATIC_FORM_STRIPE_ITEM_WORKSHOP_REGISTRATION:
        $registrationResult = $db->callProcedure("CALL ed_sp_inscripcion_taller_get_details(" . $idInscripcion . ")");
        $regDetails = $db->getData($registrationResult);
        $fullName = $regDetails['nombre'] . " " . $regDetails['apellidos'];
        $description = $item . ": " . $fullName;
        $amount = $price * 100; //price in cents (from session variable)
        $receiptEmail = $regDetails['correo_electronico'];
        break;
    case STATIC_FORM_STRIPE_ITEM_CONFERENCE_REGISTRATION:
        $registrationResult = $db->callProcedure("CALL ed_sp_inscripcion_conferencia_get_details(" . $idInscripcion . ")");
        $regDetails = $db->getData($registrationResult);
        $fullName = $regDetails['nombre'] . " " . $regDetails['apellidos'];
        $description = $item . ": " . $fullName;
        $amount = $regDetails['importe_total'] * 100; //price in cents (NB "importe_total" from ed_tb_inscr_conf)
        $receiptEmail = $regDetails['correo_electronico'];
        break;
}

\Stripe\Stripe::setApiKey($keys['secret_key']);
$publishable = $keys['publishable_key'];
$subPlantilla->assign('STRIPE_PK', $publishable); //goes to stripe_form.html

//Set API version (IMPORTANT: this needs to be updated if we switch to a newer API version)
\Stripe\Stripe::setApiVersion("2020-08-27");

/*
 * The "create" method builds the URL for the API request and posts the data to Stripe
 * The request returns a PaymentIntent object which includes a key (the "client secret")
 * The payment intent is stored in a variable in order to access the client_secret and the intent ID
 */
$intent = \Stripe\PaymentIntent::create([
    "amount" => $amount,
    "currency" => "eur",
    "payment_method_types" => ["card"],
    "description" => $description,
    "statement_descriptor" => $item,
    "receipt_email" => $receiptEmail,
    "metadata" => [
        "payer" => $fullName,
        "subaccount" => $item,
        "registration_id" => $idInscripcion
    ]
    ]);


$plantilla->assign("SECTION_FILE_CSS", "openbox.css");
$plantilla->parse("contenido_principal.css_seccion");
//	unset($_SESSION["met_user"]);
//	unset($_SESSION["conference_user"]);
//	unset($_SESSION["registration_desk"]);
//	unset($_SESSION["conference_attendee"]);
// $_SESSION["met_user"] = 1;
$_SESSION["stripe_form"] = 1;
require "includes/load_structure.inc.php";
//	unset($_SESSION["met_user"]);
//	unset($_SESSION["conference_user"]);
//	unset($_SESSION["registration_desk"]);
//	unset($_SESSION["conference_attendee"]);
	unset($_SESSION["stripe_form"]);

/**** INICIO: breadcrumb ****/
//$breadCrumbUrlDetalle = "stripe_form.php";
$breadCrumbDescripcionDetalle = "Card payment";
/**** FINAL: breadcrumb ****/

//Assign item and price to placeholders for display in Stripe form
$subPlantilla->assign("PRODUCT", $item);
$subPlantilla->assign("PRICE", $price);

/**
 * Assign client secret to data attribute of "Submit payment" button
 * The client secret serves to retrieve the payment intent from the Stripe server
 * Output HTMl form to the client
 */
$subPlantilla->assign("CLIENT_SECRET", $intent->client_secret);

//Store payment intent ID and client secret in session variables
$_SESSION["payment_intent"] = $intent->id;
//$_SESSION["client_secret"] = $intent->client_secret; //this appears to be superfluous

$idMenu = null;
require "includes/load_breadcrumb.inc.php";

//Parse upper horizontal menu
$plantilla->parse("contenido_principal.control_superior");

//Parse content of secondary template (Stripe form)
$subPlantilla->parse("contenido_principal");

//Export secondary template (Stripe form) to main template (page template)
$plantilla->assign("CONTENIDO", $subPlantilla->text("contenido_principal"));

//Parse lefthand menu
$plantilla->parse("contenido_principal.menu_left");

//Parse content of main template and output template to screen
$plantilla->parse("contenido_principal");
$plantilla->out("contenido_principal");