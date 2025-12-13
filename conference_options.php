<?php
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: 0");
    require "includes/load_main_components.inc.php";	
    session_start();
    // header("Cache-Control: no-cache, no-store, must-revalidate");
    // unset($_SESSION["offmetm_error"]);

if($_POST["conference_user"]) {
  $idAttendee = $_POST["conference_user"];
  if($idAttendee <> $_SESSION["conference_user"]) {
    $_SESSION["conference_attendee"] = $idAttendee;
  }
} else {
  $idAttendee = $_SESSION["conference_user"];
}

if ($_POST["txtContenido"]) {
  // There is a staff comment to save
$attendeeNotes = generalUtils::escaparCadena($_POST['txtContenido']);
$db->callProcedure("CALL ed_pr_set_conference_registered(" . $idAttendee . ",'". $attendeeNotes ."',false)");
}

$txtRaffle = $_POST["txtRaffle"];
// $txtRaffle = 1;
if ($txtRaffle) {
	// Get currrent conference ID
	$result = $db->callProcedure("CALL ed_pr_get_current_id_conferencia()");
	$row = $result->fetch_assoc();
	$idConferencia = $row["current_id_conferencia"];
	
	$result = $db->callProcedure("CALL ed_pr_get_max_raffle_ticket($idConferencia)");
	$row = $result->fetch_assoc();
	$raffleTicket = $row["max_raffle_ticket"] + 1;
  
    $db->callProcedure("CALL ed_pr_update_raffle_ticket($raffleTicket,$idAttendee)");
} else {
  
$txtEmail = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_EMAIL, $_POST["txtEmail"]));
$txtTelefono = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_WORKSHOP_REGISTER_CONTACT_PHONE_NO, $_POST["txtTelefono"]));
$txtDiet = $_POST["cmbDiet"];
$BadgeNombre = generalUtils::escaparCadena(str_replace("'", "&#39;",$_POST["txtBadgeNombre"]));
$BadgeApellidos = generalUtils::escaparCadena(str_replace("'", "&#39;",$_POST["txtBadgeApellidos"]));
$BadgeLength = STATIC_CONFERENCE_BADGE_LENGTH + 1;
$BadgeFirst = substr(generalUtils::escaparCadena($_POST["txtBadgeFirst"]), 0, $BadgeLength);
$BadgeFirst = str_replace("'", "&#39;",$BadgeFirst);
$BadgeSecond = substr(generalUtils::escaparCadena($_POST["txtBadgeSecond"]), 0, $BadgeLength);
$BadgeSecond = str_replace("'", "&#39;",$BadgeSecond);
$newCouncilrole = $_POST["cmbCouncil2"];
$BadgePronouns = substr(generalUtils::escaparCadena($_POST["txtBadgePronouns"]), 0, $BadgeLength);  
$BadgePronouns = str_replace("'", "&#39;",$BadgePronouns);
$clinic_question = generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_CLINIC_QUESTION_PROMPT, $_POST["txtQuestion"]));
$clinic_upd = $_POST["clinicUPD"];
$clinic_time = $_POST["clinicTime"];
$n_of_bands = $_POST["NofBands"];
  
$txtBadge = $BadgeNombre . "<br />" . $BadgeApellidos . "<br />" . $BadgeFirst . "<br />" . $BadgeSecond . "<br />" . $newCouncilrole . "<br />" . $BadgePronouns;

if (isset($_POST["cmbSpeaker"])) {
  $SpeakerHelper = $_POST["cmbSpeaker"];
 } else {
  $SpeakerHelper = 0;
}


if (!$txtBadge) {
//    $txtBadge = $HostName;
                }

// if (!$txtDiet) {
//    $txtDiet = 1;
//                }

if ($_POST["chkAttendeeList"]) {
    $chkAttendeeList = 1;
                } else {
    $chkAttendeeList = 0;
}

// if ($_POST["valorimagen"]) {
//    $txtImagefile = $_POST["valorimagen"];
//                } else {
//    $txtImagefile = $_POST["OldPhoto"];
// }
  
//If Council(First-timer, set variable
if (isset($_POST["chkFirsttimer"])) {
  $CouncilFirsttimer = 1;
    } else {
  $CouncilFirsttimer = 0;
}
//if (isset($_POST["cmbCouncil"])) {
//        $CouncilFirsttimer = $_POST["cmbCouncil"];
// }
if ($_POST["cmbCouncil"] > 0) {
        $CouncilFirsttimer = $_POST["cmbCouncil"];
 }
  
   //Update attendee photo
   require "includes/grab_photo.php";
  
$db->callProcedure("CALL ed_pr_update_attendee('$txtEmail','$txtTelefono','$txtBadge',$chkAttendeeList,'$photo_file',$idAttendee,$txtDiet,$SpeakerHelper,$CouncilFirsttimer)");

if (!$_POST["DinnerClosed"]) {
  
        $result = $db->callProcedure("CALL ed_pr_total_number_of_dinner_courses()");
	    $row = $result->fetch_assoc();
	    $max_courses = $row["number_of_courses"];

      $rgresult = $db->callProcedure("CALL ed_pr_get_guests($idAttendee,7)");
      $rgrow = $rgresult->fetch_assoc();
  
for ($x = 1; $x <= $rgrow["valor"]; $x++) {
  
    $GuestName = generalUtils::escaparCadena(str_replace("'", "&#39;",$_POST["RGuestNombre" . $x]));
    $GuestSurname = generalUtils::escaparCadena(str_replace("'", "&#39;",$_POST["RGuestApellido" . $x]));
    
    $resultGuest = $db->callProcedure("CALL ed_pr_closing_dinner_guest_names($idAttendee,$x,2)");
    $rowGuest = $resultGuest->fetch_assoc();
    if (isset($rowGuest["guest_name"]) || isset($rowGuest["guest_surname"])) {
           $db->callProcedure("CALL ed_pr_closing_dinner_guest_name_update($idAttendee,$x,'$GuestName','$GuestSurname',2)");
    } else {
           $db->callProcedure("CALL ed_pr_closing_dinner_guest_name_insert($idAttendee,$x,'$GuestName','$GuestSurname',2)");
    }
}  
  
        $dgresult = $db->callProcedure("CALL ed_pr_get_guests($idAttendee,2)");
        $dgrow = $dgresult->fetch_assoc();
  
for ($x = 0; $x <= $dgrow["valor"]; $x++) {
  
  if ($x) {
    $GuestName = generalUtils::escaparCadena(str_replace("'", "&#39;",$_POST["GuestNombre" . $x]));
    $GuestSurname = generalUtils::escaparCadena(str_replace("'", "&#39;",$_POST["GuestApellido" . $x]));
    //Delete all previously selected allergies (this has to be here so that guests can remove all their choices)
    $db->callProcedure("CALL ed_pr_web_attendee_allergy_eliminar($idAttendee,$x)");
    if ($_POST["cmbAllergy" . $x] != '') {
          // $db->callProcedure("CALL ed_pr_web_attendee_allergy_eliminar($idAttendee,$x)");
            $areas = $_POST["cmbAllergy" . $x];
            foreach ($areas as $area) {
                $db->callProcedure("CALL ed_pr_web_attendee_allergy_insertar($idAttendee,$area,$x)");
            }
        }
    
    $resultGuest = $db->callProcedure("CALL ed_pr_closing_dinner_guest_names($idAttendee,$x,1)");
    $rowGuest = $resultGuest->fetch_assoc();
//	if (isset($rowGuest["guest_name"])) {
    if (isset($rowGuest["guest_name"]) || isset($rowGuest["guest_surname"])) {
           $db->callProcedure("CALL ed_pr_closing_dinner_guest_name_update($idAttendee,$x,'$GuestName','$GuestSurname',1)");
    } else {
           $db->callProcedure("CALL ed_pr_closing_dinner_guest_name_insert($idAttendee,$x,'$GuestName','$GuestSurname',1)");
    }
  }
  
     for ($y = 1; $y <= $max_courses; $y++) {
  $resultCourse = $db->callProcedure("CALL ed_pr_closing_dinner_choices_per_course($idAttendee,$x,$y)");
  $rowCourse = $resultCourse->fetch_assoc();
       
     $comboName = "cmbClosingDinner" . $y;
     $dish = $_POST[$comboName . $x];
     if (!$dish) {
           $dish = 0;
                }
       
       if($rowCourse["id_attendee"]) {
           $db->callProcedure("CALL ed_pr_closing_dinner_choices_update($idAttendee,$x,$y,$dish)");
    } else {
           $db->callProcedure("CALL ed_pr_closing_dinner_choices_insert($idAttendee,$x,$y,$dish)");
    }
       
              }  
    }
}

for ($x = 1; $x <= $n_of_bands; $x++) {
		  
$result = $db->callProcedure("CALL ed_pr_inscripcion_offmetm_get_event($idAttendee,$x)");
$row = $result->fetch_assoc();
  
$NewChosenEvent = $_POST["cmbOffMetms" . $x];
$outcome = 0;
$offmetmchange = false;
if($NewChosenEvent!==$row["off_metm_event"]) {
  $offmetmchange = true;
  if ($row["off_metm_event"]){
     $outcome = 1;
    } else {
     $outcome = 2;
    }
  if(!$NewChosenEvent) {
    if ($row["off_metm_event"]){
     $outcome = 3;
     $NewChosenEvent = 0;
    } 
  }
}
  
if($NewChosenEvent) {
   $result2 = $db->callProcedure("CALL ed_pr_get_offmetms_signups($NewChosenEvent)");
   $row2 = $result2->fetch_assoc(); 
   // $row2["signups"] = 100;
   $result3 = $db->callProcedure("CALL ed_pr_get_max_offmetm_signups($NewChosenEvent)");
   $row3 = $result3->fetch_assoc();
  
  	if($row2["signups"]>=$row3["this_max"]) {
      $outcome = 0;
      if ($offmetmchange) {
      	$_SESSION["offmetm_error"] = true;
      }
  	}
  
  } else {
  $row2["signups"] = null;
  $row3["this_max"] = null;
  }
  $oldevent = $row["off_metm_event"];
  if($outcome==1) {
    $db->callProcedure("CALL ed_pr_update_offmetm_signup($NewChosenEvent,$oldevent,$idAttendee)");
   } elseif ($outcome==2) {
    $db->callProcedure("CALL ed_pr_insert_offmetm_signup($NewChosenEvent,$idAttendee)");
    } elseif ($outcome==3) {
    $db->callProcedure("CALL ed_pr_delete_offmetm_signup($oldevent,$idAttendee)");
    } 
}
  
//Delete all previously selected allergies (this has to be here so that people can remove all their choices)
        $db->callProcedure("CALL ed_pr_web_attendee_allergy_eliminar($idAttendee,0)");
//Insert newly selected allergies
        if ($_POST['cmbAllergy'] != '') {
          // $db->callProcedure("CALL ed_pr_web_attendee_allergy_eliminar($idAttendee,0)");
            $areas = $_POST['cmbAllergy'];
            foreach ($areas as $area) {
               // $db->callProcedure("CALL ed_pr_web_attendee_allergy_insertar($idAttendee,'" . $area . "')");
              $db->callProcedure("CALL ed_pr_web_attendee_allergy_insertar($idAttendee,$area,0)");
            }
        }
}

if ($clinic_upd) {
  // $clinic_question = "Fake question";
  $db->callProcedure("CALL ed_pr_clinic_update($clinic_upd,$clinic_time,'$clinic_question')");
}
if (!isset($_SESSION["offmetm_error"])) {
  $_SESSION["mymetm_saved"] = true;
}
generalUtils::redirigir($_SERVER["HTTP_REFERER"]);
?>