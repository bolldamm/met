<?php
/*
* Get a list of workshop attendees
* on a specified date
*/

	require "../classes/databaseConnection.php";
	require "../classes/generalUtils.php";
	require "../database/connection.php";
	require "../includes/load_template.inc.php";

$asOfDate=$_GET["enumber"];

$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_pr_get_workshop_day_attendees('".$asOfDate."')";
$resultado=$db->callProcedure($codeProcedure);

//Export to csv
header("Content-Disposition: attachment; filename=workshop-day-attendees.txt");
header("Content-type: text/csv; charset=UTF-8");

//Output header row
echo "First name\tLast name\tEmail\r\n";

//Output names
while($dato=$db->getData($resultado)){
$firstName = $dato['nombre'];
$lastName = $dato['apellidos'];
$email = $dato['correo_electronico'];
echo $firstName."\t".$lastName."\t".$email."\r\n";
}

?>