<?php

/**
 * 
 * Presentamos por pantalla este formulario
 * @author Edata S.L.
 * @copyright Edata S.L.
 * @version 1.0
 */
//Assign sister association drop down to placeholder in form template
$plantillaFormulario->assign("COMBO_ASOCIACIONES",
        generalUtils::construirCombo($db,
                "CALL ed_sp_web_asociacion_hermana_obtener_combo()",
                "cmbAsociacionHermana",
                "cmbAsociacionHermana",
                -1,
                "descripcion",
                "id_asociacion_hermana",
                STATIC_GLOBAL_COMBO_DEFAULT,
                -1,
                "onchange='reinicializarPrecioTotal()'",
                'class="inputText left" style="width:auto;"'));

$plantillaFormulario->parse("contenido_principal.bloque_sister_association");

//Retrieve details of current year's conference from the database
$resultadoConferencia = $db->callProcedure("CALL ed_sp_web_conferencia_actual()");

//Get conference prices
$datoConferencia = $db->getData($resultadoConferencia);

//Assign conference prices from the Easygestor to placeholders (values of hidden fields in template)
$plantillaFormulario->assign("CONFERENCE_PRICE_MEMBER_SPEAKER",
        $datoConferencia["price_member_speaker"]);
$plantillaFormulario->assign("CONFERENCE_PRICE_SISTER_SPEAKER",
        $datoConferencia["price_sister_association_speaker"]);
$plantillaFormulario->assign("CONFERENCE_PRICE_NON_MEMBER_SPEAKER",
        $datoConferencia["price_non_member_speaker"]);
$plantillaFormulario->assign("CONFERENCE_PRICE_MEMBER_EARLY",
        $datoConferencia["price_member_early"]);
$plantillaFormulario->assign("CONFERENCE_PRICE_SISTER_EARLY",
        $datoConferencia["price_sister_association_early"]);
$plantillaFormulario->assign("CONFERENCE_PRICE_NON_MEMBER_EARLY",
        $datoConferencia["price_non_member_early"]);
$plantillaFormulario->assign("CONFERENCE_PRICE_MEMBER_LATE",
        $datoConferencia["price_member_late"]);
$plantillaFormulario->assign("CONFERENCE_PRICE_SISTER_LATE",
        $datoConferencia["price_sister_association_late"]);
$plantillaFormulario->assign("CONFERENCE_PRICE_NON_MEMBER_LATE",
        $datoConferencia["price_non_member_late"]);
$plantillaFormulario->assign("CONFERENCE_PRICE_EXTRA_WORKSHOP",
        $datoConferencia["price_extra_workshop"]);
$plantillaFormulario->assign("CONFERENCE_PRICE_EXTRA_MINIS_earlySION",
        $datoConferencia["price_extra_minisession"]);
$plantillaFormulario->assign("CONFERENCE_PRICE_DINNER_GUEST",
        $datoConferencia["price_dinner_guest"]);
$plantillaFormulario->assign("CONFERENCE_PRICE_DINNER_OPTOUT_DISCOUNT",
        $datoConferencia["price_dinner_optout_discount"]);
$plantillaFormulario->assign("CONFERENCE_PRICE_WINE_RECEPTION_GUEST",
        $datoConferencia["price_wine_reception_guest"]);

// Before any options are selected, the total payable is either "member" or "non-member" (early or late)
if (isset($_SESSION["met_user"])) {
    if ($datoConferencia["es_early"] <= 0) {
        $preliminaryPrice = $datoConferencia["price_member_early"];
    } else {
        $preliminaryPrice = $datoConferencia["price_member_late"];
    }

    // Elements with the "ifmember" attribute are hidden (e.g. the sister association dropdown)
    $ifmember = 'display:none;';
} else {
    if ($datoConferencia["es_early"] <= 0) {
        $preliminaryPrice = $datoConferencia["price_non_member_early"];
    } else {
        $preliminaryPrice = $datoConferencia["price_non_member_late"];
    }
    $ifmember = 'display:block;';
}

// Assign the initial price (member or non-member) to the total payable field
$plantillaFormulario->assign("FORM_CONFERENCE_PRECIO_TOTAL",
        sprintf("%.0f",
                $preliminaryPrice));

//Assign other price variables
$extraWorkshopPrice = $datoConferencia[price_extra_workshop];
$extraMinisessionPrice = $datoConferencia[price_extra_minisession];
$dinnerGuestPrice = $datoConferencia[price_dinner_guest];
$dinnerOptoutDiscount = $dinnerOptoutDiscount = $datoConferencia[price_dinner_optout_discount];
$wineReceptionGuestPrice = $datoConferencia[price_wine_reception_guest];

require "includes/load_format_date.inc.php";

//Get list of workshops and minisessions for the current conference
$resultadoTallerConferencia = $db->callProcedure("CALL ed_sp_web_taller_conferencia_obtener_concreto(" . $_SESSION["id_idioma"] . ",-1)");

$fechaActual = "";
$esMini = false;
while ($datoTallerConferencia = $db->getData($resultadoTallerConferencia)) {

    // for each workshop or minisession calculate the day of the week
    if ($fechaActual != "" && $fechaActual != $datoTallerConferencia["fecha"]) {
        // convert date separator from dash to slash
        $fechaTaller = generalUtils::conversionFechaFormato($fechaActual,
                        "-",
                        "/");
        // create array of fechaTaller with d, m and y
        $mesTaller = explode("/",
                $fechaTaller);

        // create array of fechaActual with d, m and y
        $fechaTrozeada = explode("-",
                $fechaActual);

        // create a timestamp of mesTaller
        $fechaTimeStamp = mktime(0,
                0,
                0,
                $mesTaller[1],
                $mesTaller[0],
                $mesTaller[2]);
        // find the day of the week from PHP date() function with timestamp of mesTaller
        $diaSemana = $vectorSemana[date("N",
                        $fechaTimeStamp)];

        // assign calculated weekday, day number and month to placeholder (heading for list of workshops)
        $plantillaFormulario->assign("CONFERENCIA_TALLER_FECHA_BLOQUE",
                $diaSemana . ", " . intval($fechaTrozeada[2]) . " " . $vectorMes[$fechaTrozeada[1]]);

        // if it's a minisession, assign it to the minisession block
        if ($esMini) {
            $plantillaFormulario->parse("contenido_principal.bloque_fecha_taller.bloque_taller_mini");
        }//end if
        // if it's a regular workshop, assign it to the workshops block
        $plantillaFormulario->parse("contenido_principal.bloque_fecha_taller");
        $esMini = false;
    }

    // for each workshop or minisession, check whether it's full
    // and if number registered is greater than or equal to max. permitted add "Full" label
    $literalFull = "";

    if ($datoTallerConferencia["total_inscritos"] >= $datoTallerConferencia["plazas"]) {
        $literalFull = "<span style='color:#8B1513'> FULL!</span>";
        $plantillaFormulario->assign("ITEM_TALLER_DISABLED",
                "disabled='true'");
    } else {
        $plantillaFormulario->assign("ITEM_TALLER_DISABLED",
                "");
    }

    // for each workshop or minisession, assign name, date and IDs to placeholders in template
    $plantillaFormulario->assign("CONFERENCIA_TALLER",
            $datoTallerConferencia["nombre"] . $literalFull);
    $plantillaFormulario->assign("CONFERENCIA_FECHA",
            $datoTallerConferencia["fecha"]);
    $plantillaFormulario->assign("CONFERENCIA_ID_TALLER_FECHA",
            $datoTallerConferencia["id_taller_fecha"]);
    $plantillaFormulario->assign("CONFERENCIA_ID_TALLER",
            $datoTallerConferencia["id_taller"]);
    $plantillaFormulario->assign("CONFERENCIA_PRECIO_TALLER",
            $datoTallerConferencia["id_taller_fecha"]);

    // assign prices to workshop or minisession (member, sister and non-member)
    $plantillaFormulario->assign("CONFERENCIA_PRECIO_MIEMBRO_TALLER_FECHA",
            $datoTallerConferencia["precio"]);
    $plantillaFormulario->assign("CONFERENCIA_PRECIO_ASOCIACION_TALLER_FECHA",
            $datoTallerConferencia["precio_asociacion"]);
    $plantillaFormulario->assign("CONFERENCIA_PRECIO_NO_MIEMBRO_ID_TALLER_FECHA",
            $datoTallerConferencia["precio_no_socio"]);

    // if it's a minisession, assign it to the minisession block
    if ($datoTallerConferencia["es_mini"] == 1) {
        $plantillaFormulario->parse("contenido_principal.bloque_fecha_taller.bloque_taller_mini.fila_conferencia_taller_mini");
        $esMini = true;
        // otherwise assign it to the regular workshops block
    } else {
        $plantillaFormulario->parse("contenido_principal.bloque_fecha_taller.fila_conferencia_taller");
    }

    // set $fechaActual equal to the date of the next workshop of minisession in the list
    $fechaActual = $datoTallerConferencia["fecha"];
}

// create empty workshops block when there are no workshops or minisessions
if ($fechaActual != "") {
    $fechaTaller = generalUtils::conversionFechaFormato($fechaActual,
                    "-",
                    "/");
    $mesTaller = explode("/",
            $fechaTaller);

    //Proceso para obtener dia de la semana
    $fechaTrozeada = explode("-",
            $fechaActual);
    $fechaTimeStamp = mktime(0,
            0,
            0,
            $mesTaller[1],
            $mesTaller[0],
            $mesTaller[2]);
    $diaSemana = $vectorSemana[date("N",
                    $fechaTimeStamp)];

    $plantillaFormulario->assign("CONFERENCIA_TALLER_FECHA_BLOQUE",
            $diaSemana . ", " . intval($fechaTrozeada[2]) . " " . $vectorMes[$fechaTrozeada[1]]);

    //Miramos si hay mini...
    if ($esMini) {
        $plantillaFormulario->parse("contenido_principal.bloque_fecha_taller.bloque_taller_mini");
    }//end if
    //$plantillaFormulario->assign("CONFERENCIA_TALLER_FECHA_BLOQUE",$fechaActual);
    $plantillaFormulario->parse("contenido_principal.bloque_fecha_taller");
}

// hide the "I want an invoice" checkbox
$plantillaFormulario->assign("DISPLAY_BLOQUE_INVOICE",
        "style='display:none'");

// insert placeholder values into billing details fields
$plantillaFormulario->assign("FORM_PROFILE_BILLING_CUSTOMER_NIF",
        STATIC_FORM_PROFILE_BILLING_CUSTOMER_NIF);
$plantillaFormulario->assign("FORM_PROFILE_BILLING_NAME_CUSTOMER",
        STATIC_FORM_PROFILE_BILLING_NAME_CUSTOMER);
$plantillaFormulario->assign("FORM_PROFILE_BILLING_NAME_COMPANY",
        STATIC_FORM_PROFILE_BILLING_NAME_COMPANY);
$plantillaFormulario->assign("FORM_PROFILE_BILLING_ADDRESS",
        STATIC_FORM_PROFILE_BILLING_ADDRESS);
$plantillaFormulario->assign("FORM_PROFILE_BILLING_ZIPCODE",
        STATIC_FORM_PROFILE_BILLING_ZIPCODE);
$plantillaFormulario->assign("FORM_PROFILE_BILLING_CITY",
        STATIC_FORM_PROFILE_BILLING_CITY);
$plantillaFormulario->assign("FORM_PROFILE_BILLING_PROVINCE",
        STATIC_FORM_PROFILE_BILLING_PROVINCE);
$plantillaFormulario->assign("FORM_PROFILE_BILLING_COUNTRY",
        STATIC_FORM_PROFILE_BILLING_COUNTRY);

    // check early bird date and set hidden Earlybird variable (for price calculation)
    if ($datoConferencia["es_early"] <=0) {
        $plantillaFormulario->parse("contenido_principal.early_bird");
    }

// if user is logged in (i.e. a paid-up member)	
if (isset($_SESSION["met_user"])) {
    //get profile photo for attendee list
    $resultadoImagen = $db->callProcedure("CALL ed_sp_obtener_imagen(" . $_SESSION["met_user"]["id"] . ")");
    $datoImagen = $db->getData($resultadoImagen);
    $imagen_miembro = "https://metmeetings.org/files/members/thumb/" . $datoImagen["imagen"];

    // insert member details in form
    $resultadoUsuarioIndividual = $db->callProcedure("CALL ed_sp_web_usuario_web_individual_obtener_concreto(" . $_SESSION["met_user"]["id"] . ")");

    $datoUsuarioIndividual = $db->getData($resultadoUsuarioIndividual);

    $idTratamientoUsuarioWeb = $datoUsuarioIndividual["id_tratamiento_usuario_web"];
    $plantillaFormulario->assign("FORM_MEMBERSHIP_IMAGE",
            $imagen_miembro);
    $plantillaFormulario->assign("FORM_MEMBERSHIP_FIRST_NAME",
            $datoUsuarioIndividual["nombre"]);
    $plantillaFormulario->assign("FORM_MEMBERSHIP_LAST_NAMES",
            $datoUsuarioIndividual["apellidos"]);
    $plantillaFormulario->assign("FORM_MEMBERSHIP_EMAIL",
            $datoUsuarioIndividual["correo_electronico"]);
    if ($datoUsuarioIndividual["telefono_casa"] != "") {
        $plantillaFormulario->assign("FORM_WORKSHOP_REGISTER_CONTACT_PHONE_NO",
                $datoUsuarioIndividual["telefono_casa"]);
    } else {
        $plantillaFormulario->assign("FORM_WORKSHOP_REGISTER_CONTACT_PHONE_NO",
                STATIC_FORM_WORKSHOP_REGISTER_CONTACT_PHONE_NO);
    }

    // insert member billing details into form fields (for logged-in members)
    $resultadoUsuarioConcreto = $db->callProcedure("CALL ed_sp_web_usuario_web_datos_factura_obtener(" . $_SESSION["met_user"]["id"] . ")");
    $datoUsuarioConcreto = $db->getData($resultadoUsuarioConcreto);

    if ($datoUsuarioConcreto["nif_cliente_factura"] != "") {
        $plantillaFormulario->assign("FORM_PROFILE_BILLING_CUSTOMER_NIF",
                $datoUsuarioConcreto["nif_cliente_factura"]);
    }

    if ($datoUsuarioConcreto["nombre_cliente_factura"] != "") {
        $plantillaFormulario->assign("FORM_PROFILE_BILLING_NAME_CUSTOMER",
                $datoUsuarioConcreto["nombre_cliente_factura"]);
    }

    if ($datoUsuarioConcreto["nombre_empresa_factura"] != "") {
        $plantillaFormulario->assign("FORM_PROFILE_BILLING_NAME_COMPANY",
                $datoUsuarioConcreto["nombre_empresa_factura"]);
    }

    if ($datoUsuarioConcreto["direccion_factura"] != "") {
        $plantillaFormulario->assign("FORM_PROFILE_BILLING_ADDRESS",
                $datoUsuarioConcreto["direccion_factura"]);
    }

    if ($datoUsuarioConcreto["codigo_postal_factura"] != "") {
        $plantillaFormulario->assign("FORM_PROFILE_BILLING_ZIPCODE",
                $datoUsuarioConcreto["codigo_postal_factura"]);
    }

    if ($datoUsuarioConcreto["ciudad_factura"] != "") {
        $plantillaFormulario->assign("FORM_PROFILE_BILLING_CITY",
                $datoUsuarioConcreto["ciudad_factura"]);
    }

    if ($datoUsuarioConcreto["provincia_factura"] != "") {
        $plantillaFormulario->assign("FORM_PROFILE_BILLING_PROVINCE",
                $datoUsuarioConcreto["provincia_factura"]);
    }

    if ($datoUsuarioConcreto["pais_factura"] != "") {
        $plantillaFormulario->assign("FORM_PROFILE_BILLING_COUNTRY",
                $datoUsuarioConcreto["pais_factura"]);
    }

    // if user logged in (member), set hidden Login variable (for price calculation)
    $plantillaFormulario->parse("contenido_principal.input_logueado");

} else {
    // if user is not logged in (i.e. non-member or sister association member)
    // insert placeholder values into personal details fields
    $idTratamientoUsuarioWeb = -1;
    $plantillaFormulario->assign("FORM_MEMBERSHIP_FIRST_NAME",
            STATIC_FORM_MEMBERSHIP_FIRST_NAME);
    $plantillaFormulario->assign("FORM_MEMBERSHIP_LAST_NAMES",
            STATIC_FORM_MEMBERSHIP_LAST_NAMES);
    $plantillaFormulario->assign("FORM_MEMBERSHIP_EMAIL",
            STATIC_FORM_MEMBERSHIP_EMAIL);
    $plantillaFormulario->assign("FORM_WORKSHOP_REGISTER_CONTACT_PHONE_NO",
            STATIC_FORM_WORKSHOP_REGISTER_CONTACT_PHONE_NO);

    // the default image for the attendee list is the MET logo 
    $imagen_miembro = "https://metmeetings.org/files/members/default.jpg";
    $plantillaFormulario->assign("FORM_MEMBERSHIP_IMAGE",
            $imagen_miembro);

    //Combo paises
    $plantillaFormulario->assign("COMBO_PAIS",
            generalUtils::construirCombo($db,
                    "CALL ed_sp_web_pais_obtener_combo()",
                    "cmbPais",
                    "cmbPais",
                    -1,
                    "nombre_original",
                    "id_pais",
                    STATIC_FORM_MEMBERSHIP_COUNTRY_OF_RESIDENCE,
                    -1,
                    'class="inputText left required" style="width:283px;"'));

    $plantillaFormulario->parse("contenido_principal.bloque_pais");
    $plantilla->parse("contenido_principal.validar_conference_register.validacion_pais");
}

//Combo dinner guests
$matrizOrden[0]["descripcion"] = 0;
$matrizOrden[1]["descripcion"] = 1;
$matrizOrden[2]["descripcion"] = 2;
$matrizOrden[3]["descripcion"] = 3;

$plantillaFormulario->assign("COMBO_GUEST",
        generalUtils::construirComboMatriz($matrizOrden,
                "cmbInvitados",
                "cmbInvitados",
                -1,
                0,
                -1,
                "",
                'onclick="reinicializarPrecioTotal()" class="inputText" style="width:63px;"'));

//Combo wine reception guests
$matrizZrden[0]["descripcion"] = 0;
$matrizZrden[1]["descripcion"] = 1;
$matrizZrden[2]["descripcion"] = 2;
$matrizZrden[3]["descripcion"] = 3;

$plantillaFormulario->assign("COMBO_WINE_RECEPTION_GUEST",
        generalUtils::construirComboMatriz($matrizZrden,
                "cmbWineReceptionGuests",
                "cmbWineReceptionGuests",
                -1,
                0,
                -1,
                "",
                'onclick="reinicializarPrecioTotal()" class="inputText" style="width:63px;"'));

$plantillaFormulario->assign("COMBO_TITULOS",
        generalUtils::construirCombo($db,
                "CALL ed_sp_web_tratamiento_usuario_web_obtener_combo(" . $_SESSION["id_idioma"] . ")",
                "cmbTitulo",
                "cmbTitulo",
                $idTratamientoUsuarioWeb,
                "nombre",
                "id_tratamiento_usuario_web",
                STATIC_FORM_MEMBERSHIP_TITLE,
                -1,
                'class="inputText left" style="width:63px;"'));

$subPlantilla->assign("SESSION_ID",
        md5(uniqid(time())));
$plantillaFormulario->assign("IFMEMBER",
        $ifmember);
$plantilla->parse("contenido_principal.validar_conference_register");
?>

