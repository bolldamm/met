<?php
/**
 *
 * Edit movement and send emails (membership, workshop, conference)
 *
 */

//If the form has been submitted
if (count($_POST)) {
    //If hidden "Paid" variable is set
    if (isset($_POST["hdnPagado"])) {
        $pagado = $_POST["hdnPagado"];
    } else {
        $pagado = 0;
    }


    $idMovimiento = $_POST["hdnIdMovimiento"];

    //Start database transaction
    $db->startTransaction();

    //Assign content of "Description" and "Comment" fields to variables for later
    $descripcion = generalUtils::escaparCadena($_POST["txtaDescripcion"]);
    $comment = generalUtils::escaparCadena($_POST["txtaComment"]);

    //First, insert or update the movement with the values from the form
    $resultado = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_movimiento_editar(" . $idMovimiento . "," . $_POST["cmbTipo"] . "," . $_POST["cmbSubConcepto"] . "," . $_POST["hdnNonTaxable"] . "," . $_POST["cmbTipoPago"] . ",'" . generalUtils::escaparCadena($_POST["txtConceptoPersonalizado"]) . "','" . generalUtils::escaparCadena($_POST["txtPersona"]) . "','" . generalUtils::conversionFechaFormato($_POST["txtFecha"]) . "','" . $descripcion . "','" . $_POST["txtImporte"] . "'," . $pagado . ",'" . $comment . "')");


    /******** START: Membership confirmation email ********/

    if ($_POST["cmbConcepto"] == 34 && $pagado == 1) {
        //Get the details of the movement
        $resultadoMovimientoInscripcion = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_movimiento_inscripcion_obtener_concreta(" . $idMovimiento . ")");

        if ($db->getNumberRows($resultadoMovimientoInscripcion) > 0) {

            $datoMovimientoInscripcion = $db->getData($resultadoMovimientoInscripcion);


            //If email not yet sent, send email
            if ($datoMovimientoInscripcion["es_mail_enviado"] == 0) {
                $esMailEnviado = 1;
                $idInscripcion = $datoMovimientoInscripcion["id_inscripcion"];

                require "../includes/load_mailer.inc.php";

                $mail->FromName = STATIC_MAIL_FROM;
                $mail->Subject = STATIC_INSCRIPTIONS_EMAIL_SUBJECT;

                $plantilla = new XTemplate("../html/mail/mail_index.html");

                //Select email template (individual or institution) and format of recipient's name
                switch ($datoMovimientoInscripcion["id_modalidad_usuario_web"]) {
                    case MODALIDAD_USUARIO_INDIVIDUAL:
                        $subPlantillaMail = new XTemplate("../html/mail/mail_individual_member_payment_confirmed.html");
                        $usuarioNombreCompleto = $datoMovimientoInscripcion["nombre"] . " " . $datoMovimientoInscripcion["apellidos"];
                        $usuarioNombre = $datoMovimientoInscripcion["nombre"];
                        $subPlantillaMail->assign("USUARIO_NOMBRE_COMPLETO", $usuarioNombre);

                        $subPlantillaMail->parse("contenido_principal");

                        //Export email subtemplate to main template
                        $plantilla->assign("CONTENIDO", $subPlantillaMail->text("contenido_principal"));

                        $plantilla->parse("contenido_principal");

                        //Set email body
                        $mail->Body = $subPlantillaMail->text("contenido_principal");

                        //Set recipient
                        $mail->AddAddress($datoMovimientoInscripcion["correo_electronico"]);


                        //Send the email
                        if ($mail->Send()) {

                            /****** Log the email ******/
                            $idUsuarioWebCorreo = $datoMovimientoInscripcion["id_usuario_web"];

                            //Email type
                            $idTipoCorreoElectronico = EMAIL_TYPE_INSCRIPTION_ACTIVATED;


                            //Recipient
                            $vectorDestinatario = Array();
                            array_push($vectorDestinatario, $datoMovimientoInscripcion["correo_electronico"]);

                            //Subject
                            $asunto = STATIC_INSCRIPTIONS_EMAIL_SUBJECT;
                            $cuerpo = $mail->Body;

                            require "../includes/load_log_email.inc.php";

                            //Update registration to "email sent" and "paid"
                            $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_inscripcion_usuario_web_actualizar(" . $idInscripcion . ",1," . $esMailEnviado . ")");


                        }//end if

                        break;
                    case MODALIDAD_USUARIO_INSTITUTIONAL:
                        /*
                         * February 2019: No payment confirmation email to renewing institutions
                         *
                        $subPlantillaMail = new XTemplate("../html/mail/mail_institutional_member_payment_confirmed.html");
                        $usuarioNombreCompleto = $datoMovimientoInscripcion["nombre_representante"] . " " . $datoMovimientoInscripcion["apellidos_representante"];
                        $subPlantillaMail->assign("INSTITUCION_NOMBRE", $datoMovimientoInscripcion["institucion"]);
                        $subPlantillaMail->assign("USUARIO_NOMBRE_COMPLETO", $usuarioNombreCompleto);
                        */
                        break;
                }


            }//end if
        }//end if
        /******** END: Membership confirmation email ********/

        /******** START: Workshop registration email ********/

    } else if ($_POST["cmbSubConcepto"] == 55 && $pagado == 1) {

        //Miramos si la inscripcion estaba pagada y con el email de activacio enviada...
        $resultadoMovimientoInscripcion = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_movimiento_inscripcion_taller_obtener_concreta(" . $idMovimiento . "," . $_SESSION["user"]["language_id"] . ")");

        if ($db->getNumberRows($resultadoMovimientoInscripcion) > 0) {
            $datoMovimientoInscripcion = $db->getData($resultadoMovimientoInscripcion);


            //Si aun no se habia enviado el email..., debemos enviarlo, y poner que tanto la inscripcion ha sido pagada y que el email ha sido enviado...
            if ($datoMovimientoInscripcion["es_mail_enviado"] == 0) {
                $esMailEnviado = 1;
                $idInscripcion = $datoMovimientoInscripcion["id_inscripcion_taller"];

                require "../includes/load_mailer.inc.php";

                $mail->FromName = STATIC_MAIL_FROM;
                $mail->Subject = STATIC_MANAGE_MOVEMENT_WORKSHOP_REGISTER_EMAIL_SUBJECT;


                require "../includes/load_format_date.inc.php";

                $plantilla = new XTemplate("../html/mail/mail_index.html");

                //Mail to user
                $subPlantillaMail = new XTemplate("../html/mail/mail_workshop_to_user_payment_confirmed.html");

                $firstName = $datoMovimientoInscripcion["nombre"];
                $subPlantillaMail->assign("MAIL_INSCRIPTION_TO_USER_NAME_VALUE", $firstName);


                //Workshops
                $resultadoTallerListado = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_inscripcion_taller_linea_obtener(" . $idInscripcion . "," . $_SESSION["user"]["language_id"] . ")");

                //Taller listado
                $precio = 0;
                while ($datoTallerListado = $db->getData($resultadoTallerListado)) {
                    $subPlantillaMail->assign("WORKSHOP_NOMBRE", $datoTallerListado["nombre"]);


                    $fechaTaller = generalUtils::conversionFechaFormato($datoTallerListado["fecha"], "-", "/");
                    $mesTaller = explode("/", $fechaTaller);

                    //Proceso para obtener dia de la semana
                    $fechaTrozeada = explode("-", $datoTallerListado["fecha"]);
                    $subPlantillaMail->assign("WORKSHOP_FECHA", intval($fechaTrozeada[2]) . " " . $vectorMes[$fechaTrozeada[1]]);


                    $subPlantillaMail->parse("contenido_principal.item_workshop");
                }


                $subPlantillaMail->assign("USUARIO_NOMBRE_COMPLETO", $usuarioNombreCompleto);

                $subPlantillaMail->parse("contenido_principal");

                //Exportamos subPlantilla a plantilla
                $plantilla->assign("CONTENIDO", $subPlantillaMail->text("contenido_principal"));

                $plantilla->parse("contenido_principal");

                //Establecemos cuerpo del mensaje
                $mail->Body = $subPlantillaMail->text("contenido_principal");

                //Establecemos destinatario
                $mail->AddAddress($datoMovimientoInscripcion["correo_electronico"]);


                //Enviamos correo
                if ($mail->Send()) {

                    /****** Guardamos el log del correo electronico ******/
                    $idUsuarioWebCorreo = "null";

                    //Tipo correo electronico
                    $idTipoCorreoElectronico = EMAIL_TYPE_WORKSHOP_FORM_TO_USER_ACTIVATION;


                    //Destinatario
                    $vectorDestinatario = Array();
                    array_push($vectorDestinatario, $datoMovimientoInscripcion["correo_electronico"]);

                    //Asunto
                    $asunto = $mail->Subject;
                    $cuerpo = $mail->Body;

                    require "../includes/load_log_email.inc.php";

                    //Actualizamos tabla maestra de inscripcion taller
                    $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_inscripcion_taller_actualizar(" . $idInscripcion . ",1," . $esMailEnviado . ")");

                    //Actualizamos tabla hija de inscripcion taller
                    $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_inscripcion_taller_linea_actualizar(" . $idInscripcion . ",1)");

                }//end if
            }//end if
        }//end if numberRows

        /******** END: Workshop registration email ********/

        /******** START: Conference registration email ********/

    } else if ($_POST["cmbSubConcepto"] == 46 && $pagado == 1) {

        //Get the details of the movement
        $resultadoMovimientoInscripcion = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_movimiento_inscripcion_conferencia_obtener_concreta(" . $idMovimiento . "," . $_SESSION["user"]["language_id"] . ")");

        if ($db->getNumberRows($resultadoMovimientoInscripcion) > 0) {
            $datoMovimientoInscripcion = $db->getData($resultadoMovimientoInscripcion);
            $idConferencia = $datoMovimientoInscripcion["id_conferencia"];


            //If email not sent yet and payment type not credit card, set email as sent
            if ($datoMovimientoInscripcion["id_tipo_pago_movimiento"] != 10 && $datoMovimientoInscripcion["es_mail_enviado"] == 0) {
                $esMailEnviado = 1;
                $idInscripcion = $datoMovimientoInscripcion["id_inscripcion_conferencia"];

                require "../includes/load_mailer.inc.php";

                $mail->FromName = STATIC_MAIL_FROM;
                $mail->Subject = STATIC_MANAGE_MOVEMENT_CONFERENCE_REGISTER_EMAIL_SUBJECT;


                require "../includes/load_format_date.inc.php";

                $plantilla = new XTemplate("../html/mail/mail_index_white_background.html");


                //Email to user
                $subPlantillaMail = new XTemplate("../html/mail/mail_conference_to_user_payment_confirmed.html");

                $firstName = $datoMovimientoInscripcion["nombre"];
                $subPlantillaMail->assign("MAIL_INSCRIPTION_TO_USER_NAME_VALUE", $firstName);

                $resultadoConferencia = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_conferencia_obtener_concreta(" . $idConferencia . "," . $_SESSION["user"]["language_id"] . ")");
                if ($db->getNumberRows($resultadoConferencia) > 0) {
                    $datoConferencia = $db->getData($resultadoConferencia);
                    $emailText = $datoConferencia["email_to_user_payment_confirmed"];
                    $subPlantillaMail->assign("EMAIL_TO_USER_PAYMENT_CONFIRMED", $emailText);
                }

                $subPlantillaMail->assign("USUARIO_NOMBRE_COMPLETO", $usuarioNombreCompleto);

                $subPlantillaMail->parse("contenido_principal");

                //Exportamos subPlantilla a plantilla
                $plantilla->assign("CONTENIDO", $subPlantillaMail->text("contenido_principal"));

                $plantilla->parse("contenido_principal");

                //Establecemos cuerpo del mensaje
                $mail->Body = $subPlantillaMail->text("contenido_principal");

                //Establecemos destinatario
                $mail->AddAddress($datoMovimientoInscripcion["correo_electronico"]);


                //Enviamos correo
                if ($mail->Send()) {

                    /****** Guardamos el log del correo electronico ******/
                    $idUsuarioWebCorreo = "null";

                    //Tipo correo electronico
                    $idTipoCorreoElectronico = EMAIL_TYPE_CONFERENCE_FORM_TO_USER_ACTIVATION;


                    //Destinatario
                    $vectorDestinatario = Array();
                    array_push($vectorDestinatario, $datoMovimientoInscripcion["correo_electronico"]);

                    //Asunto
                    $asunto = $mail->Subject;
                    $cuerpo = $mail->Body;

                    require "../includes/load_log_email.inc.php";


                    $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_inscripcion_conferencia_actualizar(" . $idInscripcion . ",1," . $esMailEnviado . ")");


                }//end if


            }//end if
        }//end if number rows
    }//end if

    /******** END: Conference registration email ********/

    //Close database transaccion
    $db->endTransaction();

    //Return to main list of movements
    if ($_POST["hdnVolver"] == 0) {
        $referer = isset($_POST['referer']) ? $_POST['referer'] : "main_app.php?section=movement&action=view";
        generalUtils::redirigir($referer);
    } else {
        generalUtils::redirigir("main_app.php?section=movement&action=edit&id_movimiento=" . $_GET["id_movimiento"]);
    }
}


if (!isset($_GET["id_movimiento"]) || !is_numeric($_GET["id_movimiento"])) {
    generalUtils::redirigir("main_app.php?section=movement&action=view");
}
//Plantilla principal
$plantilla = new XTemplate("html/principal.html");

//Plantilla secundaria
$subPlantilla = new XTemplate("html/sections/movement/manage_movement.html");

//Sacamos la informacion del menu en cada idioma
$resultadoMovimiento = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_movimiento_obtener_concreta(" . $_GET["id_movimiento"] . "," . $_SESSION["user"]["language_id"] . ")");
$datoMovimiento = $db->getData($resultadoMovimiento);
$idTipo = $datoMovimiento["id_tipo_movimiento"];
$idConceptoPadre = $datoMovimiento["id_concepto_movimiento_padre"];
$idConcepto = $datoMovimiento["id_concepto_movimiento"];
$idTipoPago = $datoMovimiento["id_tipo_pago_movimiento"];

//REFERER URL
$subPlantilla->assign('HTTP_REFERER', $_SERVER['HTTP_REFERER']);

//Combo tipo
$subPlantilla->assign("COMBO_TIPO", generalUtils::construirCombo($db, "CALL " . OBJECT_DB_ACRONYM . "_sp_tipo_movimiento_obtener_combo(" . $_SESSION["user"]["language_id"] . ")", "cmbTipo", "cmbTipo", $idTipo, "nombre", "id_tipo_movimiento", "", 0, ""));

//Combo concepto
$subPlantilla->assign("COMBO_CONCEPTO", generalUtils::construirCombo($db, "CALL " . OBJECT_DB_ACRONYM . "_sp_concepto_movimiento_obtener_combo(null," . $_SESSION["user"]["language_id"] . ")", "cmbConcepto", "cmbConcepto", $idConceptoPadre, "nombre", "id_concepto_movimiento", STATIC_GLOBAL_COMBO_DEFAULT, 0, "onchange='obtenerComboSubConcepto(this)'"));

//Combo subconcepto
$subPlantilla->assign("COMBO_SUBCONCEPTO", generalUtils::construirCombo($db, "CALL " . OBJECT_DB_ACRONYM . "_sp_concepto_movimiento_obtener_combo(" . $idConceptoPadre . "," . $_SESSION["user"]["language_id"] . ")", "cmbSubConcepto", "cmbSubConcepto", $idConcepto, "nombre", "id_concepto_movimiento", STATIC_GLOBAL_COMBO_DEFAULT, 0, ""));

//Combo tipo pago
$subPlantilla->assign("COMBO_TIPO_PAGO", generalUtils::construirCombo($db, "CALL " . OBJECT_DB_ACRONYM . "_sp_tipo_pago_movimiento_obtener_combo(" . $_SESSION["user"]["language_id"] . ")", "cmbTipoPago", "cmbTipoPago", $idTipoPago, "nombre", "id_tipo_pago_movimiento", STATIC_GLOBAL_COMBO_DEFAULT, 0, ""));


//La primera vez, miramos si la noticia esta activo o no
if ($datoMovimiento["es_pagado"] == 1) {
    $subPlantilla->assign("PAGADO_CLASE", "checked");
} else {
    $subPlantilla->assign("PAGADO_CLASE", "unChecked");
}

//<<<07/12/2016>>>
if ($datoMovimiento["non_taxable"] == 1) {
    $subPlantilla->assign("TAXABLE_CLASE", "checked");
} else {
    $subPlantilla->assign("TAXABLE_CLASE", "unChecked");
}
$subPlantilla->assign("MOVIMIENTO_TAXABLE", $datoMovimiento["non_taxable"]);


$subPlantilla->assign("MOVIMIENTO_CONCEPTO_PERSONALIZADO", $datoMovimiento["concepto_personalizado"]);
$subPlantilla->assign("MOVIMIENTO_PERSONA", $datoMovimiento["nombre_persona"]);
$subPlantilla->assign("MOVIMIENTO_IMPORTE", $datoMovimiento["importe"]);
$subPlantilla->assign("MOVIMIENTO_PAGADO", $datoMovimiento["es_pagado"]);
$subPlantilla->assign("MOVIMIENTO_FECHA", generalUtils::conversionFechaFormato($datoMovimiento["fecha_movimiento"]));
$subPlantilla->assign("MOVIMIENTO_INSCRIPCION", $_GET["id_movimiento"]);

$subPlantilla->assign("MOVIMIENTO_COMMENT", $datoMovimiento["comments"]);
$subPlantilla->assign("MOVIMIENTO_DESCRIPCION", $datoMovimiento["mas_informacion"]);


$plantilla->assign("TEXTAREA_ID", "txtaDescripcion");
$plantilla->assign("TEXTAREA_TOOLBAR", "Minimo");
$plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");

$plantilla->parse("contenido_principal.carga_inicial.editor_finder");


$plantilla->parse("contenido_principal.carga_inicial.editor_finder");

//Migas de pan
$vectorMigas[0]["url"] = STATIC_BREADCUMB_INICIO_LINK;
$vectorMigas[0]["texto"] = STATIC_BREADCUMB_INICIO_TEXT;
$vectorMigas[1]["url"] = STATIC_BREADCUMB_MOVEMENT_VIEW_MOVEMENT_LINK;
$vectorMigas[1]["texto"] = STATIC_BREADCUMB_MOVEMENT_VIEW_MOVEMENT_TEXT;
$vectorMigas[2]["url"] = STATIC_BREADCUMB_MOVEMENT_EDIT_MOVEMENT_LINK . "&id_movimiento=" . $_GET["id_movimiento"];
$vectorMigas[2]["texto"] = $datoMovimiento["concepto"];


require "includes/load_breadcumb.inc.php";


//Parametros hidden formulario
$subPlantilla->assign("ID_MOVIMIENTO", $_GET["id_movimiento"]);
$subPlantilla->assign("ACTION", "edit");

//Informacion del usuario
require "includes/load_information_user.inc.php";

$subPlantilla->parse("contenido_principal.item_button_close");

//Date pickers
$plantilla->assign("INPUT_ID", "txtFecha");
$plantilla->parse("contenido_principal.carga_inicial.autoload_datepicker");

//Incluimos proceso boton factura
$subPlantilla->parse("contenido_principal.boton_factura");


//Incluimos script editor
$plantilla->parse("contenido_principal.editor_script");

//Incluimos proceso onload
$plantilla->parse("contenido_principal.carga_inicial");


//Submenu
$subPlantilla->parse("contenido_principal.item_submenu");


//Contruimos plantilla secundaria
$subPlantilla->parse("contenido_principal");

//Exportamos plantilla secundaria a la plantilla principal
$plantilla->assign("CONTENIDO", $subPlantilla->text("contenido_principal"));

//Construimos plantilla principal
$plantilla->parse("contenido_principal");

//Mostramos plantilla principal por pantalla
$plantilla->out("contenido_principal");
?>