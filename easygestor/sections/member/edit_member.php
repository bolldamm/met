<?php

/*
 * Edit an existing user
 * Send a membership confirmation email if registration is marked paid and email has not yet been sent
 */
//If the form has been submitted
if (count($_POST)) {
    $idUsuarioWeb = $_POST["hdnIdMiembro"];
    //Check that email isn't already in use
    $resultado = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_usuario_web_existe_registrado(" . $idUsuarioWeb . ",'" . $_POST["txtEmail"] . "')");

    //If no duplicate email, edit member and associated registrations
    if ($db->getNumberRows($resultado) == 0) {

        if (isset($_POST["hdnPublico"])) {
            $publico = $_POST["hdnPublico"];
        } else {
            $publico = 0;
        }

        //Get user mode (individual or institution) and user type
        $idInstitucion = "null";
        $idModalidadUsuario = $_POST["hdnIdModalidadUsuario"];
        if ($idModalidadUsuario == MODALIDAD_USUARIO_INDIVIDUAL) {
            $idTipoUsuario = $_POST["cmbTipoMiembro"];
        } else {
            $idTipoUsuario = "null";
        }

        //Start database transaction
        $db->startTransaction();

        //Insert generic user data
        $resultadoUsuarioWeb = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_usuario_web_editar(" . $idUsuarioWeb . "," . $idTipoUsuario . ",'" . $_POST["txtEmail"] . "','" . generalUtils::escaparCadena($_POST["txtPassword"]) . "','" . generalUtils::escaparCadena($_POST["txtOtrasActividades"]) . "','" . generalUtils::escaparCadena($_POST["txtOtrasDescripciones"]) . "','" . generalUtils::escaparCadena($_POST["txtOtrasPublicaciones"]) . "','" . generalUtils::escaparCadena($_POST["txtWeb"]) . "'," . $publico . ",'" . generalUtils::escaparCadena($_POST["txtNif"]) . "','" . generalUtils::escaparCadena($_POST["txtNombreCliente"]) . "','" . generalUtils::escaparCadena($_POST["txtNombreEmpresa"]) . "','" . generalUtils::escaparCadena($_POST["txtDireccion"]) . "','" . generalUtils::escaparCadena($_POST["txtCodigoPostal"]) . "','" . generalUtils::escaparCadena($_POST["txtCiudad"]) . "','" . generalUtils::escaparCadena($_POST["txtProvincia"]) . "','" . generalUtils::escaparCadena($_POST["txtPais"]) . "','" . generalUtils::escaparCadena($_POST["txtObservaciones"]) . "')");


        //If user mode is Institution, set variables for insertion in database
        if ($idModalidadUsuario == MODALIDAD_USUARIO_INSTITUTIONAL) {
            if (isset($_POST["hdnActivoWeb"])) {
                $activoWeb = $_POST["hdnActivoWeb"];
            } else {
                $activoWeb = 0;
            }

            if ($_POST["cmbInstitucionPais"] == 0) {
                $_POST["cmbInstitucionPais"] = "null";
            }

            if ($_POST["cmbInstitucionTitulo"] == 0) {
                $_POST["cmbInstitucionTitulo"] = "null";
            }

            $importe = PRECIO_MODALIDAD_USUARIO_INSTITUTIONAL;

            //Update institutional member
            $resultadoUsuarioWebInstitucion = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_usuario_web_institucion_editar(" . $idUsuarioWeb . "," . $_POST["cmbInstitucionTitulo"] . "," . $_POST["cmbInstitucionPais"] . ",'" . generalUtils::escaparCadena($_POST["txtInstitucionNombreInstitucion"]) . "','" . generalUtils::escaparCadena($_POST["txtInstitucionDepartamento"]) . "','" . generalUtils::escaparCadena($_POST["txtInstitucionDireccion1"]) . "','" . generalUtils::escaparCadena($_POST["txtInstitucionDireccion2"]) . "','" . generalUtils::escaparCadena($_POST["txtInstitucionCp"]) . "','" . generalUtils::escaparCadena($_POST["txtInstitucionCiudad"]) . "','" . generalUtils::escaparCadena($_POST["txtInstitucionProvincia"]) . "','" . generalUtils::escaparCadena($_POST["txtInstitucionTelefono"]) . "','" . generalUtils::escaparCadena($_POST["txtInstitucionFax"]) . "','" . generalUtils::escaparCadena($_POST["txtInstitucionEmail"]) . "','" . generalUtils::escaparCadena($_POST["txtInstitucionNombreRepresentante"]) . "','" . generalUtils::escaparCadena($_POST["txtInstitucionApellidosRepresentante"]) . "','','" . generalUtils::escaparCadena($_POST["txtInstitucionEmailUsuarioRepresentante"]) . "','" . generalUtils::escaparCadena($_POST["txtInstitucionEmailUsuarioAlternativoRepresentante"]) . "','" . generalUtils::escaparCadena($_POST["txtInstitucionTelefonoTrabajoRepresentante"]) . "','" . generalUtils::escaparCadena($_POST["txtInstitucionTelefonoMovilRepresentante"]) . "','" . generalUtils::escaparCadena($_POST["txtaDescripcionPrevia"]) . "','" . generalUtils::escaparCadena($_POST["txtaDescripcion"]) . "'," . $activoWeb . ")");
        } else {

            //If user mode is Individual, set variables for insertion in database
            $idSituacionAdicional = "null";
            $vectorSituacionAdicional = Array(SITUACION_ADICIONAL_JUBILADO, SITUACION_ADICIONAL_ESTUDIANTE);

            //Over-65 or student
            if (in_array($_POST["cmbSituacionAdicional"], $vectorSituacionAdicional)) {
                $idSituacionAdicional = $_POST["cmbSituacionAdicional"];
            }

            if ($_POST["cmbIndividualPais"] == 0) {
                $_POST["cmbIndividualPais"] = "null";
            }

            if ($_POST["cmbIndividualTitulo"] == 0) {
                $_POST["cmbIndividualTitulo"] = "null";
            }

            if ($_POST["rdSexo"] == 1) {
                $esHombre = 1;
            } else {
                $esHombre = 0;
            }

            if ($_POST["cmbAnyos"] == 0) {
                $_POST["cmbAnyos"] = "null";
            }

            $importe = PRECIO_MODALIDAD_USUARIO_INDIVIDUAL;

            //Associated institution (for nominees)
            if ($_POST["cmbInstitucion"] != "0") {
                $idInstitucion = $_POST["cmbInstitucion"];
            }

            //Update individual member
            $resultadoUsuarioWebIndividual = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_usuario_web_individual_editar(" . $idUsuarioWeb . "," . $idInstitucion . "," . $_POST["cmbIndividualTitulo"] . "," . $_POST["cmbAnyos"] . "," . $_POST["cmbIndividualPais"] . "," . $idSituacionAdicional . ",'" . generalUtils::escaparCadena($_POST["txtIndividualNombre"]) . "','" . generalUtils::escaparCadena($_POST["txtIndividualApellidos"]) . "','" . generalUtils::escaparCadena($_POST["txtIndividualNacionalidad"]) . "','" . generalUtils::escaparCadena($_POST["txtIndividualDireccion1"]) . "','" . generalUtils::escaparCadena($_POST["txtIndividualDireccion2"]) . "','" . generalUtils::escaparCadena($_POST["txtIndividualCiudad"]) . "','" . generalUtils::escaparCadena($_POST["txtIndividualProvincia"]) . "','" . generalUtils::escaparCadena($_POST["txtIndividualCp"]) . "','" . generalUtils::escaparCadena($_POST["txtIndividualEmail"]) . "','" . generalUtils::escaparCadena($_POST["txtIndividualEmailAlternativo"]) . "','" . generalUtils::escaparCadena($_POST["txtIndividualTelefonoCasa"]) . "','" . generalUtils::escaparCadena($_POST["txtIndividualTelefonoTrabajo"]) . "','" . generalUtils::escaparCadena($_POST["txtIndividualFax"]) . "','" . generalUtils::escaparCadena($_POST["txtIndividualTelefonoMovil"]) . "','" . $esHombre . "','" . generalUtils::escaparCadena($_POST["txtProfesionQualificacion"]) . "')");


            //Delete any existing professional activities
            $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_usuario_web_actividad_profesional_eliminar(" . $idUsuarioWeb . ")");

            //Insert updated professional activities
            $vectorActividadProfesional = array_filter(explode(",", $_POST["hdnIdActividadProfesional"]));

            foreach ($vectorActividadProfesional as $valor) {
                $descripcion = "";
                if ($valor == 7) {
                    //If student
                    $descripcion = $_POST["txtStudySpecification"];
                } else if ($valor == 8) {
                    //Subjects of study
                    $descripcion = $_POST["txtOtherSpecification"];
                }

                $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_usuario_web_actividad_profesional_insertar(" . $idUsuarioWeb . "," . $valor . ",'" . $descripcion . "')");
            }


            //Delete existing work situation
            $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_usuario_web_situacion_laboral_eliminar(" . $idUsuarioWeb . ")");

            //Insert updated work situation
            $vectorSituacionLaboral = array_filter(explode(",", $_POST["hdnIdSituacionLaboral"]));
            foreach ($vectorSituacionLaboral as $valor) {
                $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_usuario_web_situacion_laboral_insertar(" . $idUsuarioWeb . "," . $valor . ")");
            }

        }

        require "../includes/load_mailer.inc.php";


        //Get history of registrations
        $resultadoInscripcion = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_inscripcion_usuario_web_historial(" . $idUsuarioWeb . "," . $_SESSION["user"]["language_id"] . ")");
        while ($datoInscripcion = $db->getData($resultadoInscripcion)) {
            $esMailEnviado = -1;
            $idInscripcion = $datoInscripcion["id_inscripcion"];

            /*
             * If a registration is paid but no email has been sent
             * Set email as sent in database and send a confirmation email
             */
            if ($_POST["cmbPagado_" . $datoInscripcion["id_inscripcion"]] == "1" && $datoInscripcion["es_mail_enviado"] == 0) {
                $esMailEnviado = 1;

                $mail->FromName = STATIC_MAIL_FROM;
                $mail->Subject = STATIC_INSCRIPTIONS_EMAIL_SUBJECT;

                $plantilla = new XTemplate("../html/mail/mail_index.html");
                $plantilla->assign("CURRENT_SERVER", $_SERVER["HTTP_HOST"]);


                // Assign email template (individual or institution)
                switch ($_POST["hdnIdModalidadUsuario"]) {
                    case MODALIDAD_USUARIO_INDIVIDUAL:
                        $subPlantillaMail = new XTemplate("../html/mail/mail_individual_member_payment_confirmed.html");
                        $usuarioNombreCompleto = $_POST["txtIndividualNombre"];
                        break;
                    case MODALIDAD_USUARIO_INSTITUTIONAL:
                        $subPlantillaMail = new XTemplate("../html/mail/mail_institutional_member_payment_confirmed.html");
                        $usuarioNombreCompleto = $_POST["txtInstitucionNombreRepresentante"] . " " . $_POST["txtInstitucionApellidosRepresentante"];
                        $subPlantillaMail->assign("INSTITUCION_NOMBRE", $_POST["txtInstitucionNombreInstitucion"]);
                        break;
                }

                $subPlantillaMail->assign("USUARIO_NOMBRE_COMPLETO", $usuarioNombreCompleto);

                $subPlantillaMail->parse("contenido_principal");

                //Export subtemplate to main template
                $plantilla->assign("CONTENIDO", $subPlantillaMail->text("contenido_principal"));

                $plantilla->parse("contenido_principal");

                //Assign email body text
                $mail->Body = $subPlantillaMail->text("contenido_principal");

                //Set recipient
                $mail->AddAddress($_POST["txtEmail"]);


                //Send email
                if ($mail->Send()) {

                    /****** Log the email ******/
                    $idUsuarioWebCorreo = $idUsuarioWeb;

                    //Set email type
                    $idTipoCorreoElectronico = EMAIL_TYPE_INSCRIPTION_ACTIVATED;


                    //Set receipients
                    $vectorDestinatario = Array();
                    array_push($vectorDestinatario, $_POST["txtEmail"]);

                    //Set email subject
                    $asunto = STATIC_INSCRIPTIONS_EMAIL_SUBJECT;
                    $cuerpo = $mail->Body;


                    require "../includes/load_log_email.inc.php";
                }
            }

            //Update member details
            $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_inscripcion_usuario_web_actualizar_completo(" . $datoInscripcion["id_inscripcion"] . "," . $_POST["cmbImporte_" . $datoInscripcion["id_inscripcion"]] . ",'" . generalUtils::conversionFechaFormato($_POST["txtFechaDesde_" . $datoInscripcion["id_inscripcion"]]) . "','" . generalUtils::conversionFechaFormato($_POST["txtFechaHasta_" . $datoInscripcion["id_inscripcion"]]) . "'," . $_POST["cmbTipoPago_" . $datoInscripcion["id_inscripcion"]] . "," . $_POST["cmbPagado_" . $datoInscripcion["id_inscripcion"]] . "," . $esMailEnviado . ")");
            $mail->ClearAllRecipients();
        }

        //Update member profile photo
        require "image_member.php";

        //End database transaction
        $db->endTransaction();
    }

    //Save or save and close (redirect to list of members)
    if ($_POST["hdnVolver"] == 0) {
        generalUtils::redirigir("main_app.php?section=member&action=view");
    } else {
        generalUtils::redirigir("main_app.php?section=member&action=edit&id_miembro=" . $_GET["id_miembro"]);
    }

}

if (!isset($_GET["id_miembro"]) || !is_numeric($_GET["id_miembro"])) {
    generalUtils::redirigir("main_app.php?section=member&action=view");
}

//Main template
$plantilla = new XTemplate("html/principal.html");

//Subtemplate
$subPlantilla = new XTemplate("html/sections/member/manage_member.html");

//Breadcrumbs
$vectorMigas[0]["url"] = STATIC_BREADCUMB_INICIO_LINK;
$vectorMigas[0]["texto"] = STATIC_BREADCUMB_INICIO_TEXT;
$vectorMigas[1]["url"] = STATIC_BREADCUMB_MEMBER_VIEW_MEMBER_LINK;
$vectorMigas[1]["texto"] = STATIC_BREADCUMB_MEMBER_VIEW_MEMBER_TEXT;
$vectorMigas[2]["url"] = STATIC_BREADCUMB_MEMBER_EDIT_MEMBER_LINK . "&id_miembro=" . $_GET["id_miembro"];
$vectorMigas[2]["texto"] = STATIC_BREADCUMB_MEMBER_EDIT_MEMBER_TEXT;

//Get member details from database
$resultadoUsuarioWeb = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_usuario_web_obtener_concreto(" . $_GET["id_miembro"] . "," . $_SESSION["user"]["language_id"] . ")");
$datoUsuarioWeb = $db->getData($resultadoUsuarioWeb);

//Display member details in form
$subPlantilla->assign("MIEMBRO_CORREO_ELECTRONICO", $datoUsuarioWeb["correo_electronico"]);
$subPlantilla->assign("MIEMBRO_OTRAS_ACTIVIDADES", $datoUsuarioWeb["otros"]);
$subPlantilla->assign("MIEMBRO_OTRAS_DESCRIPCION", $datoUsuarioWeb["descripcion"]);
$subPlantilla->assign("MIEMBRO_OTRAS_PUBLICACIONES", $datoUsuarioWeb["publicaciones"]);
$subPlantilla->assign("MIEMBRO_WEB", $datoUsuarioWeb["web"]);
// Display tax ID with fallback: prefer tax_id_number, fallback to nif_cliente_factura for old records
$displayTaxId = !empty($datoUsuarioWeb["tax_id_number"])
	? $datoUsuarioWeb["tax_id_number"]
	: ($datoUsuarioWeb["nif_cliente_factura"] ?? "");
$subPlantilla->assign("MIEMBRO_FACTURA_NIF_CLIENTE", $displayTaxId);
$subPlantilla->assign("MIEMBRO_FACTURA_NOMBRE_EMPRESA", $datoUsuarioWeb["nombre_empresa_factura"]);
$subPlantilla->assign("MIEMBRO_FACTURA_NOMBRE_CLIENTE", $datoUsuarioWeb["nombre_cliente_factura"]);
$subPlantilla->assign("MIEMBRO_FACTURA_DIRECCION", $datoUsuarioWeb["direccion_factura"]);
$subPlantilla->assign("MIEMBRO_FACTURA_CODIGO_POSTAL", $datoUsuarioWeb["codigo_postal_factura"]);
$subPlantilla->assign("MIEMBRO_FACTURA_CIUDAD", $datoUsuarioWeb["ciudad_factura"]);
$subPlantilla->assign("MIEMBRO_FACTURA_PROVINCIA", $datoUsuarioWeb["provincia_factura"]);
$subPlantilla->assign("MIEMBRO_FACTURA_PAIS", $datoUsuarioWeb["pais_factura"]);
$subPlantilla->assign("MIEMBRO_OBSERVACIONES", $datoUsuarioWeb["observaciones"]);


//Profile public or members only
if ($datoUsuarioWeb["publico"] == 1) {
    $subPlantilla->assign("PUBLICO_CLASE", "checked");
} else {
    $subPlantilla->assign("PUBLICO_CLASE", "unChecked");
}
$subPlantilla->assign("MIEMBRO_PUBLICO", $datoUsuarioWeb["publico"]);

//Display member photo
if ($datoUsuarioWeb["imagen"] != "") {
    $subPlantilla->assign("IMAGEN_MIEMBRO", $datoUsuarioWeb["imagen"]);
    $subPlantilla->parse("contenido_principal.profile_photo");
}

//If nominee, display "Institution dropdown, otherwise hide
$idTipoUsuario = $datoUsuarioWeb["id_tipo_usuario_web"];
if ($idTipoUsuario == TIPO_USUARIO_INVITADO) {
    $plantilla->parse("contenido_principal.carga_inicial.bloque_nominee");

    $subPlantilla->assign("DISPLAY_INSTITUCION", "style='display:;'");
} else {
    $subPlantilla->assign("DISPLAY_INSTITUCION", "style='display:none;'");
}

$idModalidadUsuario = $datoUsuarioWeb["id_modalidad_usuario_web"];


$subPlantilla->assign("VALOR_MODALIDAD_USUARIO", $datoUsuarioWeb["modalidad_usuario"]);
$subPlantilla->assign("ID_MIEMBRO", $_GET["id_miembro"]);
$subPlantilla->assign("ID_MODALIDAD_USUARIO", $idModalidadUsuario);

if ($idModalidadUsuario == MODALIDAD_USUARIO_INDIVIDUAL) {
    //Combo tipo usuario
    $subPlantilla->assign("COMBO_TIPO_USUARIO", generalUtils::construirCombo($db, "CALL " . OBJECT_DB_ACRONYM . "_sp_tipo_usuario_web_obtener_combo(" . $_SESSION["user"]["language_id"] . ")", "cmbTipoMiembro", "cmbTipoMiembro", $idTipoUsuario, "nombre", "id_tipo_usuario_web", "", 0, "onchange='gestionarBloqueNominee(this)'"));
    $subPlantilla->assign("DISPLAY_INSTITUCION", "style='display:;'");
} else {
    $subPlantilla->assign("DISPLAY_INSTITUCION", "style='display:none;'");
    $subPlantilla->assign("VALOR_TIPO_USUARIO", $datoUsuarioWeb["tipo_usuario"]);
}


require "includes/load_breadcumb.inc.php";


$subPlantilla->assign("ACTION", "edit");


if ($idModalidadUsuario == MODALIDAD_USUARIO_INDIVIDUAL) {
    $subPlantilla->assign("BLOQUE_EXPANDED_INDIVIDUAL", "displayed");
    $subPlantilla->assign("BLOQUE_EXPANDED_INSTITUTIONAL", "noDisplayed");

    $subPlantilla->assign("CLASS_INFO_IMAGE_1", "class='displayed'");
    $subPlantilla->assign("CLASS_INFO_IMAGE_2", "class='noDisplayed'");


    //Obtenemos detalle miembro individual
    $resultadoUsuarioIndividual = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_usuario_web_individual_obtener_concreto(" . $_GET["id_miembro"] . ")");
    $datoUsuarioIndividual = $db->getData($resultadoUsuarioIndividual);

    $idTratamientoUsuario = $datoUsuarioIndividual["id_tratamiento_usuario_web"];
    $idPais = $datoUsuarioIndividual["id_pais"];
    $idEdadUsuarioWeb = $datoUsuarioIndividual["id_edad_usuario_web"];
    $idSituacionAdicional = $datoUsuarioIndividual["id_situacion_adicional"];
    if ($datoUsuarioIndividual["id_institucion"] == "") {
        $idInstitucion = 0;
    } else {
        $idInstitucion = $datoUsuarioIndividual["id_institucion"];
    }


    //Mostramos informacion
    $subPlantilla->assign("MIEMBRO_INDIVIDUAL_NOMBRE", $datoUsuarioIndividual["nombre"]);
    $subPlantilla->assign("MIEMBRO_INDIVIDUAL_APELLIDOS", $datoUsuarioIndividual["apellidos"]);
    $subPlantilla->assign("MIEMBRO_INDIVIDUAL_NACIONALIDAD", $datoUsuarioIndividual["nacionalidad"]);
    $subPlantilla->assign("MIEMBRO_INDIVIDUAL_DIRECCION_1", $datoUsuarioIndividual["direccion"]);
    $subPlantilla->assign("MIEMBRO_INDIVIDUAL_DIRECCION_2", $datoUsuarioIndividual["direccion2"]);
    $subPlantilla->assign("MIEMBRO_INDIVIDUAL_CIUDAD", $datoUsuarioIndividual["ciudad"]);
    $subPlantilla->assign("MIEMBRO_INDIVIDUAL_PROVINCIA", $datoUsuarioIndividual["provincia"]);
    $subPlantilla->assign("MIEMBRO_INDIVIDUAL_CODIGO_POSTAL", $datoUsuarioIndividual["codigo_postal"]);
    $subPlantilla->assign("MIEMBRO_INDIVIDUAL_CORREO_ELECTRONICO", $datoUsuarioIndividual["correo_electronico"]);
    $subPlantilla->assign("MIEMBRO_INDIVIDUAL_TELEFONO_CASA", $datoUsuarioIndividual["telefono_casa"]);
    $subPlantilla->assign("MIEMBRO_INDIVIDUAL_CORREO_ELECTRONICO_ALTERNATIVO", $datoUsuarioIndividual["correo_electronico_alternativo"]);
    $subPlantilla->assign("MIEMBRO_INDIVIDUAL_TELEFONO_TRABAJO", $datoUsuarioIndividual["telefono_trabajo"]);
    $subPlantilla->assign("MIEMBRO_INDIVIDUAL_TELEFONO_MOVIL", $datoUsuarioIndividual["telefono_movil"]);
    $subPlantilla->assign("MIEMBRO_INDIVIDUAL_FAX", $datoUsuarioIndividual["fax"]);
    $subPlantilla->assign("MIEMBROS_CUALIFICACIONES", $datoUsuarioIndividual["cualificaciones"]);
    $subPlantilla->assign("MIEMBRO_INDIVIDUAL_SOBRE_MET", $datoUsuarioIndividual["descripcion"]);


    //Combo pais individual
    $subPlantilla->assign("COMBO_PAIS_INDIVIDUAL", generalUtils::construirCombo($db, "CALL " . OBJECT_DB_ACRONYM . "_sp_pais_obtener_combo()", "cmbIndividualPais", "cmbIndividualPais", $idPais, "nombre_original", "id_pais", STATIC_GLOBAL_COMBO_DEFAULT, 0, ""));

    //Combo titulos individual
    $subPlantilla->assign("COMBO_TITULOS_INDIVIDUAL", generalUtils::construirCombo($db, "CALL " . OBJECT_DB_ACRONYM . "_sp_tratamiento_usuario_web_obtener_combo(" . $_SESSION["user"]["language_id"] . ")", "cmbIndividualTitulo", "cmbIndividualTitulo", $idTratamientoUsuario, "nombre", "id_tratamiento_usuario_web", STATIC_GLOBAL_COMBO_DEFAULT, 0, ""));

    //Combo años
    $subPlantilla->assign("COMBO_ANYOS", generalUtils::construirCombo($db, "CALL " . OBJECT_DB_ACRONYM . "_sp_edad_usuario_web_obtener_combo(" . $_SESSION["user"]["language_id"] . ")", "cmbAnyos", "cmbAnyos", $idEdadUsuarioWeb, "nombre", "id_edad_usuario_web", STATIC_GLOBAL_COMBO_DEFAULT, 0, ""));

    //Combo institucion
    $subPlantilla->assign("COMBO_INSTITUCION", generalUtils::construirCombo($db, "CALL " . OBJECT_DB_ACRONYM . "_sp_usuario_web_institucion_obtener_combo()", "cmbInstitucion", "cmbInstitucion", $idInstitucion, "nombre", "id_usuario_web", STATIC_GLOBAL_COMBO_DEFAULT, 0, ""));

    //Combo situacion adicional
    $subPlantilla->assign("COMBO_SITUACION_ADICIONAL", generalUtils::construirCombo($db, "CALL ed_sp_situacion_adicional_obtener_combo(" . $_SESSION["user"]["language_id"] . ")", "cmbSituacionAdicional", "cmbSituacionAdicional", $idSituacionAdicional, "nombre", "id_situacion_adicional", STATIC_GLOBAL_COMBO_DEFAULT, 0, ""));

    //Checkeamos sexo
    $subPlantilla->assign("CHECKED_SEXO_" . $datoUsuarioIndividual["es_hombre"], "checked");

    //Listado de actividades profesionales
    $resulActividadesProfesionales = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_usuario_web_actividad_profesional_obtener_listado(" . $_GET["id_miembro"] . "," . $_SESSION["user"]["language_id"] . ")");
    $validacion = "";
    $i = 1;
    while ($dataActividadProfesional = $db->getData($resulActividadesProfesionales)) {
        $subPlantilla->assign("ITEM_ACTIVIDAD_PROFESIONAL_ID", $dataActividadProfesional["id_actividad_profesional"]);
        $subPlantilla->assign("ITEM_ACTIVIDAD_PROFESIONAL_NOMBRE", $dataActividadProfesional["actividad_profesional"]);
        if ($dataActividadProfesional["id_usuario_web"] != "") {
            $subPlantilla->assign("CHECKED_ACTIVIDAD_PROFESIONAL", "checked");

            if ($dataActividadProfesional["id_actividad_profesional"] == "7") {
                //Estudios
                if ($dataActividadProfesional["descripcion"] != "") {
                    $subPlantilla->assign("MIEMBRO_ACTIVIDADES_ESTUDIOS", $dataActividadProfesional["descripcion"]);
                }
            } else if ($dataActividadProfesional["id_actividad_profesional"] == "8") {
                //Otros...
                if ($dataActividadProfesional["descripcion"] != "") {
                    $subPlantilla->assign("MIEMBRO_ACTIVIDADES_OTROS", $dataActividadProfesional["descripcion"]);
                }
            }
        } else {
            $subPlantilla->assign("CHECKED_ACTIVIDAD_PROFESIONAL", "");
        }

        if ($i % 6 == 0) {
            $subPlantilla->assign("SALTO_LINEA_PROFESION", "<br>");
        } else {
            $subPlantilla->assign("SALTO_LINEA_PROFESION", "");
        }

        $subPlantilla->parse("contenido_principal.item_actividad_profesional");
        $i++;
    }


    //Listamos situaciones laborales
    $resultadoSituacionLaboral = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_usuario_web_situacion_laboral_obtener_listado(" . $_GET["id_miembro"] . "," . $_SESSION["user"]["language_id"] . ")");
    while ($dataSituacionLaboral = $db->getData($resultadoSituacionLaboral)) {
        $subPlantilla->assign("SITUACION_LABORAL_ID", $dataSituacionLaboral["id_situacion_laboral"]);
        $subPlantilla->assign("SITUACION_LABORAL_NOMBRE", $dataSituacionLaboral["situacion_laboral"]);

        if ($dataSituacionLaboral["id_usuario_web"] != "") {
            $subPlantilla->assign("CHECKED_SITUACION_LABORAL", "checked");
        } else {
            $subPlantilla->assign("CHECKED_SITUACION_LABORAL", "");
        }
        $subPlantilla->parse("contenido_principal.item_situacion_laboral");
    }


} else {
    //Ocultamos combo situacion adicional
    $subPlantilla->assign("DISPLAY_SITUACION_ADICIONAL", "style='display:none;'");
    $subPlantilla->assign("BLOQUE_EXPANDED_INDIVIDUAL", "noDisplayed");
    $subPlantilla->assign("BLOQUE_EXPANDED_INSTITUTIONAL", "displayed");

    $subPlantilla->assign("CLASS_INFO_IMAGE_1", "class='noDisplayed'");
    $subPlantilla->assign("CLASS_INFO_IMAGE_2", "class='displayed'");

    //Obtenemos detalle miembro institucion
    $resultadoUsuarioInstitucion = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_usuario_web_institucion_obtener_concreto(" . $_GET["id_miembro"] . ")");
    $datoUsuarioInstitucion = $db->getData($resultadoUsuarioInstitucion);


    $idTratamientoUsuario = $datoUsuarioInstitucion["id_tratamiento_usuario_web"];
    $idPais = $datoUsuarioInstitucion["id_pais"];

    //Mostramos informacion
    $subPlantilla->assign("MIEMBRO_INSTITUCION_NOMBRE_INSTITUCION", $datoUsuarioInstitucion["nombre"]);
    $subPlantilla->assign("MIEMBRO_INSTITUCION_DEPARTAMENTO", $datoUsuarioInstitucion["departamento"]);
    $subPlantilla->assign("MIEMBRO_INSTITUCION_DIRECCION_1", $datoUsuarioInstitucion["direccion"]);
    $subPlantilla->assign("MIEMBRO_INSTITUCION_DIRECCION_2", $datoUsuarioInstitucion["direccion2"]);
    $subPlantilla->assign("MIEMBRO_INSTITUCION_CODIGO_POSTAL", $datoUsuarioInstitucion["codigo_postal"]);
    $subPlantilla->assign("MIEMBRO_INSTITUCION_CIUDAD", $datoUsuarioInstitucion["ciudad"]);
    $subPlantilla->assign("MIEMBRO_INSTITUCION_PROVINCIA", $datoUsuarioInstitucion["provincia"]);
    $subPlantilla->assign("MIEMBRO_INSTITUCION_TELEFONO", $datoUsuarioInstitucion["telefono"]);
    $subPlantilla->assign("MIEMBRO_INSTITUCION_FAX", $datoUsuarioInstitucion["fax"]);
    $subPlantilla->assign("MIEMBRO_INSTITUCION_CORREO_ELECTRONICO", $datoUsuarioInstitucion["correo_electronico"]);
    $subPlantilla->assign("MIEMBRO_NOMBRE_REPRESENTANTE", $datoUsuarioInstitucion["nombre_representante"]);
    $subPlantilla->assign("MIEMBRO_APELLIDOS_REPRESENTANTE", $datoUsuarioInstitucion["apellidos_representante"]);
    $subPlantilla->assign("MIEMBRO_ESTADO_REPRESENTANTE", $datoUsuarioInstitucion["pais_representante"]);
    $subPlantilla->assign("MIEMBRO_CORREO_ELECTRONICO_REPRESENTANTE", $datoUsuarioInstitucion["correo_electronico_representante"]);
    $subPlantilla->assign("MIEMBRO_CORREO_ELECTRONICO_ALTERNATIVO_REPRESENTANTE", $datoUsuarioInstitucion["correo_electronico_alternativa_representante"]);
    $subPlantilla->assign("MIEMBRO_TELEFONO_TRABAJO_REPRESENTANTE", $datoUsuarioInstitucion["telefono_trabajo"]);
    $subPlantilla->assign("MIEMBRO_TELEFONO_MOVIL_REPRESENTANTE", $datoUsuarioInstitucion["telefono_movil"]);
    $subPlantilla->assign("MIEMBRO_INSTITUCION_DESCRIPCION_PREVIA", $datoUsuarioInstitucion["descripcion_previa"]);
    $subPlantilla->assign("MIEMBRO_INSTITUCION_DESCRIPCION", $datoUsuarioInstitucion["descripcion"]);
    $subPlantilla->assign("MIEMBRO_TELEFONO_MOVIL_REPRESENTANTE", $datoUsuarioInstitucion["telefono_movil"]);

    //La primera vez, miramos si la noticia esta activo o no
    if ($datoUsuarioInstitucion["activo"] == 1) {
        $subPlantilla->assign("ACTIVO_WEB_CLASE", "checked");
    } else {
        $subPlantilla->assign("ACTIVO_WEB_CLASE", "unChecked");
    }
    $subPlantilla->assign("INSTITUCION_ACTIVO_WEB", $datoUsuarioInstitucion["activo"]);

    //Combo pais
    $subPlantilla->assign("COMBO_PAIS_INSTITUCION", generalUtils::construirCombo($db, "CALL " . OBJECT_DB_ACRONYM . "_sp_pais_obtener_combo()", "cmbInstitucionPais", "cmbInstitucionPais", $idPais, "nombre_original", "id_pais", STATIC_GLOBAL_COMBO_DEFAULT, 0, ""));

    //Combo titulos
    $subPlantilla->assign("COMBO_TITULOS_INSTITUCION", generalUtils::construirCombo($db, "CALL " . OBJECT_DB_ACRONYM . "_sp_tratamiento_usuario_web_obtener_combo(" . $_SESSION["user"]["language_id"] . ")", "cmbInstitucionTitulo", "cmbInstitucionTitulo", $idTratamientoUsuario, "nombre", "id_tratamiento_usuario_web", STATIC_GLOBAL_COMBO_DEFAULT, 0, ""));

    //Editor descripcion previa
    $plantilla->assign("TEXTAREA_ID", "txtaDescripcionPrevia");
    $plantilla->assign("TEXTAREA_TOOLBAR", "Minimo");
    $plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");

    //Editor descripcion completa
    $plantilla->assign("TEXTAREA_ID", "txtaDescripcion");
    $plantilla->assign("TEXTAREA_TOOLBAR", "Minimo");
    $plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");

    //If an institution, display a list of nominees and IDs (for link to nominee's EG profile))
    $resultadoUsuarioNomineeWeb = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_usuario_web_institucion_usuario_web_asociado(" . $_GET["id_miembro"] . ")");
    while ($datoUsuarioNomineeWeb = $db->getData($resultadoUsuarioNomineeWeb)) {
        $subPlantilla->assign("USUARIO_NOMINEE_NOMBRE", $datoUsuarioNomineeWeb["nombre"] . " " . $datoUsuarioNomineeWeb["apellidos"]);
        $subPlantilla->assign("USUARIO_NOMINEE_ID", $datoUsuarioNomineeWeb["id_usuario_web"]);

        $subPlantilla->parse("contenido_principal.bloque_usuario_nominee.item_usuario_nominee");
    }
    $subPlantilla->parse("contenido_principal.bloque_usuario_nominee");

}

//Informacion del usuario
require "includes/load_information_user.inc.php";

//Incluimos save & close
$subPlantilla->parse("contenido_principal.item_button_close");

//Date pickers
$plantilla->assign("INPUT_ID", "txtFechaDesde");
$plantilla->parse("contenido_principal.carga_inicial.autoload_datepicker");

//Date pickers
$plantilla->assign("INPUT_ID", "txtFechaHasta");
$plantilla->parse("contenido_principal.carga_inicial.autoload_datepicker");

//Incluimos script editor
$plantilla->parse("contenido_principal.editor_script");

//Incluimos proceso onload
$plantilla->parse("contenido_principal.carga_inicial");


//Historial inscripciones
$resultadoInscripcion = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_inscripcion_usuario_web_historial(" . $_GET["id_miembro"] . "," . $_SESSION["user"]["language_id"] . ")");
while ($datoInscripcion = $db->getData($resultadoInscripcion)) {
    $fechaStart = explode(" ", $datoInscripcion["fecha_inscripcion"]);
    $subPlantilla->assign("INSCRIPTIONS_HISTORY_ID", $datoInscripcion["id_inscripcion"]);
    $subPlantilla->assign("INSCRIPTIONS_HISTORY_IMPORTE", number_format($datoInscripcion["importe"]));
    if ($datoInscripcion["importe"] == PRECIO_MODALIDAD_USUARIO_INSTITUTIONAL) {
        $subPlantilla->assign("CHECKED_IMPORTE_3", "selected");
        $subPlantilla->assign("CHECKED_IMPORTE_2", "");
        $subPlantilla->assign("CHECKED_IMPORTE_1", "");
        $subPlantilla->assign("CHECKED_IMPORTE_0", "");
    } else if ($datoInscripcion["importe"] == PRECIO_MODALIDAD_USUARIO_INDIVIDUAL_DESCUENTO) {
        $subPlantilla->assign("CHECKED_IMPORTE_3", "");
        $subPlantilla->assign("CHECKED_IMPORTE_2", "selected");
        $subPlantilla->assign("CHECKED_IMPORTE_1", "");
        $subPlantilla->assign("CHECKED_IMPORTE_0", "");
    } else if ($datoInscripcion["importe"] == PRECIO_MODALIDAD_USUARIO_INDIVIDUAL) {
        $subPlantilla->assign("CHECKED_IMPORTE_3", "");
        $subPlantilla->assign("CHECKED_IMPORTE_2", "");
        $subPlantilla->assign("CHECKED_IMPORTE_1", "selected");
        $subPlantilla->assign("CHECKED_IMPORTE_0", "");
    } else {
        $subPlantilla->assign("CHECKED_IMPORTE_3", "");
        $subPlantilla->assign("CHECKED_IMPORTE_2", "");
        $subPlantilla->assign("CHECKED_IMPORTE_1", "");
        $subPlantilla->assign("CHECKED_IMPORTE_0", "selected");
    }

    $subPlantilla->assign("INSCRIPTIONS_HISTORY_INSCRIPTION_NUMBER", $datoInscripcion["numero_inscripcion"]);
    $subPlantilla->assign("INSCRIPTIONS_INSCRIPTION_START_DATE", generalUtils::conversionFechaFormato($fechaStart[0], "-", "-"));
    $subPlantilla->assign("INSCRIPTIONS_INSCRIPTION_END_DATE", generalUtils::conversionFechaFormato($datoInscripcion["fecha_finalizacion"], "-", "-"));
    if ($datoInscripcion["id_tipo_pago"] == 1) {
        $subPlantilla->assign("INSCRIPTIONS_PAYMENT_TYPE", "1");
        $subPlantilla->assign("CHECKED_PAYMENT_TYPE_1", "selected");
        $subPlantilla->assign("CHECKED_PAYMENT_TYPE_2", "");
        $subPlantilla->assign("CHECKED_PAYMENT_TYPE_3", "");
        $subPlantilla->assign("CHECKED_PAYMENT_TYPE_4", "");
    } else if ($datoInscripcion["id_tipo_pago"] == 2) {
        $subPlantilla->assign("INSCRIPTIONS_PAYMENT_TYPE", "2");
        $subPlantilla->assign("CHECKED_PAYMENT_TYPE_1", "");
        $subPlantilla->assign("CHECKED_PAYMENT_TYPE_2", "selected");
        $subPlantilla->assign("CHECKED_PAYMENT_TYPE_3", "");
        $subPlantilla->assign("CHECKED_PAYMENT_TYPE_4", "");
    } else if ($datoInscripcion["id_tipo_pago"] == 3) {
        $subPlantilla->assign("INSCRIPTIONS_PAYMENT_TYPE", "3");
        $subPlantilla->assign("CHECKED_PAYMENT_TYPE_1", "");
        $subPlantilla->assign("CHECKED_PAYMENT_TYPE_2", "");
        $subPlantilla->assign("CHECKED_PAYMENT_TYPE_3", "selected");
        $subPlantilla->assign("CHECKED_PAYMENT_TYPE_4", "");
    } else if ($datoInscripcion["id_tipo_pago"] == 4) {
        $subPlantilla->assign("INSCRIPTIONS_PAYMENT_TYPE", "4");
        $subPlantilla->assign("CHECKED_PAYMENT_TYPE_1", "");
        $subPlantilla->assign("CHECKED_PAYMENT_TYPE_2", "");
        $subPlantilla->assign("CHECKED_PAYMENT_TYPE_3", "");
        $subPlantilla->assign("CHECKED_PAYMENT_TYPE_4", "selected");
    }
    //$subPlantilla->assign("INSCRIPTIONS_PAYMENT_TYPE_DESCRIPTION",$datoInscripcion["tipo_pago"]);

    if ($datoInscripcion["pagado"] == 1) {
        $subPlantilla->assign("CHECKED_PAGADO_1", "selected");
        $subPlantilla->assign("CHECKED_PAGADO_0", "");
    } else {
        $subPlantilla->assign("CHECKED_PAGADO_1", "");
        $subPlantilla->assign("CHECKED_PAGADO_0", "selected");
    }


    $subPlantilla->parse("contenido_principal.item_inscripcion");
}

//Get details of member's conference and workshop attendance
$resultadoConferencia=$db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_usuario_web_obtener(".$_GET["id_miembro"].",".$_SESSION["id_idioma"].")");
$i=0;
$txtConferenciaProfesional = "";
while($datoConferencia=$db->getData($resultadoConferencia)){
    $txtConferenciaProfesional.=$datoConferencia["nombre"] . "<br>";
    $i++;
    }

if($i>0){
    $subPlantilla->assign("MEMBER_CONFERENCES", $txtConferenciaProfesional);
    $subPlantilla->parse("contenido_principal.item_conferences");
}

//Get member's workshops
$resultadoTaller=$db->callProcedure("CALL ed_sp_web_inscripcion_taller_usuario_web_taller_obtener(".$_GET["id_miembro"].",".$_SESSION["id_idioma"].")");
$j=0;
$memberWorkshops = "";
while($datoTaller=$db->getData($resultadoTaller)){
    $memberWorkshops.=$datoTaller["nombre_largo"] . "<br>";
    $j++;
}

if($j>0){
    $subPlantilla->assign("MEMBER_WORKSHOPS",$memberWorkshops);
    $subPlantilla->parse("contenido_principal.item_workshops");
}

//No ver about met
$subPlantilla->assign("DISPLAY_ABOUT_MET", "style='display:none;'");
//Contruimos plantilla secundaria
$subPlantilla->parse("contenido_principal");

//Exportamos plantilla secundaria a la plantilla principal
$plantilla->assign("CONTENIDO", $subPlantilla->text("contenido_principal"));

//Construimos plantilla principal
$plantilla->parse("contenido_principal");

//Mostramos plantilla principal por pantalla
$plantilla->out("contenido_principal");
?>