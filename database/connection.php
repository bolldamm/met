<?php
	/**
	 * 
	 * Es la instancia de la conexion a la base de datos
	 * @db object
	 * 
	 */

if (defined('MET_ENV') && constant('MET_ENV') == 'LOCAL') {
    require('../private/essential_string.php');
}else {
    require('/home/metmeetings/private/essential_string.php');
}

if (defined('MET_ENV') && constant('MET_ENV') == 'LOCAL') {
    $db = new DatabaseConnection("localhost", "root", "", "met");
}else{
    $db=new DatabaseConnection("localhost:3308", "maindbuser9634", $essential_string, "metmeetings_edatamet");
}

$db->connect();
	
//Definimos el acronimo de la base de datos
define("OBJECT_DB_ACRONYM","ed");