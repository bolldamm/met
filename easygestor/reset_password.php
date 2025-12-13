<?php
	/**
	 * 
	 * Scripts que presenta el formulario de recordar password
	 * @author Mike
	 * 
	 */
	require "includes/load_main_components.inc.php";
	require "config/dictionary/default.php";

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

	$esCorrecto=true;
	
	//Si no se ha pasado id_usuario... o no tiene la longitud de 32 caracteres de md5...
	if(!isset($_GET["c"]) || strlen($_GET["c"])<32){
		$esCorrecto=false;	
	}else{
		$resultado=$db->callProcedure("CALL ed_pr_hash_to_id('".$_GET["c"]."')");
      // $row = mysqli_fetch_assoc($resultado);
		// $dato=$db->getData($resultado);
      if ($resultado && $row = mysqli_fetch_assoc($resultado)) {
    if (!empty($row['id_usuario'])) {
        		$esCorrecto=true;	
    } else {
        		$esCorrecto=false;	
    }
    } 
      
    }

    if ($esCorrecto) {
        // echo $row['id_usuario'];
        redirectPost("password_expired.php", [
              "id_usuario" => $row['id_usuario'],
              ]);
    } else {
        echo "Error. Please contact webmaster.";
    }		


?>