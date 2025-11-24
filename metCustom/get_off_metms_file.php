<?php
/*
* Get a list of workshop attendees
* on a specified date
*/

	require "../classes/databaseConnection.php";
	require "../classes/generalUtils.php";
	require "../database/connection.php";
	require "../includes/load_template.inc.php";
	setlocale(LC_ALL, 'en_GB');

$result = $db->callProcedure("CALL ed_pr_get_current_id_conferencia()");
$row = $result->fetch_assoc();
$idConferencia = $row["current_id_conferencia"];

$resultado=$db->callProcedure("CALL ed_pr_get_offmetm_file($idConferencia)");

// Get possible dietary preferences
$record = $db->callProcedure("CALL ed_pr_get_dietary_preferences()");
$vdiet = array();
while($row = $record->fetch_assoc()){
$vdiet[] = $row["dietary_preference"];
}

//Export to Excel (some encoding problems)
header("Content-Disposition: attachment; filename=offmetm_file.xls");
header("Content-type: application/vnd.ms-excel, charset = utf-8"); 

//Output header row
echo "Activity\tFirst name\tLast name\tEmail\tDiet\tAllergies\n";

//Output data
while($dato=$db->getData($resultado)){
$firstName = $dato['nombre'];
$lastName = $dato['apellidos'];
$activity = $dato['name'];
$correo_electronico = $dato['correo_electronico'];
$id_attendee = $dato['id_inscripcion_conferencia'];
  
$diet = $vdiet[$dato['diet']-1];
  
if (!$diet) {
  	$diet = $vdiet[0]; 
}

    $allergies = "";
    $vectorAllergies=Array();
		$resultado2=$db->callProcedure("CALL ed_pr_get_attendee_allergies($id_attendee,0)");
		while($datoAllergy=$db->getData($resultado2)){
			array_push($vectorAllergies,$datoAllergy["allergy_name"]);
		}
		if ($vectorAllergies) {
      	$allergies = implode(" - ",$vectorAllergies); 
          }
  
echo iconv("UTF-8", "UTF-16LE//IGNORE", $activity);
echo "\t";
echo iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $firstName);
echo "\t";
echo iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $lastName);
echo "\t";
echo iconv("UTF-8", "UTF-16LE//IGNORE", $correo_electronico);
echo "\t";
echo iconv("UTF-8", "UTF-16LE//IGNORE", $diet);
echo "\t";
echo iconv("UTF-8", "UTF-16LE//IGNORE", $allergies).PHP_EOL;
  
}

?>