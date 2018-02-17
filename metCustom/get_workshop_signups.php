<?php
/*
Output a list of all the signups for all the workshops associated with a specified conference
*/

	require "../classes/databaseConnection.php";
	require "../database/connection.php";

$confid=$_GET["confid"];
$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_pr_get_workshop_signups('".$confid."')";
$resultado=$db->callProcedure($codeProcedure);

//Export to plain text with UTF-8 encoding
header("Content-Disposition: attachment; filename=workshop_signups.txt");
header("Content-type: text/plain, charset = utf-8"); 

echo "Workshop\tParticipants\tPaid\tCertificate\n";

while($dato=$db->getData($resultado)){
$workshop=$dato["nombre_largo"];
$firstname=$dato["nombre"];
$lastname=$dato["apellidos"];
if($dato["es_certificado_workshop"]==1) {
$certificate="Yes";
} else {
$certificate="No";
}
if($dato["pagado"]==1) {
$paid="Yes";
} else {
$paid="No";
}
$rows=$workshop."\t".$lastname.", ".$firstname."\t".$paid."\t".$certificate."\n";
echo $rows;
}

?>