<?php
	require "../includes/load_main_components.inc.php";
	$esAjax=true;
	require "../includes/load_validate_user.inc.php";
		
	/**
	 * 
	 * Nos indica si se ha realizado la ordenacion con correcion
	 * @var int
	 * 
	 */
	$esCorrecto=0;
	switch($_POST["t"]){
		case "menu":
			if(gestorGeneralUtils::tienePermisoUsuarioMenu($db, $_POST["id"], 2)){
				$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_menu_ordenar(".$_POST["id"].",".$_POST["ordenActual"].",".$_POST["ordenDestino"].")");
			}
			$esCorrecto=1;
			break;
		case "success":
			$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_caso_exito_ordenar(".$_POST["id"].",".$_POST["ordenActual"].",".$_POST["ordenDestino"].")");
			$esCorrecto=1;
			break;
		case "access_button":
			$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_boton_acceso_ordenar(".$_POST["id"].",".$_POST["ordenActual"].",".$_POST["ordenDestino"].")");
			$esCorrecto=1;
			break;
	}
	
	echo $esCorrecto;
?>