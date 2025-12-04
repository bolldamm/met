<?php
/**
 * Retrieves signup and payment details from Stripe objects
 * Depending on registration type (membership, renewal, workshop, conference)…
 * Includes last_step_inscription_xxx.php (update DB and send emails)
 * Redirects to inscripcion_finalizada.php
 */

require "includes/load_main_components.inc.php";

require_once('vendor/autoload.php');
if (defined('MET_ENV') && constant('MET_ENV') == 'LOCAL') {
    require(__DIR__ . '/private/webhook_secret.php');
    $keys = require(__DIR__ . '/private/stripe_keys.php');
} else {
    require('/home/metmeetings/private/webhook_secret.php');
    $keys = require('/home/metmeetings/private/stripe_keys.php');
}

try {
    $stripe = new \Stripe\StripeClient([
        'api_key' => $keys['secret_key'],
        'stripe_version' => '2025-02-24.acacia',
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
    exit;
}

//Variable used in load_send_mail_inscription(_xxx).php
$metodoPago = INSCRIPCION_TIPO_PAGO_PAYPAL;

//Retrieve the PaymentIntent object from the Stripe server (with intent ID from session)
//Use the "expand" parameter to simultaneously retrieve the BalanceTransaction object
try {
    $intent = $stripe->paymentIntents->retrieve(
        $_SESSION["payment_intent"],
        ['expand' => ['latest_charge.balance_transaction']]
    );

    // Debug: log the intent structure
    error_log('PaymentIntent status: ' . $intent->status);
    error_log('PaymentIntent has latest_charge: ' . (isset($intent->latest_charge) ? 'yes' : 'no'));

} catch (\Stripe\Exception\ApiErrorException $e) {
    http_response_code(500);
    error_log('Stripe retrieve error: '.$e->getMessage());
    echo 'Could not load your payment summary. Please contact support.';
    exit;
}

//Get the transaction ID (used in last_step(_xxx).php) from the PaymentIntent object
// Use latest_charge instead of charges->data[0] (modern API approach)
if (!empty($intent->latest_charge)) {
    $charge = is_string($intent->latest_charge)
        ? $stripe->charges->retrieve($intent->latest_charge, ['expand' => ['balance_transaction']])
        : $intent->latest_charge;

    $txnId = $charge->id;

    //Get the Stripe fee from the BalanceTransaction object
    if (!empty($charge->balance_transaction->fee_details[0])) {
        $fee = $charge->balance_transaction->fee_details[0]->amount;
        $stripeFee = $fee / 100;
    } else {
        $stripeFee = 0;
    }
} else {
    // Fallback: use the payment intent ID if charge isn't available yet
    error_log('Payment Intent latest_charge not available: ' . $_SESSION["payment_intent"]);
    $txnId = $_SESSION["payment_intent"];
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