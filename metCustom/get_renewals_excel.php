<?php
/*
* Get a list of members for renewals
*/

	require "../classes/databaseConnection.php";
	require "../classes/generalUtils.php";
	require "../database/connection.php";
	require "../includes/load_template.inc.php";


$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_pr_get_renewals()";
$resultado=$db->callProcedure($codeProcedure);

//Export to csv
header("Content-Disposition: attachment; filename=emails_for_renewals.csv");
header("Content-type: text/csv; charset=UTF-8");

//Output header row
echo "sep=/t\r\n";
echo "email\tname\tsurname\texpiry\r\n";

//Output names
while($dato=$db->getData($resultado)){
  
$datetime1 = new DateTime('2022-12-31');
$datetime2 = new DateTime($dato['expiry']); 

$interval = $datetime1->diff($datetime2);
$interval = $interval->format('%a');
  
//  if (!$interval) {
  
 echo $dato['correo_electronico']."\t".$dato['nombre']."\t".$dato['apellidos']."\t".$dato['expiry']."\r\n";
    
   
//  }
}

?>