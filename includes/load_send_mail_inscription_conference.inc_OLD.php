<?php

$absolutePath = dirname(__FILE__);


//Clase phpmailer
require $absolutePath . "/load_mailer.inc.php";

require $absolutePath . "/load_format_date.inc.php";


//El dia que haya multidioma ya lo tocaremos(ingles a saco, es por paypal temas)
$idIdioma = 3;
//Resultado workshop
$resultadoTallerConferencia = $db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_obtener_concreta(" . $idInscripcion . "," . $idIdioma . ")");
$datoTallerConferencia = $db->getData($resultadoTallerConferencia);

$esFactura = 1;
//$esFactura=$datoTallerConferencia["es_factura"];
$importeTotal = $datoTallerConferencia["importe_total"];

//Billing information
$nifFactura = generalUtils::escaparCadena($datoTallerConferencia["nif_cliente_factura"]);
$nombreClienteFactura = generalUtils::escaparCadena($datoTallerConferencia["nombre_cliente_factura"]);
$nombreEmpresaFactura = generalUtils::escaparCadena($datoTallerConferencia["nombre_empresa_factura"]);
$direccionFactura = generalUtils::escaparCadena($datoTallerConferencia["direccion_factura"]);
$codigoPostalFactura = generalUtils::escaparCadena($datoTallerConferencia["codigo_postal_factura"]);
$ciudadFactura = generalUtils::escaparCadena($datoTallerConferencia["ciudad_factura"]);
$provinciaFactura = generalUtils::escaparCadena($datoTallerConferencia["provincia_factura"]);
$paisFactura = generalUtils::escaparCadena($datoTallerConferencia["pais_factura"]);
$emailClienteFactura = generalUtils::escaparCadena($datoTallerConferencia["correo_electronico"]);
$firstName = generalUtils::escaparCadena($datoTallerConferencia["nombre"]);

//Plantilla principal
$plantillaMail = new XTemplate($absolutePath . "/../html/mail/mail_index.html");

//Subplantilla  MET
$subPlantillaMail = new XTemplate($absolutePath . "/../html/mail/mail_conference_to_met.html");

//Subplantilla  USER
$subPlantillaMailUser = new XTemplate($absolutePath . "/../html/mail/mail_conference_to_user.html");

if ($metodoPago == INSCRIPCION_TIPO_PAGO_TRANSFERENCIA) {
    $metodoPagoDescripcion = INSCRIPCION_TIPO_PAGO_TRANSFERENCIA_DESCRIPTION;
    $tipoPagoMovimiento = MOVIMIENTO_TIPO_PAGO_TRANSFERENCIA;
    $pagado = 0;

    $subPlantillaMailUser->assign("MAIL_INSCRIPTION_TO_USER_BODY_VALUE", STATIC_MAIL_INSCRIPCION_CONFERENCE_TO_USER_BANK_TRANSFER);

} else if ($metodoPago == INSCRIPCION_TIPO_PAGO_PAYPAL) {
    $metodoPagoDescripcion = INSCRIPCION_TIPO_PAGO_PAYPAL_DESCRIPTION;
    $tipoPagoMovimiento = MOVIMIENTO_TIPO_PAGO_PAYPAL;
    $pagado = 1;

    $subPlantillaMailUser->assign("MAIL_INSCRIPTION_TO_USER_BODY_VALUE", STATIC_MAIL_INSCRIPCION_CONFERENCE_TO_USER_PAYPAL);
}


$nombre = "";
if ($datoTallerConferencia["tratamiento"] != "") {
    $nombre .= $datoTallerConferencia["tratamiento"] . " ";
}
$nombre .= $datoTallerConferencia["nombre"] . " " . $datoTallerConferencia["apellidos"];

$subPlantillaMail->assign("MAIL_INSCRIPTION_TO_MET_NAME_VALUE", $nombre);
$subPlantillaMailUser->assign("MAIL_INSCRIPTION_TO_USER_NAME_VALUE", $nombre);
$subPlantillaMail->assign("MAIL_INSCRIPTION_TO_MET_EMAIL_VALUE", $datoTallerConferencia["correo_electronico"]);

if ($datoTallerConferencia["telefono"]) {
    $subPlantillaMail->assign("MAIL_INSCRIPTION_TO_MET_PHONE_VALUE", $datoTallerConferencia["telefono"]);
    $subPlantillaMail->parse("contenido_principal.bloque_phone");
}

if ($datoTallerConferencia["descripcion"]) {
    $subPlantillaMail->assign("MAIL_INSCRIPTION_TO_MET_SISTER_ASSOCIATION_VALUE", $datoTallerConferencia["descripcion"]);
    $subPlantillaMail->parse("contenido_principal.bloque_association");
    $subPlantillaMailUser->assign("MAIL_INSCRIPTION_TO_MET_SISTER_ASSOCIATION_VALUE", $datoTallerConferencia["descripcion"]);
    $subPlantillaMailUser->parse("contenido_principal.bloque_association");
}

if ($datoTallerConferencia["speaker"] == 1) {
    $subPlantillaMail->parse("contenido_principal.bloque_speaker");
    $subPlantillaMailUser->parse("contenido_principal.bloque_speaker");
}

if ($datoTallerConferencia["es_dinner"] == 1) {
    $subPlantillaMail->assign("MAIL_HAS_DINNER_VALUE", STATIC_GLOBAL_BUTTON_NO);
    $subPlantillaMailUser->assign("MAIL_HAS_DINNER_VALUE", STATIC_GLOBAL_BUTTON_NO);
    $subPlantillaMail->parse("contenido_principal.bloque_dinner");
    $subPlantillaMailUser->parse("contenido_principal.bloque_dinner");
} else {
    $subPlantillaMail->assign("MAIL_HAS_DINNER_VALUE", STATIC_GLOBAL_BUTTON_YES);
    $subPlantillaMailUser->assign("MAIL_HAS_DINNER_VALUE", STATIC_GLOBAL_BUTTON_YES);
    $subPlantillaMail->parse("contenido_principal.bloque_dinner");
    $subPlantillaMailUser->parse("contenido_principal.bloque_dinner");
}

if ($datoTallerConferencia["email_permiso"] == 1) {
    $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_EMAIL_PERMISSION_VALUE", STATIC_GLOBAL_BUTTON_NO);

} else {
    $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_EMAIL_PERMISSION_VALUE", STATIC_GLOBAL_BUTTON_YES);
}

if ($datoTallerConferencia["es_certificado"] == 1) {
    $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_CERTIFICATE_VALUE", STATIC_GLOBAL_BUTTON_YES);
} else {
    $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_CERTIFICATE_VALUE", STATIC_GLOBAL_BUTTON_NO);
}


$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_BADGE_VALUE", $datoTallerConferencia["conference_badge"]);

//talleres
$resultadoTallerListado = $db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_linea_obtener(" . $idInscripcion . "," . $idIdioma . ")");

require "load_format_date.inc.php";


//Taller listado
$precio = 0;
$talleresNormales = 0;
$talleresMini = 0;
while ($datoTallerListado = $db->getData($resultadoTallerListado)) {
    $fechaActual = $datoTallerListado["fecha"];
    $fechaTaller = generalUtils::conversionFechaFormato($fechaActual, "-", "/");
    $mesTaller = explode("/", $fechaTaller);

    //Proceso para obtener dia de la semana
    $fechaTrozeada = explode("-", $fechaActual);

    if ($datoTallerListado["es_mini"] == 1) {
        $talleresMini++;
    } else {
        $talleresNormales++;
    }//end else

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
    $precio += $datoTallerListado["importe"];
}


/*********** INICIO: extra **************/

$resultadoConferenciaExtra = $db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_extra_obtener_concreta(" . $idInscripcion . ")");
$hayExcursion = false;
$dinnerGuestCost = 0;
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

                $dinnerGuestCost = 30 * $valor;

                $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_GUEST_VALUE", $valor);
                $subPlantillaMail->parse("contenido_principal.bloque_guest");

                $subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_GUEST_VALUE", $valor);
                $subPlantillaMailUser->parse("contenido_principal.bloque_guest");

                $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_GUEST_COST_VALUE", "&euro;" . $dinnerGuestCost);
                $subPlantillaMail->parse("contenido_principal.bloque_dinner_guest_cost");

                $subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_GUEST_COST_VALUE", "&euro;" . $dinnerGuestCost);
                $subPlantillaMailUser->parse("contenido_principal.bloque_dinner_guest_cost");

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
    }
}

/*********** FIN: extra **************/

$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_PAYMENT_METHOD_VALUE", $metodoPagoDescripcion);
$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_PAYMENT_METHOD_VALUE", $metodoPagoDescripcion);

$precioTotal = $datoTallerConferencia["importe_total"];

// Deduct cost of dinner guests from conference fee displayed in email
$datoTallerConferencia["importe_total"] -= $dinnerGuestCost;


$esExtra = false;
if ($talleresNormales == 2) {
    $importeExtra = 36;
    $esExtra = true;
} else if ($talleresNormales == 1) {
    if ($talleresMini == 1) {
        $importeExtra = 18;
        $esExtra = true;
    }//end if
} else if ($talleresMini == 3) {
    $importeExtra = 18;
    $esExtra = true;
} else if ($talleresMini == 4) {
    $importeExtra = 36;
    $esExtra = true;
}//end else


if ($esExtra) {
    $subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_EXTRA_FEE_VALUE", "&euro;" . $importeExtra);
    $subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_EXTRA_FEE_VALUE", "&euro;" . $importeExtra);
    $datoTallerConferencia["importe_total"] -= $importeExtra;
    $subPlantillaMail->parse("contenido_principal.bloque_extra_workshop");
    $subPlantillaMailUser->parse("contenido_principal.bloque_extra_workshop");
}

$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_FEE_VALUE", "&euro;" . $datoTallerConferencia["importe_total"]);
$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_AMOUNT_PAYABLE_VALUE", "&euro;" . $precioTotal);


$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_FEE_VALUE", "&euro;" . $datoTallerConferencia["importe_total"]);
$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_AMOUNT_PAYABLE_VALUE", "&euro;" . $precioTotal);


if ($datoTallerConferencia["comentarios"]) {
    $subPlantillaMail->assign("MAIL_INSCRIPTION_TO_MET_COMMENTS_VALUE", $datoTallerConferencia["comentarios"]);
    $subPlantillaMail->parse("contenido_principal.bloque_comment");


    $subPlantillaMailUser->assign("MAIL_INSCRIPTION_TO_MET_COMMENTS_VALUE", $datoTallerConferencia["comentarios"]);
    $subPlantillaMailUser->parse("contenido_principal.bloque_comment");
}


//Insertamos un movimiento si estamos haciendo una nueva reserva
$resultadoMovimiento = $db->callProcedure("CALL ed_sp_web_movimiento_insertar(" . MOVIMIENTO_TIPO_ENTRADA . "," . $idConceptoMovimiento . "," . $tipoPagoMovimiento . ",null,'" . generalUtils::escaparCadena($nombre) . "','" . date("Y-m-d") . "','" . STATIC_MOVEMENT_NEW_CONFERENCE_DESCRIPTION . "','" . $importeTotal . "'," . $pagado . ")");
$datoMovimiento = $db->getData($resultadoMovimiento);
$idMovimiento = $datoMovimiento["id_movimiento"];

//Insertamos movimiento-inscripcion
$db->callProcedure("CALL ed_sp_web_movimiento_inscripcion_conferencia_insertar(" . $idMovimiento . "," . $idInscripcion . ")");


if (isset($comision)) {
    $resultadoMovimientoComision = $db->callProcedure("CALL ed_sp_web_movimiento_insertar(" . MOVIMIENTO_TIPO_SALIDA . "," . MOVIMIENTO_CONCEPTO_FEE_GENERAL_BANKING . "," . $tipoPagoMovimiento . ",null,'" . STATIC_GLOBAL_PAYPAL_FEE . "','" . date("Y-m-d") . "','" . generalUtils::escaparCadena($nombrePersonaCompleto) . "','" . $comision . "'," . $pagado . ")");
    $datoMovimientoComision = $db->getData($resultadoMovimientoComision);
    $idMovimientoComision = $datoMovimientoComision["id_movimiento"];

    //Insertamos movimiento-inscripcion
    $db->callProcedure("CALL ed_sp_web_movimiento_inscripcion_conferencia_insertar(" . $idMovimientoComision . "," . $idInscripcion . ")");

}


// Commented out by Stephen because invoice is required always
//if($esFactura==1){
$subPlantillaMail->assign("MAIL_INSCRIPCION_CONFERENCE_BILLING_NAME_VALUE", $nombreClienteFactura);
//$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_INVOICE_REQUIRED_TO_USER_VALUE",STATIC_GLOBAL_BUTTON_YES);

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


//Ponemos factura en el e-mail
$subPlantillaMail->parse("contenido_principal.bloque_invoice");
//}else{
//	$subPlantillaMailUser->assign("MAIL_INSCRIPCION_CONFERENCE_INVOICE_REQUIRED_TO_USER_VALUE",STATIC_GLOBAL_BUTTON_NO);
//}

//Insertamos factura

$resultadoFactura = $db->callProcedure("CALL ed_sp_web_factura_insertar('" . $nifFactura . "','" . $nombreClienteFactura . "','" . $nombreEmpresaFactura . "','" . $direccionFactura . "','" . $codigoPostalFactura . "','" . $ciudadFactura . "','" . $provinciaFactura . "','" . $paisFactura . "','" . $emailClienteFactura . "','" . $firstName . "')");

$datoFactura = $db->getData($resultadoFactura);
$idFactura = $datoFactura["id_factura"];

//Insertamos linea factura
$db->callProcedure("CALL ed_sp_web_linea_factura_insertar(" . $idFactura . "," . $idMovimiento . ")");


$subPlantillaMail->parse("contenido_principal");
$subPlantillaMailUser->parse("contenido_principal");
//Establecemos cuerpo del mensaje


//Primero mail a MET

$mail->Body = $subPlantillaMail->text("contenido_principal");

/**
 *
 * Incluimos la configuracion del componente phpmailer
 *
 */

$mail->AddAddress(STATIC_MAIL_TO_METM_REG_FORM);


$mail->FromName = STATIC_MAIL_FROM;
$mail->Subject = STATIC_MAIL_INSCRIPTION_CONFERENCE_SUBJECT . ": " . $nombre;


//Enviamos correo
if ($mail->Send()) {
    /****** Guardamos el log del correo electronico ******/
    $idUsuarioWebCorreo = "null";

    //Tipo correo electronico
    $idTipoCorreoElectronico = EMAIL_TYPE_CONFERENCE_FORM_TO_MET;


    //Destinatario
    $vectorDestinatario = Array();

    array_push($vectorDestinatario, STATIC_MAIL_TO_METM_REG_FORM);


    //Asunto
    $asunto = $mail->Subject;
    $cuerpo = $mail->Body;

    require $absolutePath . "/load_log_email.inc.php";


    //Pasemos al email que se le envia al usuario

    //Mail al usuario
    $mail->ClearAllRecipients();
    $mail->AddAddress($datoTallerConferencia["correo_electronico"]);
    $plantillaMail->assign("CONTENIDO", $subPlantillaMailUser->text("contenido_principal"));

    $plantillaMail->parse("contenido_principal");

    $mail->Subject = STATIC_MAIL_INSCRIPTION_CONFERENCE_SUBJECT;
    $mail->Body = $plantillaMail->text("contenido_principal");
    $mail->Send();

    //Tipo correo electronico
    $idTipoCorreoElectronico = EMAIL_TYPE_CONFERENCE_FORM_TO_USER;

    $vectorDestinatario = Array();
    array_push($vectorDestinatario, $datoTallerConferencia["correo_electronico"]);

    //Asunto
    $asunto = $mail->Subject;
    $cuerpo = $mail->Body;

    require $absolutePath . "/load_log_email.inc.php";
}
?>