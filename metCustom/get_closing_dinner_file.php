<?php
/*
* Get dinner choice file
*/

	require "../classes/databaseConnection.php";
	require "../classes/generalUtils.php";
	require "../database/connection.php";
	require "../includes/load_template.inc.php";
	setlocale(LC_ALL, 'en_GB');

$result = $db->callProcedure("CALL ed_pr_get_current_id_conferencia()");
$row = $result->fetch_assoc();
$idConferencia = $row["current_id_conferencia"];

$result = $db->callProcedure("CALL ed_pr_total_number_of_dinner_courses()");
$row = $result->fetch_assoc();
$max_courses = $row["number_of_courses"];

$populate = $db->callProcedure("CALL ed_pr_get_inscripcion_conferencia_for_closing_dinner_populate($idConferencia)");

//Export to Excel (some encoding problems)
header("Content-Disposition: attachment; filename=closing_dinner_file.xls");
header("Content-type: application/vnd.ms-excel, charset = utf-8"); 

//Output header row
// echo "Activity\tFirst name\tLast name\tEmail\tDiet\tAllergies\n";

//Output data
while($dato=$db->getData($populate)){

  for ($x = 0; $x <= $dato['valor']; $x++) {
    
            $idAttendee = $dato["id_inscripcion_conferencia"];
            $guestName = "";
  
        if($x){            
          $resultName = $db->callProcedure("CALL ed_pr_closing_dinner_guest_names($idAttendee,$x,1)");
      	  $rowName = $resultName->fetch_assoc();
			  
      	  $guestName = $rowName["guest_name"];
          
          if(!$guestName) {
            $guestName = "Guest n. " . $x;
          }
          
          $htmlName = $guestName ." (". $dato["nombre"] . " " . $dato["apellidos"] .")";
        } else {
          $htmlName = $dato["nombre"] . " " . $dato["apellidos"];
        }
    
        echo iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $htmlName);
        echo "\t";
    
        $email = $dato["correo_electronico"];
        echo iconv("UTF-8", "UTF-16LE//IGNORE", $email);
        echo "\t";
    
        $defaultdish = "";
        for ($y = 1; $y <= $max_courses; $y++) {  

        // get dishes
        
      	$resultDish = $db->callProcedure("CALL ed_pr_closing_dinner_choices_per_course($idAttendee,$x,$y)");
      	$rowDish = $resultDish ->fetch_assoc();
      	$chosendish = $rowDish["dish"];
        
		if (!isset($chosendish)) {
          $chosendish=-1;
		  $db->callProcedure("CALL ed_pr_closing_dinner_choices_insert($idAttendee,$x,$y,$chosendish)");
		}        
               
      	if ($chosendish==-1) {
      		$result2 = $db->callProcedure("CALL ed_pr_get_closing_dinner_default_dish($y)");
      		$defaultdish = "X";
      		} else {
      		$result2 = $db->callProcedure("CALL ed_pr_closing_get_dinner_single_dish($chosendish)");
      		}
            $row2 = $result2->fetch_assoc();
      		$abbr = $row2["badge_abbreviation"];
          
      		echo iconv("UTF-8", "UTF-16LE//IGNORE", $abbr);
      		echo "\t";
    }
    
      		echo iconv("UTF-8", "UTF-16LE//IGNORE", $defaultdish);
      		echo "\t";
    
    $allergies = "";
    $vectorAllergies=Array();
	$resultado2=$db->callProcedure("CALL ed_pr_get_attendee_allergies($idAttendee,$x)");
	while($datoAllergy=$db->getData($resultado2)){
			array_push($vectorAllergies,$datoAllergy["allergy_name"]);
		}
	if ($vectorAllergies) {
      	$allergies = implode(" - ",$vectorAllergies); 
		}
  
                      echo iconv("UTF-8", "UTF-16LE//IGNORE", $allergies).PHP_EOL;
 

    
  }
}

?>