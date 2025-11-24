<?php

$absolutePath = dirname(__FILE__);

//phpmailer class
require $absolutePath . "/load_mailer.inc.php";

require $absolutePath . "/load_format_date.inc.php";

//Language is English (id=3)
$idIdioma = 3;

//Get conference data (prices and email texts)
$resultConference = $db->callProcedure("CALL ed_sp_web_conferencia_actual()");
$dataConference = $db->getData($resultConference);

//Store extra prices in variables
$priceWorkshop = generalUtils::escaparCadena($dataConference["price_extra_workshop"]);
$priceMinisession = generalUtils::escaparCadena($dataConference["price_extra_minisession"]);
$priceDinnerGuest = generalUtils::escaparCadena($dataConference["price_dinner_guest"]);
$priceDinnerOptoutDiscount = generalUtils::escaparCadena($dataConference["price_dinner_optout_discount"]);
$priceWineReceptionGuest = generalUtils::escaparCadena($dataConference["price_wine_reception_guest"]);
//$priceOtherExtra=generalUtils::escaparCadena($dataConference["price_other_extra"]);

//Store email texts in variables
$emailToMet = $dataConference["email_to_met"]; //Subject line and email title
$emailToUserPaypalIntro = $dataConference["email_to_user_paypal_intro"];
$emailToUserTransferIntro = $dataConference["email_to_user_transfer_intro"];
$emailToUserBody = $dataConference["email_to_user_body"];
$emailToUserSignoff = $dataConference["email_to_user_signoff"];

//Get details of this specific conference signup
$resultadoConferencia = $db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_obtener_concreta(" . $idInscripcion . "," . $idIdioma . ")");
$datoConferencia = $db->getData($resultadoConferencia);

$esFactura = 1;

//Store billing information in variables
$nifFactura = generalUtils::escaparCadena($datoConferencia["nif_cliente_factura"]);
$nombreClienteFactura = generalUtils::escaparCadena($datoConferencia["nombre_cliente_factura"]);
$nombreEmpresaFactura = generalUtils::escaparCadena($datoConferencia["nombre_empresa_factura"]);
$direccionFactura = generalUtils::escaparCadena($datoConferencia["direccion_factura"]);
$codigoPostalFactura = generalUtils::escaparCadena($datoConferencia["codigo_postal_factura"]);
$ciudadFactura = generalUtils::escaparCadena($datoConferencia["ciudad_factura"]);
$provinciaFactura = generalUtils::escaparCadena($datoConferencia["provincia_factura"]);
$paisFactura = generalUtils::escaparCadena($datoConferencia["pais_factura"]);
$emailClienteFactura = generalUtils::escaparCadena($datoConferencia["correo_electronico"]);
$firstName = generalUtils::escaparCadena($datoConferencia["nombre"]);

//Main email template
$plantillaMail = new XTemplate($absolutePath . "/../html/mail/mail_index_white_background.html");

//Subtemplate for email to  MET
$subPlantillaMail = new XTemplate($absolutePath . "/../html/mail/mail_conference_to_met.html");

//Subtemplate for email to user
$subPlantillaMailUser = new XTemplate($absolutePath . "/../html/mail/mail_conference_to_user.html");

/*
 * For each payment method (bank transfer or credit card):
 * store payment method name and movement type in variables
 * set paid status to unpaid (bank transfer) or paid (credit card)
 * and assign appropriate email intro text
 */
if ($metodoPago == INSCRIPCION_TIPO_PAGO_TRANSFERENCIA) {
    $metodoPagoDescripcion = INSCRIPCION_TIPO_PAGO_TRANSFERENCIA_DESCRIPTION;
    $tipoPagoMovimiento = MOVIMIENTO_TIPO_PAGO_TRANSFERENCIA;
    $pagado = 0;
    $subPlantillaMailUser->assign("EMAIL_TO_USER_INTRO_VALUE", $emailToUserTransferIntro);
} else if ($metodoPago == INSCRIPCION_TIPO_PAGO_PAYPAL) {
    $metodoPagoDescripcion = INSCRIPCION_TIPO_PAGO_PAYPAL_DESCRIPTION;
    $tipoPagoMovimiento = MOVIMIENTO_TIPO_PAGO_PAYPAL;
    $pagado = 1;
    $subPlantillaMailUser->assign("EMAIL_TO_USER_INTRO_VALUE", $emailToUserPaypalIntro);
}

//Assign subject line and title to placeholder in email template (same for MET and user)
$subPlantillaMail->assign("EMAIL_TO_MET", $emailToMet);
$subPlantillaMailUser->assign("EMAIL_TO_MET", $emailToMet);

//Store user details in variables and assign variables to template placeholders
$firstName = $datoConferencia["nombre"];
$nombre = "";
if ($datoConferencia["tratamiento"] != "") {
    $nombre .= $datoConferencia["tratamiento"] . " ";
}
$nombre .= $datoConferencia["nombre"] . " " . $datoConferencia["apellidos"];

$subPlantillaMail->assign("MAIL_INSCRIPTION_USER_FULL_NAME", $nombre);
$subPlantillaMailUser->assign("MAIL_INSCRIPTION_USER_FIRST_NAME", $firstName);
$subPlantillaMail->assign("MAIL_INSCRIPTION_TO_MET_EMAIL_VALUE", $datoConferencia["correo_electronico"]);

if ($datoConferencia["telefono"]) {
    $subPlantillaMail->assign("MAIL_INSCRIPTION_TO_MET_PHONE_VALUE", $datoConferencia["telefono"]);
    $subPlantillaMail->parse("contenido_principal.bloque_phone");
}

if ($datoConferencia["descripcion"]) {
    $subPlantillaMail->assign("MAIL_INSCRIPTION_TO_MET_SISTER_ASSOCIATION_VALUE", $datoConferencia["descripcion"]);
    $subPlantillaMail->parse("contenido_principal.bloque_association");
    $subPlantillaMailUser->assign("MAIL_INSCRIPTION_TO_MET_SISTER_ASSOCIATION_VALUE", $datoConferencia["descripcion"]);
    $subPlantillaMailUser->parse("contenido_principal.bloque_association");
}

if (!$datoConferencia["id_usuario_web"]) {
    $passRand = rand(1000000000, 9999999999);
    $passRand = dechex($passRand);
    $db->callProcedure("CALL ed_sp_save_conference_password($idInscripcion,'$passRand')");
    $subPlantillaMailUser->assign("MAIL_INSCRIPTION_NON_MEMBER_PASSWORD_VALUE", $passRand);
    $subPlantillaMailUser->parse("contenido_principal.bloque_password");
}

if ($datoConferencia["speaker"] == 1) {
    $subPlantillaMail->parse("contenido_principal.bloque_speaker");
    $subPlantillaMailUser->parse("contenido_principal.bloque_speaker");
}

$esDinnerOptout = false;
$dinnerOptoutDiscount = 0;
if ($datoConferencia["es_dinner"] == 1) {
    $subPlantillaMail->assign("MAIL_HAS_DINNER_VALUE", STATIC_GLOBAL_BUTTON_NO);
    $subPlantillaMailUser->assign("MAIL_HAS_DINNER_VALUE", STATIC_GLOBAL_BUTTON_NO);
    $subPlantillaMail->parse("contenido_principal.bloque_dinner");
    $subPlantillaMailUser->parse("contenido_principal.bloque_dinner");
    $esDinnerOptout = true;
    $dinnerOptoutDiscount = $priceDinnerOptoutDiscount;
} else {
    $subPlantillaMail->assign("MAIL_HAS_DINNER_VALUE", STATIC_GLOBAL_BUTTON_YES);
    $subPlantillaMailUser->assign("MAIL_HAS_DINNER_VALUE", STATIC_GLOBAL_BUTTON_YES);
    $subPlantillaMail->parse("contenido_principal.bloque_dinner");
    $subPlantillaMailUser->parse("contenido_principal.bloque_dinner");
}

if ($datoConferencia["email_permiso"] == 0) {
    $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_EMAIL_PERMISSION_VALUE", STATIC_GLOBAL_BUTTON_NO);
} else {
    $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_EMAIL_PERMISSION_VALUE", STATIC_GLOBAL_BUTTON_YES);
}

if ($datoConferencia["es_certificado"] == 1) {
    $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_CERTIFICATE_VALUE", STATIC_GLOBAL_BUTTON_YES);
} else {
    $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_CERTIFICATE_VALUE", STATIC_GLOBAL_BUTTON_NO);
}

$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_BADGE_VALUE", $datoConferencia["conference_badge"]);

//Get workshops and minisession signup info from database
$resultadoTallerListado = $db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_linea_obtener(" . $idInscripcion . "," . $idIdioma . ")");

require "load_format_date.inc.php";


//Count up workshop and minisession choices, titles and dates
$talleresNormales = 0;
$talleresMini = 0;
while ($datoTallerListado = $db->getData($resultadoTallerListado)) {
    $fechaActual = $datoTallerListado["fecha"];
    $fechaTaller = generalUtils::conversionFechaFormato($fechaActual, "-", "/");
    $mesTaller = explode("/", $fechaTaller);

    //Split date into array [year-month-day]
    $fechaTrozeada = explode("-", $fechaActual);

    if ($datoTallerListado["es_mini"] == 1) {
        $talleresMini++;
    } else {
        $talleresNormales++;
    }

    $fechaTimeStamp = mktime(0, 0, 0, $mesTaller[1], $mesTaller[0], $mesTaller[2]);
    $diaSemana = $vectorSemana[date("N", $fechaTimeStamp)];

    $fechaFormateada = $diaSemana . ", " . intval($fechaTrozeada[2]) . " " . $vectorMes[$fechaTrozeada[1]];

    $subPlantillaMail->assign("WORKSHOP_FECHA", $fechaFormateada);
    $subPlantillaMail->assign("WORKSHOP_NOMBRE", $datoTallerListado["nombre"]);

    //Para user
    $subPlantillaMailUser->assign("WORKSHOP_NOMBRE", $datoTallerListado["nombre"]);
    $subPlantillaMailUser->assign("WORKSHOP_FECHA", $fechaFormateada);


    $subPlantillaMail->parse("contenido_principal.item_workshop");
    $subPlantillaMailUser->parse("contenido_principal.item_workshop");
}

/*********** INICIO: extra **************/

/* Get conference extras from table ed_tb_inscripcion_conferencia_extra
*  only dinner guests and wine reception guests are relevant
*  but obsolete "extras" are left here as examples
*/
$resultadoConferenciaExtra = $db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_extra_obtener_concreta(" . $idInscripcion . ")");
$hayExcursion = false;
$esDinnerGuest = false;
$esWineReceptionGuest = false;
$dinnerGuestCost = 0;
$wineReceptionGuestCost = 0;
while ($datoConferenciaExtra = $db->getData($resultadoConferenciaExtra)) {
    $valor = $datoConferenciaExtra["valor"];
    switch ($datoConferenciaExtra["id_conferencia_extra"]) {
        case 1:
            if ($valor == 1) {
                $hayExcursion = true;
                $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_GUIDE_POBLET_VALUE", STATIC_GLOBAL_BUTTON_YES);
                $subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_GUIDE_POBLET_VALUE", STATIC_GLOBAL_BUTTON_YES);
            } else {
                $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_GUIDE_POBLET_VALUE", STATIC_GLOBAL_BUTTON_NO);
                $subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_GUIDE_POBLET_VALUE", STATIC_GLOBAL_BUTTON_NO);
            }

            break;
        case 2:
            if ($valor > 0) {
                $esDinnerGuest = true;
                $dinnerGuestCost = number_format(($priceDinnerGuest * $valor), 2);

                $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_DINNER_GUESTS_VALUE", $valor);
                $subPlantillaMail->parse("contenido_principal.bloque_dinner_guest");

                $subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_DINNER_GUESTS_VALUE", $valor);
                $subPlantillaMailUser->parse("contenido_principal.bloque_dinner_guest");
            }

            break;
        case 3:
            if ($valor == 1) {
                $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_PHOTO_PORTRAIT_VALUE", STATIC_GLOBAL_BUTTON_YES);
                $subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_PHOTO_PORTRAIT_VALUE", STATIC_GLOBAL_BUTTON_YES);
            } else {
                $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_PHOTO_PORTRAIT_VALUE", STATIC_GLOBAL_BUTTON_NO);
                $subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_PHOTO_PORTRAIT_VALUE", STATIC_GLOBAL_BUTTON_NO);
            }

            break;
        case 4:
            if ($valor == 1) {
                $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_MORNING_WALK_VALUE", STATIC_GLOBAL_BUTTON_YES);
                $subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_MORNING_WALK_VALUE", STATIC_GLOBAL_BUTTON_YES);
            } else {
                $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_MORNING_WALK_VALUE", STATIC_GLOBAL_BUTTON_NO);
                $subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_MORNING_WALK_VALUE", STATIC_GLOBAL_BUTTON_NO);
            }

            break;
        case 5:
            if ($valor == 1) {
                $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_WINE_VALUE", STATIC_GLOBAL_BUTTON_YES);
                $subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_WINE_VALUE", STATIC_GLOBAL_BUTTON_YES);
            } else {
                $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_WINE_VALUE", STATIC_GLOBAL_BUTTON_NO);
                $subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_WINE_VALUE", STATIC_GLOBAL_BUTTON_NO);
            }

            break;
        case 6:
            if ($valor == 1) {
                $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_MORNING_WALK_2_VALUE", STATIC_GLOBAL_BUTTON_YES);
                $subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_MORNING_WALK_2_VALUE", STATIC_GLOBAL_BUTTON_YES);
            } else {
                $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_MORNING_WALK_2_VALUE", STATIC_GLOBAL_BUTTON_NO);
                $subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_MORNING_WALK_2_VALUE", STATIC_GLOBAL_BUTTON_NO);
            }

            break;
        case 7:
            if ($valor > 0) {
                $esWineReceptionGuest = true;
                $wineReceptionGuestCost = number_format(($priceWineReceptionGuest * $valor), 2);

                $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_WINE_RECEPTION_GUESTS_VALUE", $valor);
                $subPlantillaMail->parse("contenido_principal.bloque_wine_reception_guest");

                $subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_WINE_RECEPTION_GUESTS_VALUE", $valor);
                $subPlantillaMailUser->parse("contenido_principal.bloque_wine_reception_guest");
            }

            break;
    }
}

/*********** FIN: extra **************/

//Assign payment method to placeholder in email templates
$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_PAYMENT_METHOD_VALUE", $metodoPagoDescripcion);
$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_PAYMENT_METHOD_VALUE", $metodoPagoDescripcion);


//Calculate workshop/minisession price
$esWorkshop = false;
$workshopAmount = 0;
if ($talleresNormales > 1) {
    $esWorkshop = true;
    $workshopAmount = $priceWorkshop * $talleresNormales;
} else if ($talleresNormales == 1) {
    $esWorkshop = true;
    $workshopAmount = $priceWorkshop;
    if ($talleresMini > 0) {
        $workshopAmount += $priceMinisession * $talleresMini;
    }
} else if ($talleresMini > 0) {
    $esWorkshop = true;
    $workshopAmount = $priceMinisession * $talleresMini;
}

$workshopAmount = number_format($workshopAmount, 2);

//Store total price actually paid (from database) in a variable
$totalPrice = $datoConferencia["importe_total"];

//Calculate basic conference fee paid by substracting extras from total and assign that value to a variable

$rawConferenceFee = $totalPrice - $workshopAmount - $dinnerGuestCost - $wineReceptionGuestCost + $dinnerOptoutDiscount;

if ($rawConferenceFee <= 0) {
    // Do something if the fee is zero or negative
	$basicConferenceFee = number_format($totalPrice, 2);
} else {

$basicConferenceFee = number_format($rawConferenceFee, 2);


// $basicConferenceFee = number_format(($totalPrice - $workshopAmount - $dinnerGuestCost - $wineReceptionGuestCost + // $dinnerOptoutDiscount), 2);

//Assign components of total price (basic fee + extras) to placeholders in email template
$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_FEE_VALUE", "&euro;" . $basicConferenceFee);
$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_FEE_VALUE", "&euro;" . $basicConferenceFee);

if ($esWorkshop) {
    $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_EXTRA_WORKSHOP_FEE_VALUE", "&euro;" . $workshopAmount);
    $subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_EXTRA_WORKSHOP_FEE_VALUE", "&euro;" . $workshopAmount);
    $subPlantillaMail->parse("contenido_principal.bloque_extra_workshop_fee");
    $subPlantillaMailUser->parse("contenido_principal.bloque_extra_workshop_fee");
}

if ($esDinnerGuest) {
    $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_DINNER_GUEST_COST_VALUE", "&euro;" . $dinnerGuestCost);
    $subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_DINNER_GUEST_COST_VALUE", "&euro;" . $dinnerGuestCost);
    $subPlantillaMail->parse("contenido_principal.bloque_dinner_guest_cost");
    $subPlantillaMailUser->parse("contenido_principal.bloque_dinner_guest_cost");
}

if ($esWineReceptionGuest) {
    $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_WINE_RECEPTION_GUEST_COST_VALUE", "&euro;" . $wineReceptionGuestCost);
    $subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_WINE_RECEPTION_GUEST_COST_VALUE", "&euro;" . $wineReceptionGuestCost);
    $subPlantillaMail->parse("contenido_principal.bloque_wine_reception_guest_cost");
    $subPlantillaMailUser->parse("contenido_principal.bloque_wine_reception_guest_cost");
}

if ($esDinnerOptout) {
    $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_DINNER_OPTOUT_COST_VALUE", "- &euro;" . $dinnerOptoutDiscount);
    $subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_DINNER_OPTOUT_COST_VALUE", "- &euro;" . $dinnerOptoutDiscount);
    $subPlantillaMail->parse("contenido_principal.bloque_dinner_optout_cost");
    $subPlantillaMailUser->parse("contenido_principal.bloque_dinner_optout_cost");
}

}

$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_AMOUNT_PAYABLE_VALUE", "&euro;" . $totalPrice);
$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_AMOUNT_PAYABLE_VALUE", "&euro;" . $totalPrice);

if ($datoConferencia["comentarios"]) {
    $subPlantillaMail->assign("MAIL_INSCRIPTION_TO_MET_COMMENTS_VALUE", $datoConferencia["comentarios"]);
    $subPlantillaMail->parse("contenido_principal.bloque_comment");
    $subPlantillaMailUser->assign("MAIL_INSCRIPTION_TO_MET_COMMENTS_VALUE", $datoConferencia["comentarios"]);
    $subPlantillaMailUser->parse("contenido_principal.bloque_comment");
}

//METMs are taxable
$nonTaxable = 0;

//Insert movement into ed_tb_movimiento
$resultadoMovimiento = $db->callProcedure("CALL ed_sp_web_movimiento_insertar(" . MOVIMIENTO_TIPO_ENTRADA . "," . $idConceptoMovimiento . "," . $nonTaxable . "," . $tipoPagoMovimiento . ",null,'" . generalUtils::escaparCadena($nombre) . "','" . date("Y-m-d") . "','" . STATIC_MOVEMENT_NEW_CONFERENCE_DESCRIPTION . "','" . $totalPrice . "'," . $pagado . ")");
$datoMovimiento = $db->getData($resultadoMovimiento);
$idMovimiento = $datoMovimiento["id_movimiento"];

//Add entry to ed_tb_movimiento_inscripcion_conferencia (linking registration and movement)
$db->callProcedure("CALL ed_sp_web_movimiento_inscripcion_conferencia_insertar(" . $idMovimiento . "," . $idInscripcion . ")");

//Insert processing fee (if any) in ed_tb_movimiento
if (isset($stripeFee)) {
    $resultadoMovimientoComision = $db->callProcedure("CALL ed_sp_web_movimiento_insertar(" . MOVIMIENTO_TIPO_SALIDA . "," . MOVIMIENTO_CONCEPTO_FEE_GENERAL_BANKING . "," . $nonTaxable . "," . $tipoPagoMovimiento . ",null,'" . STATIC_GLOBAL_PAYPAL_FEE . "','" . date("Y-m-d") . "','" . generalUtils::escaparCadena($nombrePersonaCompleto) . "','" . $stripeFee . "'," . $pagado . ")");
    $datoMovimientoComision = $db->getData($resultadoMovimientoComision);
    $idMovimientoComision = $datoMovimientoComision["id_movimiento"];

    //Add entry to ed_tb_movimiento_inscripcion_conferencia (linking registration and movement)
    $db->callProcedure("CALL ed_sp_web_movimiento_inscripcion_conferencia_insertar(" . $idMovimientoComision . "," . $idInscripcion . ")");

}

//Assign billing details to placeholders in email template
$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_BILLING_NAME_VALUE", $nombreClienteFactura);

if ($nombreEmpresaFactura != "") {
    $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_BILLING_COMPANY_NAME_VALUE", $nombreEmpresaFactura);
    $subPlantillaMail->parse("contenido_principal.bloque_invoice.bloque_invoice_company_name");
}
$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_BILLING_ADDRESS_VALUE", $direccionFactura);
$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_BILLING_ZIPCODE_VALUE", $codigoPostalFactura);
$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_BILLING_CITY_VALUE", $ciudadFactura);

if ($provinciaFactura != "") {
    $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_BILLING_BILLING_PROVINCE_VALUE", $provinciaFactura);
    $subPlantillaMail->parse("contenido_principal.bloque_invoice.bloque_invoice_province");
}

$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_BILLING_BILLING_COUNTRY_VALUE", $paisFactura);

$subPlantillaMail->parse("contenido_principal.bloque_invoice");

//Insert invoice into database
$resultadoFactura = $db->callProcedure("CALL ed_sp_web_factura_insertar('" . $nifFactura . "','" . $nombreClienteFactura . "','" . $nombreEmpresaFactura . "','" . $direccionFactura . "','" . $codigoPostalFactura . "','" . $ciudadFactura . "','" . $provinciaFactura . "','" . $paisFactura . "','" . $emailClienteFactura . "','" . $firstName . "')");

$datoFactura = $db->getData($resultadoFactura);
$idFactura = $datoFactura["id_factura"];

//Insert linea_factura into database (linking id_factura, id_movimiento and precio)
$db->callProcedure("CALL ed_sp_web_linea_factura_insertar(" . $idFactura . "," . $idMovimiento . ")");

//Assign email body and signoff text to templates (to MET and to user)
$subPlantillaMailUser->assign("EMAIL_TO_USER_BODY", $emailToUserBody);
$subPlantillaMailUser->assign("EMAIL_TO_USER_SIGNOFF", $emailToUserSignoff);

$subPlantillaMail->parse("contenido_principal");
$subPlantillaMailUser->parse("contenido_principal");

$mail->Body = $subPlantillaMail->text("contenido_principal");

//Add address and subject
$mail->AddAddress(STATIC_MAIL_TO_METM_REG_FORM);
$mail->FromName = STATIC_MAIL_FROM;
$mail->Subject = $emailToMet . ": " . $nombre;


//Send emails
if ($mail->Send()) {
    //Log email to MET (email type, recipients, subject and email body)
    $idUsuarioWebCorreo = "null";
    $idTipoCorreoElectronico = EMAIL_TYPE_CONFERENCE_FORM_TO_MET;
    $vectorDestinatario = Array();
    array_push($vectorDestinatario, STATIC_MAIL_TO_METM_REG_FORM);
    $asunto = $mail->Subject;
    $cuerpo = $mail->Body;
    require $absolutePath . "/load_log_email.inc.php";


    //Clear all recipients, then send email to user, with BCC copy to MET
    $mail->ClearAllRecipients();
    $mail->AddAddress($datoConferencia["correo_electronico"]);
    $mail->AddBCC("membership@metmeetings.org");
    $plantillaMail->assign("CONTENIDO", $subPlantillaMailUser->text("contenido_principal"));

    $plantillaMail->parse("contenido_principal");


    //Send email to user (bcc to MET)
    $mail->Subject = $emailToMet;
    $mail->Body = $plantillaMail->text("contenido_principal");
    $mail->Send();


    //Log email to user (email type, recipients, subject and email body)
    $idTipoCorreoElectronico = EMAIL_TYPE_CONFERENCE_FORM_TO_USER;
    $vectorDestinatario = Array();
    array_push($vectorDestinatario, $datoConferencia["correo_electronico"]);
    $asunto = $mail->Subject;
    $cuerpo = $mail->Body;
    require $absolutePath . "/load_log_email.inc.php";
}
