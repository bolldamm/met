<?php
require_once('vendor/autoload.php');

$stripe = array(
    "secret_key"      => "sk_test_IicVWKJ0KpnIkrP7fppUvLpt",
    "publishable_key" => "pk_test_80hWYS4a2mVlhQRhoNQTV1PQ"
);

\Stripe\Stripe::setApiKey($stripe['secret_key']);
?>