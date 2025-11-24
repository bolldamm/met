<?php
$esRemoto = true;

//NB path is relative to stripe_button.html
require "includes/load_main_components.inc.php";

$idInscripcion = $_SESSION["idInscripcion"];
$numeroPedidoInscripcion = $_SESSION["numeroPedidoInscripcion"];

$idConceptoMovimiento = MOVIMIENTO_CONCEPTO_NEW_WORKSHOP;

//Open database connection
$db->startTransaction();

//Store Stripe transaction details in ed_tb_inscripcion_taller_stripe
$resultado = $db->callProcedure("CALL ed_sp_web_inscripcion_taller_stripe_guardar(" . $idInscripcion . ",'" . $txnId . "')");

//Update registration status from Pending to Confirmed
$idEstado = INSCRIPCION_ESTADO_INSCRIPCION_CONFIRMADA;
$resultado = $db->callProcedure("CALL ed_sp_web_inscripcion_taller_estado_actualizar(" . $idEstado . ",'" . $numeroPedidoInscripcion . "')");

//Update registration status from Unpaid to Paid
$pagado = 1;
$resultado = $db->callProcedure("CALL ed_sp_web_inscripcion_taller_pagado_actualizar(" . $pagado . ",'" . $numeroPedidoInscripcion . "')");

//Update "email sent" status to Sent
$resultado=$db->callProcedure("CALL ed_sp_web_inscripcion_taller_email_enviado_actualizar('".$numeroPedidoInscripcion."')");

//The path is relative to charge.php
require "includes/load_send_mail_inscription_workshop.inc.php";

//Finalizar transaccion
$db->endTransaction();
