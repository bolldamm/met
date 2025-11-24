<?php
/*
Get clinic attendees
*/

	require "../classes/databaseConnection.php";
	require "../database/connection.php";

	// Get currrent conference ID
	$result = $db->callProcedure("CALL ed_pr_get_current_id_conferencia()");
	$row = $result->fetch_assoc();
	$confid = $row["current_id_conferencia"];

// $confid=$_GET["confid"];
$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_pr_get_tech_clinic('".$confid."')";
$resultado=$db->callProcedure($codeProcedure);

//Export to Excel (some encoding problems)
header("Content-Disposition: attachment; filename=clinic_attendees.xls");
header("Content-type: application/vnd.ms-excel, charset = utf-8"); 

//Output header row
// ic.nombre, ic.apellidos, ic.correo_electronico, ti.nombre_largo, icl.time, icl.question
echo "Clinic\tTime\tName\tSurname\tEmail\tQuestion\n";

while($dato=$db->getData($resultado)){
$name = $dato['nombre'];  
$surname = $dato['apellidos']; 
$email = $dato['correo_electronico']; 
$clinic = $dato['nombre_largo']; 
$time = $dato['time']; 
$question = $dato['question']; 
$question = str_replace(["\r", "\n"], " - ", $question);
$question = str_replace("-  -", " - ", $question);
  
switch ($time) {
                  case 1:
                      $time = "9:00";
                      break;
                  case 2:
                      $time = "9:30";
                      break;
                  case 3:
                      $time = "10:00";
                      break;
                  case 4:
                      $time = "11:00";
                      break;
                  case 5:
                      $time = "11:30";
                      break;
                  case 6:
                      $time = "12:00";
                      break;
} 

//Output badge texts
echo iconv("UTF-8", "UTF-16LE//IGNORE", $clinic);
echo "\t";
echo iconv("UTF-8", "UTF-16LE//IGNORE", $time);
echo "\t";
echo iconv("UTF-8", "UTF-16LE//IGNORE", $name);
echo "\t";
echo iconv("UTF-8", "UTF-16LE//IGNORE", $surname);
echo "\t";
echo iconv("UTF-8", "UTF-16LE//IGNORE", $email);
echo "\t";
echo iconv("UTF-8", "UTF-16LE//IGNORE", $question).PHP_EOL;

}

?>