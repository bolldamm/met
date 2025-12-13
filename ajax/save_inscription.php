<?php
/**
 * Triggered by generateMemberRegistration() in index.html
 * Validates form data and displays error message in form where applicable
 * If form is valid, stores registration data in database
 * Stores payment method and "item" in process_form.html
 * generateMemberRegistration() gets payment method and "item" from process_form.html
 * Since payment method is always Stripe/Paypal,
 * generateMemberRegistration() redirects to proceed.php with "item" in URL
 * and so initiates the payment process (Stripe)
 */

require "../includes/load_main_components.inc.php";
require_once "../includes/settings.php";

$esValido = true;
$mensajeError = "";


//The process_form.html template stores the data needed for payment
$plantilla = new XTemplate("../html/ajax/process_form.html");


//Get member details from form fields (for individual members)
if ($_POST["hdnIdModalidad"] == MODALIDAD_USUARIO_INDIVIDUAL) {

    if ($_POST["cmbAnyos"] == -1) {
        $_POST["cmbAnyos"] = "null";
    }

    // Address fields commented out in form - set defaults
    $_POST["txtDireccion1"] = isset($_POST["txtDireccion1"]) ? $_POST["txtDireccion1"] : "";
    $_POST["txtDireccion2"] = isset($_POST["txtDireccion2"]) ? $_POST["txtDireccion2"] : "";

    $_POST["txtEmailUser"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_EMAIL_USER, $_POST["txtEmailUser"]));
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
    $_POST["txtProfesionQualificacion"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_DEGREES_QUALIFICATIONS, $_POST["txtProfesionQualificacion"]));
    $_POST["txtSobreMet"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_HOW_DID_YOU_HEAR_ABOUT_MET, $_POST["txtSobreMet"]));
    $_POST["txtOtherSpecification"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_IF_OTHER_SPECIFY, $_POST["txtOtherSpecification"]));
    $_POST["txtStudySpecification"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_IF_STUDENT_SUBJECT, $_POST["txtStudySpecification"]));
} else if ($_POST["hdnIdModalidad"] == MODALIDAD_USUARIO_INSTITUTIONAL) {
    $_POST["txtEmailUser"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_EMAIL_USER, $_POST["txtEmailUser"]));
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

}

//Get billing details
$_POST["txtFacturacionNifCliente"] = isset($_POST["tax_id_number"]) ? generalUtils::escaparCadena($_POST["tax_id_number"]) : "";
$_POST["txtFacturacionNombreCliente"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_NAME_CUSTOMER, $_POST["txtFacturacionNombreCliente"]));
$_POST["txtFacturacionNombreEmpresa"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_NAME_COMPANY, $_POST["txtFacturacionNombreEmpresa"]));
$_POST["txtFacturacionDireccion"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_ADDRESS, $_POST["txtFacturacionDireccion"]));
$_POST["txtFacturacionCodigoPostal"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_ZIPCODE, $_POST["txtFacturacionCodigoPostal"]));
$_POST["txtFacturacionCiudad"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_CITY, $_POST["txtFacturacionCiudad"]));
$_POST["txtFacturacionProvincia"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_PROVINCE, $_POST["txtFacturacionProvincia"]));

// Billing country comes from combo as billing_country (ISO-2 code)
// Convert ISO-2 to full country name for storage
$billingCountryIso = isset($_POST["billing_country"]) ? generalUtils::escaparCadena($_POST["billing_country"]) : "";
$paisFactura = "";
if (!empty($billingCountryIso) && $billingCountryIso !== "-1") {
    $resultPais = $db->callProcedure("CALL ed_sp_web_pais_get_name_from_iso('" . $billingCountryIso . "')");
    if ($rowPais = $db->getData($resultPais)) {
        $paisFactura = $rowPais["nombre_original"];
    }
}

//Assign billing details to variables for sending to database
$nifFactura = $_POST["txtFacturacionNifCliente"];
$nombreClienteFactura = $_POST["txtFacturacionNombreCliente"];
$nombreEmpresaFactura = $_POST["txtFacturacionNombreEmpresa"];
$direccionFactura = $_POST["txtFacturacionDireccion"];
$codigoPostalFactura = $_POST["txtFacturacionCodigoPostal"];
$ciudadFactura = $_POST["txtFacturacionCiudad"];
$provinciaFactura = $_POST["txtFacturacionProvincia"];

//Tax ID fields for Verifactu - billing_country ISO-2 is used as tax_id_country
$taxIdCountry = $billingCountryIso;
$taxIdType = isset($_POST["tax_id_type"]) ? generalUtils::escaparCadena($_POST["tax_id_type"]) : "";
$taxIdNumber = isset($_POST["tax_id_number"]) ? generalUtils::escaparCadena($_POST["tax_id_number"]) : "";

//Store tax ID in session for Stripe payment flow
$_SESSION["taxIdCountry"] = $taxIdCountry;
$_SESSION["taxIdType"] = $taxIdType;
$_SESSION["taxIdNumber"] = $taxIdNumber;

// Handle invoice type (individual = F2 simplified invoice, business = F1 standard invoice)
$invoiceType = isset($_POST["invoiceType"]) ? $_POST["invoiceType"] : "business";
if ($invoiceType === "individual") {
    // For private individuals, set billing fields to empty and use F2 (simplified invoice)
    $nifFactura = "";
    $nombreClienteFactura = "";
    $nombreEmpresaFactura = "";
    $direccionFactura = "";
    $codigoPostalFactura = "";
    $ciudadFactura = "";
    $provinciaFactura = "";
    $paisFactura = "";
    $taxIdCountry = "";
    $taxIdType = "";
    $taxIdNumber = "";
    $_SESSION["tipoFacturaVerifactu"] = "F2";
} else {
    $_SESSION["tipoFacturaVerifactu"] = "F1";
}
// Store invoice type in session for use during invoice creation
$_SESSION["invoiceType"] = $invoiceType;

//Make the sure the Privacy checkbox is checked
if (!isset($_POST["chkPrivacy"])) {
    $esValido = false;
    $privacy = 0;
} else {
    $privacy = 1;
}

//Check permission to include new member's name in next newsletter
if (!isset($_POST["chkNewsletter"])) {
    $newsletterPermission = 0;
} else {
    $newsletterPermission = 1;
}

//Assign member's email address and name to variables
$emailUsuario = $_POST["txtEmailUser"];
$nombreUsuario = $_POST["txtNombre"];
$apellidosUsuario = $_POST["txtApellidos"];


//Determine the payment method
$vectorMetodoPago = Array(INSCRIPCION_TIPO_PAGO_TRANSFERENCIA, INSCRIPCION_TIPO_PAGO_PAYPAL, INSCRIPCION_TIPO_PAGO_DEBIT);
$metodoPago = $_POST["rdMetodoPago"];

//Strip extra spaces from fields
foreach ($_POST as $clave => $valor) {
    $_POST[$clave] = trim($valor);
}

//Display error message if a non-deleted user with the same email address already exists in the database
$resultadoCorreo = $db->callProcedure("CALL ed_sp_web_usuario_web_existe_registrado('" . $_POST["txtEmailUser"] . "')");
$esValido = $db->getNumberRows($resultadoCorreo) == 0;
$mensajeError = STATIC_FORM_MEMBERSHIP_EMAIL_REPEAT;

//Display error message if no valid payment method has been selected
if (!in_array($metodoPago, $vectorMetodoPago)) {
    $esValido = false;
}

//Display error message if no professional activity has been selected
$vectorActividadProfesional = array_filter(explode(",", $_POST["hdnIdActividadProfesional"]));
if ($_POST["hdnIdModalidad"] == MODALIDAD_USUARIO_INDIVIDUAL && count($vectorActividadProfesional) == 0) {
    $esValido = false;
    $mensajeError = STATIC_FORM_MEMBERSHIP_ERROR_PROFESSION;
}

//If all the form fields are valid, process the data
if ($esValido) {

    //Set defaults for "public profile", "active", "deleted" and "invoice required" columns in ed_tb_usuario_web
    $publico = 0;
    $activo = 0;
    /**** BEWARE!!! ***/
    $borrado = 0;
    $esFactura = 1;


    if ($_POST["cmbPais"] == -1) {
        $_POST["cmbPais"] = "null";
    }

    $_POST["cmbTitulo"] = "null";

    //Set Movements Subaccount to "MET membership"
    $idConceptoMovimiento = MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP;

    $db->startTransaction();


    //If payment method is Stripe
    if ($metodoPago == INSCRIPCION_TIPO_PAGO_PAYPAL) {
        //Set registration status to "Pending" until payment is confirmed
        $idEstadoInscripcion = INSCRIPCION_ESTADO_INSCRIPCION_PENDIENTE;
    } else {
        //Set registration status to "Confirmed" (for bank transfer and direct debit), but "Unpaid"
        $pagado = 0;
        $idEstadoInscripcion = INSCRIPCION_ESTADO_INSCRIPCION_CONFIRMADA;
    }

    //Insert new member in ed_tb_usuario_web
    $resultadoUsuarioWeb = $db->callProcedure("CALL ed_sp_web_usuario_web_insertar(" . TIPO_USUARIO_SOCIO . "," . $_POST["hdnIdModalidad"] . ",'" . $_POST["txtEmailUser"] . "','" . $_POST["txtPasswd"] . "'," . $publico . ",'" . $_POST["txtFacturacionNifCliente"] . "','" . $_POST["txtFacturacionNombreCliente"] . "','" . $_POST["txtFacturacionNombreEmpresa"] . "','" . $_POST["txtFacturacionDireccion"] . "','" . $_POST["txtFacturacionCodigoPostal"] . "','" . $_POST["txtFacturacionCiudad"] . "','" . $_POST["txtFacturacionProvincia"] . "','" . $paisFactura . "'," . $privacy . "," . $newsletterPermission . "," . $activo . "," . $borrado . ")");
    $datoUsuarioWeb = $db->getData($resultadoUsuarioWeb);
    $idUsuarioWeb = $datoUsuarioWeb["id_usuario_web"];

    $fechaInscripcion = date("Y-m-d G:i:s");
    $fechaHoraDesglosada = explode(" ", $fechaInscripcion);
    $fechaDesglosada = explode("-", $fechaHoraDesglosada[0]);

    //If signup is after 30 September, set expiry to end of following year
    if ($fechaDesglosada[1] > 9) {
        $fechaFinalizacion = ($fechaDesglosada[0] + 1) . "-12-31";
    } else {
        $fechaFinalizacion = ($fechaDesglosada[0]) . "-12-31";
    }

    //Determine price (regular, student or over-65)
    if (isset($_POST["hdnIdModalidad"]) && $_POST["hdnIdModalidad"] == MODALIDAD_USUARIO_INDIVIDUAL) {
        $idSituacionAdicional = "null";
        $esHombre = 0; // Gender field not in form, default to 0
        $vectorSituacionAdicional = Array(SITUACION_ADICIONAL_JUBILADO, SITUACION_ADICIONAL_ESTUDIANTE);

        $importe = PRECIO_MODALIDAD_USUARIO_INDIVIDUAL;

        //If student or over-65, before or after 30 September, set Subaccount as appropriate, normal or prepaid
        if (in_array($_POST["cmbSituacionAdicional"], $vectorSituacionAdicional)) {
            $idSituacionAdicional = $_POST["cmbSituacionAdicional"];
            $importe = PRECIO_MODALIDAD_USUARIO_INDIVIDUAL_DESCUENTO;
            if ($fechaDesglosada[1] < 10) {
                if ($idSituacionAdicional == SITUACION_ADICIONAL_JUBILADO) {
                    $idConceptoMovimiento = MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP_RETIRED;
                } else if ($idSituacionAdicional == SITUACION_ADICIONAL_ESTUDIANTE) {
                    $idConceptoMovimiento = MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP_STUDENT;
                }
            } else {
                if ($idSituacionAdicional == SITUACION_ADICIONAL_JUBILADO) {
                    $idConceptoMovimiento = MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP_PREPAID;
                } else if ($idSituacionAdicional == SITUACION_ADICIONAL_ESTUDIANTE) {
                    $idConceptoMovimiento = MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP_PREPAID;
                }
            }
        }

        //Insert the member's personal details in ed_tb_usuario_web_individual
        $resultadoUsuarioWebIndividual = $db->callProcedure("CALL ed_sp_web_usuario_web_individual_insertar(" . $idUsuarioWeb . "," . $_POST["cmbTitulo"] . "," . $_POST["cmbAnyos"] . "," . $_POST["cmbPais"] . "," . $idSituacionAdicional . ",'" . $_POST["txtNombre"] . "','" . $_POST["txtApellidos"] . "','" . $_POST["txtNacionalidad"] . "','" . $_POST["txtDireccion1"] . "','" . $_POST["txtDireccion2"] . "','" . $_POST["txtCiudad"] . "','" . $_POST["txtProvincia"] . "','" . $_POST["txtCp"] . "','" . $_POST["txtEmail"] . "','" . $_POST["txtEmailAlternativo"] . "','" . $_POST["txtTelefonoCasa"] . "','" . $_POST["txtTelefonoTrabajo"] . "','" . $_POST["txtFax"] . "','" . $_POST["txtTelefonoMobil"] . "','" . $esHombre . "','" . $_POST["txtSobreMet"] . "','" . $_POST["txtProfesionQualificacion"] . "')");

        //Insert the member's professional activities in ed_tb_usuario_web_actividad_profesional
        foreach ($vectorActividadProfesional as $valor) {
            $descripcion = "";
            if ($valor == 7) {
                //estudio
                $descripcion = $_POST["txtStudySpecification"];
            } else if ($valor == 8) {
                //other
                $descripcion = $_POST["txtOtherSpecification"];
            }
            $db->callProcedure("CALL ed_sp_web_usuario_web_actividad_profesional_insertar(" . $idUsuarioWeb . "," . $valor . ",'" . $descripcion . "')");
        }


        //Insert the member's work situation in ed_tb_usuario_web_situacion_laboral
        $vectorSituacionLaboral = array_filter(explode(",", $_POST["hdnIdSituacionLaboral"]));
        foreach ($vectorSituacionLaboral as $valor) {
            $db->callProcedure("CALL ed_sp_web_usuario_web_situacion_laboral_insertar(" . $idUsuarioWeb . "," . $valor . ")");
        }

        // OBSOLETE: for institutional members
    } else if ($_POST["hdnIdModalidad"] == MODALIDAD_USUARIO_INSTITUTIONAL) {
        $importe = PRECIO_MODALIDAD_USUARIO_INSTITUTIONAL;
        $idConceptoMovimiento = MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP_INSTITUTIONAL;

        //Insert institutional user
        $resultadoUsuarioWebInstitucion = $db->callProcedure("CALL ed_sp_web_usuario_web_institucion_insertar(" . $idUsuarioWeb . "," . $_POST["cmbTitulo"] . "," . $_POST["cmbPais"] . ",'" . $_POST["txtNombreInstitucion"] . "','" . $_POST["txtDepartamento"] . "','" . $_POST["txtDireccion1"] . "','" . $_POST["txtDireccion2"] . "','" . $_POST["txtCp"] . "','" . $_POST["txtCiudad"] . "','" . $_POST["txtProvincia"] . "','" . $_POST["txtTelefono"] . "','" . $_POST["txtFax"] . "','" . $_POST["txtEmail"] . "','" . $_POST["txtNombre"] . "','" . $_POST["txtApellidos"] . "','" . $_POST["txtEstado"] . "','" . $_POST["txtEmailUsuario"] . "','" . $_POST["txtEmailUsuarioAlternativo"] . "','" . $_POST["txtTelefonoTrabajo"] . "','" . $_POST["txtTelefonoMovil"] . "')");
    }


    //Insert new registration in ed_tb_inscripcion
    //NB the result ($resultadoInscripcion) is the registration ID and registration number (codigo and numero_inscripcion)
    $resultadoInscripcion = $db->callProcedure("CALL ed_sp_web_inscripcion_insertar(" . $idEstadoInscripcion . "," . $metodoPago . "," . $idUsuarioWeb . ",'" . $importe . "','" . $fechaInscripcion . "','" . $fechaFinalizacion . "'," . $esFactura . ")");
    $datoInscripcion = $db->getData($resultadoInscripcion);

    //Store signup details in session variable for later
    $_SESSION["amount"] = $importe; //for display in stripe form
    $_SESSION["regCode"] = $datoInscripcion["numero_inscripcion"] . "-" . $datoInscripcion["codigo"] . "-1";

    //Store payment method in process_form.html
    $plantilla->assign("TIPO_RESULTADO_INSCRIPCION", $metodoPago);
    switch ($metodoPago) {
        case INSCRIPCION_TIPO_PAGO_PAYPAL:
            //Store subaccount in process_form.html
            $plantilla->assign("ITEM", STATIC_FORM_STRIPE_ITEM_MEMBERSHIP);
            break;
        case INSCRIPCION_TIPO_PAGO_TRANSFERENCIA:
            $idUsuarioWebCorreo = $idUsuarioWeb;
            $numeroInscripcion = $datoInscripcion["numero_inscripcion"];
            $tipoInscripcion = 1;
            $idInscripcion = $datoInscripcion["codigo"];
            require "../includes/load_send_mail_inscription.inc.php";
            break;
        case INSCRIPCION_TIPO_PAGO_DEBIT:
            $idUsuarioWebCorreo = $idUsuarioWeb;
            $numeroInscripcion = $datoInscripcion["numero_inscripcion"];
            $tipoInscripcion = 1;
            $idInscripcion = $datoInscripcion["codigo"];
            require "../includes/load_send_mail_inscription.inc.php";
            break;
    }

    $db->endTransaction();

} else {
    //If any form field is invalid, store error message in process_form.html
    $plantilla->assign("TIPO_RESULTADO_INSCRIPCION", $mensajeError);
}//end else

//Parse content and output process_form.html
$plantilla->parse("contenido_principal");
$plantilla->out("contenido_principal");

