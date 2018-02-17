<?php
	//Check whether user is logged in
	if(isset($_SESSION["met_user"])){
      
	//If logged in, check whether user is an individual or an institutional member
	if($_SESSION["met_user"]["id_modalidad"]==MODALIDAD_USUARIO_INDIVIDUAL){
		$resultadoUsuario=$db->callProcedure("CALL ed_sp_web_usuario_web_individual_obtener_concreto(".$idUsuarioWeb.")");
		$datoUsuario=$db->getData($resultadoUsuario);		

		//Assign individual member details to variables
		$nombre=$datoUsuario["nombre"];
		$apellidos=$datoUsuario["apellidos"];
		$email=$datoUsuario["correo_electronico"];

    }else if($_SESSION["met_user"]["id_modalidad"]==MODALIDAD_USUARIO_INSTITUTIONAL){
      
		$resultadoUsuario=$db->callProcedure("CALL ed_sp_web_usuario_web_institucion_obtener_concreto(".$idUsuarioWeb.")");
		$datoUsuario=$db->getData($resultadoUsuario);

		//Assign institutional member details to variables
		$nombre=$datoUsuario["nombre_representante"];
		$apellidos=$datoUsuario["apellidos_representante"];
		$email=$datoUsuario["correo_electronico_representante"];
	}

	//Assign URL of member's MET profile to variable
	$urlMiembroDetalle=CURRENT_DOMAIN."/easygestor/main_app.php?section=member&action=edit&id_miembro=".$idUsuarioWeb;

    //Assign variables to placeholders in mail template for members
	$plantillaInfoUsuario=new XTemplate("html/mail/includes/info_user_web.inc.html");
	$plantillaInfoUsuario->assign("MIEMBRO_CORREO_ELECTRONICO",$email);
	$plantillaInfoUsuario->assign("MIEMBRO_NOMBRE",$nombre." ".$apellidos);
	$plantillaInfoUsuario->assign("MIEMBRO_URL",$urlMiembroDetalle);

    }else{

    //If not logged in, assign variables to mail template for non-members
    $plantillaInfoUsuario=new XTemplate("html/mail/includes/info_non_member.inc.html");
	$plantillaInfoUsuario->assign("POSTER_CORREO_ELECTRONICO",$txtEmail);

    }

	//Parseamos contenido
	$plantillaInfoUsuario->parse("contenido_principal");	
	
	//Exportamos a la subplantilla mail
	$mailSubPlantilla->assign("DETALLE_MIEMBRO",$plantillaInfoUsuario->text("contenido_principal"));
	
?>