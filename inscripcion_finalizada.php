<?php

/*
 * Display on-screen feedback from form submission
 */

require "includes/load_main_components.inc.php";

$plantilla = new XTemplate("html/index.html");

$subPlantilla = new XTemplate("html/inscripcion_finalizada.html");

$plantilla->assign("SECTION_FILE_CSS", "openbox.css");
$plantilla->parse("contenido_principal.css_seccion");

require "includes/load_structure.inc.php";

//The type is the payment method (1=transfer, 2=Paypal, 4=direct debit)
if (isset($_GET["tipo"])) {
    switch ($_GET["tipo"]) {
        //If no payment method, display error message and break
        case 0:
            $subPlantilla->assign("INSCRIPCION_MENSAJE_FINAL", STATIC_INSCRIPTION_LAST_STEP_ERROR);
            break;
        //If bank transfer, see if conference (modo=1) or workshop (modo=2)
        case INSCRIPCION_TIPO_PAGO_TRANSFERENCIA:
            if (isset($_GET["modo"])) {
                switch ($_GET["modo"]) {
                    case 1:
                        $subPlantilla->assign("INSCRIPCION_MENSAJE_FINAL", STATIC_INSCRIPTION_CONFERENCE_LAST_STEP_SUCCESS);
                        break;
                    case 2:
                        $subPlantilla->assign("INSCRIPCION_MENSAJE_FINAL", STATIC_INSCRIPTION_WORKSHOP_LAST_STEP_SUCCESS);
                        break;
                }
            } else {
                $subPlantilla->assign("INSCRIPCION_MENSAJE_FINAL", STATIC_INSCRIPTION_LAST_STEP_SUCCESS);
            }
            break;
        //If direct debit
        case INSCRIPCION_TIPO_PAGO_DEBIT:
            $subPlantilla->assign("INSCRIPCION_MENSAJE_FINAL", STATIC_INSCRIPTION_LAST_STEP_DEBIT);
            break;
        //If Paypal, see if it's a conference or a workshop signup, or…
        case INSCRIPCION_TIPO_PAGO_PAYPAL:
            if (isset($_GET["modo"])) {
                switch ($_GET["modo"]) {
                    case 1:
                        $subPlantilla->assign("INSCRIPCION_MENSAJE_FINAL", STATIC_INSCRIPTION_CONFERENCE_LAST_STEP_PAYPAL);
                        break;
                    case 2:
                        $subPlantilla->assign("INSCRIPCION_MENSAJE_FINAL", STATIC_INSCRIPTION_WORKSHOP_LAST_STEP_PAYPAL);
                        break;
                }
                //If it's not conference or workshop, it's a membership signup, so…
            } else {
                $subPlantilla->assign("INSCRIPCION_MENSAJE_FINAL", STATIC_INSCRIPTION_LAST_STEP_PAYPAL);
            }

            break;
    }
}

/**** INICIO: breadcrumb ****/
//$breadCrumbUrlDetalle = "inscripcion_finalizada.php?tipo=".$_GET["tipo"];
$breadCrumbDescripcionDetalle = STATIC_INSCRIPTION_LAST_STEP_TITLE;
/**** FINAL: breadcrumb ****/

$subPlantilla->assign("ITEM_MENU_TITULO", STATIC_INSCRIPTION_LAST_STEP_TITLE);


$idMenu = null;
require "includes/load_breadcrumb.inc.php";


$plantilla->parse("contenido_principal.control_superior");


$subPlantilla->parse("contenido_principal");

//Exportamos plantilla secundaria a la principal
$plantilla->assign("CONTENIDO", $subPlantilla->text("contenido_principal"));

//Parse inner page content with lefthand menu
$plantilla->parse("contenido_principal.menu_left");

//Parseamos y sacamos informacion por pantalla
$plantilla->parse("contenido_principal");
$plantilla->out("contenido_principal");
?>