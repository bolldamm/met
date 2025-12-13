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
	$subPlantilla = new XTemplate("html/institutional_member_detail.html");
	
	/**
	 * Asignamos el CSS que corresponde a este apartado
	 */
	$plantilla->assign("SECTION_FILE_CSS", "institutional_member_detail.css");
	
	if(isset($_GET["elemento"])){
		$_GET["institutional_member"]=$_GET["elemento"];
	}
	
	require "includes/load_structure.inc.php";
	

		
	//Comprobamos si el existe el miembro institucional y lo presentamos
	if(isset($_GET["institutional_member"]) && is_numeric($_GET["institutional_member"])) {
		$resultMimebroInstitucional = $db->callProcedure("CALL ed_sp_web_usuario_web_institucion_obtener_detalle(".$_GET["institutional_member"].")");
		if($db->getNumberRows($resultMimebroInstitucional) > 0) {
			$dataMiembroInstitucional = $db->getData($resultMimebroInstitucional);
			$subPlantilla->assign("ITEM_MIEMBRO_INSTITUCIONAL_TITULO", $dataMiembroInstitucional["nombre"]);
			$subPlantilla->assign("ITEM_MIEMBRO_INSTITUCIONAL_DESCRIPCION", $dataMiembroInstitucional["descripcion"]);
			
			if($dataMiembroInstitucional["imagen"] != "") {
				$subPlantilla->assign("ITEM_MIEMBRO_INSTITUCIONAL_IMAGEN", "files/members/".$dataMiembroInstitucional["imagen"]);
				$subPlantilla->parse("contenido_principal.imagen");
			}
			
			/**** INICIO: breadcrumb ****/
			$vectorAtributosDetalle["idioma"] = $_SESSION["siglas"];
			$vectorAtributosDetalle["id_menu"] = $idMenu;
			$vectorAtributosDetalle["id_detalle"] = $_GET["institutional_member"];
			$vectorAtributosDetalle["seo_url"] = $dataMiembroInstitucional["nombre"];
			$breadCrumbUrlDetalle = generalUtils::generarUrlAmigableDetalle($vectorAtributosDetalle);
			$breadCrumbDescripcionDetalle = $dataMiembroInstitucional["nombre"];
			/**** FINAL: breadcrumb ****/
		}else{
			generalUtils::redirigir(CURRENT_DOMAIN);
		}
	}else{
		generalUtils::redirigir(CURRENT_DOMAIN);
	}
	
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