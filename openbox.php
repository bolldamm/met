<?php
	/**
	 * 
	 * Pagina openbox de contenido libre
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */

	require "includes/load_main_components.inc.php";
	
	// Instanciamos la clase Xtemplate con la plantilla base
	$plantilla = new XTemplate("html/index.html");
	
	// Instanciamos la clase Xtemplate con la plantilla que va a contener la información del apartado
	$subPlantilla = new XTemplate("html/openbox.html");
	
	/**
	 * Asignamos el CSS que corresponde a este apartado
	 */
	$plantilla->assign("SECTION_FILE_CSS", "openbox.css");
	
	require "includes/load_structure.inc.php";
	
	//Cargamos el menu
//	require "includes/load_menu.inc.php";
	
	//Cargamos los idiomas
//	require "includes/load_language_menu.inc.php";
	
	if(isset($_GET["menu"]) && is_numeric($_GET["menu"])) {
		//Cargamos toda la información de openbox
		$resultOpenbox = $db->callProcedure("CALL ed_sp_web_openbox_obtener(".$_SESSION["id_idioma"].", ".$_GET["menu"].")");
		if($db->getNumberRows($resultOpenbox) > 0) {
			$dataOpenbox = $db->getData($resultOpenbox);
			
			//Verificamos si tiene contenido, si no es así buscaremos auqel hijo que si lo tenga
			if($dataOpenbox["descripcion"] == "" && $dataOpenbox["id_formulario"] == "") {
				$resultOpenbox = $db->callProcedure("CALL ed_sp_web_menu_hijo_contenido_obtener(".$idMenu.",".$idMenuTipo.",".$_SESSION["id_idioma"].", 1, '', '', '')");
				$dataOpenbox = $db->getData($resultOpenbox);
				
				if($dataOpenbox["id_modulo"] == 1){
					$idMenu = $dataOpenbox["id_menu"];
				}
			}
			
			$subPlantilla->assign("OPENBOX_DESCRIPCION", $dataOpenbox["descripcion"]);
			
			//Comprobamos si esta página contiene formulario
			if($dataOpenbox["id_formulario"] > 0) {
				//Instanciamos la clase Xtemplate con la plantilla del formulario seleccionado
				$plantillaFormulario = new XTemplate("html/forms/".$dataOpenbox["formulario"]);

				
				require "includes/forms/".$dataOpenbox["verificacion"];

				if($dataOpenbox["id_formulario"]==3){
					/*if($_SESSION["met_user"]["tipoUsuario"]!=1){
						generalUtils::redirigir(CURRENT_DOMAIN);
					}*/
					if($_SESSION["met_user"]["institution_id"]!=""){
						generalUtils::redirigir(CURRENT_DOMAIN);
					}
					
					if($_SESSION["met_user"]["pagado"]==1){
						$plantillaFormulario->parse("contenido_principal.bloque_formulario");	
					}else{
						$fechaInscripcion=$_SESSION["met_user"]["fecha_inscripcion"];
						$fechaInscripcionDesglosada=explode(" ", $fechaInscripcion);
						$plantillaFormulario->assign("FECHA_INSCRIPCION",generalUtils::conversionFechaFormato($fechaInscripcionDesglosada[0]));
						$subPlantilla->assign("OPENBOX_DESCRIPCION", "");
						$plantillaFormulario->parse("contenido_principal.bloque_formulario_caducado");	
					}
				
				}
				$plantillaFormulario->parse("contenido_principal");	
				
				//Exportamos plantilla del formulario a la subplantilla
				$subPlantilla->assign("FORMULARIO",$plantillaFormulario->text("contenido_principal"));
				//Habilitamos el css para formularios
				$plantilla->parse("contenido_principal.css_form");
			}
			
		} else { generalUtils::redirigir(CURRENT_DOMAIN); }
	} else { generalUtils::redirigir(CURRENT_DOMAIN); }
	
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
	$plantilla->parse("contenido_principal.validaciones");
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