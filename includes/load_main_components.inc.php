<?php
	/**
	 * 
	 * Indicamos la ruta absoluta en donde esta alojado el fichero.
	 * @var string
	 */

	/**
	 * 
	 * Incluimos los componentes mas comunes para evitar el exceso de codigo redundante.
	 * 
	 */
	
	$absolutePath=dirname(__FILE__);
	require_once $absolutePath."/load_environment.inc.php";
	require_once $absolutePath."/../classes/databaseConnection.php";
	require_once $absolutePath."/../classes/generalUtils.php";
	require_once $absolutePath."/../database/connection.php";
	require_once $absolutePath."/load_template.inc.php";	
	require_once $absolutePath."/load_language.inc.php";
	require_once $absolutePath."/../config/dictionary/".$_SESSION["diccio"];
	require_once $absolutePath."/../config/constants.php";
?>