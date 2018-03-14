<?php
require_once('config.php');

$token = $_POST['stripeToken'];
$email = $_POST['stripeEmail'];
$amount = $_POST['amount'];
$item = $_POST['item'];
$regId = $_POST['regId'];

$customer = \Stripe\Customer::create(array(
    'email' => $email,
    'source' => $token
));

try {
    $charge = \Stripe\Charge::create(array(
        'customer' => $customer->id,
        'amount' => $amount,
        'description' => $item,  //this could be displayed to user
        'statement_descriptor' => $item,
        'currency' => 'eur',
        'metadata' => array('regId' => $regId)
    ));
} catch (\Stripe\Error\Card $e) {
    // Since it's a decline, \Stripe\Error\Card will be caught
    $body = $e->getJsonBody();
    $err = $body['error'];

    print('Status is:' . $e->getHttpStatus() . "\n");
    print('Type is:' . $err['type'] . "\n");
    print('Code is:' . $err['code'] . "\n");
    // param is '' in this case
    print('Param is:' . $err['param'] . "\n");
    print('Message is:' . $err['message'] . "\n");
} catch (\Stripe\Error\RateLimit $e) {
    // Too many requests made to the API too quickly
    print('rate limit');
} catch (\Stripe\Error\InvalidRequest $e) {
    // Invalid parameters were supplied to Stripe's API
    print('invalid request');
} catch (\Stripe\Error\Authentication $e) {
    // Authentication with Stripe's API failed
    // (maybe you changed API keys recently)
    print('authentication');
} catch (\Stripe\Error\ApiConnection $e) {
    // Network communication with Stripe failed
    print('API connection');
} catch (\Stripe\Error\Base $e) {
    // Display a very generic error to the user, and maybe send
    // yourself an email
    print('base error');
} catch (Exception $e) {
    print('random error');
    // Something else happened, completely unrelated to Stripe
}

$txnId = $charge['balance_transaction'];
$chargeId = $charge['id'];
$custId = $charge['customer'];
$datosInscripcion = explode("-", $regId);
$numeroPedidoInscripcion = $datosInscripcion[0];
$idInscripcion = $datosInscripcion[1];
$tipoInscripcion = $datosInscripcion[2];
$outcome = $charge['outcome'];
$sellerMessage = $outcome['seller_message'];

if ($charge['status'] == 'succeeded') {
    include 'ajax/last_step_inscription_conference_stripe.php';
    header('Location:inscripcion_finalizada.php?modo=1&tipo=2');
    exit();
} else {
    echo $charge['failure_message'];
}



