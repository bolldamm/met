<?php

/*
 * Sends membership confirmation email (new or renewal)
 * For institutions, inserts new registrations for nominees
 * Inserts movement and links to registration
 * If prepaid, inserts dummy movements
 * If Stripe, inserts fee movement and links to registration
 * Generates invoice
 * Sends confirmation email
 */

$absolutePath = dirname(__FILE__);

//Phpmailer class
require $absolutePath . "/load_mailer.inc.php";

//Main template
$plantillaMail = new XTemplate($absolutePath . "/../html/mail/mail_index.html");

//Subtemplate
$subPlantillaMail = new XTemplate($absolutePath . "/../html/mail/mail_inscription.html");

/*
 * Based on payment method (transfer, credit card or direct debit)
 * and registration type (new membership or membership renewal),
 * assign email contents to template and
 * store payment method and paid status (0) in variables for insertion in movement
 */
if ($metodoPago == INSCRIPCION_TIPO_PAGO_TRANSFERENCIA) {
    $tipoPagoMovimiento = MOVIMIENTO_TIPO_PAGO_TRANSFERENCIA;
    $pagado = 0;

    /*
     * For bank transfers and direct debits
     * the scope is save_inscription.php or save_renew_inscription.php
     * so the $tipoInscripcion variable is already defined (global variable)
     */
    if ($tipoInscripcion == 1) {
        //New membership
        $subPlantillaMail->assign("EMAIL_TEXTO", STATIC_MAIL_INSCRIPTION_TRANSFER_PAYMENT_FIRST_STEP);
    } else {
        //Membership renewal
        $subPlantillaMail->assign("EMAIL_TEXTO", STATIC_MAIL_RENEW_INSCRIPTION_TRANSFER_PAYMENT_FIRST_STEP);
    }
} else if ($metodoPago == INSCRIPCION_TIPO_PAGO_PAYPAL) {
    /*
     * For Stripe signups, the global scope is this file (not save_inscription.php),
     * so $tipoInscripcion is not already defined but has to be retrieved from session variable
     */
    $tipoInscripcion = $_SESSION["tipoInscripcion"];
    $tipoPagoMovimiento = MOVIMIENTO_TIPO_PAGO_PAYPAL;
    $pagado = 1;
    if ($tipoInscripcion == 1) {
        $subPlantillaMail->assign("EMAIL_TEXTO", STATIC_MAIL_INSCRIPTION_PAYPAL_PAYMENT_FIRST_STEP);
    } else {
        $subPlantillaMail->assign("EMAIL_TEXTO", STATIC_MAIL_RENEW_INSCRIPTION_PAYPAL_PAYMENT_FIRST_STEP);
    }
} else if ($metodoPago == INSCRIPCION_TIPO_PAGO_DEBIT) {
    $tipoPagoMovimiento = MOVIMIENTO_TIPO_PAGO_DEBIT;
    $pagado = 0;
    if ($tipoInscripcion == 1) {
        $subPlantillaMail->assign("EMAIL_TEXTO", STATIC_MAIL_INSCRIPTION_DEBIT_PAYMENT_FIRST_STEP);
    } else {
        $subPlantillaMail->assign("EMAIL_TEXTO", STATIC_MAIL_RENEW_INSCRIPTION_DEBIT_PAYMENT_FIRST_STEP);
    }
}

//If renewal (tipo 2) and institution (logged in), insert a new registration record for each NOMINEE
if ($tipoInscripcion == 2 && $_SESSION["met_user"]["id_modalidad"] == MODALIDAD_USUARIO_INSTITUTIONAL) {
    $resultadoInscripcionInstitucion = $db->callProcedure("CALL ed_sp_web_inscripcion_asociados_institucion_insertar(" . $idEstadoInscripcion . "," . $metodoPago . "," . $idUsuarioWeb . ",'" . $importe . "','" . $fechaInscripcion . "','" . $fechaFinalizacion . "'," . $esFactura . ")");
}

//If renewal (tipo 2) and institution (logged in), use representative and institution's name for email; otherwise just first name
if ($tipoInscripcion == 2 && $_SESSION["met_user"] && $_SESSION["met_user"]["id_modalidad"] == MODALIDAD_USUARIO_INSTITUTIONAL) {
    $nombreUsuarioInstitucion = $_SESSION["met_user"]["name"] . " " . $_SESSION["met_user"]["lastname"] . " (on behalf of " . $_SESSION["met_user"]["institution_name"] . ")";
    $subPlantillaMail->assign("EMAIL_USUARIO_WEB_NOMBRE", $nombreUsuarioInstitucion);
} else {
    $subPlantillaMail->assign("EMAIL_USUARIO_WEB_NOMBRE", $nombreUsuario);
}

$subPlantillaMail->parse("contenido_principal");

//Export subtemplate to main template
$plantillaMail->assign("CONTENIDO", $subPlantillaMail->text("contenido_principal"));

$plantillaMail->parse("contenido_principal");

//Assign message body
$mail->Body = $subPlantillaMail->text("contenido_principal");

/*
* Insert movement in database
* If signup is after 30 September, set sub-account to "Prepaid"
* Set movement to be non-taxable
*/
$fechaInscripcion = date("Y-m-d G:i:s");
$fechaHoraDesglosada = explode(" ", $fechaInscripcion);
$fechaDesglosada = explode("-", $fechaHoraDesglosada[0]);
$nextYear = $fechaDesglosada[0] + 1;
$dummyDate = $nextYear . "-01-02"; //for prepaid movements
if ($fechaDesglosada[1] > 9) {
    $idConceptoMovimiento = MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP_PREPAID;
}

$nonTaxable = 1; // MET membership registration is not taxable

/*
 * Depending whether new member or renewal (logged in, individual or institution)
 * assign the name to be associated with the movement
 */
if (!$_SESSION["met_user"]) {
    $nameOfPayer = $nombreUsuario . " " . $apellidosUsuario;
} elseif ($_SESSION["met_user"]["id_modalidad"] == MODALIDAD_USUARIO_INDIVIDUAL) {
    $nameOfPayer = $nombreUsuario . " " . $apellidosUsuario;
} elseif ($_SESSION["met_user"]["id_modalidad"] == MODALIDAD_USUARIO_INSTITUTIONAL) {
    $nameOfPayer = $_SESSION["met_user"]["institution_name"];
}

//Insert the movement
$resultadoMovimiento = $db->callProcedure("CALL ed_sp_web_movimiento_insertar(" . MOVIMIENTO_TIPO_ENTRADA . "," . $idConceptoMovimiento . "," . $nonTaxable . "," . $tipoPagoMovimiento . "," . $idUsuarioWeb . ",'" . generalUtils::escaparCadena($nameOfPayer) . "','" . date("Y-m-d") . "','" . STATIC_MOVEMENT_NEW_MEMBERSHIP_DESCRIPTION . "','" . $importe . "'," . $pagado . ")");

//Retrieve movement ID from database
$datoMovimiento = $db->getData($resultadoMovimiento);
$idMovimiento = $datoMovimiento["id_movimiento"];

//Link movement and registration in ed_tb_movimiento_inscripcion
$db->callProcedure("CALL ed_sp_web_movimiento_inscripcion_insertar(" . $idMovimiento . "," . $idInscripcion . ")");

/*
 * If signup is after 30 September, create two extra movements (1 positive, 1 negative)
 * dated 2 Jan the following year ($dummyDate)
 */
if ($fechaDesglosada[1] > 9) {
    $db->callProcedure("CALL ed_sp_web_movimiento_prepaid_positive_insertar(" . MOVIMIENTO_TIPO_ENTRADA . "," . $idConceptoMovimiento . "," . $nonTaxable . "," . $tipoPagoMovimiento . "," . $idUsuarioWeb . ",'" . generalUtils::escaparCadena($nameOfPayer) . "','" . $dummyDate . "','" . STATIC_MOVEMENT_NEW_MEMBERSHIP_DESCRIPTION . "','" . $importe . "'," . $pagado . ")");
    $db->callProcedure("CALL ed_sp_web_movimiento_prepaid_negative_insertar(" . MOVIMIENTO_TIPO_ENTRADA . "," . $idConceptoMovimiento . "," . $nonTaxable . "," . $tipoPagoMovimiento . "," . $idUsuarioWeb . ",'" . generalUtils::escaparCadena($nameOfPayer) . "','" . $dummyDate . "','" . STATIC_MOVEMENT_NEW_MEMBERSHIP_DESCRIPTION . "','" . -$importe . "'," . $pagado . ")");
}

//Insert processing fee in ed_tb_movimiento if applicable
if (isset($stripeFee)) {
    $resultadoMovimientoComision = $db->callProcedure("CALL ed_sp_web_movimiento_insertar(" . MOVIMIENTO_TIPO_SALIDA . "," . MOVIMIENTO_CONCEPTO_FEE_GENERAL_BANKING . "," . $nonTaxable . "," . $tipoPagoMovimiento . "," . $idUsuarioWeb . ",'" . generalUtils::escaparCadena($nameOfPayer) . " (" . STATIC_GLOBAL_PAYPAL_FEE . ")','" . date("Y-m-d") . "','" . generalUtils::escaparCadena($nameOfPayer) . "','" . $stripeFee . "'," . $pagado . ")");
    $datoMovimientoComision = $db->getData($resultadoMovimientoComision);
    $idMovimientoComision = $datoMovimientoComision["id_movimiento"];

    //Link fee and registration in ed_tb_movimiento_inscripcion
    $db->callProcedure("CALL ed_sp_web_movimiento_inscripcion_insertar(" . $idMovimientoComision . "," . $idInscripcion . ")");

}

//Insert invoice
$resultadoFactura = $db->callProcedure("CALL ed_sp_web_factura_insertar('" . $nifFactura . "','" . $nombreClienteFactura . "','" . $nombreEmpresaFactura . "','" . $direccionFactura . "','" . $codigoPostalFactura . "','" . $ciudadFactura . "','" . $provinciaFactura . "','" . $paisFactura . "','" . $emailUsuario . "','" . $nombreUsuario . "')");
$datoFactura = $db->getData($resultadoFactura);
$idFactura = $datoFactura["id_factura"];

//Insert invoice details (ed_tb_linea_factura)
$db->callProcedure("CALL ed_sp_web_linea_factura_insertar(" . $idFactura . "," . $idMovimiento . ")");

//Add phpmailer configuration
$mail->AddAddress($emailUsuario);
$mail->AddBCC(STATIC_MAIL_TO_MEMBERSHIP_REG_FORM);
$mail->FromName = STATIC_MAIL_FROM;
$mail->Subject = STATIC_MAIL_INSCRIPCION_SUBJECT;

/*
 * February 2019: no email to renewing institutions, except if paid using Stripe
 * So if not logged in (new member) or logged in but not an institution or if paid using Stripe, send email
 */
if ((!$_SESSION["met_user"] || $_SESSION["met_user"]["id_modalidad"] != MODALIDAD_USUARIO_INSTITUTIONAL) || $metodoPago == INSCRIPCION_TIPO_PAGO_PAYPAL) {
//Send and log the email
    if ($mail->Send()) {

        /****** Log the email (in the Sent Mail list in EasyGestor) ******/
        //Set email type (new membership or renewal)
        if ($tipoInscripcion == 1) {
            $idTipoCorreoElectronico = EMAIL_TYPE_INSCRIPTION_FORM;
        } else {
            $idTipoCorreoElectronico = EMAIL_TYPE_INSCRIPTION_RENEW_FORM;
        }


        //Recipients (user and MET)
        $vectorDestinatario = Array();
        array_push($vectorDestinatario, $emailUsuario);
        array_push($vectorDestinatario, STATIC_MAIL_TO_MEMBERSHIP_REG_FORM);

        //Email subject and body
        $asunto = STATIC_MAIL_INSCRIPCION_SUBJECT;
        $cuerpo = $mail->Body;

        require $absolutePath . "/load_log_email.inc.php";

    }
}


//Parse HTML email content
$plantillaMail->assign("CONTENIDO", $subPlantillaMail->text("contenido_principal"));
$plantillaMail->parse("contenido_principal");
?>