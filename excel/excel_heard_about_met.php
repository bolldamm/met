<?php
	/**
	 * 
	 * Pagina de inicio desde donde se inicia el portal
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */

	require "../includes/load_main_components.inc.php";
	
	
	if(!isset($_SESSION["met_user"]["tipoUsuario"]) || $_SESSION["met_user"]["tipoUsuario"]!=3){
		generalUtils::redirigir(CURRENT_DOMAIN);
	}
	
	//Plantilla principal excel
	$plantillaExcel=new XTemplate("../html/excel/index.html");
	
	//Subplantilla excel
	$subPlantillaExcel=new XTemplate("../html/excel/excel_heard_about_met.html");
	
	
	//Listamos personas que han dicho algo de about met
	$resultadoSobreMet=$db->callProcedure("CALL ed_sp_web_usuario_web_sobre_met_obtener()");
	while($datoSobreMet=$db->getData($resultadoSobreMet)){
		$subPlantillaExcel->assign("EXCEL_HEARD_ABOUT_MET_FIRST_NAME_VALUE",$datoSobreMet["nombre"]);
		$subPlantillaExcel->assign("EXCEL_HEARD_ABOUT_MET_LAST_NAME_VALUE",$datoSobreMet["apellidos"]);
		$subPlantillaExcel->assign("EXCEL_HEARD_ABOUT_MET_EMAIL_VALUE",$datoSobreMet["correo_electronico"]);
		$subPlantillaExcel->assign("EXCEL_HEARD_ABOUT_MET_DESCRIPTION_VALUE",$datoSobreMet["descripcion"]);

		$subPlantillaExcel->parse("contenido_principal.item_sobre_met");
	}
	
	$subPlantillaExcel->parse("contenido_principal");
	$plantillaExcel->assign("CONTENIDO",$subPlantillaExcel->text("contenido_principal"));
	
	$plantillaExcel->parse("contenido_principal");
	
	$fichero="heard_about_met.xls";

	header("Content-type: application/vnd.ms-excel");
	//header("Content-Type: application/force-download");
	header("Content-Disposition: attachment; filename=$fichero");
	header("Content-Transfer-Encoding: binary");
	
	$plantillaExcel->out("contenido_principal");
?>