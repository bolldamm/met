<?php
	/**
	 * 
	 * Pagina desde la que se verifica el login de un usuario
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */

	require "includes/load_main_components.inc.php";

	// MIKE'S CODE STARTS HERE 
	// Get currrent conference ID
	$result = $db->callProcedure("CALL ed_pr_get_current_id_conferencia()");
	$row = $result->fetch_assoc();
    $idConferencia = $row["current_id_conferencia"];
    // Generate a six-digit pseudorandom number
    $randomNumber = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
	// MIKE'S CODE FINISHES HERE 
	
	// If a username and password have been submitted, first filter the input to prevent SQL injection
	if(count($_POST) > 0) {
		$user = generalUtils::filtrarInyeccionSQL(generalUtils::escaparCadena($_POST["txtUsername"]));
		$pass = generalUtils::filtrarInyeccionSQL(generalUtils::escaparCadena($_POST["txtPassword"]));
      
        if ($user == STATIC_REGISTRATION_DESK_USERNAME && $pass == STATIC_REGISTRATION_DESK_PASSWORD) {
			$_SESSION["registration_desk"] = true;
			generalUtils::redirigir("https://www.metmeetings.org/en/registration:1408");
		} else {
			$_SESSION["registration_desk"] = null;
		}
		
		// Check whether the submitted user details exist in the database
		$resultLogin = $db->callProcedure("CALL ed_sp_web_usuario_web_login('".$user."', '".$pass."')");
		// If submitted username and password match a user in the database
		if($db->getNumberRows($resultLogin) == 1) {
			$dataLogin = $db->getData($resultLogin);
			
	
			// Assign session variables based on user details from database
			$_SESSION["met_user"]["id"] = $dataLogin["id_usuario_web"];
			$_SESSION["met_user"]["email"] = $_POST["txtUsername"];
			$_SESSION["met_user"]["id_modalidad"] = $dataLogin["id_modalidad_usuario_web"];
			$_SESSION["met_user"]["pagado"] = $dataLogin["pagado"];
			$_SESSION["met_user"]["fecha_inscripcion"] = $dataLogin["fecha_inscripcion"];
			$_SESSION["met_user"]["fecha_finalizacion"] = $dataLogin["fecha_finalizacion"];
			$_SESSION["met_user"]["id_situacion_adicional"] = $dataLogin["id_situacion_adicional"];
			

			
			// Assign variables according to user type (individual or institution)
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
          
          
          		// You are a MET member and you are logged in. So I can find your conference id
		$id_member = $dataLogin["id_usuario_web"];
		$result = $db->callProcedure("CALL ed_pr_get_conference_user($id_member,$idConferencia)");
		$row = $result->fetch_assoc();
		
		if ($row["id_inscripcion_conferencia"]) {
			// You are a MET member, you are logged in and I have found your conference id
			$_SESSION["conference_user"] = $row["id_inscripcion_conferencia"];
		} else {        
			// You are a MET member, you are logged in, but you have not registered for the conference yet
			$_SESSION["conference_user"] = null;
		}
      
			
			if(isset($_SESSION["auth_target"])){
				$target=$_SESSION["auth_target"] . '?reload=' . $randomNumber;
				unset($_SESSION["auth_target"]);
				generalUtils::redirigir($target);
			}else{
				if(isset($_POST["hdnUrlActual"])){
                    $target= str_replace("http://","https://",$_POST["hdnUrlActual"]);
                    generalUtils::redirigir($target);
					// generalUtils::redirigir($_POST["hdnUrlActual"]);
				}else{
					generalUtils::redirigir(CURRENT_DOMAIN);
				}//end else
			}
		/*
		 * If submitted username and password don't match any user in database
		 * set session variable "loginErrorMessage" and redirect to home page (see load_structure.inc.php, line 153)
		 */
		}else{
          
          // Check to see if conference attendee
          
          $result = $db->callProcedure("CALL ed_pr_non_member_login('".$pass."', '".$user."',$idConferencia)");
		  $row = $result->fetch_assoc();
          
	if ($row["id_inscripcion_conferencia"]) {
			// I have found your conference id
			$_SESSION["conference_user"] = $row["id_inscripcion_conferencia"];  
			generalUtils::redirigir("https://www.metmeetings.org/en/my-metm:1415?reload=" . $randomNumber);			
		} else {        
			// I cannot find your conference id
			$_SESSION["conference_user"] = null;
            $_SESSION['loginErrorMessage'] = STATIC_FORM_LOGIN_ERROR_MESSAGE;
            generalUtils::redirigir(CURRENT_DOMAIN);
		}
          
	  }
	}
?>