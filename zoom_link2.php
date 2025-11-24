<?php

	require "includes/load_main_components.inc.php";
	
	// Get currrent conference ID
//	$result = $db->callProcedure("CALL ed_pr_get_current_id_conferencia()");
//	$row = $result->fetch_assoc();
//	$idConferencia = $row["current_id_conferencia"];

//	if ($_SESSION["met_user"]["id"]) {
		// You are a MET member and you are logged in. So I can find your conference id
//		$id_member = $_SESSION["met_user"]["id"];
//		$result = $db->callProcedure("CALL ed_pr_get_conference_user($id_member,$idConferencia)");
//		$row = $result->fetch_assoc();
		
//		if ($row["id_inscripcion_conferencia"]) {
			// You are a MET member, you are logged in and I have found your conference id
//			$_SESSION["conference_user"] = $row["id_inscripcion_conferencia"];
//		} else {        
			// You are a MET member, you are logged in, but you have not registered for the conference yet
//			$_SESSION["conference_user"] = null;
//		}
//    } 

	if ($_SESSION["conference_user"]) {
      // You are are logged into your conference options
      
        unset($_SESSION["auth_target"]);
      
      $idUser = $_SESSION["conference_user"];
      $idSession = $_GET["session"];
      
        switch ($_GET["session"]) {
          case 1:
            // METM21 online - Karen Tkaczyk
            $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
            // generalUtils::redirigir(“https://www.metmeetings.org/en/metm-presentation-link:1398”);
            generalUtils::redirigir("https://vimeo.com/638203791");
            break;
            case 2:
            // METM21 online - Phillippa May Bennett
            $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
            // generalUtils::redirigir(“https://www.metmeetings.org/en/metm-presentation-link:1398”);
            generalUtils::redirigir("https://vimeo.com/632018544");
            break;
            case 3:
            // METM21 online - Emma Goldsmith
            $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
            // generalUtils::redirigir(“https://www.metmeetings.org/en/metm-presentation-link:1398”);
            generalUtils::redirigir("https://vimeo.com/632151158");
            break;
            case 4:
            // METM21 online - Clare E. Vassallo
            $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
            // generalUtils::redirigir(“https://www.metmeetings.org/en/metm-presentation-link:1398”);
            generalUtils::redirigir("https://vimeo.com/632018543");
            break;
            case 5:
            // METM21 online - Mary Ellen Kerans
            $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
            // generalUtils::redirigir(“https://www.metmeetings.org/en/metm-presentation-link:1398”);
            generalUtils::redirigir("https://vimeo.com/632151157");
            break;
            case 6:
            // METM21 online - Katarzyna Szymańska
            $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
            // generalUtils::redirigir(“https://www.metmeetings.org/en/metm-presentation-link:1398”);
            generalUtils::redirigir("https://vimeo.com/632151159");
            break;
            case 7:
            // METM21 online - Allison Wright
            $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
            // generalUtils::redirigir(“https://www.metmeetings.org/en/metm-presentation-link:1398”);
            generalUtils::redirigir("https://vimeo.com/638181205");
            break;
            case 8:
            // Allison's handout
             $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
             generalUtils::redirigir("https://www.metmeetings.org/documentacion/files/Chopping_handout.pdf");
             break;
            case 9:
            // METM21 online - Alina Cincan
            $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
            // generalUtils::redirigir(“https://www.metmeetings.org/en/metm-presentation-link:1398”);
            generalUtils::redirigir("https://vimeo.com/633027692");
            break;
            case 10:
            // METM21 online - Lucy O'Shea
            $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
            // generalUtils::redirigir(“https://www.metmeetings.org/en/metm-presentation-link:1398”);
            generalUtils::redirigir("https://vimeo.com/633023330");
            break;
          case 11:
            // Lucy's handout
             $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
             generalUtils::redirigir("https://www.metmeetings.org/documentacion/files/Engineering_your_text_handout.pdf");
             break;
            case 12:
            // METM21 online - Elina Nocera
            $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
            // generalUtils::redirigir(“https://www.metmeetings.org/en/metm-presentation-link:1398”);
            generalUtils::redirigir("https://vimeo.com/633027693");
            break;
            case 13:
            // METM21 online - Timothy Barton
            $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
            // generalUtils::redirigir(“https://www.metmeetings.org/en/metm-presentation-link:1398”);
            generalUtils::redirigir("https://vimeo.com/638224452");
            break;
            case 14:
            // METM21 online - John Bates
            $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
            // generalUtils::redirigir(“https://www.metmeetings.org/en/metm-presentation-link:1398”);
            generalUtils::redirigir("https://vimeo.com/633027690");
            break;
            case 15:
            // METM21 online - Holly Hibbert, Aleksandra Chlon
            $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
            // generalUtils::redirigir(“https://www.metmeetings.org/en/metm-presentation-link:1398”);
            generalUtils::redirigir("https://vimeo.com/633023329");
            break;
            case 16:
            // METM21 online - Kate Sotejeff-Wilson, Alice Lehtinen
            $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
            // generalUtils::redirigir(“https://www.metmeetings.org/en/metm-presentation-link:1398”);
            generalUtils::redirigir("https://vimeo.com/633027691");
            break;
            case 17:
            // METM21 online - Andrea Shah
            $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
            // generalUtils::redirigir(“https://www.metmeetings.org/en/metm-presentation-link:1398”);
            generalUtils::redirigir("https://vimeo.com/633023332");
            break;
            case 18:
            // METM21 online - Choir video
            $db->callProcedure("CALL ed_pr_add_click($idUser,$idSession)");
            // generalUtils::redirigir(“https://www.metmeetings.org/en/metm-presentation-link:1398”);
            generalUtils::redirigir("https://vimeo.com/623648095");
            break;
            case 19:
            // Video password
            ?><html>
			<body><p style="text-align: center;font-family:'Segoe UI', Frutiger, 'Frutiger Linotype', 'Dejavu Sans', 'Helvetica Neue', Arial, sans-serif;font-size: 100%;">To view a recording, click the video icon above a presentation title. The password for all recordings is <b>METMStyle</b>.<br/>[Only registered METM21 attendees can see this message.]</p></body></html>
            <?php
            break;
          default:
             generalUtils::redirigir("https://www.metmeetings.org/");
             break;
          }
      
      // generalUtils::redirigir("https://www.metmeetings.org/en/metm-presentation-link:1398");
      
    } else {
      if ($_GET["session"]<>19) {
      $_SESSION["auth_target"] = $_SERVER['REQUEST_URI'];
      generalUtils::redirigir("https://www.metmeetings.org/en/conference-login:1289");
      } else {
          ?><html>
			<body><p style="text-align: center;font-family:'Segoe UI', Frutiger, 'Frutiger Linotype', 'Dejavu Sans', 'Helvetica Neue', Arial, sans-serif;font-size: 100%;">Attendees may sign in to view recordings. Simply click the video icons above the presentation titles.</p></body></html>
            <?php
      }
    }
	
?>
