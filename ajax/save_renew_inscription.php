<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 21/11/2018
 * Time: 11:34
 */

/**
 * Validates form data and displays error message in form where applicable
 * If form is valid, stores registration data in database
 * Depending on the chosen payment method:
 * Initiates payment process (Stripe)
 * Initiates emailing process (bank transfer, direct debit)
 */

require "../includes/load_main_components.inc.php";
require "../includes/load_validate_user_web.inc.php";
require_once "../includes/settings.php";

$esValido = true;
$mensajeError = "";

if ($_SESSION["met_user"]["pagado"] == 0) {
    //Cannot renew if current registration is unpaid
    die();
}

//The process_form.html template stores the data needed for payment
$plantilla = new XTemplate("../html/ajax/process_form.html");

if($_SESSION["met_user"]["id_modalidad"]==MODALIDAD_USUARIO_INDIVIDUAL){
$importe = PRECIO_MODALIDAD_USUARIO_INDIVIDUAL;
$idConceptoMovimiento = MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP;

$idSituacionAdicional = "null";
$vectorSituacionAdicional = Array(SITUACION_ADICIONAL_JUBILADO, SITUACION_ADICIONAL_ESTUDIANTE);

//Set price according to whether regular, student or over-65 and update DB if necessary
if (in_array($_POST["cmbSituacionAdicional"], $vectorSituacionAdicional)) {
    $idSituacionAdicional = $_POST["cmbSituacionAdicional"];
    $importe = PRECIO_MODALIDAD_USUARIO_INDIVIDUAL_DESCUENTO;

    if ($idSituacionAdicional == SITUACION_ADICIONAL_JUBILADO) {
        $idConceptoMovimiento = MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP_RETIRED;
    } else if ($idSituacionAdicional == SITUACION_ADICIONAL_ESTUDIANTE) {
        $idConceptoMovimiento = MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP_STUDENT;
    }

    $db->callProcedure("CALL ed_sp_web_usuario_web_individual_situacion_adicional_editar(" . $_SESSION["met_user"]["id"] . "," . $idSituacionAdicional . ")");
}
} else if($_SESSION["met_user"]["id_modalidad"]==MODALIDAD_USUARIO_INSTITUTIONAL){
    $importe=PRECIO_MODALIDAD_USUARIO_INSTITUTIONAL;
    $idConceptoMovimiento=MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP_INSTITUTIONAL;
}

// Handle invoice type (individual = F2 simplified invoice, business = F1 standard invoice)
$invoiceType = isset($_POST["invoiceType"]) ? $_POST["invoiceType"] : "business";

//Get billing details from form
// Handle tax ID: either Spanish NIF or foreign tax ID
if (isset($_POST["hasSpanishNif"]) && $_POST["hasSpanishNif"] == 1 && !empty($_POST["spanishNifNumber"])) {
    // Spanish NIF/NIE selected
    $nifFactura = generalUtils::escaparCadena($_POST["spanishNifNumber"]);
} else {
    // Foreign tax ID selected
    $nifFactura = generalUtils::escaparCadena($_POST["tax_id_number"]);
}

// Validate required billing fields (only for business invoices)
if ($invoiceType !== "individual") {
    // Validate tax ID
    if (empty($nifFactura) || trim($nifFactura) === "") {
        $esValido = false;
        $mensajeError = "Please provide your tax identification number";
    }
    // Validate billing name
    if (empty($_POST["txtFacturacionNombreCliente"]) || trim($_POST["txtFacturacionNombreCliente"]) === "") {
        $esValido = false;
        $mensajeError = "Please provide your name for billing purposes";
    }
    // Validate billing address
    if (empty($_POST["txtFacturacionDireccion"]) || trim($_POST["txtFacturacionDireccion"]) === "") {
        $esValido = false;
        $mensajeError = "Please provide your billing address";
    }
    // Validate billing city
    if (empty($_POST["txtFacturacionCiudad"]) || trim($_POST["txtFacturacionCiudad"]) === "") {
        $esValido = false;
        $mensajeError = "Please provide your city";
    }
    // Validate billing postal code
    if (empty($_POST["txtFacturacionCodigoPostal"]) || trim($_POST["txtFacturacionCodigoPostal"]) === "") {
        $esValido = false;
        $mensajeError = "Please provide your postal code";
    }
}

$_POST["txtFacturacionNombreCliente"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_NAME_CUSTOMER, $_POST["txtFacturacionNombreCliente"]));
$_POST["txtFacturacionNombreEmpresa"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_NAME_COMPANY, $_POST["txtFacturacionNombreEmpresa"]));
$_POST["txtFacturacionDireccion"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_ADDRESS, $_POST["txtFacturacionDireccion"]));
$_POST["txtFacturacionCodigoPostal"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_ZIPCODE, $_POST["txtFacturacionCodigoPostal"]));
$_POST["txtFacturacionCiudad"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_CITY, $_POST["txtFacturacionCiudad"]));
$_POST["txtFacturacionProvincia"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_PROVINCE, $_POST["txtFacturacionProvincia"]));
$_POST["txtFacturacionPais"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_COUNTRY, $_POST["txtFacturacionPais"]));

//Store billing details in variables to be stored in DB later
// $nifFactura already set above based on Spanish NIF or foreign tax ID
$nombreClienteFactura = $_POST["txtFacturacionNombreCliente"];
$nombreEmpresaFactura = $_POST["txtFacturacionNombreEmpresa"];
$direccionFactura = $_POST["txtFacturacionDireccion"];
$codigoPostalFactura = $_POST["txtFacturacionCodigoPostal"];
$ciudadFactura = $_POST["txtFacturacionCiudad"];
$provinciaFactura = $_POST["txtFacturacionProvincia"];
$paisFactura = $_POST["txtFacturacionPais"];

//Tax ID fields for Verifactu (non-Spanish customers)
$taxIdCountry = isset($_POST["tax_id_country"]) ? generalUtils::escaparCadena($_POST["tax_id_country"]) : "";
$taxIdType = isset($_POST["tax_id_type"]) ? generalUtils::escaparCadena($_POST["tax_id_type"]) : "";
$taxIdNumber = isset($_POST["tax_id_number"]) ? generalUtils::escaparCadena($_POST["tax_id_number"]) : "";

//Store tax ID in session for Stripe payment flow
$_SESSION["taxIdCountry"] = $taxIdCountry;
$_SESSION["taxIdType"] = $taxIdType;
$_SESSION["taxIdNumber"] = $taxIdNumber;

// Handle invoice type and clear billing fields for private individuals
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

//Get payment method from form
$metodoPago = $_POST["rdMetodoPago"];
$vectorMetodoPago = Array(INSCRIPCION_TIPO_PAGO_TRANSFERENCIA, INSCRIPCION_TIPO_PAGO_PAYPAL, INSCRIPCION_TIPO_PAGO_DEBIT);

//Display error message if no valid payment method has been selected
if (!in_array($metodoPago, $vectorMetodoPago)) {
    $esValido = false;
}

//If all the form fields are valid, process the data
if ($esValido) {
    $db->startTransaction();

    $esFactura = 1;
    if ($metodoPago == INSCRIPCION_TIPO_PAGO_PAYPAL) {
        //If credit card, set registration status to "Pending" until payment is finalised
        $idEstadoInscripcion = INSCRIPCION_ESTADO_INSCRIPCION_PENDIENTE;
    } else {
        //If bank transfer or direct debit, set registration status to "Confirmed" but "Unpaid"
        $pagado = 0;
        $idEstadoInscripcion = INSCRIPCION_ESTADO_INSCRIPCION_CONFIRMADA;
    }

    $fechaInscripcion = date("Y-m-d G:i:s");
    $fechaHoraDesglosada = explode(" ", $fechaInscripcion);
    $fechaDesglosada = explode("-", $fechaHoraDesglosada[0]);
    $idUsuarioWeb = $_SESSION["met_user"]["id"];

    //Update billing details
    $db->callProcedure("CALL ed_sp_web_usuario_web_actualizar(" . $idUsuarioWeb . ",'" . $nifFactura . "','" . $_POST["txtFacturacionNombreCliente"] . "','" . $_POST["txtFacturacionNombreEmpresa"] . "','" . $_POST["txtFacturacionDireccion"] . "','" . $_POST["txtFacturacionCodigoPostal"] . "','" . $_POST["txtFacturacionCiudad"] . "','" . $_POST["txtFacturacionProvincia"] . "','" . $_POST["txtFacturacionPais"] . "')");

    //Get expiry date of last registration
    $resultadoInscripcionPrevia = $db->callProcedure("CALL ed_sp_web_inscripcion_previa(" . $idUsuarioWeb . ")");
    $datoInscripcionPrevia = $db->getData($resultadoInscripcionPrevia);
    $fechaActual = date("Y-m-d");
    $fechaPrevia = $datoInscripcionPrevia["fecha_finalizacion"];

    $fechaPreviaHoraDesglosada = explode(" ", $fechaPrevia);
    $fechaPreviaDesglosada = explode("-", $fechaPreviaHoraDesglosada[0]);

    $fechaActualAux = generalUtils::conversionFechaFormato($fechaActual, "-", "/");
    $fechaPreviaAux = generalUtils::conversionFechaFormato($fechaPrevia, "-", "/");

    if (generalUtils::compararFechas($fechaActualAux, $fechaPreviaAux) <= 2) {
        //If registration not yet expired, set expiry date to old expiry date + 1 year
        $nuevoAnyo = $fechaPreviaDesglosada[0] + 1;
        $fechaFinalizacion = $nuevoAnyo . "-12-31";
    } else {
        //If previous registration has expired, set expiry date according to whether renewal is after 30 September
        if ($fechaDesglosada[1] > 9) {
            $fechaFinalizacion = ($fechaDesglosada[0] + 1) . "-12-31";
        } else {
            $fechaFinalizacion = ($fechaDesglosada[0]) . "-12-31";
        }
    }

    //Insert new registration record in ed_tb_inscripcion
    $resultadoInscripcion = $db->callProcedure("CALL ed_sp_web_inscripcion_insertar(" . $idEstadoInscripcion . "," . $metodoPago . "," . $idUsuarioWeb . ",'" . $importe . "','" . $fechaInscripcion . "','" . $fechaFinalizacion . "'," . $esFactura . ")");
    $datoInscripcion = $db->getData($resultadoInscripcion);

    //Store amount, id_inscripcion, numero_inscripcion and type (1/2, new/renewal) in session variable
    $_SESSION["amount"] = $importe;
    $_SESSION["regCode"] = $datoInscripcion["numero_inscripcion"] . "-" . $datoInscripcion["codigo"] . "-2";

    //Store payment method in process_form.html
    $plantilla->assign("TIPO_RESULTADO_INSCRIPCION", $metodoPago);
    switch ($metodoPago) {
        case INSCRIPCION_TIPO_PAGO_PAYPAL:
            //Store subaccount in process_form.html
            $plantilla->assign("ITEM", STATIC_FORM_STRIPE_ITEM_MEMBERSHIP_RENEWAL);
            break;
        case INSCRIPCION_TIPO_PAGO_TRANSFERENCIA:
            $numeroInscripcion = $datoInscripcion["numero_inscripcion"];
            $tipoInscripcion = 2;
            $nombreUsuario = $_SESSION["met_user"]["name"];
            $apellidosUsuario = $_SESSION["met_user"]["lastname"];
            $emailUsuario = $_SESSION["met_user"]["email"];
            $idInscripcion = $datoInscripcion["codigo"];
            $idUsuarioWebCorreo = $idUsuarioWeb;
            require "../includes/load_send_mail_inscription.inc.php";
            break;
        case INSCRIPCION_TIPO_PAGO_DEBIT:
            $numeroInscripcion = $datoInscripcion["numero_inscripcion"];
            $tipoInscripcion = 2;
            $nombreUsuario = $_SESSION["met_user"]["name"];
            $apellidosUsuario = $_SESSION["met_user"]["lastname"];
            $emailUsuario = $_SESSION["met_user"]["email"];
            $idInscripcion = $datoInscripcion["codigo"];
            $idUsuarioWebCorreo = $idUsuarioWeb;
            require "../includes/load_send_mail_inscription.inc.php";
            break;
    }


    $db->endTransaction();

} else {
    //If any form field is invalid, store error message in process_form.html
    $plantilla->assign("TIPO_RESULTADO_INSCRIPCION", STATIC_FORM_MEMBERSHIP_EMAIL_REPEAT);
}//end else

//Parse content and output process_form.html
$plantilla->parse("contenido_principal");
$plantilla->out("contenido_principal");
