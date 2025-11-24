<?php

/**
 *
 * Pagina openbox de contenido libre
 * @author Edata S.L.
 * @copyright Edata S.L.
 * @version 1.0
 */
require "includes/load_main_components.inc.php";

//Obsolete
//$validarIntegridad = true;
//require "includes/load_validate_user_web.inc.php";

// Instanciamos la clase Xtemplate con la plantilla base
$plantilla = new XTemplate("html/index.html");
$subPlantilla = new XTemplate("html/profile_individual_member.html");

$plantilla->assign("SECTION_FILE_CSS", "openbox.css");

require "includes/load_structure.inc.php";

//If the form has been submitted, update the member's profile with any new values
if (count($_POST) > 0) {
    $claveActual = trim(generalUtils::escaparCadena($_POST["txtCurrentPassword"]));
    $claveNueva = trim(generalUtils::escaparCadena($_POST["txtNewPassword"]));
    $idMenu = $_POST["hdnIdMenu"];
    $blError = true;

    $db->startTransaction();

    // Handle change of password
    if ($claveActual != "" && $claveNueva != "") {
        $resultCambioClave = $db->callProcedure("CALL ed_sp_web_usuario_web_restablecer_clave(" . $_SESSION["met_user"]["id"] . ", '" . $claveActual . "', '" . $claveNueva . "')");
        $dataCambioClave = $db->getData($resultCambioClave);
        if ($dataCambioClave["codigo"] == -1) {
            $codigoError = 3;
            $blError = false;
        } else {
            $codigoError = 1;
        }
    } else if ($claveNueva != "" && $claveActual == "") {
        $codigoError = 3;
        $blError = false;
    }

    //If there are no errors, assign data from form to variables
    if ($blError) {
        if (isset($_POST["chkPublico"])) {
            $publico = 1;
        } else {
            $publico = 0;
        }

        $_POST["txtOtherCPD"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_OTHER, $_POST["txtOtherCPD"]));
        $_POST["txtOtrasDescripciones"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_MEMBER_DESCRIPTION, $_POST["txtOtrasDescripciones"]));
        $_POST["txtOtrasPublicaciones"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_MEMBER_PUBLICATIONS, $_POST["txtOtrasPublicaciones"]));
        $_POST["txtWeb"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_CHANGE_WEB, $_POST["txtWeb"]));

        //Datos facturacion
        $_POST["txtFacturacionNifCliente"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_CUSTOMER_NIF, $_POST["txtFacturacionNifCliente"]));
        $_POST["txtFacturacionNombreCliente"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_NAME_CUSTOMER, $_POST["txtFacturacionNombreCliente"]));
        $_POST["txtFacturacionNombreEmpresa"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_NAME_COMPANY, $_POST["txtFacturacionNombreEmpresa"]));
        $_POST["txtFacturacionDireccion"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_ADDRESS, $_POST["txtFacturacionDireccion"]));
        $_POST["txtFacturacionCodigoPostal"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_ZIPCODE, $_POST["txtFacturacionCodigoPostal"]));
        $_POST["txtFacturacionCiudad"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_CITY, $_POST["txtFacturacionCiudad"]));
        $_POST["txtFacturacionProvincia"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_PROVINCE, $_POST["txtFacturacionProvincia"]));
        $_POST["txtFacturacionPais"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_COUNTRY, $_POST["txtFacturacionPais"]));

        //Insert new values into member's profile
        $resultadoUsuario = $db->callProcedure("CALL ed_sp_web_usuario_web_editar(" . $_SESSION["met_user"]["id"] . "," . $publico . ",'" . $_POST["txtOtherCPD"] . "','" . $_POST["txtOtrasDescripciones"] . "','" . $_POST["txtOtrasPublicaciones"] . "','" . $_POST["txtWeb"] . "','" . $_POST["txtFacturacionNifCliente"] . "','" . $_POST["txtFacturacionNombreCliente"] . "','" . $_POST["txtFacturacionNombreEmpresa"] . "','" . $_POST["txtFacturacionDireccion"] . "','" . $_POST["txtFacturacionCodigoPostal"] . "','" . $_POST["txtFacturacionCiudad"] . "','" . $_POST["txtFacturacionProvincia"] . "','" . $_POST["txtFacturacionPais"] . "')");

        //Delete working languages from profile
        $db->callProcedure("CALL ed_sp_web_usuario_web_working_languages_eliminar(" . $_SESSION["met_user"]["id"] . ")");

        //Insert selected working languages
        if ($_POST['cmbSource'] != '' && $_POST['cmbTarget'] != '') {
            $sourceLangs = array();
            $targetLangs = array();
            $idLangsAll = array();
            $idLangs = array();
            $sourceLangs = $_POST['cmbSource'];
            $targetLangs = $_POST['cmbTarget'];
            $idLangsAll = array_merge($sourceLangs, $targetLangs);
            $idLangs = array_unique($idLangsAll);
            foreach ($idLangs as $idLang) {
                if (in_array($idLang, $sourceLangs)) {
                    $isSource = '1';
                } else {
                    $isSource = '0';
                }
                if (in_array($idLang, $targetLangs)) {
                    $isTarget = '1';
                } else {
                    $isTarget = '0';
                }
                $db->callProcedure("CALL ed_sp_web_usuario_web_working_languages_insertar(" . $_SESSION["met_user"]["id"] . ",'" . $idLang . "','" . $isSource . "','" . $isTarget . "')");
            }
        }

        //Delete all previously selected areas of expertise from profile
        $db->callProcedure("CALL ed_sp_web_usuario_web_areas_of_expertise_eliminar(" . $_SESSION["met_user"]["id"] . ")");
        //Insert newly selected areas of expertise
        if ($_POST['cmbAreas'] != '') {
            $areas = $_POST['cmbAreas'];
            foreach ($areas as $area) {
                $db->callProcedure("CALL ed_sp_web_usuario_web_areas_of_expertise_insertar(" . $_SESSION["met_user"]["id"] . ",'" . $area . "')");
            }
        }

        if ($_POST["cmbPais"] == -1) {
            $_POST["cmbPais"] = "null";
        }

        /*
        if ($_POST["cmbTitulo"] == -1) {
            $_POST["cmbTitulo"] = "null";
        }

        if ($_POST["chkSex"] == 1) {
            $esHombre = 1;
        } else {
            $esHombre = 0;
        }
        */

        $_POST["cmbTitulo"] = "null";
        $esHombre = "null";


        if ($_POST["cmbAnyos"] == -1) {
            $_POST["cmbAnyos"] = "null";
        }


        $_POST["txtNombre"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_FIRST_NAME, $_POST["txtNombre"]));
        $_POST["txtApellidos"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_LAST_NAMES, $_POST["txtApellidos"]));
        $_POST["txtNacionalidad"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_NATIONALITY, $_POST["txtNacionalidad"]));
        $_POST["txtDireccion1"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_STREET_1, $_POST["txtDireccion1"]));
        $_POST["txtDireccion2"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_STREET_2, $_POST["txtDireccion2"]));
        $_POST["txtCiudad"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_TOWN_CITY, $_POST["txtCiudad"]));
        $_POST["txtProvincia"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_PROVINCE, $_POST["txtProvincia"]));
        $_POST["txtCp"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_POSTCODE, $_POST["txtCp"]));
        $_POST["txtEmail"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_EMAIL, $_POST["txtEmail"]));
        $_POST["txtTelefonoCasa"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_HOME_PHONE, $_POST["txtTelefonoCasa"]));
        $_POST["txtEmailAlternativo"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_ALTERNATIVE_EMAIL, $_POST["txtEmailAlternativo"]));
        $_POST["txtTelefonoTrabajo"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_WORK_PHONE, $_POST["txtTelefonoTrabajo"]));
        $_POST["txtFax"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_FAX, $_POST["txtFax"]));
        $_POST["txtTelefonoMobil"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_MOBILE_PHONE, $_POST["txtTelefonoMobil"]));
        //$_POST["txtSpecifyOther"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_IF_OTHER_SPECIFY, $_POST["txtSpecifyOther"]));
        //$_POST["txtSpecifyStudy"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_IF_STUDENT_SUBJECT, $_POST["txtSpecifyStudy"]));
        $_POST["txtProfesionQualificacion"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_DEGREES_QUALIFICATIONS, $_POST["txtProfesionQualificacion"]));
        //$_POST["txtSobreMet"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_HOW_DID_YOU_HEAR_ABOUT_MET,$_POST["txtSobreMet"]));
        $_POST["txtOtherSpecification"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_IF_OTHER_SPECIFY, $_POST["txtOtherSpecification"]));
        $_POST["txtStudySpecification"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_IF_STUDENT_SUBJECT, $_POST["txtStudySpecification"]));

        //Editamos datos usuario individual
        $resultadoUsuarioWebIndividual = $db->callProcedure("CALL ed_sp_web_usuario_web_individual_editar(" . $_SESSION["met_user"]["id"] . "," . $_POST["cmbTitulo"] . "," . $_POST["cmbAnyos"] . "," . $_POST["cmbPais"] . ",'" . $_POST["txtNombre"] . "','" . $_POST["txtApellidos"] . "','" . $_POST["txtNacionalidad"] . "','" . $_POST["txtDireccion1"] . "','" . $_POST["txtDireccion2"] . "','" . $_POST["txtCiudad"] . "','" . $_POST["txtProvincia"] . "','" . $_POST["txtCp"] . "','" . $_POST["txtEmail"] . "','" . $_POST["txtEmailAlternativo"] . "','" . $_POST["txtTelefonoCasa"] . "','" . $_POST["txtTelefonoTrabajo"] . "','" . $_POST["txtFax"] . "','" . $_POST["txtTelefonoMobil"] . "','" . $esHombre . "','" . $_POST["txtProfesionQualificacion"] . "')");

        //Delete existing professional activities
        $db->callProcedure("CALL ed_sp_web_usuario_web_actividad_profesional_eliminar(" . $_SESSION["met_user"]["id"] . ")");

        //Insert new professional activities, associating text field content with "Student" and "Other" checkboxes
        $vectorActividadProfesional = array_filter(explode(",", $_POST["hdnIdActividadProfesional"]));
        foreach ($vectorActividadProfesional as $valor) {
            $descripcion = "";
            if ($valor == 7) {
                //estudio
                $descripcion = $_POST["txtStudySpecification"];
            } else if ($valor == 8) {
                //other
                $descripcion = $_POST["txtOtherSpecification"];
            }

            $db->callProcedure("CALL ed_sp_web_usuario_web_actividad_profesional_insertar(" . $_SESSION["met_user"]["id"] . "," . $valor . ",'" . $descripcion . "')");
        }

        //Delete existing work situation
        $db->callProcedure("CALL ed_sp_web_usuario_web_situacion_laboral_eliminar(" . $_SESSION["met_user"]["id"] . ")");

        //Insert new work situation
        $vectorSituacionLaboral = array_filter(explode(",", $_POST["hdnIdSituacionLaboral"]));
        foreach ($vectorSituacionLaboral as $valor) {
            $db->callProcedure("CALL ed_sp_web_usuario_web_situacion_laboral_insertar(" . $_SESSION["met_user"]["id"] . "," . $valor . ")");
        }

        $codigoError = 1;

        //Ir con cuidado
        //$_SESSION["met_user"]["username"] = $nombre." ".$apellidos;		
        $idUsuarioWebLogin = $_SESSION["met_user"]["id"];
        require_once "includes/load_upload_image_profile.inc.php";

        $db->endTransaction();
    }

    //If there is an error, redirect to the Edit profile page
    //The parameter c appended to the redirect URL indicates the error code
    generalUtils::redirigir("profile_edit.php?menu=" . $idMenu . "&c=" . $codigoError);
}

//If the form has not been submitted, build the form and populate it with the member's current details
//This can be a regular link to the page or a redirect link from form submission, containing an error code

//Construct the pretty URL of the current page, i.e. the Edit profile page of the currently logged-in member
//Doesn't seem to be used, unless it's for the paginator
$resultadoMenuSeo = $db->callProcedure("CALL ed_sp_web_menu_seo_obtener(" . $_GET["menu"] . "," . $_SESSION["id_idioma"] . ")");
$datoMenuSeo = $db->getData($resultadoMenuSeo);
$vectorAtributosMenu["idioma"] = $_SESSION["siglas"];
$vectorAtributosMenu["id_menu"] = $_GET["menu"];
$vectorAtributosMenu["seo_url"] = $datoMenuSeo["seo_url"];
$urlActual = generalUtils::generarUrlAmigableMenu($vectorAtributosMenu);

//Assign Edit profile explanatory text (from Easygestor) to placeholder
$subPlantilla->assign("CONTENIDO_DESCRIPCION", $datoMenuSeo["descripcion"]);

//If the page request URL contains an error parameter c (i.e. form has already been submitted)
//display appropriate error or success message
if (isset($_GET["c"]) && is_numeric($_GET["c"])) {
    $claseMensaje = "msgOK";
    $txtMensaje = "";
    switch ($_GET["c"]) {
        case 1:
            $txtMensaje = STATIC_FORM_PROFILE_MESSAGE_UPDATES_OK;
            break;
        case 2:
            $txtMensaje = STATIC_FORM_PROFILE_MESSAGE_CHANGE_PASS;
            break;
        case 3:
            $txtMensaje = STATIC_FORM_PROFILE_MESSAGE_ERROR_CHANGE_PASS;
            $claseMensaje = "msgKO";
            break;
    }
    //Assign message text and CSS class attributes to placeholders
    $subPlantilla->assign("MENSAJE_ACCION_CLASS", $claseMensaje);
    $subPlantilla->assign("MENSAJE_ACCION_PERFIL", $txtMensaje);
    $subPlantilla->assign("MENSAJE_ACCION_DISPLAY", "");
} else {
    $subPlantilla->assign("MENSAJE_ACCION_PERFIL", "");
    $subPlantilla->assign("MENSAJE_ACCION_CLASS", "msgKO");
    $subPlantilla->assign("MENSAJE_ACCION_DISPLAY", "display:none;");
}

$subPlantilla->assign("ID_MENU", $_GET["menu"]);

$idPais = -1;

/*
 * The profile_account_member.inc.html template contains:
 * the View profile button, the Make public checkbox
 * and the Change password fields (email-username can't be changed)
 */
$plantillaPerfilDatosUsuario = new XTemplate("html/includes/profile_account_member.inc.html");

//Get member's details from the database
$resultadoUsuarioIndividual = $db->callProcedure("CALL ed_sp_web_usuario_web_individual_obtener_concreto(" . $_SESSION["met_user"]["id"] . ")");
$datoUsuarioIndividual = $db->getData($resultadoUsuarioIndividual);

//Recommended size for profile photo
$subPlantilla->assign("RECOMMENDED_SIZE", STATIC_FORM_PROFILE_INDIVIDUAL_MEMBER_RECOMMENDED_SIZE);

//Assign empty values to form input variables
$nombreMiembro = "";
$apellidosMiembro = "";
$nacionalidadMiembro = "";
$calle1Miembro = "";
$calle2Miembro = "";
$ciudadMiembro = "";
$provinciaMiembro = "";
$codigoPostalMiembro = "";
$correoMiembro = "";
$telefonoCasaMiembro = "";
$correoAlternativoMiembro = "";
$telefonoTrabajoMiembro = "";
$telefonoMovilMiembro = "";
$faxMiembro = "";
$especificacionOtrosMiembro = "";
$especificacionEstudiosMiembro = "";
$cualificacionesMiembro = "";

//Member directory ID is a constant (8); procedure returns id_menu of members directory page
$resultadoMenuModulo = $db->callProcedure("CALL ed_sp_web_menu_modulo_obtener(" . MODULE_MEMBER_DIRECTORY_ID . ")");
$datoMenuModulo = $db->getData($resultadoMenuModulo);
$idMenuModulo = $datoMenuModulo["id_menu"];

//Use the id_menu to construct a link to the member's profile
$vectorAtributosDetalle["idioma"] = $_SESSION["siglas"];
$vectorAtributosDetalle["id_menu"] = $idMenuModulo;
$vectorAtributosDetalle["id_detalle"] = $_SESSION["met_user"]["id"];
$vectorAtributosDetalle["seo_url"] = $datoUsuarioIndividual["nombre"] . " " . $datoUsuarioIndividual["apellidos"];
//Assign link to placeholder in "profile_account_member" template
$plantillaPerfilDatosUsuario->assign("ENLACE_PERFIL", generalUtils::generarUrlAmigableDetalle($vectorAtributosDetalle));

//Where profile data already exist, assign existing values to form input variables
if ($datoUsuarioIndividual["nombre"] != "") {
    $nombreMiembro = $datoUsuarioIndividual["nombre"];
}
if ($datoUsuarioIndividual["apellidos"] != "") {
    $apellidosMiembro = $datoUsuarioIndividual["apellidos"];
}
if ($datoUsuarioIndividual["nacionalidad"] != "") {
    $nacionalidadMiembro = $datoUsuarioIndividual["nacionalidad"];
}
if ($datoUsuarioIndividual["direccion"] != "") {
    $calle1Miembro = $datoUsuarioIndividual["direccion"];
}
if ($datoUsuarioIndividual["direccion2"] != "") {
    $calle2Miembro = $datoUsuarioIndividual["direccion2"];
}
if ($datoUsuarioIndividual["ciudad"] != "") {
    $ciudadMiembro = $datoUsuarioIndividual["ciudad"];
}
if ($datoUsuarioIndividual["provincia"] != "") {
    $provinciaMiembro = $datoUsuarioIndividual["provincia"];
}
if ($datoUsuarioIndividual["codigo_postal"] != "") {
    $codigoPostalMiembro = $datoUsuarioIndividual["codigo_postal"];
}
if ($datoUsuarioIndividual["correo_electronico"] != "") {
    $correoMiembro = $datoUsuarioIndividual["correo_electronico"];
}
if ($datoUsuarioIndividual["telefono_casa"] != "") {
    $telefonoCasaMiembro = $datoUsuarioIndividual["telefono_casa"];
}
if ($datoUsuarioIndividual["correo_electronico_alternativo"] != "") {
    $correoAlternativoMiembro = $datoUsuarioIndividual["correo_electronico_alternativo"];
}
if ($datoUsuarioIndividual["telefono_trabajo"] != "") {
    $telefonoTrabajoMiembro = $datoUsuarioIndividual["telefono_trabajo"];
}
if ($datoUsuarioIndividual["telefono_movil"] != "") {
    $telefonoMovilMiembro = $datoUsuarioIndividual["telefono_movil"];
}
if ($datoUsuarioIndividual["fax"] != "") {
    $faxMiembro = $datoUsuarioIndividual["fax"];
}
if ($datoUsuarioIndividual["descripcion"] != "") {
    $sobreMetMiembro = $datoUsuarioIndividual["descripcion"];
}
if ($datoUsuarioIndividual["cualificaciones"] != "") {
    $cualificacionesMiembro = $datoUsuarioIndividual["cualificaciones"];
}

//Assign variables containing actual or default values to template placeholders
$subPlantilla->assign("MIEMBRO_NOMBRE", $nombreMiembro);
$subPlantilla->assign("MIEMBRO_APELLIDOS", $apellidosMiembro);
$subPlantilla->assign("MIEMBRO_NACIONALIDAD", $nacionalidadMiembro);
$subPlantilla->assign("MIEMBRO_CALLE1", $calle1Miembro);
$subPlantilla->assign("MIEMBRO_CALLE2", $calle2Miembro);
$subPlantilla->assign("MIEMBRO_CIUDAD", $ciudadMiembro);
$subPlantilla->assign("MIEMBRO_PROVINCIA", $provinciaMiembro);
$subPlantilla->assign("MIEMBRO_CODIGO_POSTAL", $codigoPostalMiembro);
$subPlantilla->assign("MIEMBRO_CORREO_ELECTRONICO", $correoMiembro);
$subPlantilla->assign("MIEMBRO_TELEFONO_CASA", $telefonoCasaMiembro);
$subPlantilla->assign("MIEMBRO_CORREO_ELECTRONICO_ALTERNATIVO", $correoAlternativoMiembro);
$subPlantilla->assign("MIEMBRO_TELEFONO", $telefonoTrabajoMiembro);
$subPlantilla->assign("MIEMBRO_TELEFONO_MOVIL", $telefonoMovilMiembro);
$subPlantilla->assign("MIEMBRO_FAX", $faxMiembro);
$subPlantilla->assign("MIEMBRO_PROFESION_CUALIFICACION", $cualificacionesMiembro);
$subPlantilla->assign("MIEMBRO_ESPECIFICACION_ESTUDIOS", $especificacionEstudiosMiembro);
$subPlantilla->assign("MIEMBRO_ESPECIFICACION_OTROS", $especificacionOtrosMiembro);

//Get list of professional activities
$resulActividadesProfesionales = $db->callProcedure("CALL ed_sp_web_usuario_web_actividad_profesional_obtener_listado(" . $_SESSION["met_user"]["id"] . "," . $_SESSION["id_idioma"] . ")");
//Obsolete
//$validacion = "";
//For each professional activity, assign values (name for reference, database ID and display name) to placeholders
while ($dataActividadProfesional = $db->getData($resulActividadesProfesionales)) {
    $nombreElemento = str_replace("-", "", $dataActividadProfesional["actividad_profesional"]);
    $subPlantilla->assign("ITEM_ACTIVIDAD_PROFESIONAL_ID", $dataActividadProfesional["id_actividad_profesional"]);
    $subPlantilla->assign("ITEM_ACTIVIDAD_PROFESIONAL_NOMBRE", $dataActividadProfesional["actividad_profesional"]);
    $subPlantilla->assign("ITEM_ACTIVIDAD_PROFESIONAL_NOMBRE_ELEMENTO", $nombreElemento);
    //If the member has the current activity, make the checkbox checked
    if ($dataActividadProfesional["id_usuario_web"] != "") {
        $subPlantilla->assign("CHECKED_ACTIVIDAD_PROFESIONAL", "checked");

        //If the current activity is "Student" (and is in the member's profile)
        if ($dataActividadProfesional["id_actividad_profesional"] == "7") {
            //Assign the description of subjects, etc. (if any) to a placeholder
            if ($dataActividadProfesional["descripcion"] != "") {
                $subPlantilla->assign("MIEMBRO_ESPECIFICACION_ESTUDIOS", $dataActividadProfesional["descripcion"]);
            }
            //If the current activity is "Other" (and is in the member's profile)
        } else if ($dataActividadProfesional["id_actividad_profesional"] == "8") {
            //Assign the description of the other activity (if any) to a placeholder
            if ($dataActividadProfesional["descripcion"] != "") {
                $subPlantilla->assign("MIEMBRO_ESPECIFICACION_OTROS", $dataActividadProfesional["descripcion"]);
            }
        }
        //If the member doesn't have the current activity, leave "checked" placeholder empty
    } else {
        $subPlantilla->assign("CHECKED_ACTIVIDAD_PROFESIONAL", "");
    }

    $subPlantilla->parse("contenido_principal.item_actividad_profesional");
}

//Obsolete
//$plantilla->assign("VALIDACION_CHECKS", substr($validacion, 0, -3));

//Assign sex variable to placeholder
//$subPlantilla->assign("CHECKED_SEXO_" . $datoUsuarioIndividual["es_hombre"], "checked");


//Get a list of work situations (freelance, in-house, student/retired)
$resultadoSituacionLaboral = $db->callProcedure("CALL ed_sp_web_usuario_web_situacion_laboral_obtener_listado(" . $_SESSION["met_user"]["id"] . "," . $_SESSION["id_idioma"] . ")");
//For each work situation, assign values (database ID and name) to placeholders
while ($dataSituacionLaboral = $db->getData($resultadoSituacionLaboral)) {
    $subPlantilla->assign("SITUACION_LABORAL_ID", $dataSituacionLaboral["id_situacion_laboral"]);
    $subPlantilla->assign("SITUACION_LABORAL_NOMBRE", $dataSituacionLaboral["situacion_laboral"]);
    //If the member has the current work situation, make the checkbox checked
    if ($dataSituacionLaboral["id_usuario_web"] != "") {
        $subPlantilla->assign("CHECKED_SITUACION_LABORAL", "checked");
    } else {
        $subPlantilla->assign("CHECKED_SITUACION_LABORAL", "");
    }
    $subPlantilla->parse("contenido_principal.item_situacion_laboral");
}

//Generate title (Mr/Ms/Dr/Prof) dropdown and assign to a placeholder
//$subPlantilla->assign("COMBO_TITULOS", generalUtils::construirCombo($db, "CALL ed_sp_web_tratamiento_usuario_web_obtener_combo(" . $_SESSION["id_idioma"] . ")", "cmbTitulo", "cmbTitulo", $datoUsuarioIndividual["id_tratamiento_usuario_web"], "nombre", "id_tratamiento_usuario_web", STATIC_FORM_MEMBERSHIP_TITLE, -1, 'class="form-control"'));

//Generate age dropdown and assign to a placeholder
$subPlantilla->assign("COMBO_ANYOS", generalUtils::construirCombo($db, "CALL ed_sp_web_edad_usuario_web_obtener_combo(" . $_SESSION["id_idioma"] . ")", "cmbAnyos", "cmbAnyos", $datoUsuarioIndividual["id_edad_usuario_web"], "nombre", "id_edad_usuario_web", STATIC_FORM_MEMBERSHIP_AGE, -1, 'class="form-control"'));

//Generate country dropdown and assign to a placeholder
$subPlantilla->assign("COMBO_PAIS", generalUtils::construirCombo($db,
    "CALL ed_sp_web_pais_obtener_combo()",
    "cmbPais",
    "cmbPais",
    $datoUsuarioIndividual["id_pais"],
    "nombre_original",
    "id_pais",
    STATIC_FORM_MEMBERSHIP_COUNTRY_OF_RESIDENCE . "*",
    -1,
    "class='form-control' style='color:slategray;' autocomplete='country-name'"));

//Assign IDs and WYSIWYG editor to text fields
$plantilla->assign("TEXTAREA_ID", "txtOtherCPD");
$plantilla->assign("TEXTAREA_TOOLBAR", "Minimo");
$plantilla->parse("contenido_principal.bloque_ready.inicializar_ckeditor");

$plantilla->assign("TEXTAREA_ID", "txtOtrasDescripciones");
$plantilla->assign("TEXTAREA_TOOLBAR", "Minimo");
$plantilla->parse("contenido_principal.bloque_ready.inicializar_ckeditor");

$plantilla->assign("TEXTAREA_ID", "txtOtrasPublicaciones");
$plantilla->assign("TEXTAREA_TOOLBAR", "Minimo");
$plantilla->parse("contenido_principal.bloque_ready.inicializar_ckeditor");

//Generate source language dropdown and assign to a placeholder
$subPlantilla->assign("COMBO_SOURCE_LANGUAGES", generalUtils::construirMultiCombo($db, "CALL ed_sp_get_working_languages(" . $_SESSION["id_idioma"] . ")", "CALL ed_sp_get_member_profile_source_languages(" . $_SESSION["id_idioma"] . "," . $_SESSION["met_user"]["id"] . ")", "nombre", "nombre", "cmbSource", "cmbSource", "nombre", "id_working_language", STATIC_FORM_PROFILE_SOURCE_LANGS, -1, 'class="form-control multiSelect"'));

//Generate target language dropdown and assign to a placeholder
$subPlantilla->assign("COMBO_TARGET_LANGUAGES", generalUtils::construirMultiCombo($db, "CALL ed_sp_get_working_languages(" . $_SESSION["id_idioma"] . ")", "CALL ed_sp_get_member_profile_target_languages(" . $_SESSION["id_idioma"] . "," . $_SESSION["met_user"]["id"] . ")", "nombre", "nombre", "cmbTarget", "cmbTarget", "nombre", "id_working_language", STATIC_FORM_PROFILE_TARGET_LANGS, -1, 'class="form-control multiSelect"'));

//Generate areas of expertise dropdown and assign to a placeholder
$subPlantilla->assign("COMBO_AREAS_OF_EXPERTISE", generalUtils::construirMultiCombo($db, "CALL ed_sp_get_areas_of_expertise(" . $_SESSION["id_idioma"] . ")", "CALL ed_sp_get_member_profile_areas_of_expertise(" . $_SESSION["id_idioma"] . "," . $_SESSION["met_user"]["id"] . ")", "nombre", "nombre", "cmbAreas", "cmbAreas", "nombre", "id_area", STATIC_FORM_PROFILE_AREAS_OF_EXPERTISE, -1, 'class="form-control multiSelect"'));

//Fetch the member's details from the database
$resultadoUsuarioWeb = $db->callProcedure("CALL ed_sp_web_usuario_web_obtener_concreto(" . $_SESSION["met_user"]["id"] . ")");
$datoUsuarioWeb = $db->getData($resultadoUsuarioWeb);

$plantillaPerfilMasInformacionUsuario = new XTemplate("html/includes/profile_other_CPD.inc.html");

//Assign "Website" label to placeholder
$subPlantilla->assign("PERFIL_VALOR_WEB", STATIC_FORM_PROFILE_CHANGE_WEB);

if ($datoUsuarioWeb["imagen"] != "") {
    //$imagen_miembro = "files/members/thumb/" . $datoUsuarioWeb["imagen"];
    $subPlantilla->assign("PERFIL_IMAGEN_ACTUAL_NOMBRE", $datoUsuarioWeb["imagen"]);
    $subPlantilla->assign("PERFIL_IMAGEN_NOMBRE", $datoUsuarioWeb["imagen"]);
    $subPlantilla->parse("contenido_principal.imagen_controles");
    $plantilla->parse("contenido_principal.script_colorbox");
} else {
    //$imagen_miembro = "files/members/default.jpg";
    $subPlantilla->assign("PROFILE_DELETE_PHOTO_ICON_CLASS", "d-none");
    $subPlantilla->assign("PERFIL_IMAGEN_NOMBRE", "default.jpg");
    $subPlantilla->parse("contenido_principal.imagen_controles");
    $plantilla->parse("contenido_principal.script_colorbox");
}

//Assign "public" or "non-public" flag to placeholder
$plantillaPerfilDatosUsuario->assign("PERFIL_VALOR_PUBLICO", ($datoUsuarioWeb["publico"] == 1) ? "checked" : "");

//Parse the "profile_account_member" template
$plantillaPerfilDatosUsuario->parse("contenido_principal");

//Export the "profile_account_member" template to the "profile_individual_member" subtemplate
$subPlantilla->assign("PERFIL_DATOS_USUARIO", $plantillaPerfilDatosUsuario->text("contenido_principal"));

//If the member has data in the text fields (Description, Publications, Other CPD), assign the text content to placeholders
if ($datoUsuarioWeb["otros"] != "") {
    $plantillaPerfilMasInformacionUsuario->assign("PROFILE_OTHER_CPD", $datoUsuarioWeb["otros"]);
}
if ($datoUsuarioWeb["descripcion"] != "") {
    $subPlantilla->assign("PERFIL_VALOR_OTRAS_DESCRIPCION", $datoUsuarioWeb["descripcion"]);
}
if ($datoUsuarioWeb["publicaciones"] != "") {
    $subPlantilla->assign("PERFIL_VALOR_OTRAS_PUBLICACIONES", $datoUsuarioWeb["publicaciones"]);
}
//If the member has data for website or a profile photo, assign to placeholders
if ($datoUsuarioWeb["web"] != "") {
    $subPlantilla->assign("PERFIL_VALOR_WEB", $datoUsuarioWeb["web"]);
}
if ($datoUsuarioWeb["imagen"] != "") {
    $imagen_miembro = "files/members/thumb/" . $datoUsuarioWeb["imagen"];
} else {
    $imagen_miembro = "files/members/default.jpg";
}
$subPlantilla->assign("PERFIL_IMAGEN_NOMBRE", $imagen_miembro);

//Parse the validation script in index.html
$plantilla->parse("contenido_principal.validate_edit_profile");

//Parse the "profile_other_CPD" template
$plantillaPerfilMasInformacionUsuario->parse("contenido_principal");

//Export the "profile_other_CPD" template to the "profile_individual_member" subtemplate
$subPlantilla->assign("PROFILE_OTHER_CPD", $plantillaPerfilMasInformacionUsuario->text("contenido_principal"));

//Billing details have their own template, "profile_billing_member"
$plantillaPerfilFacturacionUsuario = new XTemplate("html/includes/profile_billing_member.inc.html");
//Assign empty values to billing variables
$plantillaPerfilFacturacionUsuario->assign("PERFIL_NIF_CLIENTE", "");
$plantillaPerfilFacturacionUsuario->assign("PERFIL_NOMBRE_CLIENTE", "");
$plantillaPerfilFacturacionUsuario->assign("PERFIL_NOMBRE_EMPRESA", "");
$plantillaPerfilFacturacionUsuario->assign("PERFIL_DIRECCION", "");
$plantillaPerfilFacturacionUsuario->assign("PERFIL_CODIGO_POSTAL", "");
$plantillaPerfilFacturacionUsuario->assign("PERFIL_CIUDAD", "");
$plantillaPerfilFacturacionUsuario->assign("PERFIL_PROVINCIA", "");
$plantillaPerfilFacturacionUsuario->assign("PERFIL_PAIS", "");

//If the member has billing details in the database, insert them into the placeholders
if ($datoUsuarioWeb["nif_cliente_factura"] != "") {
    $plantillaPerfilFacturacionUsuario->assign("PERFIL_NIF_CLIENTE", $datoUsuarioWeb["nif_cliente_factura"]);
}
if ($datoUsuarioWeb["nombre_cliente_factura"] != "") {
    $plantillaPerfilFacturacionUsuario->assign("PERFIL_NOMBRE_CLIENTE", $datoUsuarioWeb["nombre_cliente_factura"]);
}
if ($datoUsuarioWeb["nombre_empresa_factura"] != "") {
    $plantillaPerfilFacturacionUsuario->assign("PERFIL_NOMBRE_EMPRESA", $datoUsuarioWeb["nombre_empresa_factura"]);
}
if ($datoUsuarioWeb["direccion_factura"] != "") {
    $plantillaPerfilFacturacionUsuario->assign("PERFIL_DIRECCION", $datoUsuarioWeb["direccion_factura"]);
}
if ($datoUsuarioWeb["codigo_postal_factura"] != "") {
    $plantillaPerfilFacturacionUsuario->assign("PERFIL_CODIGO_POSTAL", $datoUsuarioWeb["codigo_postal_factura"]);
}
if ($datoUsuarioWeb["ciudad_factura"] != "") {
    $plantillaPerfilFacturacionUsuario->assign("PERFIL_CIUDAD", $datoUsuarioWeb["ciudad_factura"]);
}
if ($datoUsuarioWeb["provincia_factura"] != "") {
    $plantillaPerfilFacturacionUsuario->assign("PERFIL_PROVINCIA", $datoUsuarioWeb["provincia_factura"]);
}
if ($datoUsuarioWeb["pais_factura"] != "") {
    $plantillaPerfilFacturacionUsuario->assign("PERFIL_PAIS", $datoUsuarioWeb["pais_factura"]);
}

//Parse the billing details template
$plantillaPerfilFacturacionUsuario->parse("contenido_principal");

//Export the billing details template to the "profile_individual_member" subtemplate
$subPlantilla->assign("PERFIL_FACTURACION", $plantillaPerfilFacturacionUsuario->text("contenido_principal"));

/*
 * Get membership history and assign data to placeholders
 */
$resultadoInscripcion = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_inscripcion_usuario_web_historial(" . $_SESSION["met_user"]["id"] . "," . $_SESSION["id_idioma"] . ")");
while ($datoInscripcion = $db->getData($resultadoInscripcion)) {
    if ($datoInscripcion["pagado"] == 1) {
        $fechaStart = explode(" ", $datoInscripcion["fecha_inscripcion"]);
        $subPlantilla->assign("INSCRIPTIONS_HISTORY_ID", $datoInscripcion["id_inscripcion"]);
        $subPlantilla->assign("INSCRIPTIONS_HISTORY_AMOUNT", $datoInscripcion["importe"]);
        $subPlantilla->assign("INSCRIPTIONS_HISTORY_NUMBER", $datoInscripcion["numero_inscripcion"]);
        $subPlantilla->assign("INSCRIPTIONS_HISTORY_START_DATE", generalUtils::conversionFechaFormato($fechaStart[0], "-", "-"));
      
        $subPlantilla->assign("INSCRIPTIONS_HISTORY_END_DATE", generalUtils::conversionFechaFormato($datoInscripcion["fecha_finalizacion"], "-", "-"));
        $subPlantilla->assign("INSCRIPTIONS_HISTORY_PAYMENT_TYPE", $datoInscripcion["tipo_pago"]);

        $subPlantilla->parse("contenido_principal.item_inscripcion");
    }
}

//Loan the breadcrumbs
require "includes/load_breadcrumb.inc.php";

//Load the lefthand menu
require "includes/load_menu_left.inc.php";

//Parse the "profile_individual_member" subtemplate
$subPlantilla->parse("contenido_principal");

//Parse all the other required items
//Multiselect script, WYSIWYG editor script, stylesheet for forms, top menu and other scripts
$plantilla->parse("contenido_principal.editor_script");
$plantilla->parse("contenido_principal.css_form");
$plantilla->parse("contenido_principal.control_superior");
$plantilla->parse("contenido_principal.bloque_ready");
$plantilla->parse("contenido_principal.script_multiselect");
$plantilla->parse("contenido_principal.placeholders_script");

//Export subtemplate to the main index.html template
$plantilla->assign("CONTENIDO", $subPlantilla->text("contenido_principal"));

//Parse the "lefthand menu" block of the main template
$plantilla->parse("contenido_principal.menu_left");

//Parse the main template and output to screen
$plantilla->parse("contenido_principal");
$plantilla->out("contenido_principal");
?>