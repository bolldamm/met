<?php

//The path is relative to charge.php
require "includes/load_main_components.inc.php";

$idInscripcion = $_SESSION["idInscripcion"];
$numeroPedidoInscripcion = $_SESSION["numeroPedidoInscripcion"];

//Store account name ("METM registration") in variable
$idConceptoMovimiento = MOVIMIENTO_CONCEPTO_NEW_CONFERENCE;

//Connect to database to update records
$db->startTransaction();

//Store registration ID, transaction ID and customer ID in the database
$resultado = $db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_stripe_guardar(" . $idInscripcion . ",'" . $txnId . "')");

//Update payment status from "Pending" to "Confirmed"
$idEstado = INSCRIPCION_ESTADO_INSCRIPCION_CONFIRMADA;
$resultado = $db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_estado_actualizar(" . $idEstado . ",'" . $numeroPedidoInscripcion . "')");

//Update conference registration status from "Unpaid" to "Paid"
$pagado = 1;
$resultado = $db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_pagado_actualizar(" . $pagado . ",'" . $numeroPedidoInscripcion . "')");

//Update workshop status from "Unpaid" to "Paid"
$pagado = 1;
$resultado = $db->callProcedure("CALL ed_sp_inscripcion_conferencia_linea_pagado_actualizar(" . $numeroPedidoInscripcion . ",'" . $pagado . "')");

//Update "email sent" status to Sent
$resultado=$db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_email_enviado_actualizar('".$numeroPedidoInscripcion."')");


//End database transaction and call script to send emails
$db->endTransaction();

//The path is relative to charge.php
require "includes/load_send_mail_inscription_conference.inc.php";

?>