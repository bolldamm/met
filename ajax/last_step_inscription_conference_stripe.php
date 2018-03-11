<?php

require "../includes/load_main_components.inc.php";

    //Store registration ID, number and type and payment method in variables
    $datosInscripcion = explode("-", $_POST["custom"]);
    $numeroPedidoInscripcion = $datosInscripcion[0];
    $idInscripcion = $datosInscripcion[1];
    $tipoInscripcion = $datosInscripcion[2];
    $metodoPago = INSCRIPCION_TIPO_PAGO_PAYPAL;

    //Collect data returned by Paypal (transaction ID, payer ID and fee)
    $txnId = $_POST["txn_id"];
    $payerId = $_POST["payer_id"];
    if (isset($_POST["mc_fee"]) && $_POST["mc_fee"] != "") {
        $comision = $_POST["mc_fee"];
    } else {
        $comision = 0;
    }

    //Store account name ("METM registration") in variable
    $idConceptoMovimiento = MOVIMIENTO_CONCEPTO_NEW_CONFERENCE;


    //Connect to database to update records
    $db->startTransaction();


    //Store registration ID, Paypal transaction ID and payer ID in the database
    $resultado = $db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_paypal_guardar(" . $idInscripcion . ",'" . $txnId . "','" . $payerId . "')");


    //Update payment status from "Pending" to "Confirmed"
    $idEstado = INSCRIPCION_ESTADO_INSCRIPCION_CONFIRMADA;
    $resultado = $db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_estado_actualizar(" . $idEstado . ",'" . $numeroPedidoInscripcion . "')");


    //Update conference registration status from "Unpaid" to "Paid"
    $pagado = 1;
    $resultado = $db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_pagado_actualizar(" . $pagado . ",'" . $numeroPedidoInscripcion . "')");


    //Update workshop status from "Unpaid" to "Paid"
    $pagado = 1;
    $resultado = $db->callProcedure("CALL ed_sp_inscripcion_conferencia_linea_pagado_actualizar(" . $numeroPedidoInscripcion . ",'" . $pagado . "')");


    //End database transaction and call script to send email
    $db->endTransaction();
    require "../includes/load_send_mail_inscription_conference.inc.php";


?>