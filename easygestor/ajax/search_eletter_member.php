<?php
	require "../includes/load_main_components.inc.php";
	$esAjax=true;
	require "../includes/load_validate_user.inc.php";
	require "../config/dictionary/".$_SESSION["user"]["language_dictio"];
	
	$plantilla=new XTemplate("../html/ajax/search_eletter_member.html");
	
	$resultadoUsuarioWeb=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_eletter_usuario_web_listar('".generalUtils::escaparCadena($_POST["texto"])."')");
	$totalUsuarios=$db->getNumberRows($resultadoUsuarioWeb);
	$i=1;
	
	
	while($datoUsuarioWeb=$db->getData($resultadoUsuarioWeb)){
		$plantilla->assign("ID_MIEMBRO",$datoUsuarioWeb["id_usuario_web"]);
		$plantilla->assign("ELETTER_CORREO_ELECTRONICO",$datoUsuarioWeb["correo_electronico"]);
		$plantilla->assign("ELETTER_NOMBRE_MIEMBRO",$datoUsuarioWeb["nombre"]." ".$datoUsuarioWeb["apellidos"]);

		
		$plantilla->parse("contenido_principal.item_usuario.item_usuario_columna");
		
		if($i==$totalUsuarios || $i%2==0){
			$plantilla->parse("contenido_principal.item_usuario");
		}
		
		$i++;
	}
	$plantilla->parse("contenido_principal");
	
	$plantilla->out("contenido_principal");
?>