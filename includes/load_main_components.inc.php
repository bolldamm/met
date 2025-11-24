<?php

	/**
	 *
	 * Include the most common components
     * All file paths are relative to this file: dirname(__FILE__)
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
	require_once $absolutePath."/check_timeout.inc.php";