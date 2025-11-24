<?php
	require "../includes/load_main_components.inc.php";
	$esAjax=true;
	require "../includes/load_validate_user.inc.php";
	require "../config/dictionary/".$_SESSION["user"]["language_dictio"];
		
	
	switch($_GET["i"]){
		case 1:
			//Get user ID from URL
			if($_GET["id"]==""){
				$idUsuario="null";
			}else{
				$idUsuario=$_GET["id"];
			}
			
			//Call MySQL procedure to get any duplicates of email (0 rows = no duplicates)
			$resultado=$db->callProcedure("CALL ed_sp_usuario_web_existe_registrado(".$idUsuario.",'".$_GET["mail"]."')");
			echo $db->getNumberRows($resultado);
			
			break;
	}
?>