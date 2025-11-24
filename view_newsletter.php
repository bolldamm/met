<?php
	/**
	 * 
	 * Pagina de inicio desde donde se inicia el portal
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */

	require "includes/load_main_components.inc.php";
	
	
	// Instanciamos la clase Xtemplate con la plantilla que va a contener la información del apartado
	$plantilla = new XTemplate("html/view_newsletter.html");
	

	require "includes/load_structure.inc.php";
	
		
	//Comprobamos si existe el newsletter y la presentamos
	if(isset($_GET["id"]) && $_GET["id"]!="") {
		$resultNewsletter = $db->callProcedure("CALL ed_sp_web_eletter_obtener('".generalUtils::escaparCadena($_GET["id"])."')");
		if($db->getNumberRows($resultNewsletter) > 0) {
			$dataNewsletter =  $db->getData($resultNewsletter);
			$plantilla->assign("NEWSLETTER_DESCRIPCION",$dataNewsletter["descripcion"]);

		}else{
			generalUtils::redirigir(CURRENT_DOMAIN);
		}
	}else{
		generalUtils::redirigir(CURRENT_DOMAIN);
	}
	
	//Cargamos el breadcrumb
	require "includes/load_breadcrumb.inc.php";
		
	
	//Cargamos el slider en caso de que tenga imagenes
	require "includes/load_slider.inc.php";
	
	/**
	 * Realizamos todos los parse realcionados con este apartado
	 */
	
	$plantilla->parse("contenido_principal.bloque_ready");
	
	$plantilla->parse("contenido_principal");
	

	$plantilla->out("contenido_principal");
?>