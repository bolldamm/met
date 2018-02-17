<?php
	/**
	 * 
	 * Mostraremos por pantalla toda la informacion relacionada con el usuario logueado: usuario,nombre y apellidos,avatar,etc
	 * @author eData
	 */

	if($_SESSION["user"]["avatar"]!=""){
		$plantilla->assign("AVATAR_USER",$_SESSION["user"]["avatar"]);
		$plantilla->parse("contenido_principal.avatar_user");
	}else{
		$plantilla->parse("contenido_principal.avatar_default");
	}
	
	//Nombre de usuario
	$plantilla->assign("USERNAME_USER",$_SESSION["user"]["username"]);
	
	$idPermiso=2;
	//Secciones asociadas al usuario
	$resultadoSeccion=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_easygestor_seccion_usuario_obtener(".$_SESSION["user"]["id"].",".$_SESSION["user"]["language_id"].",".$idPermiso.",".$_SESSION["user"]["rol_global"].")");
	while($datoSeccion=$db->getData($resultadoSeccion)){
		$vectorSeccion["URL"]=$datoSeccion["url"];
		$vectorSeccion["ICON"]=$datoSeccion["icono"];
		$vectorSeccion["NAME"]=$datoSeccion["nombre"];
		
		$plantilla->assign("EASYGESTOR_SECTION",$vectorSeccion);
		$plantilla->parse("contenido_principal.item_seccion");
	}
?>