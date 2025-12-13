<?php

	require "includes/load_main_components.inc.php";
	
	// Get currrent conference ID
	$result = $db->callProcedure("CALL ed_pr_get_current_id_conferencia()");
	$row = $result->fetch_assoc();
	$idConferencia = $row["current_id_conferencia"];

	if ($_SESSION["met_user"]["id"]) {
		// You are a MET member and you are logged in. So I can find your conference id
		$id_member = $_SESSION["met_user"]["id"];
		$result = $db->callProcedure("CALL ed_pr_get_conference_user($id_member,$idConferencia)");
		$row = $result->fetch_assoc();
		
		if ($row["id_inscripcion_conferencia"]) {
			// You are a MET member, you are logged in and I have found your conference id
			$_SESSION["conference_user"] = $row["id_inscripcion_conferencia"];
		} else {        
			// You are a MET member, you are logged in, but you have not registered for the conference yet
			$_SESSION["conference_user"] = null;
		}
    } 

	if ($_SESSION["conference_user"]) {
      // You are are logged into your conference options
      
        unset($_SESSION["auth_target"]);
        $nowDate = strtotime(date("Y-m-d H:i:s"));
        // echo $nowDate . "<br>";
        $dateBeginThursday = strtotime("2021-10-14 12:00:00");
		// echo $dateBeginThursday . "<br>";
        $dateBeginThursdayB = strtotime("2021-10-14 14:00:00");
		// echo $dateBeginThursdayB . "<br>";
		$dateEndThursday = strtotime("2021-10-14 23:55:00");
		//echo $dateEndThursday . "<br>";
        $dateBeginFriday = strtotime("2021-10-15 12:00:00");
		// echo $dateBeginFriday . "<br>";
		$dateEndFriday = strtotime("2021-10-15 23:55:00");
		//echo $dateEndFriday . "<br>";
      
      $idUser = $_SESSION["conference_user"];
      $idSession = $_GET["session"];
      
        switch ($_GET["session"]) {
          case 1:
            // METM21 online - Thursday track one
            if($nowDate > $dateBeginThursday && $nowDate < $dateEndThursday) {
             $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
             generalUtils::redirigir("https://us02web.zoom.us/j/82411579340");
            }
             break;
          case 2:
            // METM21 online - Friday track one
            if($nowDate > $dateBeginFriday && $nowDate < $dateEndFriday) {
             $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
             generalUtils::redirigir("https://us02web.zoom.us/j/87809041833");
            }
             break;
          case 3:
            // METM21 online - Thursday track two A
            if($nowDate > $dateBeginThursday && $nowDate < $dateEndThursday) {
             $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
             generalUtils::redirigir("https://us02web.zoom.us/j/86363442394");
            }
             break;
          case 4:
            // METM21 online - Friday track two
            if($nowDate > $dateBeginFriday && $nowDate < $dateEndFriday) {
             $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
             generalUtils::redirigir("https://us02web.zoom.us/j/84483713222");
            }
             break;
          case 5:
            // METM21 online - Thursday track two B
            if($nowDate > $dateBeginThursdayB && $nowDate < $dateEndThursday) {
             $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
             generalUtils::redirigir("https://us02web.zoom.us/j/83232651228");
            }
             break;
          case 6:
            // METM21 online - Thursday Coffee Break
            if($nowDate > $dateBeginThursday && $nowDate < $dateEndThursday) {
             $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
             generalUtils::redirigir("https://wonder.me/r?id=fdf62a30-52fe-4adf-9899-1ff5e7cbdc6d");
            }
             break;
          case 7:
            // METM21 online - Friday Coffee Break
            if($nowDate > $dateBeginFriday && $nowDate < $dateEndFriday) {
             $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
             generalUtils::redirigir("https://wonder.me/r?id=fdf62a30-52fe-4adf-9899-1ff5e7cbdc6d");
            }
             break;
          case 8:
            // METM21 Editing slam
            if($nowDate > $dateBeginThursday && $nowDate < $dateEndThursday) {
             $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
             generalUtils::redirigir("https://us02web.zoom.us/j/85018033466");
            }
             break;
          case 9:
            // METM21 Pick 'n ' Mix
            if($nowDate > $dateBeginThursday && $nowDate < $dateEndThursday) {
             $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
             generalUtils::redirigir("https://us02web.zoom.us/j/87366089782");
            }
             break;
          case 10:
            // METM21 Friday Off-METM
            if($nowDate > $dateBeginFriday && $nowDate < $dateEndFriday) {
             $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
             generalUtils::redirigir("https://us02web.zoom.us/j/85970941813");
            }
             break;
          case 11:
            // METM21 online - After party
            if($nowDate > $dateBeginFriday && $nowDate < $dateEndFriday) {
             $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
             generalUtils::redirigir("https://wonder.me/r?id=fdf62a30-52fe-4adf-9899-1ff5e7cbdc6d");
            }
             break;
          case 12:
            // Allison's handout
            if($nowDate > $dateBeginThursday && $nowDate < $dateEndThursday) {
             $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
             generalUtils::redirigir("https://www.metmeetings.org/documentacion/files/Chopping_handout.pdf");
            }
             break;
          case 13:
            // Lucy's handout
            if($nowDate > $dateBeginThursday && $nowDate < $dateEndFriday) {
             $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
             generalUtils::redirigir("https://www.metmeetings.org/documentacion/files/Engineering_your_text_handout.pdf");
            }
             break;
          default:
             generalUtils::redirigir("https://www.metmeetings.org/");
             break;
          }
      
      generalUtils::redirigir("https://www.metmeetings.org/en/metm-presentation-link:1398");
      
    } else {
      $_SESSION["auth_target"] = $_SERVER['REQUEST_URI'];
      generalUtils::redirigir("https://www.metmeetings.org/en/conference-login:1289");
    }
	
?>