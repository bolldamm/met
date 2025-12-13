<?php
	/**
	 * 
	 * @author Mike
	 */
	
	// session_start();
	require "includes/load_main_components.inc.php";
	
	// Instanciamos la clase Xtemplate con la plantilla base
	$plantilla = new XTemplate("html/index.html");

$subPlantilla = new XTemplate("html/forms/conference_backoffice.html");

$subPlantilla->assign("ID_MENU",$_GET["menu"]);

// MIKE'S CODE

	// Get currrent conference ID
	$result = $db->callProcedure("CALL ed_pr_get_current_id_conferencia()");
	$row = $result->fetch_assoc();
	$idConferencia = $row["current_id_conferencia"];

      // Get conference form freeze data
	  $result = $db->callProcedure("CALL ed_pr_get_conference_freeze_data($idConferencia)");
	  $row = $result->fetch_assoc();

if ($row["freeze_dinner_choices"]) {
  $subPlantilla->assign("DINNER", "checked");
  }
if ($row["freeze_all_metm_choices"]) {
  $subPlantilla->assign("FREEZE", "checked");
    }
if ($row["time_band_incompatibility"]) {
  $subPlantilla->assign("INCOMPATIBILITY", "checked");
      }
if ($row["freeze_offmetm_choices"]) {
  $subPlantilla->assign("OFFMETM", "checked");
      }
if ($row["choir_overlap_warning"]) {
  $subPlantilla->assign("CHOIR", "checked");
      }
if ($row["disable_raffle"]) {
  $subPlantilla->assign("RAFFLE", "checked");
      }
if ($row["disable_catchup"]) {
  $subPlantilla->assign("CATCHUP", "checked");
      }
if ($row["disable_lastminute"]) {
  $subPlantilla->assign("LASTMINUTE", "checked");
      }


// END OF MIKE'S CODE
	
	/**
	 * Asignamos el CSS que corresponde a este apartado
	 */
	$plantilla->assign("SECTION_FILE_CSS", "openbox.css");
	$plantilla->parse("contenido_principal.css_seccion");

	require "includes/load_structure.inc.php";

	if(isset($_GET["menu"]) && is_numeric($_GET["menu"])) {
		//Cargamos toda la información de openbox
		$resultOpenbox = $db->callProcedure("CALL ed_sp_web_openbox_obtener(".$_SESSION["id_idioma"].", ".$_GET["menu"].")");
		if($db->getNumberRows($resultOpenbox) > 0) {
			$dataOpenbox = $db->getData($resultOpenbox);
			$subPlantilla->assign("OPENBOX_DESCRIPCION", $dataOpenbox["descripcion"]);
		}
	}
	$plantilla->assign("TITULO_WEB", STATIC_TITLE_WEB_HOME);
	//Cargamos el breadcrumb
	require "includes/load_breadcrumb.inc.php";
	
	//Cargamos los menus hijos del lateral derecho
	require "includes/load_menu_left.inc.php";

	//Cargamos el slider en caso de que tenga imagenes
	require "includes/load_slider.inc.php";
	
	$subPlantilla->parse("contenido_principal");
	
	/**
	 * Realizamos todos los parse realcionados con este apartado
	 */
	$plantilla->parse("contenido_principal.css_form");
	$plantilla->parse("contenido_principal.bloque_ready");
	$plantilla->parse("contenido_principal.control_superior");
	
	//Exportamos plantilla secundaria a la principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
    //Parse inner page content with lefthand menu
    $plantilla->parse("contenido_principal.menu_left");

	//Parseamos y sacamos informacion por pantalla
	$plantilla->parse("contenido_principal");
	$plantilla->out("contenido_principal");

?>