<?php
/*
* Get all conference notes
*/

	require "../classes/databaseConnection.php";
	require "../classes/generalUtils.php";
	require "../database/connection.php";
	require "../includes/load_template.inc.php";
	setlocale(LC_ALL, 'en_GB');

$result = $db->callProcedure("CALL ed_pr_get_current_id_conferencia()");
$row = $result->fetch_assoc();
$idConferencia = $row["current_id_conferencia"];

$resultado=$db->callProcedure("CALL ed_pr_get_notes($idConferencia)");

//Export to Excel (some encoding problems)
header("Content-Disposition: attachment; filename=notes.xls");
header("Content-type: application/vnd.ms-excel, charset = utf-8"); 

//Output header row
echo "First name\tLast name\tAttendee note\tRegistration desk note\n";

//Output data
while($dato=$db->getData($resultado)){
$firstName = $dato['nombre'];
$lastName = $dato['apellidos'];
$dNote = str_replace(array("\r\n", "\n", "\r"), ' - ', strip_tags($dato['observaciones']));
$aNote = str_replace(array("\r\n", "\n", "\r"), ' - ', strip_tags($dato['comentarios']));

echo iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $firstName);
echo "\t";
echo iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $lastName);
echo "\t";
echo iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $aNote);
echo "\t";
echo iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $dNote).PHP_EOL;
  
}

?>