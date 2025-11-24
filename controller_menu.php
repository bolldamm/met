<?php 
	/**
	 * 
	 * Pagina principal que viene desde .htaccess y nos indica a que url tiene que ir el script segn sea el id_modulo del menu introducido
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */

	require "includes/load_main_components.inc.php";

	if(!isset($_GET["menu"]) || !is_numeric($_GET["menu"])){
		$_GET["menu"]=0;
	}

	$resultadoModulo=$db->callProcedure("CALL ed_sp_web_menu_obtener_concreto(".$_GET["menu"].",".$_SESSION["id_idioma"].")");

	if($db->getNumberRows($resultadoModulo)==0){
		generalUtils::redirigir(CURRENT_DOMAIN);
	}
	$datoModulo=$db->getData($resultadoModulo);

	if($_GET["detalle"]==0){
		$urlIncluir=$datoModulo["url"];
	}else{
		$idModulo=$datoModulo["id_modulo"];
		$urlIncluir=$datoModulo["url_detalle"];
	}

	//Incluimos archivo a atacar
	require $urlIncluir;
?>