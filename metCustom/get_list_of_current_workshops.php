<?php
/*
* Get a list of current workshops
*/

	require "../classes/databaseConnection.php";
	require "../classes/generalUtils.php";
	require "../database/connection.php";
	require "../includes/load_template.inc.php";

$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_pr_get_list_of_current_workshops()";
$resultado=$db->callProcedure($codeProcedure);

//Export to csv
header("Content-Disposition: attachment; filename=current_workshops.txt");
header("Content-type: text/csv; charset=UTF-8");

//Output header row
echo "ID\tURL\r\n";

//Output names
while($dato=$db->getData($resultado)){
$idw = $dato['id_taller'];
$urlw = $dato['enlace'];
echo $idw."\t".$urlw."\r\n";
}

?>