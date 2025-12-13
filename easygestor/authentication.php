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
	 $N = 12; // expiry threshold in months

	require "includes/load_main_components.inc.php";

	// Helper function to redirect with POST
	function redirectPost($url, $data) {
	    echo '<form id="redirForm" action="' . htmlspecialchars($url) . '" method="POST">';
	    foreach ($data as $key => $value) {
	        echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
	    }
	    echo '</form>';
	    echo '<script>document.getElementById("redirForm").submit();</script>';
	    exit();
	}

	if(count($_POST)>0){
		$user=generalUtils::filtrarInyeccionSQL($_POST["txtUsuario"]);
		$password=generalUtils::filtrarInyeccionSQL($_POST["txtPassword"]);
		
		//Una vez hemos validado que existe, guardamos en sesion los parametros devueltos por el usuario
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_usuario_obtener_login('".generalUtils::escaparCadena($user)."','".generalUtils::escaparCadena($password)."')");
		if($db->getNumberRows($resultado)==1){
			$datos=$db->getData($resultado);
          
          // Case 1: activated is NULL
          if (empty($datos["activated"])) {
          //    $db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_pr_add_old_password('".$datos["id_usuario"]."','".generalUtils::escaparCadena($password)."')");
              redirectPost("password_expired.php", [
              "id_usuario" => $datos["id_usuario"],
              "username"   => $user
              ]);
          }

          // Case 2: activated exists, check date difference
          $activatedDate = new DateTime($datos["activated"]);
          $now = new DateTime();

          // Add N months to the activation date
          $expirationDate = (clone $activatedDate)->modify("+{$N} months");
          // $expirationDate = (clone $activatedDate)->modify("-{$N} months");

          // If expired
          if ($now > $expirationDate) {
          //    $db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_pr_add_old_password('".$datos["id_usuario"]."','".generalUtils::escaparCadena($password)."')");
              redirectPost("password_expired.php", [
              "id_usuario" => $datos["id_usuario"],
              "username"   => $user
              ]);
          }
  
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
			$_SESSION["id_idioma"] = 3;
			//Accedemos a la aplicacion
			generalUtils::redirigir("main_app.php?section=menu&action=view");
		}else{
			//Usuario no valido
			generalUtils::redirigir(CURRENT_DOMAIN_EASYGESTOR);
		}
	}
?>