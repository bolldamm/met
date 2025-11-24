<?php
/**
 * If valid, stores result (payment method) in "process_form.html"
 * If invalid, stores error message in "process_form.html"
 * Gets data from signup form and validates it
 * Inserts signup data in database and submits data to Paypal
 * Passes data to load_save_inscription_workshop.inc.php for movement and emails
 */

require "../includes/load_main_components.inc.php";

$esValido = true;
$mensajeError = "";


//Initialise template that will contain the result, i.e., success or failure
//If bank transfer and successful, all the "process_form.html" template contains is the hidden "result" variable (i.e. the payment method, which is 1)
$plantilla = new XTemplate("../html/ajax/process_form.html");

//Delete placeholders from form fields that haven't been filled and add backslashes before any characters that need to be escaped
$_POST["txtNombre"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_FIRST_NAME, $_POST["txtNombre"]));
$_POST["txtApellidos"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_LAST_NAMES, $_POST["txtApellidos"]));
$_POST["txtEmail"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_EMAIL, $_POST["txtEmail"]));
$_POST["txtTelefono"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_WORKSHOP_REGISTER_CONTACT_PHONE_NO, $_POST["txtTelefono"]));
$_POST["txtaComentarios"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_WORKSHOP_REGISTER_LEGEND_COMMENTS, $_POST["txtaComentarios"]));

//Store personal details etc. from the form in variables (for the database procedure)
$nombreUsuario = $_POST["txtNombre"];
$apellidosUsuario = $_POST["txtApellidos"];
$emailUsuario = $_POST["txtEmail"];
$telefonoUsuario = $_POST["txtTelefono"];
$comentarios = $_POST["txtaComentarios"];
$privacy = $_POST["chkPrivacy"];
$payCode = strtolower($_POST["paymentCode"]);

if (!isset($_POST["chkPrivacy"])) {
    $esValido = false;
    $privacy = 0;
} else {
    $privacy = 1;
}

//Store the selected payment method in a variable
$vectorMetodoPago = Array(INSCRIPCION_TIPO_PAGO_TRANSFERENCIA, INSCRIPCION_TIPO_PAGO_PAYPAL);
$metodoPago = $_POST["rdMetodoPago"];

//Strip extra spaces from the content of all input fields
foreach ($_POST as $clave => $valor) {
    $_POST[$clave] = trim($valor);
}

//If payment method not in array of possible methods, set "Invalid" flag
if (!in_array($metodoPago, $vectorMetodoPago)) {
    $esValido = false;
}

//Check whether captcha text matches captcha image
include("../classes/securimage/securimage.php");
$img = new Securimage();

$valid = $img->check($_POST["txtCaptcha"], false);

//If captcha is valid (i.e., text matches image and check returns "true"
if ($valid) {
    //Assign selected workshop IDs to $vectorTalleres array (stripping out commas between IDs)
    $vectorTalleres = array_filter(explode(",", $_POST["hdnIdTaller"]));
    $totalVectorTalleres = count($vectorTalleres);
    $i = 0;
    $precio = 0;
    $esAutenticado = false;

    $idUsuarioWeb = "null";
    if (isset($_SESSION["met_user"])) {
        $esAutenticado = true;
        $idUsuarioWeb = $_SESSION["met_user"]["id"];
    }

    //Initialise an empty array to hold the workshop data (ID, price and paid/unpaid) for insertion in the database
    $vectorTalleresInsertar = Array();

    //If there are IDs in the $vectorTalleres array
    if ($totalVectorTalleres > 0) {
        //For each selected workshop (by ID)
        while ($i < $totalVectorTalleres) {
            //Get the user type (member, sister, non-member), prices (x3), max. places, no. registered, and workshop title
            $resultadoTaller = $db->callProcedure("CALL ed_sp_web_taller_fecha_obtener_verificaciones(" . $_SESSION["id_idioma"] . "," . $vectorTalleres[$i] . ")");
            $datoTaller = $db->getData($resultadoTaller);

            //Insert workshop ID into workshops array for insertion into database
            $vectorTalleresInsertar[$i]["id"] = $vectorTalleres[$i];

            //Insert applicable price into workshops array, based on user type (member, sister, non-member)
            if ($esAutenticado) {
                $vectorTalleresInsertar[$i]["precio"] = $datoTaller["precio"];
            } else {
                if ($_POST["cmbAsociacionHermana"] == -1) {
                    $vectorTalleresInsertar[$i]["precio"] = $datoTaller["precio_no_socio"];
                } else {
                    $vectorTalleresInsertar[$i]["precio"] = $datoTaller["precio_asociacion"];
                }
            }

            //If price is zero or payment type is Paypal, set signup to "Paid", otherwise "Not paid"
            if ($vectorTalleresInsertar[$i]["precio"] == 0 || $metodoPago == INSCRIPCION_TIPO_PAGO_PAYPAL) {
                $vectorTalleresInsertar[$i]["pagado"] = 1;
            } else {
                $vectorTalleresInsertar[$i]["pagado"] = 0;
            }

            //If workshop is "member only" and user is non-member (not logged in), set "Invalid" flag and assign error message
            if ($datoTaller["es_socio"] == 1 && (!isset($_SESSION["met_user"])/* && $_POST["cmbAsociacionHermana"]==-1*/)) {
                $esValido = false;
                $mensajeError .= htmlspecialchars("<li style='padding-top:5px'>" . STATIC_FORM_WORKSHOP_REGISTER_NOT_MEMBER_1 . " \"" . $datoTaller["nombre"] . "\" " . STATIC_FORM_WORKSHOP_REGISTER_NOT_MEMBER_2 . "</li>");
                //If number registered is equal to or greater than max. places, set "Invalid" flag and assign error message
            } else if ($datoTaller["total_inscritos"] >= $datoTaller["plazas"]) {
                //Pongo >= hasta que haga unas modificaciones
                $esValido = false;
                $mensajeError .= "<li style='padding-top:5px'>' " . $datoTaller["nombre"] . " ' " . STATIC_FORM_WORKSHOP_REGISTER_FULL_MEMBER . "</li>";
            }
            //Add workshop price to cumulative total in workshops array
            $precio += $vectorTalleresInsertar[$i]["precio"];
            $i++;

        }

    if ($precio == 0) {
		$metodoPago = INSCRIPCION_TIPO_PAGO_TRANSFERENCIA;
    }
        //Add format (unordered list) to error message
        if ($mensajeError != "") {
            $mensajeError = "<ul>" . $mensajeError . "</ul>";
        }
    } else {
        //If no workshops selected, assign specific error message
        $mensajeError = STATIC_FORM_WORKSHOP_REGISTER_NO_SELECTED;
        $esValido = false;
    }
} else {
    //If captcha is not valid, assign specific error message
    $esValido = false;
    $mensajeError = STATIC_CAPTCHA_ENTER_LETTERS_NUMBERS_IMAGE;
}

//If form data is valid, process the signup
if ($esValido) {

    //NB This variable is still needed for the database call ed_sp_web_inscripcion_taller_insertar
    $_POST["cmbTitulo"] = "null";

    if ($_POST["cmbAsociacionHermana"] == -1) {
        $_POST["cmbAsociacionHermana"] = "null";
    }

    $esFactura = 1;
    $esMailEnviado = 0;
    $pagado = 0;

    //Remove placeholders from billing detail inputs and add backslashes before any characters that need to be escaped
    $_POST["txtFacturacionNifCliente"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_CUSTOMER_NIF, $_POST["txtFacturacionNifCliente"]));
    $_POST["txtFacturacionNombreCliente"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_NAME_CUSTOMER, $_POST["txtFacturacionNombreCliente"]));
    $_POST["txtFacturacionNombreEmpresa"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_NAME_COMPANY, $_POST["txtFacturacionNombreEmpresa"]));
    $_POST["txtFacturacionDireccion"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_ADDRESS, $_POST["txtFacturacionDireccion"]));
    $_POST["txtFacturacionCodigoPostal"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_ZIPCODE, $_POST["txtFacturacionCodigoPostal"]));
    $_POST["txtFacturacionCiudad"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_CITY, $_POST["txtFacturacionCiudad"]));
    $_POST["txtFacturacionProvincia"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_PROVINCE, $_POST["txtFacturacionProvincia"]));
    $_POST["txtFacturacionPais"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_COUNTRY, $_POST["txtFacturacionPais"]));

    //Store values from billing detail inputs in variables
    $nifFactura = $_POST["txtFacturacionNifCliente"];
    $nombreClienteFactura = $_POST["txtFacturacionNombreCliente"];
    $nombreEmpresaFactura = $_POST["txtFacturacionNombreEmpresa"];
    $direccionFactura = $_POST["txtFacturacionDireccion"];
    $codigoPostalFactura = $_POST["txtFacturacionCodigoPostal"];
    $ciudadFactura = $_POST["txtFacturacionCiudad"];
    $provinciaFactura = $_POST["txtFacturacionProvincia"];
    $paisFactura = $_POST["txtFacturacionPais"];


    //Set subaccount to "Registration" (id_concepto_movimiento = 55)
    $idConceptoMovimiento = MOVIMIENTO_CONCEPTO_NEW_WORKSHOP;

    $db->startTransaction();

    //If payment method is Stripe
    if ($metodoPago == INSCRIPCION_TIPO_PAGO_PAYPAL) {
        //Set registration status to "Pending" until payment is confirmed
        $idEstadoInscripcion = INSCRIPCION_ESTADO_INSCRIPCION_PENDIENTE;
    } else {
        //Otherwise set registration status to "Confirmed" (for bank transfer) but "Not paid"
        $pagado = 0;
        $idEstadoInscripcion = INSCRIPCION_ESTADO_INSCRIPCION_CONFIRMADA;
    }

    //If price is zero, set email as "Sent" and registration as "Paid"
    if ($precio == 0) {
        $esMailEnviado = 1;
        $pagado = 1;
//        $metodoPago = INSCRIPCION_TIPO_PAGO_TRANSFERENCIA;
    }


    //Insert workshop signup in "ed_tb_inscripcion_taller" in database, using variables from above
    //All workshops are inserted, both bank transfer and Stripe
    $resultadoInscripcion = $db->callProcedure("CALL ed_sp_web_inscripcion_taller_insertar(" . $idUsuarioWeb . "," . $_POST["cmbAsociacionHermana"] . "," . $idEstadoInscripcion . "," . $metodoPago . "," . $_POST["cmbTitulo"] . ",'" . $_POST["txtNombre"] . "','" . $_POST["txtApellidos"] . "','" . $_POST["txtEmail"] . "','" . $_POST["txtTelefono"] . "','" . $comentarios . "','" . $_POST["txtFacturacionNifCliente"] . "','" . $_POST["txtFacturacionNombreCliente"] . "','" . $_POST["txtFacturacionNombreEmpresa"] . "','" . $_POST["txtFacturacionDireccion"] . "','" . $_POST["txtFacturacionCodigoPostal"] . "','" . $_POST["txtFacturacionCiudad"] . "','" . $_POST["txtFacturacionProvincia"] . "','" . $_POST["txtFacturacionPais"] . "'," . $esFactura . "," . $pagado . "," . $esMailEnviado . "," . $privacy . ")");
    //Retrieve signup data from the database (each signup now a database row)
    $datoInscripcion = $db->getData($resultadoInscripcion);

    /*
     * For each workshop signup in turn,
     * create a record in "ed_tb_inscripcion_taller_linea" (price, paid, attended and deleted)
     * add workshop price to total for this workshop signup
     */
    $signupTotal = 0;
    for ($i = 0; $i < $totalVectorTalleres; $i++) {
        //The "codigo" field is the id_inscripcion_taller generated automatically on insertion
        $db->callProcedure("CALL ed_sp_web_inscripcion_taller_linea_insertar(" . $datoInscripcion["codigo"] . "," . $vectorTalleresInsertar[$i]["id"] . ",'" . $vectorTalleresInsertar[$i]["precio"] . "','" . $vectorTalleresInsertar[$i]["pagado"] . "')");
        $signupTotal += $vectorTalleresInsertar[$i]["precio"];
    }

    //Store payment method (id_tipo_pago) in TIPO_RESULTADO_INSCRIPCION in "process_form.html" template
    $plantilla->assign("TIPO_RESULTADO_INSCRIPCION", $metodoPago);

    /*
     * Store registration details in session variable for use in stripe_form.php
     * Price is sum of individual workshop signups
     */

  	$discountCode = STATIC_DISCOUNT_WORKSHOP;
  
  	if (strpos($payCode, $discountCode) !== false) {
		$payCode = str_replace($discountCode,"",$payCode);
		$payCode = trim($payCode);
		if ($payCode > 0) {
			$signupTotal = $payCode;
		}
    }  
  
    $_SESSION["regCode"] = $datoInscripcion["numero_inscripcion"] . "-" . $datoInscripcion["codigo"] . "-1";
    $_SESSION["amount"] = $signupTotal;
	
    //Initiate next step in process, depending on payment method
    switch ($metodoPago) {
        //If payment method is Paypal…
        case INSCRIPCION_TIPO_PAGO_PAYPAL:
            //Store payment details (subaccount, amount, email and regID) in process_form.html
            $plantilla->assign("ITEM", STATIC_FORM_STRIPE_ITEM_WORKSHOP_REGISTRATION);
            break;
        //If payment method is bank transfer, store signup ID etc. in variables and move to next process
        case INSCRIPCION_TIPO_PAGO_TRANSFERENCIA:
            $idUsuarioWebCorreo = $idUsuarioWeb;
            $numeroInscripcion = $datoInscripcion["numero_inscripcion"];
            $tipoInscripcion = 1;
            $idInscripcion = $datoInscripcion["codigo"];

            require "../includes/load_send_mail_inscription_workshop.inc.php";
            break;
    }
    //Clear captcha image
    //$img->clearCode();
    $db->endTransaction();

    //If form data is not valid, store error message in the "TIPO_RESULTADO_INSCRIPCION" placeholder in the "process_form.html" template
} else {
    $plantilla->assign("TIPO_RESULTADO_INSCRIPCION", $mensajeError);
}//end else

$plantilla->parse("contenido_principal");
$plantilla->out("contenido_principal");
?>