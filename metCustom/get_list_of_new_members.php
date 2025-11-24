<?php
/*
* Get a list of new members
* from a specified date
*/

	require "../classes/databaseConnection.php";
	require "../classes/generalUtils.php";
	require "../database/connection.php";
	require "../includes/load_template.inc.php";

$asOfDate=$_GET["date_p"];

$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_pr_get_list_of_new_members('".$asOfDate."')";
$resultado=$db->callProcedure($codeProcedure);

//Export to csv
header("Content-Disposition: attachment; filename=new_members.txt");
header("Content-type: text/csv; charset=UTF-8");

//Output header row
echo "First name\tLast name\n\r";

//Output names
while($dato=$db->getData($resultado)){
$firstName = $dato['nombre'];
$lastName = $dato['apellidos'];
echo $firstName."\t".$lastName."\n\r";
}

?>