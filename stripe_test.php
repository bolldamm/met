<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 12/03/2018
 * Time: 17:21
 */

require_once('config.php');

try {
    $transaction = \Stripe\BalanceTransaction::retrieve('txn_1C4ut9BHXryFxJTVWEW52HKh');
    $feeDetails = $transaction['fee_details'];

} catch(\Stripe\Error\Card $e) {
    // Since it's a decline, \Stripe\Error\Card will be caught
    $body = $e->getJsonBody();
    $err  = $body['error'];

    print('Status is:' . $e->getHttpStatus() . "\n");
    print('Type is:' . $err['type'] . "\n");
    print('Code is:' . $err['code'] . "\n");
    // param is '' in this case
    print('Param is:' . $err['param'] . "\n");
    print('Message is card error' . "\n");
} catch (\Stripe\Error\RateLimit $e) {
    // Too many requests made to the API too quickly
} catch (\Stripe\Error\InvalidRequest $e) {
    // Invalid parameters were supplied to Stripe's API
    var_dump($e);
} catch (\Stripe\Error\Authentication $e) {
    // Authentication with Stripe's API failed
    // (maybe you changed API keys recently)
    print('Message is authentication error!' . "\n");
} catch (\Stripe\Error\ApiConnection $e) {
    // Network communication with Stripe failed
    print('Message is API connection error' . "\n");
} catch (\Stripe\Error\Base $e) {
    // Display a very generic error to the user, and maybe send
    // yourself an email
    print('Message is: Hi!' . "\n");
} catch (Exception $e) {
    // Something else happened, completely unrelated to Stripe
}

?>