<?php
/**
 *
 * This script is called by the Ajax request generateConferenceRegistration() in index.html
 * First, it validates the form data
 * If there are no errors, the form data is stored in variables to be used in the database queries
 * and the signup is saved in ed_tb_inscripcion_conferencia/conferencia_linea/conferencia_extra
 *
 * Registration ID and amount are stored in session variables for use in the Stripe form
 * The signup result is stored in a hidden field (hdnTipoResultado) in process_form.html
 * If there are no errors, the result is the payment method (1=transfer, 2=credit card)
 * If payment method = credit card, registration type is stored in a second hidden field in process_form.html
 * If payment method = transfer, the script to send the automatic emails is called
 * If there are validation errors, the result is an error message
 *
 * Finally, process_form.html is sent back to the Ajax script, which either—
 * redirects to inscripcion_finalizada.php if result = 1 (transfer)
 * redirects to stripe_form.php if result = 2 (credit card), or
 * returns to conference form if captcha error or form field validation error
 *
 */

require "../includes/load_main_components.inc.php";

$esValido = true;
$mensajeError = "";

//$myfile = fopen("../html/newfile.txt", "w") or die("Unable to open file!");
//$txt = "Process started\n";
//fwrite($myfile, $txt);
//fclose($myfile);

//The template contains a pair of hidden input fields to be injected back into the conference registration form
$plantilla = new XTemplate("../html/ajax/process_form.html");

//Get values from text inputs in form
$_POST["txtNombre"] = str_replace("'", "’", $_POST["txtNombre"]);
$_POST["txtApellidos"] = str_replace("'", "’", $_POST["txtApellidos"]);

$_POST["txtNombre"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_FIRST_NAME, $_POST["txtNombre"]));
$_POST["txtApellidos"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_LAST_NAMES, $_POST["txtApellidos"]));
$_POST["txtEmail"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_EMAIL, $_POST["txtEmail"]));
$_POST["txtTelefono"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_WORKSHOP_REGISTER_CONTACT_PHONE_NO, $_POST["txtTelefono"]));
$_POST["txtaComentarios"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_WORKSHOP_REGISTER_LEGEND_COMMENTS, $_POST["txtaComentarios"]));
//$_POST["txtaBadge"] = generalUtils::escaparCadena($_POST["txtaBadge"]);
$BadgeNombre = generalUtils::escaparCadena($_POST["txtBadgeNombre"]);
if (!$BadgeNombre) {
  $BadgeNombre = $_POST["txtNombre"];
}
$BadgeNombre = str_replace("'", "&#39;",$BadgeNombre);
$BadgeApellidos = generalUtils::escaparCadena($_POST["txtBadgeApellidos"]);
if (!$BadgeApellidos) {
  $BadgeApellidos = $_POST["txtApellidos"];
}
$BadgeApellidos = str_replace("'", "&#39;",$BadgeApellidos);
$BadgeLength = STATIC_CONFERENCE_BADGE_LENGTH + 1;
$BadgeFirst = substr(generalUtils::escaparCadena($_POST["txtBadgeFirst"]), 0, $BadgeLength);
$BadgeFirst = str_replace("'", "&#39;",$BadgeFirst);
$BadgeSecond = substr(generalUtils::escaparCadena($_POST["txtBadgeSecond"]), 0, $BadgeLength);
$BadgeSecond = str_replace("'", "&#39;",$BadgeSecond);
$newCouncilrole = $_POST["cmbCouncil2"];
$BadgePronouns = substr(generalUtils::escaparCadena($_POST["txtBadgePronouns"]), 0, $BadgeLength);  
$BadgePronouns = str_replace("'", "&#39;",$BadgePronouns);
$txtBadge = $BadgeNombre . "<br />" . $BadgeApellidos . "<br />" . $BadgeFirst . "<br />" . $BadgeSecond . "<br />" . $newCouncilrole . "<br />" . $BadgePronouns;

//$txt = "Got to line 57\n";
//fwrite($myfile, $txt);

//Store form field values in variables and check privacy, set error flag if not
$nombreUsuario = $_POST["txtNombre"];
$apellidosUsuario = $_POST["txtApellidos"];
$emailUsuario = $_POST["txtEmail"];
$telefonoUsuario = $_POST["txtTelefono"];
$comentarios = $_POST["txtaComentarios"];
$idPais = "null";
$payCode = strtolower($_POST["paymentCode"]);
if (!isset($_POST["chkPrivacy"])) {
    $esValido = false;
    $privacy = 0;
} else {
    $privacy = 1;
}

//Capture photo for attendee list
$fotoconferencia = $_POST["valorimagen"];

//Create array of valid payment methods and get selected paymjent method
$vectorMetodoPago = Array(INSCRIPCION_TIPO_PAGO_TRANSFERENCIA, INSCRIPCION_TIPO_PAGO_PAYPAL);
$metodoPago = $_POST["rdMetodoPago"];

//Strip extra spaces from form field values
foreach ($_POST as $clave => $valor) {
    $_POST[$clave] = trim($valor);
}

//Set error flag if selected payment method is not in the array of valid methods
if (!in_array($metodoPago, $vectorMetodoPago)) {
    $esValido = false;
}

//$txt = "Got to line 92\n";
//fwrite($myfile, $txt);

//See if captcha is correct
include("../classes/securimage/securimage.php");
$img = new Securimage();

$valid = $img->check($_POST["txtCaptcha"]);

//If captcha is valid, proceed with registration
if ($valid) {
    //Get dates of conference workshops and minisessions from database
    $resultadoTallerFecha = $db->callProcedure("CALL ed_sp_web_taller_conferencia_fecha_bloque_obtener(" . $_SESSION["id_idioma"] . ")");

    $vectorTalleres = Array();
    $vectorMinisessions = Array();

    //Get list of workshops selected on each date
    while ($datoTallerFecha = $db->getData($resultadoTallerFecha)) {
        if (isset($_POST["rdnFecha_" . $datoTallerFecha["fecha"]])) {
            array_push($vectorTalleres, $_POST["rdnFecha_" . $datoTallerFecha["fecha"]]);
        }
    }
//$txt = "Got to line 115\n";
//fwrite($myfile, $txt);
    //Get list of minisessions selected on each date
    $resultadoTallerConferenciaMini = $db->callProcedure("CALL ed_sp_web_taller_conferencia_obtener_concreto(" . $_SESSION["id_idioma"] . ",1)");
    if ($db->getNumberRows($resultadoTallerConferenciaMini) > 0) {
        while ($datoTallerConferenciaMini = $db->getData($resultadoTallerConferenciaMini)) {
            if (isset($_POST["chkFechaMini_" . $datoTallerConferenciaMini["fecha"] . "_" . $datoTallerConferenciaMini["id_taller_fecha"]])) {
                array_push($vectorMinisessions, $datoTallerConferenciaMini["id_taller_fecha"]);
            }//end if
        }//end if
    }//end if
//$txt = "Got to line 126\n";
//fwrite($myfile, $txt);
    //Count number of selected workshops and minisessions
    $totalVectorTalleres = count($vectorTalleres);
    $totalVectorMinisessions = count($vectorMinisessions);
    $i = 0;
    $mini = 0;
    $precio = 0;

    $vectorTalleresInsertar = Array();
    $vectorMinisessiones = Array();

//$txt = "Got to line 138\n";
//fwrite($myfile, $txt);
    //If at least one workshop or minisession or the "No workshops" checkbox is selected
    if ($totalVectorTalleres > 0 || $totalVectorMinisessions > 0) {
        $talleresNormales = 0;
        $talleresMini = 0;
        //Count number of workshops selected, get details and check whether full or not
        while ($i < $totalVectorTalleres) {
            $resultadoTaller = $db->callProcedure("CALL ed_sp_web_taller_fecha_conferencia_obtener_verificaciones(" . $_SESSION["id_idioma"] . "," . $vectorTalleres[$i] . ")");
            $datoTaller = $db->getData($resultadoTaller);

            $vectorTalleresInsertar[$i]["id"] = $vectorTalleres[$i];
            $vectorTalleresInsertar[$i]["precio"] = $datoTaller["precio"];

            if ($datoTaller["es_mini"] == 0) {
                $talleresNormales++;
            }

            //If selected workshop is full, set error flag
            if ($datoTaller["total_inscritos"] >= $datoTaller["plazas"]) {
                $esValido = false;
                $mensajeError .= "<li style='padding-top:5px'>'" . $datoTaller["nombre"] . "' " . STATIC_FORM_WORKSHOP_REGISTER_FULL_MEMBER . "</li>";
            }
            $i++;
        }//end while
//$txt = "Got to line 163\n";
//fwrite($myfile, $txt);
        //Count number of minisessions selected and get details
        while ($mini < $totalVectorMinisessions) {
            $resultadoMiniTaller = $db->callProcedure("CALL ed_sp_web_taller_fecha_conferencia_obtener_verificaciones(" . $_SESSION["id_idioma"] . "," . $vectorMinisessions[$mini] . ")");
            $datoMiniTaller = $db->getData($resultadoMiniTaller);

            $vectorMinisessiones[$mini]["id"] = $vectorMinisessions[$mini];
            $vectorMinisessiones[$mini]["precio"] = $datoMiniTaller["precio"];

            if ($datoMiniTaller["es_mini"] == 1) {
                $talleresMini++;
            }

            $mini++;
        }//end while

        if ($mensajeError != "") {
            $mensajeError = "<ul>" . $mensajeError . "</ul>";
        }//end if

        //If no country is selected, set error flag
//        if (!isset($_SESSION["met_user"])) {
//            if ($_POST["cmbPais"] < 0) {
//                $mensajeError = STATIC_FORM_MEMBERSHIP_ERROR_COUNTRY_RESIDENCE;
//                $esValido = false;
//            } else {
//                $idPais = $_POST["cmbPais"];
//            }
//        }
    }

//If the captcha is invalid, set error flag
} else {
    $esValido = false;
    $mensajeError = STATIC_CAPTCHA_ENTER_LETTERS_NUMBERS_IMAGE;
}
//$txt = "Got to line 200\n";
//fwrite($myfile, $txt);
//If all validations are correct
if ($esValido) {
//$txt = "Got to line 204\n";
//fwrite($myfile, $txt);
    //NB This variable is still needed for the database call ed_sp_web_inscripcion_conferencia_insertar
    $_POST["cmbTitulo"] = "null";

    if ($_POST["cmbAsociacionHermana"] == -1) {
        $_POST["cmbAsociacionHermana"] = "null";
    }
    $idUsuarioWeb = "null";

    $esFactura = 1;
    // $esSpeaker = 0;
    $dinnerOptout = 0;
    $emailPermiso = 0;
    $esCertificado = 0;
    $esAttendeeList = 0;
    $CouncilFirsttimer = 0;

    $resultadoConferencia = $db->callProcedure("CALL ed_sp_web_conferencia_actual()");

    $datoConferencia = $db->getData($resultadoConferencia);
    $idConferencia = $datoConferencia["id_conferencia"];
    $workshopPrice = $datoConferencia["price_extra_workshop"];
    $minisessionPrice = $datoConferencia["price_extra_minisession"];
    $dinnerGuestPrice = $datoConferencia["price_dinner_guest"];
    $dinnerOptoutDiscount = $dinnerOptoutDiscount = $datoConferencia["price_dinner_optout_discount"];
    $wineReceptionGuestPrice = $datoConferencia["price_wine_reception_guest"];


    // Assign basic price (member, sister association and non-member)
  	if ($_POST["cmbSpeaker"] <= 1) {
//    if ($_POST["cmbSpeaker"]  < 1) {
        if ($datoConferencia["es_early"] > 0) {
            if (!isset($_SESSION["met_user"])) {
                if ($_POST["cmbAsociacionHermana"] == "null") {
                    $preliminaryPrice = $datoConferencia["price_non_member_late"];
                } else {
                    $preliminaryPrice = $datoConferencia["price_sister_association_late"];
                }
            } else {
                $preliminaryPrice = $datoConferencia["price_member_late"];
            }
        } else {
            if (!isset($_SESSION["met_user"])) {
                if ($_POST["cmbAsociacionHermana"] == "null") {
                    $preliminaryPrice = $datoConferencia["price_non_member_early"];
                } else {
                    $preliminaryPrice = $datoConferencia["price_sister_association_early"];
                }
            } else {
                $preliminaryPrice = $datoConferencia["price_member_early"];
            }
        }
    } else {
        if (!isset($_SESSION["met_user"])) {
            if ($_POST["cmbAsociacionHermana"] == "null") {
                $preliminaryPrice = $datoConferencia["price_non_member_speaker"];
            } else {
                $preliminaryPrice = $datoConferencia["price_sister_association_speaker"];
            }
        } else {
            $preliminaryPrice = $datoConferencia["price_member_speaker"];
        }
    }


    //Total payable before any extra or any discount
    $totalPayable = $preliminaryPrice;

    //Add extras to total payable
    $esDinnerGuests = $_POST["cmbInvitados"];
    $esWineReceptionGuests = $_POST["cmbWineReceptionGuests"];
    $dinnerGuestSupplement = $esDinnerGuests * $dinnerGuestPrice;
    $wineReceptionGuestSupplement = $esWineReceptionGuests * $wineReceptionGuestPrice;


    //If speaker checked, set variable for database call
    // if (isset($_POST["chkSpeaker"])) {
    //    $esSpeaker = 1;
    // }

    //If dinner guests, add supplement
    if ($_POST["cmbInvitados"] > 0) {
        $totalPayable += $dinnerGuestSupplement;
    }

    //If dinner optout checked, subtract discount and set variable for database call
    if (isset($_POST["chkDinner"])) {
        $totalPayable -= $dinnerOptoutDiscount;
        $dinnerOptout = 1;
    }

    //If wine reception guests, add supplement
    if ($_POST["cmbWineReceptionGuests"] > 0) {
        $totalPayable += $wineReceptionGuestSupplement;
    }

    //If email permission checked, set variable
    if (isset($_POST["chkEmailPermission"])) {
        $emailPermiso = 1;
    }

    //Certificate is not automatically on
    $esCertificado = 1;

    //If attendee list permission checked, set variable
    if (isset($_POST["chkAttendeeList"])) {
        $esAttendeeList = 1;
    }
  
    //If Council(First-timer, set variable
    if (isset($_POST["chkFirsttimer"])) {
        $CouncilFirsttimer = 1;
    } 
    if (isset($_POST["cmbCouncil"])) {
        $CouncilFirsttimer = $_POST["cmbCouncil"];
    }
//$txt = "Got to line 320\n";
//fwrite($myfile, $txt);
    /******* INICIO: ADD WORKSHOP OR MINISESSION PRICE TO TOTAL *******/
    $totalTalleresInsertar = count($vectorTalleresInsertar);
    if ($talleresNormales > 1) {
        $totalPayable += $workshopPrice * $talleresNormales;
    } else if ($talleresNormales === 1) {
        if ($talleresMini > 0) {
            $totalPayable += $workshopPrice + ($minisessionPrice * $talleresMini);
        } else {
            $totalPayable += $workshopPrice;
        }
    } else if ($talleresMini > 0) {
        $totalPayable += $minisessionPrice * $talleresMini;
    }

    //Billing details
    $_POST["txtFacturacionNifCliente"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_CUSTOMER_NIF, $_POST["txtFacturacionNifCliente"]));
    $_POST["txtFacturacionNombreCliente"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_NAME_CUSTOMER, $_POST["txtFacturacionNombreCliente"]));
    $_POST["txtFacturacionNombreEmpresa"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_NAME_COMPANY, $_POST["txtFacturacionNombreEmpresa"]));
    $_POST["txtFacturacionDireccion"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_ADDRESS, $_POST["txtFacturacionDireccion"]));
    $_POST["txtFacturacionCodigoPostal"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_ZIPCODE, $_POST["txtFacturacionCodigoPostal"]));
    $_POST["txtFacturacionCiudad"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_CITY, $_POST["txtFacturacionCiudad"]));
    $_POST["txtFacturacionProvincia"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_PROVINCE, $_POST["txtFacturacionProvincia"]));
    $_POST["txtFacturacionPais"] = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_COUNTRY, $_POST["txtFacturacionPais"]));

    //Store billing details in variables for later
    $nifFactura = $_POST["txtFacturacionNifCliente"];
    $nombreClienteFactura = $_POST["txtFacturacionNombreCliente"];
    $nombreEmpresaFactura = $_POST["txtFacturacionNombreEmpresa"];
    $direccionFactura = $_POST["txtFacturacionDireccion"];
    $codigoPostalFactura = $_POST["txtFacturacionCodigoPostal"];
    $ciudadFactura = $_POST["txtFacturacionCiudad"];
    $provinciaFactura = $_POST["txtFacturacionProvincia"];
    $paisFactura = $_POST["txtFacturacionPais"];

    //Subaccount ID is 46, "METM registration"
    $idConceptoMovimiento = MOVIMIENTO_CONCEPTO_NEW_CONFERENCE;

    $db->startTransaction();

    if ($metodoPago == INSCRIPCION_TIPO_PAGO_PAYPAL) {
        //If paying by credit card, status of registration is "Pending" until finalised
        $idEstadoInscripcion = INSCRIPCION_ESTADO_INSCRIPCION_PENDIENTE;
    } else {
        $pagado = 0;
        $idEstadoInscripcion = INSCRIPCION_ESTADO_INSCRIPCION_CONFIRMADA;
    }

    $discountCode = STATIC_DISCOUNT_CONFERENCE;
  
  	if (strpos($payCode, $discountCode) !== false) {
		$payCode = str_replace($discountCode,"",$payCode);
		$payCode = trim($payCode);
		if ($payCode > 0) {
			$totalPayable = $payCode;
		}
    }
  

    if (isset($_SESSION["met_user"])) {
        $idUsuarioWeb = $_SESSION["met_user"]["id"];
    }
//$txt = "Got to line 373". $idConferencia . "," . $idUsuarioWeb . "," . $_POST["cmbAsociacionHermana"] . "," . $idEstadoInscripcion . "," . $idPais . "," . $metodoPago . "," . $_POST["cmbTitulo"] . ",'" . $_POST["txtNombre"] . "','" . $_POST["txtApellidos"] . "','" . $_POST["txtEmail"] . "','" . $_POST["txtTelefono"] . "','" . nl2br($comentarios) . "','" . nl2br($txtBadge) . "','" . $_POST["txtFacturacionNifCliente"] . "','" . $_POST["txtFacturacionNombreCliente"] . "','" . $_POST["txtFacturacionNombreEmpresa"] . "','" . $_POST["txtFacturacionDireccion"] . "','" . $_POST["txtFacturacionCodigoPostal"] . "','" . $_POST["txtFacturacionCiudad"] . "','" . $_POST["txtFacturacionProvincia"] . "','" . $_POST["txtFacturacionPais"] . "'," . $esFactura . "," . $_POST["cmbSpeaker"] . "," . $emailPermiso . "," . $dinnerOptout . "," . $esCertificado . ",'" . $totalPayable . "','" . $fotoconferencia . "'," . $esAttendeeList . "," . $privacy . "," . $_POST["cmbDiet"] . "," . $CouncilFirsttimer;
//fwrite($myfile, $txt);
    //Insert conference registration into database
    $resultadoInscripcion = $db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_insertar(" . $idConferencia . "," . $idUsuarioWeb . "," . $_POST["cmbAsociacionHermana"] . "," . $idEstadoInscripcion . "," . $idPais . "," . $metodoPago . "," . $_POST["cmbTitulo"] . ",'" . $_POST["txtNombre"] . "','" . $_POST["txtApellidos"] . "','" . $_POST["txtEmail"] . "','" . $_POST["txtTelefono"] . "','" . nl2br($comentarios) . "','" . nl2br($txtBadge) . "','" . $_POST["txtFacturacionNifCliente"] . "','" . $_POST["txtFacturacionNombreCliente"] . "','" . $_POST["txtFacturacionNombreEmpresa"] . "','" . $_POST["txtFacturacionDireccion"] . "','" . $_POST["txtFacturacionCodigoPostal"] . "','" . $_POST["txtFacturacionCiudad"] . "','" . $_POST["txtFacturacionProvincia"] . "','" . $_POST["txtFacturacionPais"] . "'," . $esFactura . "," . $_POST["cmbSpeaker"] . "," . $emailPermiso . "," . $dinnerOptout . "," . $esCertificado . ",'" . $totalPayable . "','" . $fotoconferencia . "'," . $esAttendeeList . "," . $privacy . "," . $_POST["cmbDiet"] . "," . $CouncilFirsttimer . ")");
//$txt = "Got to line 377\n";
//fwrite($myfile, $txt);
    //The database procedure returns the registration ID (integer) and numero_inscripcion (varchar)
    $datoInscripcion = $db->getData($resultadoInscripcion);

    //Insert workshop signups in ed_tb_inscripcion_conferencia_linea using the $datoInscripcion
    for ($i = 0; $i < $totalVectorTalleres; $i++) {
        $db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_linea_insertar(" . $datoInscripcion["codigo"] . "," . $vectorTalleresInsertar[$i]["id"] . ",'" . $vectorTalleresInsertar[$i]["precio"] . "')");
    }
    //Insert minisession signups in ed_tb_inscripcion_conferencia_linea using the $datoInscripcion
    for ($i = 0; $i < $totalVectorMinisessions; $i++) {
        $db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_linea_insertar(" . $datoInscripcion["codigo"] . "," . $vectorMinisessiones[$i]["id"] . ",'" . $vectorMinisessiones[$i]["precio"] . "')");
    }
  
    /************** INICIO: EXTRAS **************/
    $vectorExtras = Array();

    $vectorExtras[0]["id"] = 2;
    $vectorExtras[0]["valor"] = $esDinnerGuests;

    $vectorExtras[1]["id"] = 7;
    $vectorExtras[1]["valor"] = $esWineReceptionGuests;

    //Insert extras (dinner guests and reception guests) in ed_sp_web_inscripcion_conferencia_extra
    //Changed "$i<=2" to "$i<count($vectorExtras)"
    for ($i = 0; $i < count($vectorExtras); $i++) {
        $db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_extra(" . $datoInscripcion["codigo"] . "," . $vectorExtras[$i]["id"] . "," . $vectorExtras[$i]["valor"] . ")");
    }
    /************** FIN: EXTRAS **************/

    //Store registration details in session variable for use in stripe_form.php
    $_SESSION["regCode"] = $datoInscripcion["numero_inscripcion"] . "-" . $datoInscripcion["codigo"] . "-1";
    $_SESSION["amount"] = $totalPayable;
  
//    $discountCode = STATIC_DISCOUNT_CONFERENCE;
  
//  	if (strpos($payCode, $discountCode) !== false) {
//		$payCode = str_replace($discountCode,"",$payCode);
//		$payCode = trim($payCode);
//		if ($payCode > 0) {
//			$_SESSION["amount"] = $payCode;
//		}
//    }

    //Store payment method ID in hidden field in process_form.html
    $plantilla->assign("TIPO_RESULTADO_INSCRIPCION", $metodoPago);
    //If credit card
    switch ($metodoPago) {
        case INSCRIPCION_TIPO_PAGO_PAYPAL:
            $plantilla->assign("ITEM", STATIC_FORM_STRIPE_ITEM_CONFERENCE_REGISTRATION);
            break;

        case INSCRIPCION_TIPO_PAGO_TRANSFERENCIA:
            $idUsuarioWebCorreo = $idUsuarioWeb;
            $numeroInscripcion = $datoInscripcion["numero_inscripcion"];
            $tipoInscripcion = 1;
            $idInscripcion = $datoInscripcion["codigo"];

            //Send emails to MET and to user for bank transfer
            require "../includes/load_send_mail_inscription_conference.inc.php";
            break;
    }
    //$img->clearCode();
    $db->endTransaction();

} else {
    $plantilla->assign("TIPO_RESULTADO_INSCRIPCION", $mensajeError);
}//end else

$plantilla->parse("contenido_principal");
$plantilla->out("contenido_principal");
//$txt = "Got to end of file\n";
//fwrite($myfile, $txt);
?>