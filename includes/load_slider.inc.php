<?php
	/**
	 * 
	 * Presentamos por pantalla el slider en caso necesario
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */

	$resultSlider = $db->callProcedure("CALL ed_sp_web_menu_archivo_obtener_listado(".$idMenu.")");
	$totalImagenes = $db->getNumberRows($resultSlider);
	if($totalImagenes > 0) {
		//Instanciamos las plantilla del slider
		$plantillaSlider = new XTemplate("html/includes/slider.inc.html");
	
		while($dataSlider = $db->getData($resultSlider)) {
			$plantillaSlider->assign("ITEM_SLIDER_IMAGEN", "files/menu/".$dataSlider["nombre"]);
			$plantillaSlider->parse("contenido_principal.item_slider");
		}
		
		$plantillaSlider->parse("contenido_principal");
		
		$plantilla->assign("SLIDER", $plantillaSlider->text("contenido_principal"));
	
		if($totalImagenes==1){
			$plantilla->parse("contenido_principal.script_nivoslider");
			
		}
		
		$plantilla->parse("contenido_principal.script_nivoslider");
		$plantilla->parse("contenido_principal.bloque_ready.javascript_nivoslider");
	}
?>