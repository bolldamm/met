<?php
/**
 *
 * Presentamos por pantalla este formulario
 * @author Edata S.L.
 * @copyright Edata S.L.
 * @version 1.0
 */


//Insert movement
if (count($_POST) > 0) {
    $idUsuarioWeb = $_SESSION["met_user"]["id"];
    $tipoMovimiento = 2;
    $nombre = generalUtils::escaparCadena($_POST["txtNombre"]);
    $concepto = $_POST["cmbSubConcepto"];
    $fecha = $_POST["txtFecha"];
    $contenido = generalUtils::escaparCadena($_POST["txtContenido"]);
    $tipoPago = $_POST["cmbTipoPago"];
    $importe = str_replace(",", ".", $_POST["txtImporte"]);
    $esPagado = 0;

/*
*    The regex in form_expense.html to validate the amount is as follows:
*    ^                         # Start of string
*     [0-9]+                   # Require one or more numbers
*           (                  # Begin optional group
*            [\.,]             # Point or comma; point must be escaped or it is treated as "any character"
*              [0-9]{1,2}      # One or two numbers
*                        )?    # End group--signify that it's optional with "?"
*                          $   # End of string
*/
  
  
    $resultMovimiento = $db->callProcedure("CALL ed_sp_web_movimiento_insertar(" . $tipoMovimiento . "," . $concepto . ",0," . $tipoPago . "," . $_SESSION["met_user"]["id"] . ",'" . $nombre . "','" . generalUtils::conversionFechaFormato($fecha) . "','" . $contenido . "','" . $importe . "'," . $esPagado . ")");
    $datoMovimiento = $db->getData($resultMovimiento);
    $idMovimiento = $datoMovimiento["id_movimiento"];

    //Send mail
    require "includes/load_mailer.inc.php";

    $mailPlantilla = new XTemplate("html/mail/mail_index.html");

    //Content mail
    $mailSubPlantilla = new XTemplate("html/mail/mail_expense.html");

    //Id movement
    $mailSubPlantilla->assign("MOVIMIENTO_ID", $idMovimiento);

    //Account for movement
    $resultadoConcepto = $db->callProcedure("CALL ed_sp_web_concepto_movimiento_obtener_concreto(" . $_POST["cmbConcepto"] . "," . $_SESSION["id_idioma"] . ")");
    $datoConcepto = $db->getData($resultadoConcepto);

    //Subaccount
    $resultadoSubConcepto = $db->callProcedure("CALL ed_sp_web_concepto_movimiento_obtener_concreto(" . $_POST["cmbSubConcepto"] . "," . $_SESSION["id_idioma"] . ")");
    $datoSubConcepto = $db->getData($resultadoSubConcepto);


    $mailSubPlantilla->assign("EXPENSE_FORM_NAME_VALUE", $nombre);
    $mailSubPlantilla->assign("EXPENSE_FORM_EMAIL_VALUE", $_SESSION["met_user"]["email"]);
    $mailSubPlantilla->assign("EXPENSE_FORM_DATE_INCURRED_VALUE", $fecha);
    $mailSubPlantilla->assign("EXPENSE_FORM_TYPE_EXPENSE_VALUE", $datoConcepto["nombre"]);
    $mailSubPlantilla->assign("EXPENSE_FORM_SUBTYPE_EXPENSE_VALUE", $datoSubConcepto["nombre"]);
    $mailSubPlantilla->assign("EXPENSE_FORM_AMOUNT_VALUE", number_format($importe, 2, ",", ""));
    $mailSubPlantilla->assign("EXPENSE_FORM_DESCRIPTION_VALUE", $contenido);

    $mailSubPlantilla->assign("MIEMBRO_URL", CURRENT_DOMAIN . "/easygestor/main_app.php?section=member&action=edit&id_miembro=" . $idUsuarioWeb);


    $mailSubPlantilla->parse("contenido_principal");

    //Exportamos subPlantilla a plantilla
    $mailPlantilla->assign("CONTENIDO", $mailSubPlantilla->text("contenido_principal"));

    $mailPlantilla->parse("contenido_principal");

    //Establecemos cuerpo del mensaje
    $mail->Body = $mailSubPlantilla->text("contenido_principal");


    /**
     *
     * Incluimos la configuracion del componente phpmailer
     *
     */
    $mail->AddAddress(STATIC_MAIL_TO_TREASURER);
    $mail->FromName = STATIC_MAIL_FROM;
    $mail->Subject = STATIC_SUBMIT_EXPENSE_MAIL_SUBJECT . ": " . $nombre;


    //Enviamos correo
    if ($mail->Send()) {
        /****** Guardamos el log del correo electronico ******/
        $idUsuarioWebCorreo = $_SESSION["met_user"]["id"];

        //Tipo correo electronico
        $idTipoCorreoElectronico = EMAIL_TYPE_EXPENSE_FORM;


        //Destinatario
        $vectorDestinatario = Array();
        array_push($vectorDestinatario, STATIC_MAIL_TO_TREASURER);

        //Asunto
        $asunto = $mail->Subject;
        $cuerpo = $mail->Body;

        $db->startTransaction();

        require "includes/load_log_email.inc.php";

        $db->endTransaction();
    }


    generalUtils::redirigir(CURRENT_DOMAIN . "/openbox.php?menu=" . $idMenu . "&c=1");
    //}
    //generalUtils::redirigir(CURRENT_DOMAIN."/openbox.php?menu=".$idMenu."&c=2");
}

if (isset($_GET["c"]) && is_numeric($_GET["c"])) {
    if ($_GET["c"] == 1) {
        $plantillaFormulario->assign("MENSAJE_ACCION_PERFIL", STATIC_EXPENSE_FORM_INTEREST_SEND_OK);
        $plantillaFormulario->assign("MENSAJE_ACCION_CLASS", "msgOK");
        $plantillaFormulario->assign("MENSAJE_ACCION_DISPLAY", "");
    } else if ($_GET["c"] == 2) {
        $plantillaFormulario->assign("MENSAJE_ACCION_PERFIL", STATIC_EXPENSE_FORM_INTEREST_SEND_KO);
        $plantillaFormulario->assign("MENSAJE_ACCION_CLASS", "msgKO");
        $plantillaFormulario->assign("MENSAJE_ACCION_DISPLAY", "");
    } else {
        $plantillaFormulario->assign("MENSAJE_ACCION_PERFIL", "");
        $plantillaFormulario->assign("MENSAJE_ACCION_CLASS", "msgKO");
        $plantillaFormulario->assign("MENSAJE_ACCION_DISPLAY", "display:none;");
    }

    //Hacemos que la pagina baje hacia la parte que le pertoca(donde se esta mostrando el mensaje)
    $plantilla->assign("ELEMENTO_ANCLAR", "anclaExito");
    $plantilla->parse("contenido_principal.bloque_ready.bloque_anclar_elemento");
}


//Nombre por defecto
$plantillaFormulario->assign("EXPENSE_FORM_NAME", $_SESSION["met_user"]["username"]);


//Expense account combo
//In both cases, set $opcionDefectoValor to "", so that form validation will work
//Class "required" is for style (red asterisk) and attribute "required" is for validation
$plantillaFormulario->assign("COMBO_CONCEPTO", generalUtils::construirCombo($db, "CALL ed_sp_web_concepto_movimiento_obtener_combo(null," . $_SESSION["id_idioma"] . ")", "cmbConcepto", "cmbConcepto", -1, "nombre", "id_concepto_movimiento", STATIC_EXPENSE_FORM_TYPE_EXPENSE, "", "onchange='obtenerComboSubConcepto(this)'", "class='form-control selected required' required"));

//Payment method combo
$plantillaFormulario->assign("COMBO_TIPO_PAGO", generalUtils::construirCombo($db, "CALL ed_sp_web_tipo_pago_movimiento_obtener_combo_expense_form(" . $_SESSION["id_idioma"] . ")", "cmbTipoPago", "cmbTipoPago", -1, "nombre", "id_tipo_pago_movimiento", STATIC_EXPENSE_FORM_DESIRED_PAYMENT, "", "class='form-control selected required' required"));

$plantilla->parse("contenido_principal.script_datetimepicker");
$plantilla->parse("contenido_principal.bloque_ready.datetimepicker");
$plantilla->parse("contenido_principal.validate_expense_form");
?>