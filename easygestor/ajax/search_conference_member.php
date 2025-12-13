<?php
	require "../includes/load_main_components.inc.php";
	$esAjax=true;
	require "../includes/load_validate_user.inc.php";
	require "../config/dictionary/".$_SESSION["user"]["language_dictio"];
	
	$plantilla=new XTemplate("../html/ajax/search_conference_member.html");
	
	$resultadoUsuarioWebConferencia=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_conferencia_usuario_web_listar('".generalUtils::escaparCadena($_POST["texto"])."')");
	$totalUsuarios=$db->getNumberRows($resultadoUsuarioWebConferencia);
	$i=1;
	
	
	while($datoUsuarioWebConferencia=$db->getData($resultadoUsuarioWebConferencia)){
		$plantilla->assign("ID_MIEMBRO",$datoUsuarioWebConferencia["id_usuario_web"]);
		$plantilla->assign("CONFERENCIA_CORREO_ELECTRONICO",$datoUsuarioWebConferencia["correo_electronico"]);
		$plantilla->assign("CONFERENCIA_NOMBRE_MIEMBRO",$datoUsuarioWebConferencia["nombre"]." ".$datoUsuarioWebConferencia["apellidos"]);

		
		$plantilla->parse("contenido_principal.item_usuario.item_usuario_columna");
		
		if($i==$totalUsuarios || $i%2==0){
			$plantilla->parse("contenido_principal.item_usuario");
		}
		
		$i++;
	}
	$plantilla->parse("contenido_principal");
	
	$plantilla->out("contenido_principal");
?>