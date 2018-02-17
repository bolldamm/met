<?php
	/**
	 * 
	 * Pagina desde la que se verifica el login de un usuario
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */

	require "includes/load_main_components.inc.php";
	
	if(count($_POST) > 0) {
		$user = generalUtils::filtrarInyeccionSQL(generalUtils::escaparCadena($_POST["txtUsername"]));
		$pass = generalUtils::filtrarInyeccionSQL(generalUtils::escaparCadena($_POST["txtPassword"]));
		
		//Verificamos si el usuario con estos datos existe
		$resultLogin = $db->callProcedure("CALL ed_sp_web_usuario_web_login('".$user."', '".$pass."')");
		if($db->getNumberRows($resultLogin) == 1) {
			$dataLogin = $db->getData($resultLogin);
			
	
			//Creamos la sessión con los valores
			$_SESSION["met_user"]["id"] = $dataLogin["id_usuario_web"];
			$_SESSION["met_user"]["email"] = $_POST["txtUsername"];
			$_SESSION["met_user"]["id_modalidad"] = $dataLogin["id_modalidad_usuario_web"];
			$_SESSION["met_user"]["pagado"] = $dataLogin["pagado"];
			$_SESSION["met_user"]["fecha_inscripcion"] = $dataLogin["fecha_inscripcion"];
			$_SESSION["met_user"]["fecha_finalizacion"] = $dataLogin["fecha_finalizacion"];
			$_SESSION["met_user"]["id_situacion_adicional"] = $dataLogin["id_situacion_adicional"];
			

			
			//Segun sea la modalidad
			if($dataLogin["id_modalidad_usuario_web"]==MODALIDAD_USUARIO_INDIVIDUAL){
				$_SESSION["met_user"]["username"] = $dataLogin["nombre"]." ".$dataLogin["apellidos"];
				$_SESSION["met_user"]["name"] = $dataLogin["nombre"];
				$_SESSION["met_user"]["lastname"] = $dataLogin["apellidos"];
				$_SESSION["met_user"]["institution_id"] = $dataLogin["id_institucion"];
				$_SESSION["met_user"]["institution_name"]= "";
			}else if ($dataLogin["id_modalidad_usuario_web"]==MODALIDAD_USUARIO_INSTITUTIONAL){
				$_SESSION["met_user"]["username"] = $dataLogin["nombre_representante"]." ".$dataLogin["apellidos_representante"];
				$_SESSION["met_user"]["name"] = $dataLogin["nombre_representante"];
				$_SESSION["met_user"]["lastname"] = $dataLogin["apellidos_representante"];
				$_SESSION["met_user"]["institution_id"] = "";
				$_SESSION["met_user"]["institution_name"] = $dataLogin["institucion_nombre"];
			}
			
			$_SESSION["met_user"]["tipoUsuario"] = $dataLogin["id_tipo_usuario_web"];
			$_SESSION["met_user"]["tipoPago"] = $dataLogin["id_tipo_pago"];
			
			if(isset($_SESSION["auth_target"])){
				$target=$_SESSION["auth_target"];
				unset($_SESSION["auth_target"]);
				generalUtils::redirigir($target);
			}else{
				if(isset($_POST["hdnUrlActual"])){
					generalUtils::redirigir($_POST["hdnUrlActual"]);
					
				}else{
					generalUtils::redirigir(CURRENT_DOMAIN);
				}//end else
			}
		}else{
			
			generalUtils::redirigir(CURRENT_DOMAIN."?c=1");
		}
		
	}
?>