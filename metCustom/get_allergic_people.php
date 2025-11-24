<?php
/*
Get allergic people
*/

	require "../classes/databaseConnection.php";
	require "../database/connection.php";

	// Get currrent conference ID
	$result = $db->callProcedure("CALL ed_pr_get_current_id_conferencia()");
	$row = $result->fetch_assoc();
	$confid = $row["current_id_conferencia"];

//Export to Excel (some encoding problems)
header("Content-Disposition: attachment; filename=allergic-people.xls");
header("Content-type: application/vnd.ms-excel, charset = utf-8"); 

//Output header row
// ic.nombre, ic.apellidos, ic.correo_electronico, ti.nombre_largo, icl.time, icl.question
echo "Name\tClosing dinner\tAllergies\n";

$codeProcedure = "CALL ed_sp_obtener_listado_conferencia_completo('.$confid.',2)";

$resultMiembros = $db->callProcedure($codeProcedure);

while ($dataMiembro = $db->getData($resultMiembros)) {
  
  $idAttendee = $dataMiembro["id_inscripcion_conferencia"];
  
    for ($x = 0; $x <= 3; $x++) {
      
        if($x){            
          $resultName = $db->callProcedure("CALL ed_pr_closing_dinner_guest_names($idAttendee,$x)");
      	  $rowName = $resultName->fetch_assoc();
			  
      	  $guestName = $rowName["guest_name"];
          
          if(!$guestName) {
            $guestName = "Guest n. " . $x;
          }
          
          $htmlName = $guestName ." (". $dataMiembro["nombre"] . " " . $dataMiembro["apellidos"] .")";
        } else {
          $htmlName = $dataMiembro["nombre"] . " " . $dataMiembro["apellidos"];
        }
      
      if ($dataMiembro["es_dinner"] == 0) {
          $dinner = "yes";
        } else {
         $dinner = "no";
      }
      
    $allergies = "";
    $vectorAllergies=Array();
	$resultado2=$db->callProcedure("CALL ed_pr_get_attendee_allergies($idAttendee,$x)");
	while($datoAllergy=$db->getData($resultado2)){
			array_push($vectorAllergies,$datoAllergy["allergy_name"]);
		}
	if ($vectorAllergies) {
      	$allergies = implode(" - ",$vectorAllergies); 
		}
      if ($allergies) {
//Output badge texts
echo iconv("UTF-8", "UTF-16LE//IGNORE", $htmlName);
echo "\t";
echo iconv("UTF-8", "UTF-16LE//IGNORE", $dinner);
echo "\t";
echo iconv("UTF-8", "UTF-16LE//IGNORE", $allergies);
        echo "\n";
      }
    }          
    }



?>