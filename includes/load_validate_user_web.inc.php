<?php
	/**
	 * 
	 * Si no estamos logueado, enviamos al miembro fuera del panel.
	 * 
	 */
  
	$idUsuarioWebLogin = -1;
	
	if(!isset($_SESSION["met_user"])){
		generalUtils::redirigir(CURRENT_DOMAIN);
	}else{
		if(isset($validarIntegridad)){
			$fechaActual=date("d/m/Y");
			$fechaPrevia=generalUtils::conversionFechaFormato($_SESSION["met_user"]["fecha_finalizacion"],"-","/");
			if($_SESSION["met_user"]["pagado"]==0 || generalUtils::compararFechas($fechaActual,$fechaPrevia)==3){
				generalUtils::redirigir(CURRENT_DOMAIN);
			}
		}
		$idUsuarioWebLogin = $_SESSION["met_user"]["id"];
		$idTipoUsusarioWebLogin = $_SESSION["met_user"]["tipoUsuario"];
	}

	if($validar && $idUsuarioWebLogin == -1) { generalUtils::redirigir(CURRENT_DOMAIN); }
?>