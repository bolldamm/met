<?php
  require_once('config.php');

  $token  = $_POST['stripeToken'];
  $email  = $_POST['stripeEmail'];
  $amount = $_POST['amount'];
  $custom = $_POST['custom'];

  $customer = \Stripe\Customer::create(array(
      'email' => $email,
      'source'  => $token
  ));

  $charge = \Stripe\Charge::create(array(
      'customer' => $customer->id,
      'amount'   => $amount,
      'description'  => $item,
      'statement_descriptor' => $item,
      'currency' => 'eur',
      'metadata' => array('custom' => $custom)
  ));

  $txnId = $charge['id'];
    $datosInscripcion = explode("-", $_POST["custom"]);
    $numeroPedidoInscripcion = $datosInscripcion[0];
    $idInscripcion = $datosInscripcion[1];
    $tipoInscripcion = $datosInscripcion[2];

  if ($charge['status'] == 'succeeded') {
      include 'ajax/last_step_inscription_conference_stripe.php';
      header('Location:inscripcion_finalizada.php?modo=1&tipo=2');
      exit();
      //echo '<h1>Successfully charged &euro;' . $amount / 100 . '!</h1>';
  } else {
      echo $charge['failure_message'];
  }

//echo '<pre>';
//print_r($charge);


