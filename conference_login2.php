<?php
	/**
	 * 
	 */

	require "includes/load_main_components.inc.php";

	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

    $clinic_upd = "";
    $clinic_time = "";
    $disableWSblock1 = "0";
    $disableWSblock2 = "0";
// Initialize the global exclusive lists dynamically
    $maxExclusive = "10";
    for ($i = 1; $i <= $maxExclusive; $i++) {
    $GLOBALS['exclusiveList' . $i] = '';
    }

    $leftMenu = "contenido_principal.menu_left";

	// Get currrent conference ID
	$result = $db->callProcedure("CALL ed_pr_get_current_id_conferencia()");
	$row = $result->fetch_assoc();
	$idConferencia = $row["current_id_conferencia"];

    if($_SESSION["conference_attendee"]) {
        // $leftMenu = "contenido_principal.full_width_content";
//    	if($_SESSION["met_user"]["tipoUsuario"] < TIPO_USUARIO_EDITOR) {
    		if(!isset($_SESSION["registration_desk"])) {
              if($_SESSION["met_user"]["tipoUsuario"] < TIPO_USUARIO_EDITOR) {
    			generalUtils::redirigir(CURRENT_DOMAIN);
                // $leftMenu = "contenido_principal.full_width_content";
                // $admin = true;
              }
              
    		} else {
              $leftMenu = "contenido_principal.full_width_content";
              $admin = true;
            }
//    	}
      $id_attendee = $_SESSION["conference_attendee"];
      unset($_SESSION["conference_attendee"]);
      $admin = true;
    } else {
      $id_attendee = $_SESSION["conference_user"];
      $admin = false;
    }

	if($_SESSION["met_user"]["id"]){
		if(!$_SESSION["conference_user"]) {
			$id_member = $_SESSION["met_user"]["id"];
			$resultthis = $db->callProcedure("CALL ed_pr_get_conference_user($id_member,$idConferencia)");
			$rowthis = $resultthis->fetch_assoc();
			if ($rowthis["id_inscripcion_conferencia"]) {
			// You are a MET member, you are logged in and I have found your conference id
			$_SESSION["conference_user"] = $rowthis["id_inscripcion_conferencia"];
			}
		}
	}

	// Instanciamos la clase Xtemplate con la plantilla base
	$plantilla = new XTemplate("html/index.html");

	// Instanciamos la clase Xtemplate con la plantilla que va a contener la información del apartado
	
	if ($id_attendee) {
      // You are are logged into your conference options
      
      if(isset($_SESSION["auth_target"])){
				$target=$_SESSION["auth_target"];
				unset($_SESSION["auth_target"]);
				generalUtils::redirigir($target);
			}
      
//      $subPlantilla = new XTemplate("html/forms/form_edit_conference_options.html");
      $subPlantilla = new XTemplate("html/forms/form_edit_conference_options_metm.html");
      $subPlantilla->assign("ID_ATTENDEE", $id_attendee);
      
      if (isset($_SESSION["offmetm_error"])) {
        $subPlantilla->assign("OFFMETM_ERROR", "1");
        unset($_SESSION["offmetm_error"]);
      } else {
        $subPlantilla->assign("OFFMETM_ERROR", "0");
      }
      
      if (isset($_SESSION["mymetm_saved"])) {
        $subPlantilla->assign("FIRST_IMPORTANT", STATIC_CHANGES_SAVED);
        unset($_SESSION["mymetm_saved"]);
      } else {
        $subPlantilla->assign("FIRST_IMPORTANT", STATIC_PRESS_SAVE);
      }

      $actual_uri = "$_SERVER[REQUEST_URI]";
      if(strpos($actual_uri, "my-metm") > 0){
        $admin = false;
      }
     
      if($admin) {
         $subPlantilla->assign("FORM_EVENT3", "style='display:none'");
         $subPlantilla->assign("FORM_EVENT4", "");
      }else{
         $subPlantilla->assign("FORM_EVENT3", "");
         $subPlantilla->assign("FORM_EVENT4", "style='display:none'");
      }
      
      	// Get off-metm time band names
		$record = $db->callProcedure("CALL ed_pr_get_offmetm_time_band_names()");
		$head = array();
		while($row = $record->fetch_assoc()){
		  $head[] = $row["time_band_name"];
		}
      
      $programme = DYNAMIC_SEE_PROGRAMME;
      
      // Get conference form freeze data
	  $result = $db->callProcedure("CALL ed_pr_get_conference_freeze_data($idConferencia)");
	  $row = $result->fetch_assoc();
      $dinner_choices_closed = $row["freeze_dinner_choices"];
//	  $three_four_time_band_incompatibility = $row["time_band_incompatibility"];
      $offmetm_choices_closed = $row["freeze_offmetm_choices"];

      if ($row["choir_overlap_warning"]) {
        $choirwarningjava = "choirwarning();";
      } else {
        $choirwarningjava = "";
      }
      $raffle_disabled = $row["disable_raffle"];
	  if($row["disable_catchup"]) {
         $subPlantilla->assign("FORM_EVENT6", "style='display:none'");
      }
	  if($row["disable_lastminute"]) {
         $subPlantilla->assign("FORM_EVENT7", "style='display:none'");
      }	  
	  // The parameter below is the total freeze
      $all_metm_choices_closed = $row["freeze_all_metm_choices"];
      
      if ($offmetm_choices_closed) {
		$head = str_replace("*", "", $head);
		// NEW BIT
		$three_four_time_band_incompatibility = false;
		} else {
			$three_four_time_band_incompatibility = $row["time_band_incompatibility"];
			// NEW BIT ENDS
		}
      
     if($admin) {    			
     	if(isset($_SESSION["conference_edit"])) {
            unset($_SESSION["conference_edit"]);
    		$all_metm_choices_closed = false;
            $subPlantilla->assign("SUPER_FREEZE", "style='display:none'");
          	if($_SESSION["met_user"]["tipoUsuario"] <= TIPO_USUARIO_EDITOR) {
				$subPlantilla->assign("SUPER_FREEZE2", "style='display:none'");
			}
    	} else {
            $all_metm_choices_closed = true;
            $subPlantilla->assign("SUPER_FREEZE3", "readonly");
        }
     }
      
	  if ($all_metm_choices_closed) {
		    $offmetm_choices_closed = true;
		    $dinner_teaser = "";
			$workshop_change = "";
			$reception_change = "";
			$dinner_change = "";
		  } else {
			$dinner_teaser = DYNAMIC_DINNER_TEASER;
			$workshop_change = DYNAMIC_WORKSHOP_CHANGE;
		    $reception_change = DYNAMIC_RECEPTION_CHANGE;
			$dinner_change = DYNAMIC_DINNER_CHANGE;
 		}
		
		$subPlantilla->assign("WORKSHOP_CHANGE", $workshop_change);
		$subPlantilla->assign("RECEPTION_CHANGE", $reception_change);
		$subPlantilla->assign("DINNER_CHANGE", $dinner_change);

	  $resultnew = $db->callProcedure("CALL ed_pr_total_number_of_offmetm_time_bands()");
      $rownew = $resultnew->fetch_assoc();
      $subPlantilla->assign("STATIC_DIET", STATIC_CONFERENCE_OPTIONS_DIET);
      $subPlantilla->assign("STATIC_DIET2", STATIC_CONFERENCE_OPTIONS_DIET2);
      if ($offmetm_choices_closed) {
  	  // OFF-METM CHOICES ARE NOT OPEN YET
		for ($x = 1; $x <= $rownew["number_of_bands"]; $x++) {
			$subPlantilla->assign("OFFMETM_HEAD".$x, "");
			}
        $subPlantilla->assign("COMING_SOON", DYNAMIC_OFFMETM_TEASER);
        $subPlantilla->assign("OFFMETM_FOOTNOTE", "");
        } else {
  	  // OFF-METM CHOICES ARE OPEN			
		for ($x = 1; $x <= $rownew["number_of_bands"]; $x++) {
			$subPlantilla->assign("OFFMETM_HEAD".$x, "<i>" . $head[$x-1] . "</i>");
			}
		$subPlantilla->assign("COMING_SOON", "");
        $subPlantilla->assign("PROGRAMME", $programme);
        $subPlantilla->assign("OFFMETM_FOOTNOTE", STATIC_EMAIL_SHARE);
        // $subPlantilla->assign("STATIC_DIET", STATIC_CONFERENCE_OPTIONS_DIET);
        // $subPlantilla->assign("STATIC_DIET2", STATIC_CONFERENCE_OPTIONS_DIET2);
        }
		
		if ($dinner_choices_closed) {
			// DINNER CHOICES ARE NOT OPEN YET				
			$dinner_choices_event = "style='display:none'";
			$subPlantilla->assign("CONFERENCE_DINNER_COMING_SOON", $dinner_teaser);
            $subPlantilla->assign("DINNER_CLOSED", "1");
              } else {
			// DINNER CHOICES ARE OPEN
            $dinner_choices_event = "";    
            $subPlantilla->assign("DINNER_CLOSED", "0");
        	}
      
      if ($all_metm_choices_closed) {
		  	// THE FORM IS COMPLETELY FROZEN
			$dinner_choices_event = "disabled";
        	$diet_event = "disabled";
			$subPlantilla->assign("DINNER_CLOSED", "1");
            $offmetm_choices_event = "style='display:none'";
            $subPlantilla->assign("FORM_EVENT", "disabled");
            $subPlantilla->assign("FORM_EVENT2", "style='display:none'");
            $subPlantilla->assign("DINNER_CLOSED", "1");
			$subPlantilla->assign("COMING_SOON", DYNAMIC_OFFMETM_TEASER2);
			for ($x = 1; $x <= $rownew["number_of_bands"]; $x++) {
			$subPlantilla->assign("OFFMETM_HEAD".$x, "");
				}
            $subPlantilla->assign("OFFMETM_FOOTNOTE", DYNAMIC_OFFMETM_FOOTNOTE);
            } else {     
			// THE FORM IS NOT FROZEN		
            $diet_event = "";
            $subPlantilla->assign("FORM_EVENT", "");
            $subPlantilla->assign("FORM_EVENT2", "");
			// $subPlantilla->assign("OFFMETM_FOOTNOTE", STATIC_EMAIL_SHARE);
            }
			
		// End of get conference form freeze data
      
	  $result = $db->callProcedure("CALL ed_pr_inscripcion_conferencia_get_options($id_attendee)");
      $row = $result->fetch_assoc();

      $badgetext = $row["conference_badge"];
      $badgelines = explode("<br />", $badgetext);
      $badgelines = str_replace("&comma;", ",", $badgelines);
      
	  $notgoingtodinner = $row["es_dinner"];
      
      if ($row["es_attendee_list"]) {
        $attendee_list_default = "checked";
                    } else {
        $attendee_list_default = "";
        }
      
        $councilBadge = $row["first_timer_or_council"];
        if ($row["first_timer_or_council"] == 1) {
        $firsttimerdefault = "checked";
                    } else {
        $firsttimerdefault = "";
        }	
      
      $dietaryorientation = $row["diet"];
      $subPlantilla->assign("CONFERENCE_PASSWORD", "");
      if($admin) {
      	if ($row["conference_password"]) {
      	$subPlantilla->assign("CONFERENCE_PASSWORD", "<p><strong>Your conference password is " . $row["conference_password"] . "</strong></p>");
      	} 
      }
      $photoholder = $row["imagen"];
      $raffleholder = $row["raffle_ticket"];
      $subPlantilla->assign("FIRST_TIMER", $firsttimerdefault);
      $subPlantilla->assign("ATTENDEE_LIST", $attendee_list_default);
      $subPlantilla->assign("CONFERENCE_ID", $row["id_inscripcion_conferencia"]);
      $firstsurname = str_replace("'", "&apos;", $row["nombre"]);
	  $subPlantilla->assign("CONFERENCE_NAME", $firstsurname);
	  $surname = str_replace("'", "&apos;", $row["apellidos"]);
	  $subPlantilla->assign("CONFERENCE_SURNAME", $surname);
      $subPlantilla->assign("CONFERENCE_EMAIL", $row["correo_electronico"]);
      $subPlantilla->assign("CONFERENCE_TELEPHONE", $row["telefono"]);
      if ($row["comentarios"]) {
      	$subPlantilla->assign("ATTENDEE_COMMENT", $row["comentarios"]);
      } else {
        $subPlantilla->assign("ATTENDEE_COMMENT", "None");
      }
      $subPlantilla->assign("ATTENDEE_NOTE", $row["observaciones"]);
      $subPlantilla->assign("ATTENDEE_ASISTIDO", $row["asistido"]);
      $subPlantilla->assign("CONFERENCE_BADGE0", substr($badgelines[0],0,30));
      $subPlantilla->assign("CONFERENCE_BADGE1", substr($badgelines[1],0,30));
      $subPlantilla->assign("CONFERENCE_BADGE2", substr($badgelines[2],0,30));
      $subPlantilla->assign("CONFERENCE_BADGE3", substr($badgelines[3],0,30));
      $subPlantilla->assign("CONFERENCE_BADGE4", substr($badgelines[5],0,30));
      $subPlantilla->assign("CONFERENCE_NO", $idConferencia);
      $FTorCouncil = $row["first_timer_or_council"];
      $SpeakerorHelper = $row["speaker"];

      if ($row["id_usuario_web"]) {
			$ProfileWarning = DYNAMIC_CHANGES_WARNING;
            $id_member = $row["id_usuario_web"];
	  } else {        
      		$ProfileWarning = "";
	  }
      $subPlantilla->assign("MET_PROFILE_WARNING", $ProfileWarning);
      require "includes/load_badge_code.php";
      
      if ($raffleholder) {
      $subPlantilla->assign("RAFFLE_TICKET_NUMBER", "<p style='text-align:center;' class='form-text'><big>" . str_pad($raffleholder, 3, '0', STR_PAD_LEFT) . "</big></p>");
      } else {        
        if ($raffle_disabled) {        
        $subPlantilla->assign("RAFFLE_TICKET_NUMBER", STATIC_CONFERENCE_RAFFLE_DISABLED);          
          } else {
          $subPlantilla->assign("RAFFLE_TICKET_NUMBER", STATIC_CONFERENCE_RAFFLE_ENABLED);          
        }          
          
      }

        $old_attendee_photo = "";
        $attendee_photo = "https://www.metmeetings.org/files/members/default.jpg";
        if ($photoholder) {
        $attendee_photo = "https://www.metmeetings.org/files/METM_attendees/" . $photoholder;
        $old_attendee_photo = $photoholder;
                    } elseif ($id_member) {
        $mresult = $db->callProcedure("CALL ed_sp_obtener_imagen($id_member)");
        $mrow = $mresult->fetch_assoc();
          if ($mrow["imagen"]) {
        $attendee_photo = "https://www.metmeetings.org/files/members/" . $mrow["imagen"];
          }
                    } 

      $subPlantilla->assign("CONFERENCE_PICTURE", $attendee_photo);
      $subPlantilla->assign("OLD_CONFERENCE_PICTURE", $old_attendee_photo);
      
      // Get reception guests
      
      $result = $db->callProcedure("CALL ed_pr_get_guests($id_attendee,7)");
      $row = $result->fetch_assoc();
      $receptionguests = STATIC_CONFERENCE_OPTIONS_BRINGING . " " . $row["valor"] . " " . STATIC_FORM_CONFERENCE_REGISTER_WINE_RECEPTION_GUEST_2;
      if ($row["valor"]==1) {
      	$receptionguests = str_replace("guests", "guest", $receptionguests);
      }
      $subPlantilla->assign("RECEPTION_GUESTS", $receptionguests);

          $total_guests = $row["valor"];
		  $receptionChoicecombos = "";
		  for ($x = 0; $x <= $total_guests; $x++) {
            
          $result = $db->callProcedure("CALL ed_pr_closing_dinner_guest_names($id_attendee,$x,2)");
      	  $row = $result->fetch_assoc();
			  
            $dinername = $row["guest_name"];
            $dinersurname = $row["guest_surname"];
            if($x){
              $receptionChoicecombos = $receptionChoicecombos . "<p class='form-text'><div class='form-row'><div class='col-12 col-md-6'><input type='text' class='form-control' name='RGuestNombre" . $x . "' id='RGuestNombre" . $x . "' value='" . $dinername . "' placeholder='" . STATIC_RECEPTION_GUEST_NAME . $x . "' autocomplete='given-name'" . $dinner_choices_event . "></div><div class='col-12 col-md-6'><input type='text' class='form-control' name='RGuestApellido" . $x . "' id='RGuestApellido" . $x . "' value='" . $dinersurname . "' placeholder='" . STATIC_RECEPTION_GUEST_SURNAME . $x . "' autocomplete='family-name'" . $dinner_choices_event . "></div></div></p>";
            }
		  } 
//      		  $receptionChoicecombos = "<p class='form-text'>Prova</p>";
              $subPlantilla->assign("RECEPTION_GUEST_NAMES",$receptionChoicecombos);
      
      		//Obtenemos los talleres que habian elegidos

		$vectorTallerInscripcion=Array();
		$resultadoTallerInscripcion=$db->callProcedure("CALL ed_pr_inscripcion_taller_conferencia($id_attendee)");
		$i=0;
		while($datoTallerInscripcion=$db->getData($resultadoTallerInscripcion)){
          if($datoTallerInscripcion["precio"] == "0.00") {
            
            $clinic_question = $datoTallerInscripcion["question"];
            $clinic_upd = $datoTallerInscripcion['id_inscripcion_conferencia_linea'];
            if(!$datoTallerInscripcion["time"]) {
              
              $clinic_n = $datoTallerInscripcion['id_taller_fecha'];
              for ($clinic_time = 1; $clinic_time <= 6; $clinic_time++) {
	              $timeresult = $db->callProcedure("CALL ed_pr_clinic_time_slot_check($clinic_time,$clinic_n)");
                  $timerow = $timeresult->fetch_assoc();
                  if(!$timerow["time"]) {
                    break;
                  }
                 }
             $db->callProcedure("CALL ed_pr_clinic_update($clinic_upd,$clinic_time,null)");

            } else {
              $clinic_time = $datoTallerInscripcion["time"];
              }
            switch ($clinic_time) {
                  case 0:
                      $WSTime = " (to be assigned)";
                      break;
                  case 1:
                      $WSTime = " (9:00)";
                      break;
                  case 2:
                      $WSTime = " (9:30)";
                      break;
                  case 3:
                      $WSTime = " (10:00)";
                      break;
                  case 4:
                      $WSTime = " (11:00)";
                      break;
                  case 5:
                      $WSTime = " (11:30)";
                      break;
                  case 6:
                      $WSTime = " (12:00)";
                      break;
            }            
            $show_question = "";
          } else {
            $WSTime = "";
            $show_question = "style='display:none'";
          }
			array_push($vectorTallerInscripcion,$datoTallerInscripcion["fecha"],$datoTallerInscripcion["nombre_largo"].$WSTime);
		}
		if ($vectorTallerInscripcion) {
      	$comma_separated = implode("<br/>",$vectorTallerInscripcion);
    
        if (strpos($comma_separated, STATIC_CONFERENCE_FIRST_WORKSHOP_DATE) !== false) {
          $disableWSblock1 = "1";
        } else {
          $disableWSblock1 = "0";
        }
        if (strpos($comma_separated, STATIC_CONFERENCE_SECOND_WORKSHOP_DATE) !== false) {
          $disableWSblock2 = "1";
        } else {
          $disableWSblock2 = "0";
        }
          
        $comma_separated = str_replace(STATIC_CONFERENCE_FIRST_WORKSHOP_DATE, STATIC_CONFERENCE_FIRST_WORKSHOP_DAY, $comma_separated);
        $comma_separated = str_replace(STATIC_CONFERENCE_SECOND_WORKSHOP_DATE, STATIC_CONFERENCE_SECOND_WORKSHOP_DAY, $comma_separated);
        $programme2 = DYNAMIC_SEE_PROGRAMME_W;
        $subPlantilla->assign("PROGRAMME_W", $programme2);
          } else {
        $comma_separated = STATIC_CONFERENCE_OPTIONS_NONE;
        $show_question = "style='display:none'";
          }
      	$subPlantilla->assign("CONFERENCE_WORKSHOP_1", $comma_separated);
   		$subPlantilla->assign("FORM_EVENT5", $show_question);
        $subPlantilla->assign("CLINIC_QUESTION", $clinic_question);
        $subPlantilla->assign("CLINIC_UPD", $clinic_upd);
        $subPlantilla->assign("CLINIC_TIME", $clinic_time);
        $subPlantilla->assign("WS_CLASH1", $disableWSblock1);
        $subPlantilla->assign("WS_CLASH2", $disableWSblock2);
      
      	// Deal with closing dinner
        // $notgoingtodinner = true;

        if ($notgoingtodinner) {
          
        // Not going to dinner
        $dinner_option = DYNAMIC_CONFERENCE_OPTIONS_NO_DINNER;
          
          } else {
          
        // Going to dinner
        $dinner_option = DYNAMIC_CONFERENCE_OPTIONS_DINNER;
          
        // Get dinner guests  
          
        $result = $db->callProcedure("CALL ed_pr_get_guests($id_attendee,2)");
        $row = $result->fetch_assoc();
        $total_guests = $row["valor"];
        $dinnerguests = STATIC_CONFERENCE_OPTIONS_BRINGING . " " . $total_guests . " " . DYNAMIC_CONFERENCE_OPTIONS_DINNER_GUESTS;
        $dinnerguests = str_replace("1 guests", "1 guest", $dinnerguests);
        $subPlantilla->assign("TOTAL_GUESTS", $row["valor"]);
		$dinnerChoicecombos = "";
          
        $result = $db->callProcedure("CALL ed_pr_total_number_of_dinner_courses()");
	    $row = $result->fetch_assoc();
	    $max_courses = $row["number_of_courses"];
        $subPlantilla->assign("MAX_COURSES", $row["number_of_courses"]);
 		
		// Closing dinner choices

		  for ($x = 0; $x <= $total_guests; $x++) {
            
          $result = $db->callProcedure("CALL ed_pr_closing_dinner_guest_names($id_attendee,$x,1)");
      	  $row = $result->fetch_assoc();
			  
            $dinername = $row["guest_name"];
            $dinersurname = $row["guest_surname"];
            if($x){
              if($x==1) {
                $dinnerChoicecombos = $dinnerChoicecombos ."<p class='form-text'>". $dinnerguests . "</p>";
              }
              $dinnerChoicecombos = $dinnerChoicecombos . "<p class='form-text'><div class='form-row'><div class='col-12 col-md-6'><input type='text' class='form-control' name='GuestNombre" . $x . "' id='GuestNombre" . $x . "' value='" . $dinername . "' placeholder='" . STATIC_DINNER_GUEST_NAME . $x . "' autocomplete='given-name'" . $dinner_choices_event . "></div><div class='col-12 col-md-6'><input type='text' class='form-control' name='GuestApellido" . $x . "' id='GuestApellido" . $x . "' value='" . $dinersurname . "' placeholder='" . STATIC_DINNER_GUEST_SURNAME . $x . "' autocomplete='family-name'" . $dinner_choices_event . "></div></div></p>";
            }
            
            for ($y = 1; $y <= $max_courses; $y++) {
              
                $result = $db->callProcedure("CALL ed_pr_closing_dinner_choices_per_course($id_attendee,$x,$y)");
      	        $row = $result->fetch_assoc();
              
                $chosendish = $row["dish"];
                $combostyle = "class='form-control' style='color:slategray;'";
              
              	if (($row["dish"]==-1) && $all_metm_choices_closed) {
                  $result2 = $db->callProcedure("CALL ed_pr_get_closing_dinner_default_dish($y)");
  				  $row2 = $result2->fetch_assoc();
  				  $chosendish = $row2["ID"];
                  $combostyle = "class='form-control' style='color:red;'";
                  } 
                                      
                $combodish = generalUtils::construirCombo($db,
                "CALL ed_pr_closing_dinner_dish_obtener_combo($y)",
                "cmbClosingDinner"  . $y . $x,
                "cmbClosingDinner"  . $y . $x,
                $chosendish,
                "dish",
                "ID",
                STATIC_CONFERENCE_CHOOSE_DISH_PROMPT,
                -1,
                $dinner_choices_event,
                $combostyle);   
              
                $dinnerChoicecombos = $dinnerChoicecombos ."<p class='form-text'>". $combodish . "</p>";
                }  
                if (!$dinner_choices_closed) {
            			if($x){
                          if(!$all_metm_choices_closed) {
                          $dinnerChoicecombos = $dinnerChoicecombos . "<p class='form-text'>" . STATIC_GUEST_ALLERGIES_START . $x . STATIC_GUEST_ALLERGIES_END . "</p>";

                          $guestallergyph = STATIC_CONFERENCE_CHOOSE_ALLERGIES_PROMPT . " for guest n. " . $x;
                          $allergyList = generalUtils::construirMultiCombo($db, "CALL ed_pr_get_allergies()", "CALL ed_pr_get_attendee_allergies($id_attendee,$x)", "allergy_name", "allergy_name", "cmbAllergy" . $x, "cmbAllergy" . $x, "allergy_name", "id_allergy", $guestallergyph, -1, 'class="form-control multiSelect"');
                          // $dinnerChoicecombos = $dinnerChoicecombos . $allergyList;
                          } else {
                          $allergyList = "";
                          $resultal = $db->callProcedure("CALL ed_pr_get_attendee_allergies($id_attendee,$x)");
                          while ($rowal = $db->getData($resultal)) {
                          	$allergyList = $allergyList . ", " . $rowal["allergy_name"];
                             }
						  if ($allergyList != ""){
							$allergyList = STATIC_GUEST_ALLERGIES_FROZEN . " " . substr($allergyList, 2);
							}
                          }
                          $dinnerChoicecombos = $dinnerChoicecombos . $allergyList;
                        }
                // $subPlantilla->assign("COMBO_DISHES",$dinnerChoicecombos);
                } else {
                  $dinnerChoicecombos = "<p class='form-text'>". $dinnerguests . "</p>";
                }
     		   }
              $subPlantilla->assign("COMBO_DISHES",$dinnerChoicecombos);
			}

		$subPlantilla->assign("CONFERENCE_DINNER_OPT_OUT", $dinner_option);
      
        $firstband = STATIC_CONFERENCE_FIRST_BAND;
        $secondband = STATIC_CONFERENCE_SECOND_BAND;
		
		if($three_four_time_band_incompatibility) {
          		$subPlantilla->assign("LIMITATION_INSTRUCTION", DYNAMIC_LIMITATION_INSTRUCTION);
				$blocktodisable = 0;
//          		$firstband = STATIC_CONFERENCE_FIRST_BAND;
//                $secondband = STATIC_CONFERENCE_SECOND_BAND;
          		$bandresult = $db->callProcedure("CALL ed_pr_get_number_events_in_band(" . $firstband . ")");
				$bandrow = $bandresult->fetch_assoc();
				$eventsinbandthree = $bandrow["eventsinband"] + 1;
                $bandresult = $db->callProcedure("CALL ed_pr_get_number_events_in_band(" . $secondband . ")");
				$bandrow = $bandresult->fetch_assoc();
				$eventsinbandfour = $bandrow["eventsinband"] + 1; 
                $bandthreejava = "onclick=disableSection('cmbOffMetms" . $firstband . "','cmbOffMetms" . $secondband . "'," . $eventsinbandfour . ");";
//				$subPlantilla->assign("OFF_METM_BAND3_EXCL", $bandthreejava);
                $bandfourjava = "onclick=disableSection('cmbOffMetms" . $secondband . "','cmbOffMetms" . $firstband . "'," . $eventsinbandthree . ");";
//                $bandfourjava = "onclick=disableSection('cmbOffMetms" . $secondband . "','cmbOffMetms" . $firstband . "'," . $eventsinbandthree . ");" . $choirwarningjava;
//				$subPlantilla->assign("OFF_METM_BAND4_EXCL", $bandfourjava);
				$result = $db->callProcedure("CALL ed_pr_inscripcion_offmetm_get_event($id_attendee," . $firstband . ")");
				$row = $result->fetch_assoc();
				if($row["off_metm_event"]) {
					$blocktodisable = $secondband;
					}
				$result = $db->callProcedure("CALL ed_pr_inscripcion_offmetm_get_event($id_attendee," . $secondband . ")");
				$row = $result->fetch_assoc();
				if($row["off_metm_event"]) {
					$blocktodisable = $firstband;
					}
		} else {
//          		$bandfourchoir = "onclick=" . $choirwarningjava;
                $bandfourjava = "onclick=" . $choirwarningjava;
          		$subPlantilla->assign("LIMITATION_INSTRUCTION", "");
//          		$subPlantilla->assign("OFF_METM_BAND4_EXCL", $bandfourchoir);
          }
      if ($offmetm_choices_event) {
      		$no_off_metm = true;
        } else {
      		$no_off_metm = false;
        }
      
      for ($x = 1; $x <= $rownew["number_of_bands"]; $x++) {
      
                $result = $db->callProcedure("CALL ed_pr_inscripcion_offmetm_get_event($id_attendee,$x)");
      	  		$row = $result->fetch_assoc();
				
				If ($blocktodisable == $x) {
					$disableblock = "disabled";
					} else {
					$disableblock = "";
					}
        
		$off_metm_combo = construirCheckbox($db,
                "CALL ed_pr_get_offmetms($x)",
                "cmbOffMetms"  . $x,
                "cmbOffMetms"  . $x,
                $row["off_metm_event"],
                "name",
                "id_off_metm",
                -1,
                $offmetm_choices_closed,
				$disableblock,
                'class="form-control"');
        
        $exclusivejava = "onclick=disableExclusive();";

        if (strpos($off_metm_combo, "style='font-weight: bold'") !== false) {
           		$no_off_metm = false;
                $headband = $head[$x-1];
           } else {
                $headband = "";
          }
        
        if (!$offmetm_choices_closed) {
                          $headband = $head[$x-1];
        }

        if ($x == $firstband) {
                  $offMETMsection = $offMETMsection . "<p class='form-text'><i>" . $headband . "</i></p><div " . $bandthreejava . "><p class='form-text'>" . $off_metm_combo . "</p></div>";
        } elseif ($x == $secondband) {
                  $offMETMsection = $offMETMsection . "<p class='form-text'><i>" . $headband . "</i></p><div " . $bandfourjava . $choirwarningjava . "><p class='form-text'>" . $off_metm_combo . "</p></div>";
//                  $offMETMsection = $offMETMsection . "<p class='form-text'><i>" . $headband . "</i></p><div " . $bandfourjava . "><p class='form-text'>" . $off_metm_combo . "</p></div>";
        } else {
                  $offMETMsection = $offMETMsection . "<p class='form-text'><i>" . $headband . "</i></p><div " . $exclusivejava . "><p class='form-text'>" . $off_metm_combo . "</p></div>";
        }        
    }
      
      if ($no_off_metm) {
          $offMETMsection = "<strong>" . STATIC_CONFERENCE_OPTIONS_NONE . "</strong>";
          }
      $subPlantilla->assign("OFFMETM_SECTION", $offMETMsection);
      $subPlantilla->assign("N_OFFMETM_BANDS", $rownew["number_of_bands"]);
//      $subPlantilla->assign("EXCLUSIVE_LIST", str_replace("cmbOffMetms","",$exclusiveList1));
//      $subPlantilla->assign("EXCLUSIVE_LIST2", str_replace("cmbOffMetms","",$exclusiveList2));
      
      $exclusiveList = '';
      for ($i = 1; $i <= $maxExclusive; $i++) {
        
          if (empty($GLOBALS['exclusiveList' . $i])) {
        	break;
          }
        
          $exclusiveList .= "processEList([" . $GLOBALS['exclusiveList' . $i] . "]); ";
      }
      
//      $exclusiveList = "processEList([" . $exclusiveList1 . "]); processEList([" . $exclusiveList2 . "]);";      
      $subPlantilla->assign("EXCLUSIVE_LIST3", str_replace("cmbOffMetms","",$exclusiveList));
      
//      if(!$offmetm_choices_closed) {
      //Combo dietary preferences

 $combodiet = generalUtils::construirCombo($db,
    "CALL ed_pr_get_dietary_preferences()",
    "cmbDiet",
    "cmbDiet",
    $dietaryorientation,
    "dietary_preference",
    "id_diet",
    STATIC_FORM_CONFERENCE_REGISTER_DIETARY_PREFERENCES,
    -1,
    $diet_event,
    'class="form-control" style="color:slategray;"');   
              
$subPlantilla->assign("COMBO_DIET",$combodiet);
      
if(!$all_metm_choices_closed) {
		$allergyList = generalUtils::construirMultiCombo($db, "CALL ed_pr_get_allergies()", "CALL ed_pr_get_attendee_allergies($id_attendee,0)", "allergy_name", "allergy_name", "cmbAllergy", "cmbAllergy", "allergy_name", "id_allergy", STATIC_CONFERENCE_CHOOSE_ALLERGIES_PROMPT, -1, 'class="form-control multiSelect"');
      } else {
		$allergyList = "";
		$resultal = $db->callProcedure("CALL ed_pr_get_attendee_allergies($id_attendee,0)");
		while ($rowal = $db->getData($resultal)) {
			$allergyList = $allergyList . ", " . $rowal["allergy_name"];
		}
  		$allergyList = substr($allergyList, 2);
  		if ($allergyList != ""){
        	$subPlantilla->assign("STATIC_DIET2", STATIC_ALLERGIES_CLOSED);
           	} else {
            $subPlantilla->assign("STATIC_DIET2", "");
            }
	  }
//      $specialRateSQL = "CALL ed_pr_metm_special_rate_met(1)";
      $subPlantilla->assign("COMBO_ALLERGIES",$allergyList);
      if ($id_member) {
        $specialRateSQL = "CALL ed_pr_metm_special_rate_met(1)";
      } else {
        $specialRateSQL = "CALL ed_pr_metm_special_rate_non_met()";
      }
      if ($_SESSION["met_user"]["tipoUsuario"] != TIPO_USUARIO_CONSEJO) {
  $subPlantilla->assign("FORM_EVENT8","style='display:none'");
} 
 //       $subPlantilla->assign("FORM_EVENT8","style='display:none'");
        $subPlantilla->assign("COMBO_COUNCIL",$comboCouncil);
		$subPlantilla->assign("COMBO_COUNCIL2",$comboCouncil2);
        $subPlantilla->parse("contenido_principal.bloque_council");
      if (!$comboCouncil) {
        $perhapsfirsttimer = 'display:block;';
      }
        $subPlantilla->assign("PERHAPSFIRSTTIMER",$perhapsfirsttimer);
        $subPlantilla->assign("DYNAMIC_BADGE_JAVASCRIPT",$badgeJavascript);
      
          $results = $db->callProcedure("CALL ed_pr_get_last_minute_news()");
          $newsString = "";
          $count = 0;
          if ($db->getNumberRows($results) > 0) {
          	while ($newsData = $db->getData($results)) {  
              if ($count) {
                $newsString = $newsString . "<br /><br />";
              }
              $count = $count + 1;
              $newsItem = $newsData["item"];
              $newsItem = str_replace("<p>", "", $newsItem);
              $newsItem = str_replace("</p>", "&nbsp;", $newsItem);
              $newsDate =  date('d/m/Y H:i', strtotime($newsData["time"]) - STATIC_CONFERENCE_TMIE_OFFSET_MINUTES);
          	$newsString = $newsString . $newsDate . " - " . $newsItem;      
          	}
          }
      $subPlantilla->assign("LAST_MINUTE_NEWS",$newsString);
      
     $subPlantilla->assign("DYNAMIC_SRATE",$jsonsRate);
     $subPlantilla->assign("DYNAMIC_COLOUR",$jsonColour);
     $subPlantilla->assign("DYNAMIC_COUNCIL_MEMBER",$jsonsCouncil);
     $subPlantilla->assign("DYNAMIC_COUNCIL_COLOUR",$jsonsCouncilcol); 
     $subPlantilla->assign("DYNAMIC_BADGE_TEMPLATE",$badgeTemplate);
 
      //Assign special rate drop down to placeholder in form template
     $subPlantilla->assign("COMBO_SPECIAL_RATE",
        generalUtils::construirCombo($db,
                $specialRateSQL,
                "cmbSpeaker",
                "cmbSpeaker",
                $SpeakerorHelper,
                "name_type",
                "id_type",
                STATIC_GLOBAL_COMBO_DEFAULT,
                -1,
                "",
                'class="form-control" style="color:slategray;" onchange="speaker()"'));

     $subPlantilla->parse("contenido_principal.bloque_special_rate");

	
    } else { 
          		if(isset($_SESSION["registration_desk"])) {
    			generalUtils::redirigir("https://www.metmeetings.org/en/their-metm:1410");
    		}
//	        if ($_GET['reload'] < 1) {
//          		$randomNumber = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
//          		generalUtils::redirigir("https://www.metmeetings.org/en/my-metm:1415?reload=" . $randomNumber);
//          		exit; // Always good to stop execution after a redirect
//	        }
	        $subPlantilla = new XTemplate("html/forms/register_for_conference_first.html");	
	
    }  

	// MIKE'S CODE ENDS HERE

	/**
	 * Asignamos el CSS que corresponde a este apartado
	 */
	$plantilla->assign("SECTION_FILE_CSS", "openbox.css");
	
	require "includes/load_structure.inc.php";
	
	//Cargamos el breadcrumb
	require "includes/load_breadcrumb.inc.php";
	
	//Cargamos los menus hijos del lateral derecho
	require "includes/load_menu_left.inc.php";
	
	$subPlantilla->parse("contenido_principal");
	
	/**
	 * Realizamos todos los parse realcionados con este apartado
	 */
	$plantilla->parse("contenido_principal.css_form");
	$plantilla->parse("contenido_principal.bloque_ready");
	$plantilla->parse("contenido_principal.control_superior");
    $plantilla->parse("contenido_principal.script_multiselect");
	//Parse the validation script in index.html
	$plantilla->parse("contenido_principal.validate_confedit_form");
	
	//Exportamos plantilla secundaria a la principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
    //Parse inner page content with lefthand menu
    $plantilla->parse($leftMenu);

	//Parseamos y sacamos informacion por pantalla
	$plantilla->parse("contenido_principal");
	$plantilla->out("contenido_principal");

function construirCheckbox($db,$procedure,$nombreCheck,$idCheck,$valorActual,$opcionTexto,$opcionValor,$opcionDefectoValor,$event,$block,$clase=""){			
  			if ($event) {
					$evento = "style='display:none'";
                    $evento5 = "style='font-weight: bold'";
					}else{
						$evento = "";
                        $evento5 = "";              			
					}
			$resultado=$db->callProcedure($procedure);
			while($dato=$db->getData($resultado)){
                ++$counter;
              
              	$result = $db->callProcedure("CALL ed_pr_get_offmetms_signups($dato[$opcionValor])");
      	  		$row = $result->fetch_assoc();
                if ($event) {
                  
                $resulto = $db->callProcedure("CALL ed_pr_get_offmetms_signups_names($dato[$opcionValor])");
				$memberstring = strtoupper($dato[$opcionTexto]) . "\\n";
				// $memberstring = str_replace ("'","\'",$memberstring);
                $membercounter = true;  
				if ($db->getNumberRows($resulto) > 0) {
					while ($data = $db->getData($resulto)) {      
                      if ($membercounter) {
                        $memberstring = $memberstring . $data["nombre"] . " " . $data["apellidos"] . ", ";
                        $membercounter = false;
                      } else {
                        $memberstring = $memberstring . $data["nombre"] . " " . $data["apellidos"] . ",\\n";
                        $membercounter = true;
                      }
					}
				}
//                $memberstring = substr($memberstring, 0, -2);
                $memberstring = str_replace ("'","\'",$memberstring);
                $memberstring = $memberstring . ".";
                $memberstring = str_replace (", .",".",$memberstring);
                $memberstring = str_replace (",\\n.",".",$memberstring);
                $groupIcon = " <a onclick='event".$dato[$opcionValor]."()'><img alt='' height='20' src='/documentacion/images/off-METM-group.png' width='31' /></a><script>function event".$dato[$opcionValor]."(){alert('".$memberstring."');}</script>";
                } else {
                  $groupIcon = "";
                }
              
				if(!$dato["max_signups"]) {
                  	  $evento2 = "style='display:none'";
                  	  $evento3 = "&nbsp;<b>&ndash;</b>";
                  	  $evento4 = "";
					}elseif ($row["signups"]==$dato["max_signups"]) {
					  $evento2 = "style='display:none'";
                      $evento3 = "&#10060;";
                      $evento4 = "<span style='color: red'>(Full)</span>";
					}else{
						$evento2 = "";
                  		$evento3 = "";
                  		$evento4 = "";
					}
//              if ($dato["exclusive"]=="1") {
//                                $GLOBALS['exclusiveList1'] = $GLOBALS['exclusiveList1'] . "," .$idCheck.$counter;
//              }
//              if ($dato["exclusive"]=="2") {
//                                $GLOBALS['exclusiveList2'] = $GLOBALS['exclusiveList2'] . "," .$idCheck.$counter;
//              }
    $maxExclusive = "10";
    for ($i = 1; $i <= $maxExclusive; $i++) {
        if ($dato["exclusive"] == $i) {
            $GLOBALS['exclusiveList' . $i] .= "," . $idCheck . $counter;
        }
    }              
				if($dato[$opcionValor]==$valorActual){
					$combo.="<input type='radio' $evento checked name='".$nombreCheck."' id='".$idCheck.$counter."' value='$dato[$opcionValor]'><span $evento5> $dato[$opcionTexto] $groupIcon<br></span>";
				}else{
					$combo.="<span $evento><input type='radio' $block $evento2 name='".$nombreCheck."' id='".$idCheck.$counter."' value='$dato[$opcionValor]'> $evento3 $dato[$opcionTexto] $evento4 $groupIcon<br></span>";
				}//end else
			}//end while
//  			if($counter==1){
//              $combo = str_replace("type='radio'", "type='checkbox'", $combo);
//             } 
			return $combo;
		}//end function
?>