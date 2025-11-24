<?php
	/**
	 * 
	 * Incluimos los componentes mas comunes para evitar el exceso de codigo redundante.
	 * 
	 */
	session_start();
	header("Content-Type: text/html; charset=utf-8");
	/**
	 * 
	 * Indicamos la ruta absoluta en donde esta alojado el fichero.
	 * @var string
	 */

	$absolutePath=dirname(__FILE__);
	
	require_once $absolutePath."/load_environment.inc.php";
    require $absolutePath."/../../classes/databaseConnection.php";
	require $absolutePath."/../../classes/generalUtils.php";
	require $absolutePath."/../classes/gestorGeneralUtils.php";
	require $absolutePath."/../../database/connection.php";
	require $absolutePath."/../../includes/load_template.inc.php";
