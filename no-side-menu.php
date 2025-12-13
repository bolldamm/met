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
	
	//Parseamos funcion de validacion
	// $plantilla->parse("contenido_principal.validar_formulario_contacto");
	
	// Instanciamos la clase Xtemplate con la plantilla que va a contener la información del apartado
	$subPlantilla = new XTemplate("html/no-side-menu.html");
	
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
	
//	if(isset($_GET["code"]) && $_GET["code"]){
//		$subPlantilla->assign("CLASE_MENSAJE_ESTADO","OK");
//		$subPlantilla->assign("DISPLAY_MENSAJE_ESTADO","");	
//		$subPlantilla->assign("MENSAJE_ESTADO",STATIC_CONTACT_SENDED);	
//				
//		}else{
//		$subPlantilla->assign("CLASE_MENSAJE_ESTADO","KO");
//				$subPlantilla->assign("DISPLAY_MENSAJE_ESTADO","none");
//			}
	
	
	$plantilla->assign("TITULO_WEB", STATIC_TITLE_WEB_HOME);
	
	//Cargamos el breadcrumb
	require "includes/load_breadcrumb.inc.php";
	
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
    $plantilla->parse("contenido_principal.full_width_content");

	//Parseamos y sacamos informacion por pantalla
	$plantilla->parse("contenido_principal");
	$plantilla->out("contenido_principal");
?>