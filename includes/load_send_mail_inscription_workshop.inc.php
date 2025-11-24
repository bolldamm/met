<?php

$absolutePath = dirname(__FILE__);


//phpmailer class
require $absolutePath . "/load_mailer.inc.php";

require $absolutePath . "/load_format_date.inc.php";

require $absolutePath . "/settings.php";


//Set language to English (id_idioma=3)
$idIdioma = 3;
//Get workshop signup details from database
$resultadoTallerConcreto = $db->callProcedure("CALL ed_sp_web_inscripcion_taller_obtener_concreta(" . $idInscripcion . "," . $idIdioma . ")");
$datoTallerConcreto = $db->getData($resultadoTallerConcreto);

$esFactura = 1;

//Assign billing information to variables
$nifFactura = generalUtils::escaparCadena($datoTallerConcreto["nif_cliente_factura"]);
$nombreClienteFactura = generalUtils::escaparCadena($datoTallerConcreto["nombre_cliente_factura"]);
$nombreEmpresaFactura = generalUtils::escaparCadena($datoTallerConcreto["nombre_empresa_factura"]);
$direccionFactura = generalUtils::escaparCadena($datoTallerConcreto["direccion_factura"]);
$codigoPostalFactura = generalUtils::escaparCadena($datoTallerConcreto["codigo_postal_factura"]);
$ciudadFactura = generalUtils::escaparCadena($datoTallerConcreto["ciudad_factura"]);
$provinciaFactura = generalUtils::escaparCadena($datoTallerConcreto["provincia_factura"]);
$paisFactura = generalUtils::escaparCadena($datoTallerConcreto["pais_factura"]);
$emailClienteFactura = generalUtils::escaparCadena($datoTallerConcreto["correo_electronico"]);
$firstName = generalUtils::escaparCadena($datoTallerConcreto["nombre"]);

//Assign main template
$plantillaMail = new XTemplate($absolutePath . "/../html/mail/mail_index_white_background.html");

//Assign payment method to a variable and set unpaid/paid status accordingly
if ($metodoPago == INSCRIPCION_TIPO_PAGO_TRANSFERENCIA) {
    $tipoPagoMovimiento = MOVIMIENTO_TIPO_PAGO_TRANSFERENCIA;
    if (!isset($pagado)) {
        $pagado = 0;
    }

} else if ($metodoPago == INSCRIPCION_TIPO_PAGO_PAYPAL) {
    $tipoPagoMovimiento = MOVIMIENTO_TIPO_PAGO_PAYPAL;
    $pagado = 1;
}


//Assign subtemplate for email to MET
$subPlantillaMail = new XTemplate($absolutePath . "/../html/mail/mail_workshop_to_met.html");

//Assign subtemplate for email to user
$subPlantillaMailUser = new XTemplate($absolutePath . "/../html/mail/mail_workshop_to_user.html");

$nombre = $datoTallerConcreto["nombre"]; //first name only for email to user
$nombreCompleto = $datoTallerConcreto["nombre"] . " " . $datoTallerConcreto["apellidos"]; //full name for email to MET and for DB

$subPlantillaMail->assign("MAIL_INSCRIPTION_TO_MET_NAME_VALUE", $nombreCompleto);
$subPlantillaMailUser->assign("MAIL_INSCRIPTION_TO_USER_NAME_VALUE", $nombre);
$subPlantillaMail->assign("MAIL_INSCRIPTION_TO_MET_EMAIL_VALUE", $datoTallerConcreto["correo_electronico"]);

if ($datoTallerConcreto["telefono"]) {
    $subPlantillaMail->assign("MAIL_INSCRIPTION_TO_MET_PHONE_VALUE", $datoTallerConcreto["telefono"]);
    $subPlantillaMail->parse("contenido_principal.bloque_phone");
}

if ($datoTallerConcreto["descripcion"]) {
    $subPlantillaMail->assign("MAIL_INSCRIPTION_TO_MET_SISTER_ASSOCIATION_VALUE", $datoTallerConcreto["descripcion"]);
    $subPlantillaMail->parse("contenido_principal.bloque_association");
}

//Get workshop signup data from database
$resultadoTallerListado = $db->callProcedure("CALL ed_sp_web_inscripcion_taller_linea_obtener(" . $idInscripcion . "," . $idIdioma . ")");


//Get list of workshops and prices for inclusion in email
$precio = 0;
while ($datoTallerListado = $db->getData($resultadoTallerListado)) {
    $subPlantillaMail->assign("WORKSHOP_NOMBRE", $datoTallerListado["nombre_largo"]);

    //for the user
    $subPlantillaMailUser->assign("WORKSHOP_NOMBRE", $datoTallerListado["nombre_largo"]);


    $fechaTaller = generalUtils::conversionFechaFormato($datoTallerListado["fecha"], "-", "/");
    $mesTaller = explode("/", $fechaTaller);

    //Get the day of the week
    $fechaTrozeada = explode("-", $datoTallerListado["fecha"]);
    $subPlantillaMailUser->assign("WORKSHOP_FECHA", intval($fechaTrozeada[2]) . " " . $vectorMes[$fechaTrozeada[1]]);
    $subPlantillaMailUser->assign("WORKSHOP_PRECIO", $datoTallerListado["importe"]);


    $subPlantillaMail->parse("contenido_principal.item_workshop");
    $subPlantillaMailUser->parse("contenido_principal.item_workshop");
    $precio += $datoTallerListado["importe"];
}


if ($datoTallerConcreto["comentarios"]) {
    $subPlantillaMail->assign("MAIL_INSCRIPTION_TO_MET_COMMENTS_VALUE", $datoTallerConcreto["comentarios"]);
    $subPlantillaMail->parse("contenido_principal.bloque_comment");
}

//Set registration type and price (sister association,member or non-member)
if ($datoTallerConcreto["id_usuario_web"] != "") {
    $asuntoTipoInscrito = STATIC_MAIL_INSCRIPTION_WORKSHOP_SUBJECT_MEMBER_TYPE;
    $subPlantillaMailUser->assign("FORM_WORKSHOP_REGISTER_LEGEND_WORKSHOPS_PRICE", STATIC_FORM_WORKSHOP_REGISTER_LEGEND_WORKSHOPS_PRICE);
} else {
    if ($datoTallerConcreto["id_asociacion_hermana"] == "") {
        $asuntoTipoInscrito = STATIC_MAIL_INSCRIPTION_WORKSHOP_SUBJECT_NON_MEMBER_TYPE;
        $subPlantillaMailUser->assign("FORM_WORKSHOP_REGISTER_LEGEND_WORKSHOPS_PRICE", STATIC_FORM_WORKSHOP_REGISTER_LEGEND_WORKSHOPS_PRICE_NON_MEMBER);
    } else {
        $asuntoTipoInscrito = STATIC_MAIL_INSCRIPTION_WORKSHOP_SUBJECT_SISTER_ASSOCIATION_TYPE;
        $subPlantillaMailUser->assign("FORM_WORKSHOP_REGISTER_LEGEND_WORKSHOPS_PRICE", STATIC_FORM_WORKSHOP_REGISTER_LEGEND_WORKSHOPS_PRICE_SISTER);
    }
}


$subPlantillaMail->parse("contenido_principal");
$subPlantillaMailUser->parse("contenido_principal");


//Create the body of the email


//First the email to MET

$mail->Body = $subPlantillaMail->text("contenido_principal");

$nonTaxable = 0; //workshops signups are taxable!

//Insert new movement in ed_tb_movimiento
$resultadoMovimiento = $db->callProcedure("CALL ed_sp_web_movimiento_insertar(" . MOVIMIENTO_TIPO_ENTRADA . "," . $idConceptoMovimiento . "," . $nonTaxable . "," . $tipoPagoMovimiento . ",null,'" . generalUtils::escaparCadena($nombreCompleto) . "','" . date("Y-m-d") . "','" . STATIC_MOVEMENT_NEW_WORKSHOP_DESCRIPTION . "','" . $precio . "'," . $pagado . ")");
$datoMovimiento = $db->getData($resultadoMovimiento);
$idMovimiento = $datoMovimiento["id_movimiento"];

//Insert entry in ed_tb_movimiento_inscripcion_taller to link movement and workshop registration ID
$db->callProcedure("CALL ed_sp_web_movimiento_inscripcion_taller_insertar(" . $idMovimiento . "," . $idInscripcion . ")");

//Insert processing fee in ed_tb_movimiento if applicable
if (isset($stripeFee)) {
    $resultadoMovimientoComision = $db->callProcedure("CALL ed_sp_web_movimiento_insertar(" . MOVIMIENTO_TIPO_SALIDA . "," . MOVIMIENTO_CONCEPTO_FEE_GENERAL_BANKING . "," . $nonTaxable . "," . $tipoPagoMovimiento . ",null,'" . STATIC_GLOBAL_PAYPAL_FEE . "','" . date("Y-m-d") . "','" . generalUtils::escaparCadena($nombrePersonaCompleto) . "','" . $stripeFee . "'," . $pagado . ")");
    $datoMovimientoComision = $db->getData($resultadoMovimientoComision);
    $idMovimientoComision = $datoMovimientoComision["id_movimiento"];

    //Insert entry in ed_tb_movimiento_inscripcion_taller to link fee and workshop registration ID
    $db->callProcedure("CALL ed_sp_web_movimiento_inscripcion_taller_insertar(" . $idMovimientoComision . "," . $idInscripcion . ")");
}

//Insert invoice
$resultadoFactura = $db->callProcedure("CALL ed_sp_web_factura_insertar('" . $nifFactura . "','" . $nombreClienteFactura . "','" . $nombreEmpresaFactura . "','" . $direccionFactura . "','" . $codigoPostalFactura . "','" . $ciudadFactura . "','" . $provinciaFactura . "','" . $paisFactura . "','" . $emailClienteFactura . "','" . $firstName . "')");

$datoFactura = $db->getData($resultadoFactura);
$idFactura = $datoFactura["id_factura"];

//Insert invoice details (ed_tb_linea_factura)
$db->callProcedure("CALL ed_sp_web_linea_factura_insertar(" . $idFactura . "," . $idMovimiento . ")");


/**
 *
 * Add phpmailer configuration
 *
 */

$mail->AddAddress(STATIC_MAIL_TO_WORKSHOP_REG_FORM);
$mail->FromName = STATIC_MAIL_FROM;
$mail->Subject = STATIC_MAIL_INSCRIPTION_WORKSHOP_SUBJECT . ": " . $nombreCompleto . " (" . $asuntoTipoInscrito . ")";


//Send the email
if ($mail->Send()) {
    /****** Log the email (in the Sent Mail list in EastGestor) ******/
    $idUsuarioWebCorreo = "null";

    //Set email type
    $idTipoCorreoElectronico = EMAIL_TYPE_WORKSHOP_FORM_TO_MET;

    //define("EMAIL_TYPE_WORKSHOP_FORM_TO_USER",16);


    //Recipient (workshop-registration@metmeetings.org)
    $vectorDestinatario = Array();

    array_push($vectorDestinatario, STATIC_MAIL_TO_WORKSHOP_REG_FORM);

    //Email subject and body
    $asunto = $mail->Subject;
    $cuerpo = $mail->Body;

    require $absolutePath . "/load_log_email.inc.php";


    //Now the email to the user

    //Email to user
    $mail->ClearAllRecipients();
    $mail->AddAddress($datoTallerConcreto["correo_electronico"]);
    $plantillaMail->assign("CONTENIDO", $subPlantillaMailUser->text("contenido_principal"));

    $plantillaMail->parse("contenido_principal");

    $mail->Subject = STATIC_MAIL_INSCRIPTION_WORKSHOP_SUBJECT;
    $mail->Body = $plantillaMail->text("contenido_principal");
    $mail->Send();

    //Set email type
    $idTipoCorreoElectronico = EMAIL_TYPE_WORKSHOP_FORM_TO_USER;

    $vectorDestinatario = Array();
    array_push($vectorDestinatario, $datoTallerConcreto["correo_electronico"]);

    //Email subject and body
    $asunto = $mail->Subject;
    $cuerpo = $mail->Body;

    require $absolutePath . "/load_log_email.inc.php";
}
