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
if (defined('MET_ENV') && constant('MET_ENV') == 'LOCAL') {
    $keys = require(__DIR__ . '/private/stripe_keys.php');
} else {
    $keys = require('/home/metmeetings/private/stripe_keys.php');
}

// Get item from URL parameter or fallback to session variable
$item = isset($_GET["it"]) && !empty($_GET["it"]) ? $_GET["it"] : (isset($_SESSION["item"]) ? $_SESSION["item"] : "");

// Check if item is still empty after fallback
if (empty($item)) {
    die("Error: Payment item not specified. Please go back and submit the form again.");
}

$_SESSION["regType"] = $item; //store in session variable

$price = $_SESSION["amount"]; //price to display on Stripe form (in euros)

//get registration ID, registration number and type (new vs renewal) from session variable
//and store them back individually in separate session variables
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

        // Check if we got valid data
        if (!$regDetails || !is_array($regDetails)) {
            die("Error: Registration details not found for ID: " . $idInscripcion);
        }

        $fullName = ($regDetails['nombre'] ?? '') . " " . ($regDetails['apellidos'] ?? '');
        $description = $item . ": " . $fullName; // Full details for Stripe Dashboard
        $statementDescriptor = 'Membership'; // Short descriptor for card statement
        $amount = ($regDetails['importe'] ?? 0) * 100; //price in cents for paymentintent
        $receiptEmail = $regDetails['correo_electronico'] ?? '';
        break;
    case STATIC_FORM_STRIPE_ITEM_WORKSHOP_REGISTRATION:
        $registrationResult = $db->callProcedure("CALL ed_sp_inscripcion_taller_get_details(" . $idInscripcion . ")");
        $regDetails = $db->getData($registrationResult);

        // Check if we got valid data
        if (!$regDetails || !is_array($regDetails)) {
            die("Error: Workshop registration details not found for ID: " . $idInscripcion);
        }

        $fullName = ($regDetails['nombre'] ?? '') . " " . ($regDetails['apellidos'] ?? '');
        $description = $item . ": " . $fullName; // Full details for Stripe Dashboard
        $statementDescriptor = 'Workshop'; // Short descriptor for card statement
        $amount = $price * 100; //price in cents (from session variable)
        $receiptEmail = $regDetails['correo_electronico'] ?? '';
        break;
    case STATIC_FORM_STRIPE_ITEM_CONFERENCE_REGISTRATION:
        $registrationResult = $db->callProcedure("CALL ed_sp_inscripcion_conferencia_get_details(" . $idInscripcion . ")");
        $regDetails = $db->getData($registrationResult);

        // Check if we got valid data
        if (!$regDetails || !is_array($regDetails)) {
            die("Error: Conference registration details not found for ID: " . $idInscripcion);
        }

        $fullName = ($regDetails['nombre'] ?? '') . " " . ($regDetails['apellidos'] ?? '');
        $description = $item . ": " . $fullName; // Full details for Stripe Dashboard
        $statementDescriptor = 'Conference'; // Short descriptor for card statement
        $amount = ($regDetails['importe_total'] ?? 0) * 100; //price in cents (NB "importe_total" from ed_tb_inscr_conf)
        $receiptEmail = $regDetails['correo_electronico'] ?? '';
        break;
}

try {
$stripe = new \Stripe\StripeClient([
    'api_key' => $keys['secret_key'],
    'stripe_version' => '2025-02-24.acacia',
]);

$intent = $stripe->paymentIntents->create([
    'amount' => $amount,
    'currency' => 'eur',
    'automatic_payment_methods' => ['enabled' => true],
    'description' => $description, // Full details visible in Stripe Dashboard
    'statement_descriptor_suffix' => $statementDescriptor, // Short text for card statement (22 char max)
    'receipt_email' => $receiptEmail,
    'metadata' => [
        'payer' => $fullName, // Do we need this??
        'subaccount' => $item,
        'registration_id' => $idInscripcion,
    ],
]);

} catch (\Stripe\Exception\ApiErrorException $e) {
    http_response_code(400);
    error_log("Stripe API error:
      http_status=" . $e->getHttpStatus() . "
      type=" . ($e->getError()->type ?? '') . "
      code=" . ($e->getError()->code ?? '') . "
      param=" . ($e->getError()->param ?? '') . "
      message=" . ($e->getError()->message ?? '') . "
      request_id=" . ($e->getRequestId() ?? '') . "
    ");
    exit;

} catch (\Throwable $e) {
    http_response_code(500);
    error_log('Unexpected: '.$e->getMessage());
    echo 'Unexpected error. Please try again.';
    exit;
}


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

// Assign publishable key to stripe_form.html
$subPlantilla->assign("STRIPE_PK", $keys['publishable_key']);

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