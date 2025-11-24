<?php
/*
Output csv file with email addresses and names of currently paid-up members 
*/

	require "../classes/databaseConnection.php";
	require "../database/connection.php";

$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_pr_get_members_for_newsletter()";
$resultado=$db->callProcedure($codeProcedure);

//Export to csv
header("Content-Disposition: attachment; filename=current_members.txt");
header("Content-type: text/csv; charset=UTF-8");

//Output header row (with or without ID column)
//echo "ID\tEmail address\tFirst name\tLast name\n";
//echo "Email address\tFirst name\tLast name\n";

//Output member details
while($dato=$db->getData($resultado)){
//$memberId = $dato['id_usuario_web'];
$emailAddress = $dato['correo_electronico'];  
$firstName = $dato['nombre'];
$lastName = $dato['apellidos'];
//echo $memberId."\t".$emailAddress."\t".$firstName."\t".$lastName."\n";
//echo $emailAddress."\t".$firstName."\t".$lastName."\n";
echo $emailAddress.";";

}

?>