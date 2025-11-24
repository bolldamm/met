<?php
/*
Output the text for the conference badges 
*/

	require "../classes/databaseConnection.php";
	require "../database/connection.php";

$confid=$_GET["confid"];
$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_pr_get_conference_badges('".$confid."')";
$resultado=$db->callProcedure($codeProcedure);

//Export to Excel (some encoding problems)
header("Content-Disposition: attachment; filename=conference_badges.xls");
header("Content-type: application/vnd.ms-excel, charset = utf-8"); 

//Output header row
echo "Name\tAffiliation\n";

while($dato=$db->getData($resultado)){
$badgeLines = explode("\n", $dato['conference_badge']);  
$lineOne = $dato['nombre']." ".$dato['apellidos'];
$lineTwo = strip_tags($badgeLines[1]);
$lineThree = strip_tags($badgeLines[2]);  

//Output badge texts
echo iconv("UTF-8", "UTF-16LE//IGNORE", $lineOne);
echo "\t";
echo iconv("UTF-8", "UTF-16LE//IGNORE", $lineTwo);
echo "#";
echo iconv("UTF-8", "UTF-16LE//IGNORE", $lineThree).PHP_EOL;

}

?>