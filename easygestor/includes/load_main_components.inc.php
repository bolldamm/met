<?php
	/**
	 *
	 * Incluimos los componentes mas comunes para evitar el exceso de codigo redundante.
	 *
	 */
	if (session_status() === PHP_SESSION_NONE) {
		// Extend session lifetime to 4 hours (14400 seconds)
		ini_set('session.gc_maxlifetime', 14400);
		session_set_cookie_params([
			'lifetime' => 14400,
			'path' => '/',
			'secure' => true,
			'httponly' => true,
			'samesite' => 'Lax'
		]);
		session_start();
	}
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
