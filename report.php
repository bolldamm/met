<?php
//Stripe webhook endpoint

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/home/metmeetings/private/stripe_keys.php';
require_once __DIR__ . '/home/metmeetings/private/webhook_secret.php';

$payload   = @file_get_contents('php://input');
$sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

$endpointSecret = STRIPE_WEBHOOK_SECRET;
if (!$endpointSecret) { 
  error_log('No STRIPE_WEBHOOK_SECRET'); 
  http_response_code(500); exit; 
}

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload,
        $sigHeader,
        $endpointSecret   
    );
} catch (\UnexpectedValueException $e) {
    http_response_code(400); exit('Invalid payload');
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    http_response_code(400); exit('Invalid signature');
}

// Handle the events you care about
try {
switch ($event->type) {
//   case 'checkout.session.completed':
        /** @var \Stripe\Checkout\Session $session */
        //$session = $event->data->object;
        // TODO: mark order paid in DB by your own order ID (from metadata)
        // You can fetch more details if needed:
        // $sc = new \Stripe\StripeClient(STRIPE_SECRET_KEY);
        // $sessionFull = $sc->checkout->sessions->retrieve($session->id, ['expand' => ['payment_intent', 'customer']]);
        //break;

    case 'payment_intent.succeeded':
        /** @var \Stripe\PaymentIntent $pi */
        $pi = $event->data->object;
        // TODO: persist success, reconcile to your order
        break;

    // Add invoice.* events if you enabled invoice_creation
}

http_response_code(200); // Acknowledge
} catch (\Throwable $e) {
    // You can still return 200 if the event was valid but your handler failed
    // to avoid repeated retries, but make sure you log and have a retry path.
    error_log('Webhook handler error: '.$e->getMessage());
    http_response_code(200);
}
