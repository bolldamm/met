<?php
	require "../includes/load_main_components.inc.php";
	$esAjax=true;
	require "../includes/load_validate_user.inc.php";
	require "../config/dictionary/".$_SESSION["user"]["language_dictio"];
	
	$plantilla=new XTemplate("../html/ajax/search_workshop_member.html");
	
	$resultadoUsuarioWebTaller=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_taller_usuario_web_listar('".generalUtils::escaparCadena($_POST["texto"])."')");
	$totalUsuarios=$db->getNumberRows($resultadoUsuarioWebTaller);
	$i=1;
	
	
	while($datoUsuarioWebTaller=$db->getData($resultadoUsuarioWebTaller)){
		$plantilla->assign("ID_MIEMBRO",$datoUsuarioWebTaller["id_usuario_web"]);
		$plantilla->assign("TALLER_CORREO_ELECTRONICO",$datoUsuarioWebTaller["correo_electronico"]);
		$plantilla->assign("TALLER_NOMBRE_MIEMBRO",$datoUsuarioWebTaller["nombre"]." ".$datoUsuarioWebTaller["apellidos"]);

		
		$plantilla->parse("contenido_principal.item_usuario.item_usuario_columna");
		
		if($i==$totalUsuarios || $i%2==0){
			$plantilla->parse("contenido_principal.item_usuario");
		}
		
		$i++;
	}
	$plantilla->parse("contenido_principal");
	
	$plantilla->out("contenido_principal");
?>