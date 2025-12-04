<?php
	
	/**
	 * 
	 * Generamos las migas de pan
	 * @author eData
	 * 
	 */

	$plantillaBreadCumb=new XTemplate("html/breadcumb.html");
	
	/**
	 * 
	 * Almacenamos el total de migas de pan a mostrar
	 * @var int
	 * 
	 */
	$totalMigas=count($vectorMigas);
	/**
	 * 
	 * Nos servira para hacer de contador en el interior de la iteracion
	 * @var int
	 
	 */
	$i=0;
	for($i=0;$i<$totalMigas;$i++){
        $vectorMigas[$i]["texto"] = strip_tags($vectorMigas[$i]["texto"] ?? "");
		$plantillaBreadCumb->assign("BREADCUMB_TEXTO",$vectorMigas[$i]["texto"]);
		$plantillaBreadCumb->assign("BREADCUMB_ENLACE",$vectorMigas[$i]["url"]);
		if($i==$totalMigas-1){
			$separador="";
			$plantillaBreadCumb->parse("contenido_principal.item_breadcumb.item_breadcumb_id");
		}else{
			$separador=">";
		}
		$plantillaBreadCumb->assign("BREADCUMB_SEPARADOR",$separador);
		$plantillaBreadCumb->parse("contenido_principal.item_breadcumb");
	}
		
	$plantillaBreadCumb->parse("contenido_principal");
	
	//Exportamos a plantilla principal
	$plantilla->assign("BREADCUMB",$plantillaBreadCumb->text("contenido_principal"));
?>