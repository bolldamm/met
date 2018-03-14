<?php

require "includes/load_main_components.inc.php";

    $metodoPago = INSCRIPCION_TIPO_PAGO_PAYPAL;

    $transaction = \Stripe\BalanceTransaction::retrieve('txn_1C4ut9BHXryFxJTVWEW52HKh');
    $feeDetails = $transaction['fee_details'];
    $fee = $feeDetails[0]['amount'];

    if ($fee != "") {
        $stripeFee = $fee / 100;
    } else {
        $stripeFee = 0;
    }

    //Store account name ("METM registration") in variable
    $idConceptoMovimiento = MOVIMIENTO_CONCEPTO_NEW_CONFERENCE;

    //Connect to database to update records
    $db->startTransaction();

    //Store registration ID, transaction ID and customer ID in the database
    $resultado = $db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_stripe_guardar(" . $idInscripcion . ",'" . $txnId . "','" . $chargeId . "','" . $custId . "')");

    //Update payment status from "Pending" to "Confirmed"
    $idEstado = INSCRIPCION_ESTADO_INSCRIPCION_CONFIRMADA;
    $resultado = $db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_estado_actualizar(" . $idEstado . ",'" . $numeroPedidoInscripcion . "')");

    //Update conference registration status from "Unpaid" to "Paid"
    $pagado = 1;
    $resultado = $db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_pagado_actualizar(" . $pagado . ",'" . $numeroPedidoInscripcion . "')");

    //Update workshop status from "Unpaid" to "Paid"
    $pagado = 1;
    $resultado = $db->callProcedure("CALL ed_sp_inscripcion_conferencia_linea_pagado_actualizar(" . $numeroPedidoInscripcion . ",'" . $pagado . "')");

    //End database transaction and call script to send emails
    $db->endTransaction();
    require "includes/load_send_mail_inscription_conference_stripe.inc.php";

?>