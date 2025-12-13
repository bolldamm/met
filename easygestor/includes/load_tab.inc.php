<?php
	/**
	 * 
	 * Generamos las tabs multidioma para easyGestor
	 * @author eData
	 * 
	 */
	
	//Plantilla tab
	$plantillaTab=new XTemplate("html/tab.html");

	//Obtenemos idiomas
	$resultadoIdioma=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_idioma_obtener()");
	
	/**
	 * 
	 * Almacenaremos todos los idiomas en un vector para tenerlos posteriormente 
	 * para generar el contenido idioma y no volver a llamar al store
	 * @var array
	 *
	 */
	$vectorIdioma=Array();
	
	$i=0;
	while($dato=$db->getData($resultadoIdioma)){
		$plantillaTab->assign("IDIOMA_DESCRIPCION",$dato["nombre"]);
		$plantillaTab->assign("IDIOMA_ID",$dato["id_idioma"]);
		if($i==0){
			$plantillaTab->assign("IDIOMA_DEFECTO_ID",$dato["id_idioma"]);
			$plantillaTab->assign("IDIOMA_CLASE","class='active'");
		}else{
			$plantillaTab->assign("IDIOMA_CLASE","");
		}
		$plantillaTab->parse("contenido_principal.item_tab");
		
		//Guardamos en matriz
		array_push($vectorIdioma,$dato["id_idioma"]);
		$i++;
	}
	$totalVectorIdioma=count($vectorIdioma);
	
	//Contruimos bloque tab
	$plantillaTab->parse("contenido_principal");
	
	//Exportamos a subplantilla
	$subPlantilla->assign("TABS",$plantillaTab->text("contenido_principal"));
?>