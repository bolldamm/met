<?php

/*
 * Display on-screen feedback from form submission
 */

require "includes/load_main_components.inc.php";

$plantilla = new XTemplate("html/index.html");

$subPlantilla = new XTemplate("html/stripe_button.html");

$plantilla->assign("SECTION_FILE_CSS", "openbox.css");
$plantilla->parse("contenido_principal.css_seccion");

require "includes/load_structure.inc.php";

require_once('config.php');
$item = $_GET["it"];
$amount = $_GET["am"];
$amountInCents = $amount * 100;
$email = $_GET["email"];
$custom = $_GET["custom"];


/**** INICIO: breadcrumb ****/
$breadCrumbUrlDetalle = "stripe_form.php";
$breadCrumbDescripcionDetalle = STATIC_INSCRIPTION_LAST_STEP_TITLE;
/**** FINAL: breadcrumb ****/

$subPlantilla->assign("ITEM_MENU_TITULO","We've done it!");
$subPlantilla->assign("KEY", $stripe["publishable_key"]);
$subPlantilla->assign("AMOUNT",$amountInCents);
$subPlantilla->assign("ITEM",$item);
$subPlantilla->assign("EMAIL",$email);
$subPlantilla->assign("CUSTOM",$custom);


$idMenu=null;
require "includes/load_breadcrumb.inc.php";


$plantilla->parse("contenido_principal.control_superior");


$subPlantilla->parse("contenido_principal");

//Exportamos plantilla secundaria a la principal
$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));

//Parse inner page content with lefthand menu
$plantilla->parse("contenido_principal.menu_left");

//Parseamos y sacamos informacion por pantalla
$plantilla->parse("contenido_principal");
$plantilla->out("contenido_principal");
?>