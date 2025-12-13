<?php
	require "includes/load_main_components.inc.php";
	require "includes/load_validate_user.inc.php";
	require "config/constants.php";
	require "config/dictionary/en_EN.php";

if (empty($_SESSION['user']['language_dictio'])) {
    generalUtils::redirigir(CURRENT_DOMAIN_EASYGESTOR . "logout.php");
    exit(); // Always exit after redirect
}

	/**
	 * 
	 * Nos indica si hemos entrado por post
	 * @var boolean
	 * 
	 */
	$esPost=false;
	if(count($_POST)>0){
		$section=$_POST["section"];
		$action=$_POST["action"];
		$esPost=true;
	}else{
		$section=$_GET["section"];
		$action=$_GET["action"];
	}
		
	/**
	 * 
	 * Incluimos la informacion sobre las secciones,acciones y las rutas de acceso al script
	 * 
	 */
	require "config/controller.php";
	
	/**
	 * 
	 * Verificamos que existe la seccion y accion
	 * 
	 */
	if(isset($controller[$section][$action])){
		$idSeccion=$controller[$section]['ID_SECTION'];
      // The "if" below appears to do nothing since the function always returns true
		 if($idSeccion!=-1 && !gestorGeneralUtils::tienePermisoUsuarioSeccion($db, $idSeccion, 2)){
			die();
		 }
		
		require $controller[$section][$action];
	}else{
		generalUtils::redirigir("index.php");
	}
?>