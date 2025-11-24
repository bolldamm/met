<?php
/**
 * Retrieves signup and payment details from Stripe objects
 * Depending on registration type (membership, renewal, workshop, conference)…
 * Includes last_step_inscription_xxx.php (update DB and send emails)
 * Redirects to inscripcion_finalizada.php
 */

require "includes/load_main_components.inc.php";

//$environment = require('/home/metmeetings/private/environment_variables.php');
// $stripeEnv = $environment['STRIPE_ENV'];
// $stripeEnv = (isset($_SESSION['test']) && $_SESSION['test'] == '1') ? 'test' : 'live';
require_once('vendor/autoload.php');
$keys = require('/home/metmeetings/private/stripe_keys.php');

\Stripe\Stripe::setApiKey($keys['secret_key']);

//Set API version (IMPORTANT: this needs to be updated if we switch to a newer API version)
\Stripe\Stripe::setApiVersion("2020-08-27");

//Variable used in load_send_mail_inscription(_xxx).php
$metodoPago = INSCRIPCION_TIPO_PAGO_PAYPAL;

//Retrieve the PaymentIntent object from the Stripe server (with intent ID from session)
//Use the "expand" parameter to simultaneously retrieve the BalanceTransaction object
$intent = \Stripe\PaymentIntent::retrieve([
    'id' => $_SESSION["payment_intent"],
    'expand' => ['charges.data.balance_transaction']
]);

//Get the transaction ID (used in last_step(_xxx).php) from the PaymentIntent object
$txnId = $intent->charges->data[0]->id;

//Get the Stripe fee from the BalanceTransaction object
$fee = $intent->charges->data[0]->balance_transaction->fee_details[0]->amount;

//Convert fee in cents to fee in euros ($stripeFee is used in load_send_mail_inscription(_xxx).php)
if ($fee != "") {
    $stripeFee = $fee / 100;
} else {
    $stripeFee = 0;
}

//Variable used to update database records in last_step(_xxx).php
$idEstadoInscripcion=INSCRIPCION_ESTADO_INSCRIPCION_CONFIRMADA;

// unset($_SESSION['test']);

switch ($_SESSION["regType"]) {
    case STATIC_FORM_STRIPE_ITEM_MEMBERSHIP:
        include 'ajax/last_step_inscription_stripe.php';
        generalUtils::redirigir('inscripcion_finalizada.php?tipo=2');
        break;
    case STATIC_FORM_STRIPE_ITEM_MEMBERSHIP_RENEWAL:
        include 'ajax/last_step_inscription_stripe.php';
        generalUtils::redirigir('inscripcion_finalizada.php?tipo=2');
        break;
    case STATIC_FORM_STRIPE_ITEM_WORKSHOP_REGISTRATION:
        include 'ajax/last_step_inscription_workshop_stripe.php';
        generalUtils::redirigir('inscripcion_finalizada.php?modo=2&tipo=2');
        break;
    case STATIC_FORM_STRIPE_ITEM_CONFERENCE_REGISTRATION:
        include 'ajax/last_step_inscription_conference_stripe.php';
        generalUtils::redirigir('inscripcion_finalizada.php?modo=1&tipo=2');
        break;
}