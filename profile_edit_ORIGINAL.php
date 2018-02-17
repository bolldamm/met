<?php

/**
 *
 * Pagina openbox de contenido libre
 * @author Edata S.L.
 * @copyright Edata S.L.
 * @version 1.0
 */
require "includes/load_main_components.inc.php";

$validarIntegridad = true;
//require "includes/load_validate_user_web.inc.php";
// Instanciamos la clase Xtemplate con la plantilla base
$plantilla = new XTemplate("html/index.html");
if ($_SESSION["met_user"]["tipoUsuario"] == 4) {
    generalUtils::redirigir(CURRENT_DOMAIN);
} else {
    // Instanciamos la clase Xtemplate con la plantilla que va a contener la información del apartado
    if ($_SESSION["met_user"]["id_modalidad"] == MODALIDAD_USUARIO_INDIVIDUAL) {
        $subPlantilla = new XTemplate("html/profile_individual_member.html");
    } else if ($_SESSION["met_user"]["id_modalidad"] == MODALIDAD_USUARIO_INSTITUTIONAL) {
        $subPlantilla = new XTemplate("html/profile_institutional_member.html");
    } else {
        exit();
    }
}

/**
 * Asignamos el CSS que corresponde a este apartado
 */
$plantilla->assign("SECTION_FILE_CSS", "openbox.css");


require "includes/load_structure.inc.php";


if (count($_POST) > 0) {
    //Validamos si la contraseña ha sido modificada
    $claveActual = trim(generalUtils::escaparCadena($_POST["txtCurrentPassword"]));
    $claveNueva = trim(generalUtils::escaparCadena($_POST["txtPassword"]));
    $idMenu = $_POST["hdnIdMenu"];
    $blError = true;


    $db->startTransaction();

    if ($claveActual != "") {
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

    if ($blError) {
        if (isset($_POST["chkPublico"])) {
            $publico = 1;
        } else {
            $publico = 0;
        }

        $_POST["txtOtrasActividades"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_OTHER, $_POST["txtOtrasActividades"]));
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

        //Actualizamos datos generales
        $resultadoUsuario = $db->callProcedure("CALL ed_sp_web_usuario_web_editar(" . $_SESSION["met_user"]["id"] . "," . $publico . ",'" . $_POST["txtOtrasActividades"] . "','" . $_POST["txtOtrasDescripciones"] . "','" . $_POST["txtOtrasPublicaciones"] . "','" . $_POST["txtWeb"] . "','" . $_POST["txtFacturacionNifCliente"] . "','" . $_POST["txtFacturacionNombreCliente"] . "','" . $_POST["txtFacturacionNombreEmpresa"] . "','" . $_POST["txtFacturacionDireccion"] . "','" . $_POST["txtFacturacionCodigoPostal"] . "','" . $_POST["txtFacturacionCiudad"] . "','" . $_POST["txtFacturacionProvincia"] . "','" . $_POST["txtFacturacionPais"] . "')");

        //Delete working languages from profile
        $db->callProcedure("CALL ed_sp_web_usuario_web_working_languages_eliminar(" . $_SESSION["met_user"]["id"] . ")");

        //Insert selected working languages
        if($_POST['cmbSource'] != '' && $_POST['cmbTarget'] != '') {
            $sourceLangs = array();
            $targetLangs = array();
            $idLangsAll = array();
            $idLangs = array();
            $sourceLangs = $_POST['cmbSource'];
            $targetLangs = $_POST['cmbTarget'];
            $idLangsAll = array_merge($sourceLangs,$targetLangs);
            $idLangs = array_unique($idLangsAll);
            foreach($idLangs as $idLang) {
                if(in_array($idLang, $sourceLangs)) {
                    $isSource = '1';
                }
                else {
                    $isSource = '0';
                }
                if(in_array($idLang, $targetLangs)) {
                    $isTarget = '1';
                }
                else {
                    $isTarget = '0';
                }
                $db->callProcedure("CALL ed_sp_web_usuario_web_working_languages_insertar(" . $_SESSION["met_user"]["id"] . ",'" . $idLang . "','" . $isSource . "','" . $isTarget . "')");
            }
        }

        //Delete all previously selected areas of expertise from profile
        $db->callProcedure("CALL ed_sp_web_usuario_web_areas_of_expertise_eliminar(" . $_SESSION["met_user"]["id"] . ")");
        //Insert newly selected areas of expertise
        if($_POST['cmbAreas'] != '') {
            $areas = $_POST['cmbAreas'];
            foreach($areas as $area){
                $db->callProcedure("CALL ed_sp_web_usuario_web_areas_of_expertise_insertar(" . $_SESSION["met_user"]["id"] . ",'" . $area . "')");
            }
        } else {}

        if ($_POST["cmbPais"] == -1) {
            $_POST["cmbPais"] = "null";
        }

        if ($_POST["cmbTitulo"] == -1) {
            $_POST["cmbTitulo"] = "null";
        }

        //Si somos usuarios individuales...
        if ($_SESSION["met_user"]["id_modalidad"] == MODALIDAD_USUARIO_INDIVIDUAL) {
            if ($_POST["chkSex"] == 1) {
                $esHombre = 1;
            } else {
                $esHombre = 0;
            }

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

            //Previous version (removed $_POST["txtSobreMet"] and deleted parameter "descripcion_p" from procedure)
//            $resultadoUsuarioWebIndividual = $db->callProcedure("CALL ed_sp_web_usuario_web_individual_editar(" . $_SESSION["met_user"]["id"] . "," . $_POST["cmbTitulo"] . "," . $_POST["cmbAnyos"] . "," . $_POST["cmbPais"] . ",'" . $_POST["txtNombre"] . "','" . $_POST["txtApellidos"] . "','" . $_POST["txtNacionalidad"] . "','" . $_POST["txtDireccion1"] . "','" . $_POST["txtDireccion2"] . "','" . $_POST["txtCiudad"] . "','" . $_POST["txtProvincia"] . "','" . $_POST["txtCp"] . "','" . $_POST["txtEmail"] . "','" . $_POST["txtEmailAlternativo"] . "','" . $_POST["txtTelefonoCasa"] . "','" . $_POST["txtTelefonoTrabajo"] . "','" . $_POST["txtFax"] . "','" . $_POST["txtTelefonoMobil"] . "','" . $esHombre . "','" . $_POST["txtSobreMet"] . "','" . $_POST["txtProfesionQualificacion"] . "')");


            //Eliminamos actividades web actuales
            $db->callProcedure("CALL ed_sp_web_usuario_web_actividad_profesional_eliminar(" . $_SESSION["met_user"]["id"] . ")");

            //Insertamos actividades web
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

            //Eliminamos situacion laboral actual
            $db->callProcedure("CALL ed_sp_web_usuario_web_situacion_laboral_eliminar(" . $_SESSION["met_user"]["id"] . ")");

            //Situacion laboral
            $vectorSituacionLaboral = array_filter(explode(",", $_POST["hdnIdSituacionLaboral"]));
            foreach ($vectorSituacionLaboral as $valor) {
                $db->callProcedure("CALL ed_sp_web_usuario_web_situacion_laboral_insertar(" . $_SESSION["met_user"]["id"] . "," . $valor . ")");
            }

            $codigoError = 1;
        } else if ($_SESSION["met_user"]["id_modalidad"] == MODALIDAD_USUARIO_INSTITUTIONAL) {
            $_POST["txtNombreInstitucion"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_INSTIT_NAME_INSTITUTION, $_POST["txtNombreInstitucion"]));
            $_POST["txtDepartamento"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_INSTIT_DEPARTMENT_IF_APPLICABLE, $_POST["txtDepartamento"]));
            $_POST["txtDireccion1"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_STREET_1, $_POST["txtDireccion1"]));
            $_POST["txtDireccion2"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_STREET_2, $_POST["txtDireccion2"]));
            $_POST["txtCp"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_POSTCODE, $_POST["txtCp"]));
            $_POST["txtCiudad"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_TOWN_CITY, $_POST["txtCiudad"]));
            $_POST["txtProvincia"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_PROVINCE, $_POST["txtProvincia"]));
            $_POST["txtTelefono"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_INSTIT_PHONE_NO, $_POST["txtTelefono"]));
            $_POST["txtFax"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_INSTIT_FAX_NO, $_POST["txtFax"]));
            $_POST["txtEmail"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_INSTIT_EMAIL_ADDRESS, $_POST["txtEmail"]));
            $_POST["txtNombre"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_FIRST_NAME, $_POST["txtNombre"]));
            $_POST["txtApellidos"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_LAST_NAMES, $_POST["txtApellidos"]));
            $_POST["txtEstado"] = "";
            //$_POST["txtEstado"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_INSTIT_IF_OTHER_PLEASE_STATE,$_POST["txtEstado"]));
            $_POST["txtEmailUsuario"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_INSTIT_EMAIL_TO_USER, $_POST["txtEmailUsuario"]));
            $_POST["txtEmailUsuarioAlternativo"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_INSTIT_ALTERNATIVE_EMAIL, $_POST["txtEmailUsuarioAlternativo"]));
            $_POST["txtTelefonoTrabajo"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_WORK_PHONE, $_POST["txtTelefonoTrabajo"]));
            $_POST["txtTelefonoMovil"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_MOBILE_PHONE, $_POST["txtTelefonoMovil"]));

            //Editamos perfil institucion
            $resultadoUsuarioWebInstitucion = $db->callProcedure("CALL ed_sp_web_usuario_web_institucion_editar(" . $_SESSION["met_user"]["id"] . "," . $_POST["cmbTitulo"] . "," . $_POST["cmbPais"] . ",'" . $_POST["txtNombreInstitucion"] . "','" . $_POST["txtDepartamento"] . "','" . $_POST["txtDireccion1"] . "','" . $_POST["txtDireccion2"] . "','" . $_POST["txtCp"] . "','" . $_POST["txtCiudad"] . "','" . $_POST["txtProvincia"] . "','" . $_POST["txtTelefono"] . "','" . $_POST["txtFax"] . "','" . $_POST["txtEmail"] . "','" . $_POST["txtNombre"] . "','" . $_POST["txtApellidos"] . "','" . $_POST["txtEstado"] . "','" . $_POST["txtEmailUsuario"] . "','" . $_POST["txtEmailUsuarioAlternativo"] . "','" . $_POST["txtTelefonoTrabajo"] . "','" . $_POST["txtTelefonoMovil"] . "')");
            $codigoError = 1;
        }

        //Ir con cuidado
        //$_SESSION["met_user"]["username"] = $nombre." ".$apellidos;
        $idUsuarioWebLogin = $_SESSION["met_user"]["id"];
        require_once "includes/load_upload_image_profile.inc.php";


        $db->endTransaction();
    }

    //This line was commented out, giving an "undefined index" on $_GET["menu"] below
    generalUtils::redirigir("profile_edit.php?menu=" . $idMenu . "&c=" . $codigoError);
}

//Obtenemos la url asociada a perfil
$resultadoMenuSeo = $db->callProcedure("CALL ed_sp_web_menu_seo_obtener(" . $_GET["menu"] . "," . $_SESSION["id_idioma"] . ")");
$datoMenuSeo = $db->getData($resultadoMenuSeo);
$vectorAtributosMenu["idioma"] = $_SESSION["siglas"];
$vectorAtributosMenu["id_menu"] = $_GET["menu"];
$vectorAtributosMenu["seo_url"] = $datoMenuSeo["seo_url"];
$urlActual = generalUtils::generarUrlAmigableMenu($vectorAtributosMenu);

$subPlantilla->assign("CONTENIDO_DESCRIPCION", $datoMenuSeo["descripcion"]);

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


/* Primero de todo incorporamos los datos account(en principio no dejaremos cambiar el correo con el que se loguean asi que solo mostraremos
 * el cambiar contraseña
 */
$plantillaPerfilDatosUsuario = new XTemplate("html/includes/profile_account_member.inc.html");




//Si soy un usuario individual, llamamos a este procedure
if ($_SESSION["met_user"]["id_modalidad"] == MODALIDAD_USUARIO_INDIVIDUAL) {
    $resultadoUsuarioIndividual = $db->callProcedure("CALL ed_sp_web_usuario_web_individual_obtener_concreto(" . $_SESSION["met_user"]["id"] . ")");
    $datoUsuarioIndividual = $db->getData($resultadoUsuarioIndividual);

    //Recommended size
    $subPlantilla->assign("RECOMMENDED_SIZE", STATIC_FORM_PROFILE_INDIVIDUAL_MEMBER_RECOMMENDED_SIZE);

    //Valores defecto...
    $nombreMiembro = STATIC_FORM_MEMBERSHIP_FIRST_NAME;
    $apellidosMiembro = STATIC_FORM_MEMBERSHIP_LAST_NAMES;
    $nacionalidadMiembro = STATIC_FORM_MEMBERSHIP_NATIONALITY;
    $calle1Miembro = STATIC_FORM_MEMBERSHIP_STREET_1;
    $calle2Miembro = STATIC_FORM_MEMBERSHIP_STREET_2;
    $ciudadMiembro = STATIC_FORM_MEMBERSHIP_TOWN_CITY;
    $provinciaMiembro = STATIC_FORM_MEMBERSHIP_PROVINCE;
    $codigoPostalMiembro = STATIC_FORM_MEMBERSHIP_POSTCODE;
    $correoMiembro = STATIC_FORM_MEMBERSHIP_EMAIL;
    $telefonoCasaMiembro = STATIC_FORM_MEMBERSHIP_HOME_PHONE;
    $correoAlternativoMiembro = STATIC_FORM_MEMBERSHIP_ALTERNATIVE_EMAIL;
    $telefonoTrabajoMiembro = STATIC_FORM_MEMBERSHIP_WORK_PHONE;
    $telefonoMovilMiembro = STATIC_FORM_MEMBERSHIP_MOBILE_PHONE;
    $faxMiembro = STATIC_FORM_MEMBERSHIP_FAX;
    $especificacionOtrosMiembro = STATIC_FORM_MEMBERSHIP_IF_OTHER_SPECIFY;
    $especificacionEstudiosMiembro = STATIC_FORM_MEMBERSHIP_IF_STUDENT_SUBJECT;
    $sobreMetMiembro = STATIC_FORM_MEMBERSHIP_HOW_DID_YOU_HEAR_ABOUT_MET;
    $cualificacionesMiembro = STATIC_FORM_MEMBERSHIP_DEGREES_QUALIFICATIONS;

    //Cogemos actualidad

    $resultadoMenuModulo = $db->callProcedure("CALL ed_sp_web_menu_modulo_obtener(" . MODULE_MEMBER_DIRECTORY_ID . ")");
    $datoMenuModulo = $db->getData($resultadoMenuModulo);
    $idMenuModulo = $datoMenuModulo["id_menu"];

    //Enlace a mi perfil
    $vectorAtributosDetalle["idioma"] = $_SESSION["siglas"];
    $vectorAtributosDetalle["id_menu"] = $idMenuModulo;
    $vectorAtributosDetalle["id_detalle"] = $_SESSION["met_user"]["id"];
    $vectorAtributosDetalle["seo_url"] = $datoUsuarioIndividual["nombre"] . " " . $datoUsuarioIndividual["apellidos"];


    $plantillaPerfilDatosUsuario->assign("ENLACE_PERFIL", generalUtils::generarUrlAmigableDetalle($vectorAtributosDetalle));

    //Si tienen valor, sobreescribimos el 'placeholder/value' que hemos puesto en el paso anterior
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


    //Asignamos estos valores
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
    $subPlantilla->assign("MIEMBRO_SOBRE_MET", $sobreMetMiembro);
    $subPlantilla->assign("MIEMBRO_PROFESION_CUALIFICACION", $cualificacionesMiembro);
    $subPlantilla->assign("MIEMBRO_ESPECIFICACION_ESTUDIOS", $especificacionEstudiosMiembro);
    $subPlantilla->assign("MIEMBRO_ESPECIFICACION_OTROS", $especificacionOtrosMiembro);


    //Listado de actividades profesionales
    $resulActividadesProfesionales = $db->callProcedure("CALL ed_sp_web_usuario_web_actividad_profesional_obtener_listado(" . $_SESSION["met_user"]["id"] . "," . $_SESSION["id_idioma"] . ")");
    $validacion = "";
    while ($dataActividadProfesional = $db->getData($resulActividadesProfesionales)) {
        $nombreElemento = str_replace("-", "", $dataActividadProfesional["actividad_profesional"]);
        $subPlantilla->assign("ITEM_ACTIVIDAD_PROFESIONAL_ID", $dataActividadProfesional["id_actividad_profesional"]);
        $subPlantilla->assign("ITEM_ACTIVIDAD_PROFESIONAL_NOMBRE", $dataActividadProfesional["actividad_profesional"]);
        $subPlantilla->assign("ITEM_ACTIVIDAD_PROFESIONAL_NOMBRE_ELEMENTO", $nombreElemento);
        if ($dataActividadProfesional["id_usuario_web"] != "") {
            $subPlantilla->assign("CHECKED_ACTIVIDAD_PROFESIONAL", "checked");

            if ($dataActividadProfesional["id_actividad_profesional"] == "7") {
                //Estudios
                if ($dataActividadProfesional["descripcion"] != "") {
                    $subPlantilla->assign("MIEMBRO_ESPECIFICACION_ESTUDIOS", $dataActividadProfesional["descripcion"]);
                }
            } else if ($dataActividadProfesional["id_actividad_profesional"] == "8") {
                //Otros...
                if ($dataActividadProfesional["descripcion"] != "") {
                    $subPlantilla->assign("MIEMBRO_ESPECIFICACION_OTROS", $dataActividadProfesional["descripcion"]);
                }
            }
        } else {
            $subPlantilla->assign("CHECKED_ACTIVIDAD_PROFESIONAL", "");
        }
        $validacion .= "!document.frmMembership.chk" . $nombreElemento . ".checked && ";

        $subPlantilla->parse("contenido_principal.item_actividad_profesional");
    }

    $plantilla->assign("VALIDACION_CHECKS", substr($validacion, 0, -3));


    //Checkeamos sexo
    $subPlantilla->assign("CHECKED_SEXO_" . $datoUsuarioIndividual["es_hombre"], "checked");


    //Listamos situaciones laborales
    $resultadoSituacionLaboral = $db->callProcedure("CALL ed_sp_web_usuario_web_situacion_laboral_obtener_listado(" . $_SESSION["met_user"]["id"] . "," . $_SESSION["id_idioma"] . ")");
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

    //Combo tratamiento
    $subPlantilla->assign("COMBO_TITULOS", generalUtils::construirCombo($db, "CALL ed_sp_web_tratamiento_usuario_web_obtener_combo(" . $_SESSION["id_idioma"] . ")", "cmbTitulo", "cmbTitulo", $datoUsuarioIndividual["id_tratamiento_usuario_web"], "nombre", "id_tratamiento_usuario_web", STATIC_FORM_MEMBERSHIP_TITLE, -1, 'class="inputText left" style="width:63px;"'));

    //Combo años
    $subPlantilla->assign("COMBO_ANYOS", generalUtils::construirCombo($db, "CALL ed_sp_web_edad_usuario_web_obtener_combo(" . $_SESSION["id_idioma"] . ")", "cmbAnyos", "cmbAnyos", $datoUsuarioIndividual["id_edad_usuario_web"], "nombre", "id_edad_usuario_web", STATIC_FORM_MEMBERSHIP_AGE, -1, 'class="inputText left" style="width:auto; margin-top:10px;"'));

    //Combo paises
    $subPlantilla->assign("COMBO_PAIS", generalUtils::construirCombo($db, "CALL ed_sp_web_pais_obtener_combo()", "cmbPais", "cmbPais", $datoUsuarioIndividual["id_pais"], "nombre_original", "id_pais", STATIC_FORM_MEMBERSHIP_COUNTRY_OF_RESIDENCE, -1, 'class="inputText left required" style="width:284px;"'));

    $plantilla->assign("TEXTAREA_ID", "txtOtrasActividades");
    $plantilla->assign("TEXTAREA_TOOLBAR", "Minimo");
    $plantilla->parse("contenido_principal.bloque_ready.inicializar_ckeditor");

    $plantilla->assign("TEXTAREA_ID", "txtOtrasDescripciones");
    $plantilla->assign("TEXTAREA_TOOLBAR", "Minimo");
    $plantilla->parse("contenido_principal.bloque_ready.inicializar_ckeditor");

    $plantilla->assign("TEXTAREA_ID", "txtOtrasPublicaciones");
    $plantilla->assign("TEXTAREA_TOOLBAR", "Minimo");
    $plantilla->parse("contenido_principal.bloque_ready.inicializar_ckeditor");
} else if ($_SESSION["met_user"]["id_modalidad"] == MODALIDAD_USUARIO_INSTITUTIONAL) {
    $resultadoUsuarioInstitucion = $db->callProcedure("CALL ed_sp_web_usuario_web_institucion_obtener_concreto(" . $_SESSION["met_user"]["id"] . ")");
    $datoUsuarioInstitucion = $db->getData($resultadoUsuarioInstitucion);


    //Recommended size
    $subPlantilla->assign("RECOMMENDED_SIZE", STATIC_FORM_PROFILE_INSTITUTIONAL_MEMBER_RECOMMENDED_SIZE);


    //Valores defecto...
    $nombreInstitucion = STATIC_FORM_MEMBERSHIP_INSTIT_NAME_INSTITUTION;
    $departamentoInstitucion = STATIC_FORM_MEMBERSHIP_INSTIT_DEPARTMENT_IF_APPLICABLE;
    $calle1Institucion = STATIC_FORM_MEMBERSHIP_STREET_1;
    $calle2Institucion = STATIC_FORM_MEMBERSHIP_STREET_2;
    $codigoPostalInstitucion = STATIC_FORM_MEMBERSHIP_POSTCODE;
    $ciudadInstitucion = STATIC_FORM_MEMBERSHIP_TOWN_CITY;
    $provinciaInstitucion = STATIC_FORM_MEMBERSHIP_PROVINCE;
    $telefonoInstitucion = STATIC_FORM_MEMBERSHIP_INSTIT_PHONE_NO;
    $faxInstitucion = STATIC_FORM_MEMBERSHIP_INSTIT_FAX_NO;
    $correoElectronicoInstitucion = STATIC_FORM_MEMBERSHIP_INSTIT_EMAIL_ADDRESS;
    $nombreRepresentante = STATIC_FORM_MEMBERSHIP_FIRST_NAME;
    $apellidosRepresentante = STATIC_FORM_MEMBERSHIP_LAST_NAMES;
    $estadoRepresentante = STATIC_FORM_MEMBERSHIP_INSTIT_IF_OTHER_PLEASE_STATE;
    $correoElectronicoRepresentante = STATIC_FORM_MEMBERSHIP_INSTIT_EMAIL_TO_USER;
    $correoElectronicoAlternativo = STATIC_FORM_MEMBERSHIP_INSTIT_ALTERNATIVE_EMAIL;
    $telefonoTrabajoRepresentante = STATIC_FORM_MEMBERSHIP_WORK_PHONE;
    $telefonoMovilRepresentante = STATIC_FORM_MEMBERSHIP_MOBILE_PHONE;


    $resultadoMenuModulo = $db->callProcedure("CALL ed_sp_web_menu_modulo_obtener(" . MODULE_MEMBER_INSTITUTIONAL_ID . ")");
    $datoMenuModulo = $db->getData($resultadoMenuModulo);
    $idMenuModulo = $datoMenuModulo["id_menu"];

    //Url detalle
    $vectorAtributosDetalle["idioma"] = $_SESSION["siglas"];
    $vectorAtributosDetalle["id_menu"] = $idMenuModulo;
    $vectorAtributosDetalle["id_detalle"] = $_SESSION["met_user"]["id"];
    $vectorAtributosDetalle["seo_url"] = $datoUsuarioInstitucion["nombre"];
    $plantillaPerfilDatosUsuario->assign("ENLACE_PERFIL", generalUtils::generarUrlAmigableDetalle($vectorAtributosDetalle));

    //Si tienen valor, sobreescribimos el 'placeholder/value' que hemos puesto en el paso anterior
    if ($datoUsuarioInstitucion["nombre"] != "") {
        $nombreInstitucion = $datoUsuarioInstitucion["nombre"];
    }
    if ($datoUsuarioInstitucion["departamento"] != "") {
        $departamentoInstitucion = $datoUsuarioInstitucion["departamento"];
    }
    if ($datoUsuarioInstitucion["direccion"] != "") {
        $calle1Institucion = $datoUsuarioInstitucion["direccion"];
    }
    if ($datoUsuarioInstitucion["direccion2"] != "") {
        $calle2Institucion = $datoUsuarioInstitucion["direccion2"];
    }
    if ($datoUsuarioInstitucion["codigo_postal"] != "") {
        $codigoPostalInstitucion = $datoUsuarioInstitucion["codigo_postal"];
    }
    if ($datoUsuarioInstitucion["ciudad"] != "") {
        $ciudadInstitucion = $datoUsuarioInstitucion["ciudad"];
    }
    if ($datoUsuarioInstitucion["provincia"] != "") {
        $provinciaInstitucion = $datoUsuarioInstitucion["provincia"];
    }
    if ($datoUsuarioInstitucion["telefono"] != "") {
        $telefonoInstitucion = $datoUsuarioInstitucion["telefono"];
    }
    if ($datoUsuarioInstitucion["fax"] != "") {
        $faxInstitucion = $datoUsuarioInstitucion["fax"];
    }
    if ($datoUsuarioInstitucion["correo_electronico"] != "") {
        $correoElectronicoInstitucion = $datoUsuarioInstitucion["correo_electronico"];
    }
    if ($datoUsuarioInstitucion["nombre_representante"] != "") {
        $nombreRepresentante = $datoUsuarioInstitucion["nombre_representante"];
    }
    if ($datoUsuarioInstitucion["apellidos_representante"] != "") {
        $apellidosRepresentante = $datoUsuarioInstitucion["apellidos_representante"];
    }
    if ($datoUsuarioInstitucion["pais_representante"] != "") {
        $estadoRepresentante = $datoUsuarioInstitucion["pais_representante"];
    }
    if ($datoUsuarioInstitucion["correo_electronico_representante"] != "") {
        $correoElectronicoRepresentante = $datoUsuarioInstitucion["correo_electronico_representante"];
    }
    if ($datoUsuarioInstitucion["correo_electronico_alternativa_representante"] != "") {
        $correoElectronicoAlternativo = $datoUsuarioInstitucion["correo_electronico_alternativa_representante"];
    }
    if ($datoUsuarioInstitucion["telefono_trabajo"] != "") {
        $telefonoTrabajoRepresentante = $datoUsuarioInstitucion["telefono_trabajo"];
    }
    if ($datoUsuarioInstitucion["telefono_movil"] != "") {
        $telefonoMovilRepresentante = $datoUsuarioInstitucion["telefono_movil"];
    }


    //Asignamos estos valores
    $subPlantilla->assign("MIEMBRO_NOMBRE_INSTITUCION", $nombreInstitucion);
    $subPlantilla->assign("MIEMBRO_DEPARTAMENTO_INSTITUCION", $departamentoInstitucion);
    $subPlantilla->assign("MIEMBRO_CALLE1_INSTITUCION", $calle1Institucion);
    $subPlantilla->assign("MIEMBRO_CALLE2_INSTITUCION", $calle2Institucion);
    $subPlantilla->assign("MIEMBRO_CODIGO_POSTAL_INSTITUCION", $codigoPostalInstitucion);
    $subPlantilla->assign("MIEMBRO_CIUDAD_INSTITUCION", $ciudadInstitucion);
    $subPlantilla->assign("MIEMBRO_PROVINCIA_INSTITUCION", $provinciaInstitucion);
    $subPlantilla->assign("MIEMBRO_TELEFONO_INSTITUCION", $telefonoInstitucion);
    $subPlantilla->assign("MIEMBRO_FAX_INSTITUCION", $faxInstitucion);
    $subPlantilla->assign("MIEMBRO_CORREO_ELECTRONICO_INSTITUCION", $correoElectronicoInstitucion);
    $subPlantilla->assign("MIEMBRO_NOMBRE_REPRESENTANTE", $nombreRepresentante);
    $subPlantilla->assign("MIEMBRO_APELLIDOS_REPRESENTANTE", $apellidosRepresentante);
    $subPlantilla->assign("MIEMBRO_ESTADO_REPRESENTANTE", $estadoRepresentante);
    $subPlantilla->assign("MIEMBRO_CORREO_ELECTRONICO_REPRESENTANTE", $correoElectronicoRepresentante);
    $subPlantilla->assign("MIEMBRO_CORREO_ELECTRONICO_ALTERNATIVO_REPRESENTANTE", $correoElectronicoAlternativo);
    $subPlantilla->assign("MIEMBRO_TELEFONO_TRABAJO_REPRESENTANTE", $telefonoTrabajoRepresentante);
    $subPlantilla->assign("MIEMBRO_TELEFONO_MOVIL_REPRESENTANTE", $telefonoMovilRepresentante);

    $subPlantilla->assign("COMBO_TITULOS", generalUtils::construirCombo($db, "CALL ed_sp_web_tratamiento_usuario_web_obtener_combo(" . $_SESSION["id_idioma"] . ")", "cmbTitulo", "cmbTitulo", $datoUsuarioInstitucion["id_tratamiento_usuario_web"], "nombre", "id_tratamiento_usuario_web", STATIC_FORM_MEMBERSHIP_TITLE, -1, 'class="inputText left" style="width:63px;"'));

    //Combo paises
    $subPlantilla->assign("COMBO_PAIS", generalUtils::construirCombo($db, "CALL ed_sp_web_pais_obtener_combo()", "cmbPais", "cmbPais", $datoUsuarioInstitucion["id_pais"], "nombre_original", "id_pais", STATIC_FORM_MEMBERSHIP_COUNTRY_OF_RESIDENCE, -1, 'class="inputText left required" style="width:284px;"'));
}

//Combo source languages
$subPlantilla->assign("COMBO_SOURCE_LANGUAGES", generalUtils::construirMultiCombo($db, "CALL ed_sp_get_working_languages(" . $_SESSION["id_idioma"] . ")", "CALL ed_sp_get_member_profile_source_languages(" . $_SESSION["id_idioma"] . "," . $_SESSION["met_user"]["id"] . ")", "nombre", "nombre", "cmbSource", "cmbSource", "nombre", "id_working_language", STATIC_FORM_PROFILE_SOURCE_LANGS, -1, 'class="inputText left" style="width:272px;"'));

//Combo target languages
$subPlantilla->assign("COMBO_TARGET_LANGUAGES", generalUtils::construirMultiCombo($db, "CALL ed_sp_get_working_languages(" . $_SESSION["id_idioma"] . ")", "CALL ed_sp_get_member_profile_target_languages(" . $_SESSION["id_idioma"] . "," . $_SESSION["met_user"]["id"] . ")", "nombre", "nombre", "cmbTarget", "cmbTarget", "nombre", "id_working_language", STATIC_FORM_PROFILE_TARGET_LANGS, -1, 'class="inputText left" style="width:272px;"'));

//Combo areas of expertise
$subPlantilla->assign("COMBO_AREAS_OF_EXPERTISE", generalUtils::construirMultiCombo($db, "CALL ed_sp_get_areas_of_expertise(" . $_SESSION["id_idioma"] . ")", "CALL ed_sp_get_member_profile_areas_of_expertise(" . $_SESSION["id_idioma"] . "," . $_SESSION["met_user"]["id"] . ")", "nombre", "nombre", "cmbAreas", "cmbAreas", "nombre", "id_area", STATIC_FORM_PROFILE_AREAS_OF_EXPERTISE, -1, 'class="inputText left" style="width:515px;"'));

$plantilla->parse("contenido_principal.script_multiselect");

//    Alternative code for language multiple select
//    $html_source = '<select multiple name="cmbSource[]" id="cmbSource" value="" class="inputText left" style="width:272px;">';
//    $resultadoSource = $db->callProcedure("CALL ed_sp_get_member_profile_source_languages(" . $_SESSION["id_idioma"] . "," . $_SESSION["met_user"]["id"] . ")");
//    $resultadoLangs = $db->callProcedure("CALL ed_sp_get_working_languages(" . $_SESSION["id_idioma"] . ")");
//    while($datoSource = $db->getData($resultadoSource)) {
//        $sourceLangs[] = $datoSource["nombre"]; }
//    while($datoLangs = $db->getData($resultadoLangs)) {
//        $langNames[] = $datoLangs["nombre"]; }
//    foreach($langNames as $langName) {
//    $selected = in_array($langName,$sourceLangs) ? "selected " : "";
//    $html_source .= '<option ' . $selected . 'value="' . $langName . '">' . $langName . '</option>'; }

//Exportamos informacion adicional a la plantilla principal
$resultadoUsuarioWeb = $db->callProcedure("CALL ed_sp_web_usuario_web_obtener_concreto(" . $_SESSION["met_user"]["id"] . ")");
$datoUsuarioWeb = $db->getData($resultadoUsuarioWeb);
$plantillaPerfilMasInformacionUsuario = new XTemplate("html/includes/profile_more_detail_member.inc.html");
$subPlantilla->assign("PERFIL_VALOR_WEB", STATIC_FORM_PROFILE_CHANGE_WEB);

$plantillaPerfilDatosUsuario->assign("PERFIL_VALOR_PUBLICO", ($datoUsuarioWeb["publico"] == 1) ? "checked" : "");

//Parseamos bloque
$plantillaPerfilDatosUsuario->parse("contenido_principal");

//Exportamos a la plantilla profile
$subPlantilla->assign("PERFIL_DATOS_USUARIO", $plantillaPerfilDatosUsuario->text("contenido_principal"));

if ($datoUsuarioWeb["otros"] != "") {
    $plantillaPerfilMasInformacionUsuario->assign("PERFIL_VALOR_OTRAS_ACTIVIDADES", $datoUsuarioWeb["otros"]);
}

if ($datoUsuarioWeb["descripcion"] != "") {
    $subPlantilla->assign("PERFIL_VALOR_OTRAS_DESCRIPCION", $datoUsuarioWeb["descripcion"]);
}
if ($datoUsuarioWeb["publicaciones"] != "") {
    $subPlantilla->assign("PERFIL_VALOR_OTRAS_PUBLICACIONES", $datoUsuarioWeb["publicaciones"]);
}
if ($datoUsuarioWeb["web"] != "") {
    $subPlantilla->assign("PERFIL_VALOR_WEB", $datoUsuarioWeb["web"]);
}


if ($datoUsuarioWeb["imagen"] != "") {
    $subPlantilla->assign("PERFIL_IMAGEN_NOMBRE", $datoUsuarioWeb["imagen"]);
    $subPlantilla->parse("contenido_principal.imagen_controles");

    $plantilla->parse("contenido_principal.script_colorbox");
} else {
    $subPlantilla->assign("PERFIL_IMAGEN_NOMBRE", "");
}

// Instanciamos la clase Xtemplate con la plantilla que va a contener la información del apartado
if ($_SESSION["met_user"]["id_modalidad"] == MODALIDAD_USUARIO_INDIVIDUAL) {
    $plantilla->parse("contenido_principal.validar_usuario_web_editar");
} else if ($_SESSION["met_user"]["id_modalidad"] == MODALIDAD_USUARIO_INSTITUTIONAL) {
    $plantilla->parse("contenido_principal.validar_institucion_web_editar");
}


//Parseamos bloque
$plantillaPerfilMasInformacionUsuario->parse("contenido_principal");


//Exportamos a la plantilla profile
$subPlantilla->assign("PERFIL_MAS_INFORMACION", $plantillaPerfilMasInformacionUsuario->text("contenido_principal"));


//Datos facturacion
$plantillaPerfilFacturacionUsuario = new XTemplate("html/includes/profile_billing_member.inc.html");
//Datos
$plantillaPerfilFacturacionUsuario->assign("PERFIL_NIF_CLIENTE", STATIC_FORM_PROFILE_BILLING_CUSTOMER_NIF);
$plantillaPerfilFacturacionUsuario->assign("PERFIL_NOMBRE_CLIENTE", STATIC_FORM_PROFILE_BILLING_NAME_CUSTOMER);
$plantillaPerfilFacturacionUsuario->assign("PERFIL_NOMBRE_EMPRESA", STATIC_FORM_PROFILE_BILLING_NAME_COMPANY);
$plantillaPerfilFacturacionUsuario->assign("PERFIL_DIRECCION", STATIC_FORM_PROFILE_BILLING_ADDRESS);
$plantillaPerfilFacturacionUsuario->assign("PERFIL_CODIGO_POSTAL", STATIC_FORM_PROFILE_BILLING_ZIPCODE);
$plantillaPerfilFacturacionUsuario->assign("PERFIL_CIUDAD", STATIC_FORM_PROFILE_BILLING_CITY);
$plantillaPerfilFacturacionUsuario->assign("PERFIL_PROVINCIA", STATIC_FORM_PROFILE_BILLING_PROVINCE);
$plantillaPerfilFacturacionUsuario->assign("PERFIL_PAIS", STATIC_FORM_PROFILE_BILLING_COUNTRY);

//Miramos si estan los datos introducis
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

//Parseamos bloque
$plantillaPerfilFacturacionUsuario->parse("contenido_principal");


//Exportamos a la plantilla profile
$subPlantilla->assign("PERFIL_FACTURACION", $plantillaPerfilFacturacionUsuario->text("contenido_principal"));

/*

  //Listamos las conferencias y talleres
  $resultConferencias = $db->callProcedure("CALL ed_sp_web_conferencia_taller_obtener_listado(".$_SESSION["id_idioma"].", ".$idUsuarioWebLogin.")");
  $idConferencia = 0;
  $blPrimero = 0;
  while($dataConferencia = $db->getData($resultConferencias)) {
  if($dataConferencia["id_conferencia"] != $idConferencia && $blPrimero == 1) {
  $subPlantilla->parse("contenido_principal.item_conferencia");
  $subPlantilla->assign("ITEM_CONFERENCIA_NOMBRE", $dataConferencia["descripcion"]);
  }else if($blPrimero == 0) {
  $subPlantilla->assign("ITEM_CONFERENCIA_NOMBRE", $dataConferencia["descripcion"]);
  $blPrimero = 1;
  }
  $subPlantilla->assign("ITEM_CONFERENCIA_TALLER_NOMBRE", $dataConferencia["taller"]);
  $subPlantilla->assign("ITEM_CONFERENCIA_TALLER_ID", $dataConferencia["id_conferencia_taller"]);
  $subPlantilla->assign("ITEM_CONFERENCIA_TALLER_CHECK", ($dataConferencia["presencial"] == 1) ? "checked='checked'" : "");

  $subPlantilla->parse("contenido_principal.item_conferencia.item_taller");

  $idConferencia = $dataConferencia["id_conferencia"];
  }
  $subPlantilla->parse("contenido_principal.item_conferencia"); */

//Historial inscripciones
//$resultadoInscripcion=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_inscripcion_usuario_web_historial(".$_SESSION["met_user"]["id"].",".$_SESSION["met_user"]["language_id"].")");
$resultadoInscripcion=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_inscripcion_usuario_web_historial(".$_SESSION["met_user"]["id"].",".$_SESSION["id_idioma"].")");
while($datoInscripcion=$db->getData($resultadoInscripcion)){
    if($datoInscripcion["pagado"]==1){
        $fechaStart=explode(" ",$datoInscripcion["fecha_inscripcion"]);
        $subPlantilla->assign("INSCRIPTIONS_HISTORY_ID",$datoInscripcion["id_inscripcion"]);
        $subPlantilla->assign("INSCRIPTIONS_HISTORY_AMOUNT",$datoInscripcion["importe"]);
        $subPlantilla->assign("INSCRIPTIONS_HISTORY_NUMBER",$datoInscripcion["numero_inscripcion"]);
        $subPlantilla->assign("INSCRIPTIONS_HISTORY_START_DATE",generalUtils::conversionFechaFormato($fechaStart[0],"-","-"));
        $subPlantilla->assign("INSCRIPTIONS_HISTORY_END_DATE",generalUtils::conversionFechaFormato($datoInscripcion["fecha_finalizacion"],"-","-"));
        $subPlantilla->assign("INSCRIPTIONS_HISTORY_PAYMENT_TYPE",$datoInscripcion["tipo_pago"]);

//  if($datoInscripcion["pagado"]==1){
//  $subPlantilla->assign("INSCRIPTIONS_HISTORY_PAID","Paid");
//  }else{
//  $subPlantilla->assign("INSCRIPTIONS_HISTORY_PAID","Not paid");
//  }

        $subPlantilla->parse("contenido_principal.item_inscripcion");
    }
    else {
    }
}

//Cargamos el breadcrumb
require "includes/load_breadcrumb.inc.php";

//Cargamos los menus hijos del lateral derecho
require "includes/load_menu_left.inc.php";

$subPlantilla->parse("contenido_principal");

/**
 * Realizamos todos los parse realcionados con este apartado
 */
//Incluimos script editor
$plantilla->parse("contenido_principal.editor_script");
$plantilla->parse("contenido_principal.css_form");
$plantilla->parse("contenido_principal.validaciones");
$plantilla->parse("contenido_principal.control_superior");
$plantilla->parse("contenido_principal.bloque_ready");

//Exportamos plantilla secundaria a la principal
$plantilla->assign("CONTENIDO", $subPlantilla->text("contenido_principal"));

//Parseamos y sacamos informacion por pantalla
$plantilla->parse("contenido_principal");
$plantilla->out("contenido_principal");
?>