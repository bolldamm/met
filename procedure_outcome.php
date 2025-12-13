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
    // Note: Since Stripe API 2024-04-10, balance transactions are created asynchronously
    // so we need to handle the case where it's not yet available or is just a string ID
    $stripeFee = 0;
    $maxRetries = 3;
    $retryDelay = 1; // seconds

    for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
        // Refresh the charge to get the latest balance_transaction status
        if ($attempt > 1) {
            sleep($retryDelay);
            try {
                $charge = $stripe->charges->retrieve($txnId, ['expand' => ['balance_transaction']]);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                error_log('Retry ' . $attempt . ' - Could not retrieve charge: ' . $e->getMessage());
                continue;
            }
        }

        $balanceTxn = $charge->balance_transaction ?? null;

        // If balance_transaction is a string (ID), retrieve the full object
        if (is_string($balanceTxn) && !empty($balanceTxn)) {
            try {
                $balanceTxn = $stripe->balanceTransactions->retrieve($balanceTxn);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                error_log('Attempt ' . $attempt . ' - Could not retrieve balance transaction: ' . $e->getMessage());
                $balanceTxn = null;
            }
        }

        // Check if we have the fee details
        if (is_object($balanceTxn) && !empty($balanceTxn->fee_details)) {
            // Sum all fee components (Stripe fee, application fee, etc.)
            $totalFee = 0;
            foreach ($balanceTxn->fee_details as $feeDetail) {
                $totalFee += $feeDetail->amount;
            }
            $stripeFee = $totalFee / 100;
            error_log('Stripe fee retrieved on attempt ' . $attempt . ': ' . $stripeFee . ' EUR');
            break; // Success - exit the retry loop
        }

        if ($attempt < $maxRetries) {
            error_log('Balance transaction not yet available (attempt ' . $attempt . '), retrying in ' . $retryDelay . 's...');
        }
    }

    // If still no fee after all retries, log a warning
    if ($stripeFee == 0) {
        error_log('WARNING: Could not retrieve Stripe fee for charge ' . $txnId . ' after ' . $maxRetries . ' attempts. Fee recorded as 0.');
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