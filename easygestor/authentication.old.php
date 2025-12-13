<?php
	/**
	 * 
	 * Este script valida la existencia de un usuario mediante:
	 * -nombre usuario
	 * -password
	 * @author eData
	 * @version 4.0
	 * 
	 */

	require "includes/load_main_components.inc.php";

	if(count($_POST)>0){
		$user=generalUtils::filtrarInyeccionSQL($_POST["txtUsuario"]);
		$password=generalUtils::filtrarInyeccionSQL($_POST["txtPassword"]);
		
		//Una vez hemos validado que existe, guardamos en sesion los parametros devueltos por el usuario
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_usuario_obtener_login('".generalUtils::escaparCadena($user)."','".generalUtils::escaparCadena($password)."')");
		if($db->getNumberRows($resultado)==1){
			$datos=$db->getData($resultado);
			//Valores de sesion
			$_SESSION["user"]["id"]=$datos["id_usuario"];
			$_SESSION["user"]["username"]=$user;
			$_SESSION["user"]["name"]=$datos["nombre"];
			$_SESSION["user"]["first_name"]=$datos["primer_apellido"];
			$_SESSION["user"]["last_name"]=$datos["segundo_apellido"];
			$_SESSION["user"]["rol_id"]=$datos["id_rol"];
			$_SESSION["user"]["rol_global"]=$datos["es_global"];
			$_SESSION["user"]["rol_desc"]=$datos["rol"];
			$_SESSION["user"]["language_id"]=$datos["id_idioma"];
			$_SESSION["user"]["language_dictio"]=$datos["diccionario"];
			$_SESSION["user"]["avatar"]=$datos["imagen"];
			// $_SESSION["user"]["activated"]=time();
			
			//Accedemos a la aplicacion
			generalUtils::redirigir("main_app.php?section=menu&action=view");
		}else{
			//Usuario no valido
			generalUtils::redirigir("https://www.metmeetings.org/easygestor/");
		}
	}
?>