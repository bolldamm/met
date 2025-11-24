<?php
	/**
	 * 
	 * Pagina de inicio desde donde se inicia el portal
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */

	require "includes/load_main_components.inc.php";
	
if($_SESSION["met_user"]["tipoUsuario"] < TIPO_USUARIO_EDITOR)
{
	if(!isset($_SESSION["registration_desk"]))
		{
			generalUtils::redirigir(CURRENT_DOMAIN);
		}
}

	$attendeeName = $_SESSION["conference_attendee_name"];
	unset($_SESSION["conference_attendee_name"]);
	$attendeeNumber = $_SESSION["conference_attendee"];
	unset($_SESSION["conference_attendee"]);

    if(!$attendeeNumber) {
    			generalUtils::redirigir("https://www.metmeetings.org/en/registration:1408");
    		}

	// Instanciamos la clase Xtemplate con la plantilla base
	$plantilla = new XTemplate("html/index.html");
	
	// Instanciamos la clase Xtemplate con la plantilla que va a contener la información del apartado
	$subPlantilla = new XTemplate("html/forms/form_attendee_register.html");
	
	$subPlantilla->assign("ID_MENU",$_GET["menu"]);
	
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
	
	//Cargamos el slider en caso de que tenga imagenes
	require "includes/load_slider.inc.php";

	$result = $db->callProcedure("CALL ed_pr_get_conference_notes(" . $attendeeNumber . ")");
	$row = $result->fetch_assoc();

	$subPlantilla->assign("ATTENDEE_NAME", $attendeeName);
	$subPlantilla->assign("ATTENDEE_NUMBER", $attendeeNumber);
	$subPlantilla->assign("ATTENDEE_NOTES", $row["observaciones"]);
	
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
    $plantilla->parse("contenido_principal.full_width_content");

	//Parseamos y sacamos informacion por pantalla
	$plantilla->parse("contenido_principal");
	$plantilla->out("contenido_principal");
?>