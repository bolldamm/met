<?php

/**
 * 
 * Pagina de inicio desde donde se inicia el portal
 * @author Edata S.L.
 * @copyright Edata S.L.
 * @version 1.0
 */
require "includes/load_main_components.inc.php";

// Instanciamos la clase Xtemplate con la plantilla base
$plantilla = new XTemplate("html/index.html");

// Instanciamos la clase Xtemplate con la plantilla que va a contener la información del apartado
$subPlantilla = new XTemplate("html/calendar.html");

// Asignamos el CSS que corresponde a este apartado
$plantilla->assign("SECTION_FILE_CSS", "openbox.css");

require "includes/load_structure.inc.php";

$subPlantilla->assign("MENU_ID",
       $idMenu);

//Cargamos el breadcrumb
require "includes/load_breadcrumb.inc.php";

//Cargamos los menus hijos del lateral derecho
require "includes/load_menu_left.inc.php";

//Cargamos el slider en caso de que tenga imagenes
require "includes/load_slider.inc.php";

// Mike's code

$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_sp_agenda_test()";
$resultado=$db->callProcedure($codeProcedure);

$agregate = "[";
//Output names
while($dato=$db->getData($resultado)){
$fecha = $dato['fecha'];
$titulo = $dato['titulo'];
$titulo = str_replace("'","&#8217;",$titulo);
// $titulo = htmlentities($titulo);
$time = $dato['descripcion_previa'];
$link = $dato['descripcion_completa'];
  
    if ($link) {
    $summary = "', summary: '<a href=\"".$link."\">".$titulo."<br><br><small><i>Click for details</i></small><a>'},";
} else {
    $summary = "', summary: '".$titulo."'},";
}
  
  $agregate = $agregate."{ startDate: '".$fecha." ".$time."', endDate: '".$fecha.$summary;
  $agregate = str_replace(array("\r", "\n"), '', $agregate);
  
//  $agregate = $agregate."{ startDate: '".$fecha." ".$time."', endDate: '".$fecha."', summary: '<a href=\"".$link."\">".$titulo."<br><br><small><i>Click for details</i></small><a>'},";
}
$eventArray = $agregate."]";

define("DYNAMIC_EVENT_ARRAY",$eventArray);

// End of Mike's code

$subPlantilla->parse("contenido_principal");

/**
 * Realizamos todos los parse realcionados con este apartado
 */
$plantilla->parse("contenido_principal.css_form");
$plantilla->parse("contenido_principal.control_superior");
$plantilla->parse("contenido_principal.bloque_ready");

//Exportamos plantilla secundaria a la principal
$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));

//Parse inner page content with lefthand menu
$plantilla->parse("contenido_principal.menu_left");

//Parseamos y sacamos informacion por pantalla
$plantilla->parse("contenido_principal");
$plantilla->out("contenido_principal");
?>