<?php
	require "includes/load_main_components.inc.php";
	require "includes/load_validate_user.inc.php";
	require "config/constants.php";
	require "config/dictionary/".$_SESSION["user"]["language_dictio"];

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
		if($idSeccion!=-1 && !gestorGeneralUtils::tienePermisoUsuarioSeccion($db, $idSeccion, 2)){
			die();
		}
		
		require $controller[$section][$action];
	}else{
		generalUtils::redirigir("index.php");
	}
?>