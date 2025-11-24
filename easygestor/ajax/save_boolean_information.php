<?php
	require "../includes/load_main_components.inc.php";
	$esAjax=true;
	require "../includes/load_validate_user.inc.php";
	require "../config/dictionary/".$_SESSION["user"]["language_dictio"];
		
	
	//Valor actual
	if($_POST["valorActual"]==1){
		$valorDestino=0;
		$valorRetorno=STATIC_GLOBAL_BUTTON_NO;
	}else{
		$valorDestino=1;
		$valorRetorno=STATIC_GLOBAL_BUTTON_YES;
	}
	

	
	switch($_POST["idDestino"]){
		case 1:
			//Cambiamos el attended de una inscripcion taller
			$idLineaTrozeada=explode("-",$_POST["idElemento"]);
			

			//Llamamos procedure
			$resultado=$db->callProcedure("CALL ed_sp_inscripcion_taller_linea_asistencia_editar(".$idLineaTrozeada[1].",".$valorDestino.")");

			
			break;
		case 2:
			//Cambiamos el attended de una inscripcion conference
			
			//Cambiamos el attended de una inscripcion taller
			$idLineaTrozeada=explode("-",$_POST["idElemento"]);
			
			//Llamamos procedure
			$resultado=$db->callProcedure("CALL ed_sp_inscripcion_conferencia_linea_asistencia_editar(".$idLineaTrozeada[1].",".$valorDestino.")");

			
			break;
		case 3:
			//Cambiamos el attended de una inscripcion taller

			//Llamamos procedure
			$resultado=$db->callProcedure("CALL ed_sp_inscripcion_conferencia_asistencia_editar(".$_POST["idElemento"].",".$valorDestino.")");

			
			break;
			
		case 4:
			//Cambiamos el paid de una inscripcion taller
			$idLineaTrozeada=explode("-",$_POST["idElemento"]);
			

			//Llamamos procedure
			$resultado=$db->callProcedure("CALL ed_sp_inscripcion_taller_linea_pagado_editar(".$idLineaTrozeada[1].",".$valorDestino.")");

			
			break;
		case 5:
			//Cambiamos el paid de una inscripcion conference
			
			//Cambiamos el attended de una inscripcion taller
			$idLineaTrozeada=explode("-",$_POST["idElemento"]);
			
			//Llamamos procedure
			$resultado=$db->callProcedure("CALL ed_sp_inscripcion_conferencia_linea_pagado_editar(".$idLineaTrozeada[1].",".$valorDestino.")");

			
			break;
		case 6:
			//Cambiamos el paid de una inscripcion taller

			//Llamamos procedure
			$resultado=$db->callProcedure("CALL ed_sp_inscripcion_conferencia_pagado_editar(".$_POST["idElemento"].",".$valorDestino.")");

			
			break;
			
	}
	
	echo $valorRetorno."-".$valorDestino;
?>