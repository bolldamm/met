<?php
	/**
	 * 
	 * Pagina de inicio desde donde se inicia el portal
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */

	require "includes/load_main_components.inc.php";
    header("Cache-Control: no-cache, no-store, must-revalidate");
	
	// Instanciamos la clase Xtemplate con la plantilla base
	$plantilla = new XTemplate("html/index.html");
	
	// Instanciamos la clase Xtemplate con la plantilla que va a contener la información del apartado
	
	// MIKE'S CODE STARTS HERE 
	// Get currrent conference ID
	$result = $db->callProcedure("CALL ed_pr_get_current_id_conferencia()");
	$row = $result->fetch_assoc();
	$idConferencia = $row["current_id_conferencia"];
	// $idConferencia = 19;

// IF THERE ARE REG DESK PROBLEMS, DISABLE THE PART BELOW
	if ($_SESSION["met_user"]["id"]) {
		// You are a MET member and you are logged in. So I can find your conference id
		$id_member = $_SESSION["met_user"]["id"];
		$result = $db->callProcedure("CALL ed_pr_get_conference_user($id_member,$idConferencia)");
		$row = $result->fetch_assoc();
		
		if ($row["id_inscripcion_conferencia"]) {
			// You are a MET member, you are logged in and I have found your conference id
			$_SESSION["conference_user"] = $row["id_inscripcion_conferencia"];
            // $pageclose = "";
//			$ProfileWarning = DYNAMIC_CHANGES_WARNING;
		} 
//      else {        
//			// You are a MET member, you are logged in, but you have not registered for the conference yet
//			$_SESSION["conference_user"] = null;
//		}
//      } else {
      // $pageclose = "<a href='close_conference_options_page.php'>Sign out of conference</a>";
//      $ProfileWarning = "";
	}
// IF THERE ARE REG DESK PROBLEMS, DISABLE THE PART ABOVE

	if ($_POST["confpassword"]) {
	// You are attempting to login as a non member
	$confemail = generalUtils::filtrarInyeccionSQL(generalUtils::escaparCadena($_POST["confemail"]));
	$confpassword = generalUtils::filtrarInyeccionSQL(generalUtils::escaparCadena($_POST["confpassword"]));
    // $confpassword = hexdec($confpassword);
    // $confpassword = substr($confpassword, 0, -4);
	$result = $db->callProcedure("CALL ed_pr_non_member_login('$confpassword','$confemail',$idConferencia)");
	$row = $result->fetch_assoc();

	if ($row["id_inscripcion_conferencia"]) {
			// I have found your conference id
			$_SESSION["conference_user"] = $row["id_inscripcion_conferencia"];            
		} else {        
			// I cannot find your conference id
			$_SESSION["conference_user"] = null;
		}	
	}
	
	if ($_SESSION["conference_user"]) {
      // You are are logged into your conference options
      
      if(isset($_SESSION["auth_target"])){
				$target=$_SESSION["auth_target"];
				unset($_SESSION["auth_target"]);
				generalUtils::redirigir($target);
			}
      
      $id_attendee = $_SESSION["conference_user"];
      
      $subPlantilla = new XTemplate("html/forms/form_edit_conference_options.html");
      
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
	  $three_four_time_band_incompatibility = $row["time_band_incompatibility"];
      $offmetm_choices_closed = $row["freeze_offmetm_choices"];
      $choirwarning = STATIC_CHOIR_WARNING;
      if ($row["choir_overlap_warning"]) {
        $choirwarningjava = "window.alert('$choirwarning');";
      } else {
        $choirwarningjava = "";
      }
      $raffle_disabled = $row["disable_raffle"];
	  // The parameter below is the total freeze
      // $all_metm_choices_closed = $row["freeze_all_metm_choices"];
      $all_metm_choices_closed = true;
	  
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
      
      if ($offmetm_choices_closed) {
  	  // OFF-METM CHOICES ARE NOT OPEN YET
		for ($x = 1; $x <= $rownew["number_of_bands"]; $x++) {
			$subPlantilla->assign("OFFMETM_HEAD".$x, "");
			}
        $subPlantilla->assign("COMING_SOON", DYNAMIC_OFFMETM_TEASER);

        } else {
  	  // OFF-METM CHOICES ARE OPEN			
		for ($x = 1; $x <= $rownew["number_of_bands"]; $x++) {
			$subPlantilla->assign("OFFMETM_HEAD".$x, "<i>" . $head[$x-1] . "</i>");
			}
		$subPlantilla->assign("COMING_SOON", "");
        $subPlantilla->assign("PROGRAMME", $programme);
        $subPlantilla->assign("STATIC_DIET", STATIC_CONFERENCE_OPTIONS_DIET);
        $subPlantilla->assign("STATIC_DIET2", STATIC_CONFERENCE_OPTIONS_DIET2);
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
			$subPlantilla->assign("DINNER_CLOSED", "1");
            $offmetm_choices_event = "style='display:none'";
            $subPlantilla->assign("FORM_EVENT", "disabled");
            $subPlantilla->assign("FORM_EVENT2", "style='display:none'");
            $subPlantilla->assign("DINNER_CLOSED", "1");
			$subPlantilla->assign("COMING_SOON", DYNAMIC_OFFMETM_TEASER2);
			for ($x = 1; $x <= $rownew["number_of_bands"]; $x++) {
			$subPlantilla->assign("OFFMETM_HEAD".$x, "");
			}
            $subPlantilla->assign("STATIC_DIET", "");
            $subPlantilla->assign("STATIC_DIET2", "");
            $subPlantilla->assign("OFFMETM_FOOTNOTE", DYNAMIC_OFFMETM_FOOTNOTE);
            } else {     
			// THE FORM IS NOT FROZEN			
            $subPlantilla->assign("FORM_EVENT", "");
            $subPlantilla->assign("FORM_EVENT2", "");
			$subPlantilla->assign("OFFMETM_FOOTNOTE", "");
            }
			
		// End of get conference form freeze data
      
	  $result = $db->callProcedure("CALL ed_pr_inscripcion_conferencia_get_options($id_attendee)");
      $row = $result->fetch_assoc();
	  $newbadge = str_replace ( "<br />", "", $row["conference_badge"] );
	  $notgoingtodinner = $row["es_dinner"];
      
      if ($row["es_attendee_list"]) {
        $attendee_list_default = "checked";
                    } else {
        $attendee_list_default = "";
        }
      
      $dietaryorientation = $row["diet"];
      $subPlantilla->assign("ATTENDEE_LIST", $attendee_list_default);
      $subPlantilla->assign("CONFERENCE_ID", $row["id_inscripcion_conferencia"]);
      $firstsurname = str_replace("'", "&apos;", $row["nombre"]);
	  $subPlantilla->assign("CONFERENCE_NAME", $firstsurname);
	  $surname = str_replace("'", "&apos;", $row["apellidos"]);
	  $subPlantilla->assign("CONFERENCE_SURNAME", $surname);
      $subPlantilla->assign("CONFERENCE_EMAIL", $row["correo_electronico"]);
      $subPlantilla->assign("CONFERENCE_TELEPHONE", $row["telefono"]);
      $subPlantilla->assign("CONFERENCE_BADGE", $newbadge);
      $subPlantilla->assign("CONFERENCE_NO", $idConferencia);
      // $subPlantilla->assign("CLOSE_CONFERENCE_PAGE", $pageclose);
      if ($_SESSION["met_user"]["id"]) {
			$ProfileWarning = DYNAMIC_CHANGES_WARNING;
            $id_member = $_SESSION["met_user"]["id"];
	  } else {        
      		$ProfileWarning = "";
	  }
      $subPlantilla->assign("MET_PROFILE_WARNING", $ProfileWarning);
      
      if ($row["raffle_ticket"]) {
      $subPlantilla->assign("RAFFLE_TICKET_NUMBER", "<p style='text-align:center;' class='form-text'><big>" . str_pad($row["raffle_ticket"], 3, '0', STR_PAD_LEFT) . "</big></p>");
      } else {        
        if ($raffle_disabled) {        
        $subPlantilla->assign("RAFFLE_TICKET_NUMBER", "<p class='form-text'>On Friday evening, click here to get your raffle ticket.</p><div style='text-align:center;'><button disabled>Get raffle ticket</button></div>");          
          } else {
          $subPlantilla->assign("RAFFLE_TICKET_NUMBER", "<p class='form-text'>On Friday evening, click here to get your raffle ticket.</p><div style='text-align:center;'><button onclick='pickRaffleTicket()'>Get raffle ticket</button></div>");          
        }
          
          
      }
     
        $old_attendee_photo = "";

        if ($row["imagen"]) {
        $attendee_photo = "https://www.metmeetings.org/files/METM_attendees/" . $row["imagen"];
        $old_attendee_photo = $row["imagen"];
                    } elseif ($id_member) {
        $result = $db->callProcedure("CALL ed_sp_obtener_imagen($id_member)");
        $row = $result->fetch_assoc();
        $attendee_photo = "https://www.metmeetings.org/files/members/" . $row["imagen"];
                    } else {
        $attendee_photo = "https://www.metmeetings.org/files/members/default.jpg";
        }
      
      $subPlantilla->assign("CONFERENCE_PICTURE", $attendee_photo);
      $subPlantilla->assign("OLD_CONFERENCE_PICTURE", $old_attendee_photo);
      
      // Get reception guests
      
      $result = $db->callProcedure("CALL ed_pr_get_guests($id_attendee,7)");
      $row = $result->fetch_assoc();
      $subPlantilla->assign("RECEPTION_GUESTS", $row["valor"]);     
      	        
      		//Obtenemos los talleres que habian elegidos
		$vectorTallerInscripcion=Array();
		$resultadoTallerInscripcion=$db->callProcedure("CALL ed_pr_inscripcion_taller_conferencia($id_attendee)");
		$i=0;
		while($datoTallerInscripcion=$db->getData($resultadoTallerInscripcion)){
			array_push($vectorTallerInscripcion,$datoTallerInscripcion["fecha"],$datoTallerInscripcion["nombre_largo"]);
		}
		if ($vectorTallerInscripcion) {
      	$comma_separated = implode("<br/>",$vectorTallerInscripcion);
        $comma_separated = str_replace("2019-09-26", "</strong><i>Thursday afternoon</i><strong>", $comma_separated);
        $comma_separated = str_replace("2019-09-27", "</strong><i>Friday morning</i><strong>", $comma_separated);
        $subPlantilla->assign("PROGRAMME_W", $programme);
          } else {
        $comma_separated = STATIC_CONFERENCE_OPTIONS_NONE;
          }
      	$subPlantilla->assign("CONFERENCE_WORKSHOP_1", $comma_separated);
      
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
		// $row["valor"] = 2;
        $total_guests = $row["valor"];
        $dinnerguests = STATIC_CONFERENCE_OPTIONS_BRINGING . " " . $total_guests . " " . DYNAMIC_CONFERENCE_OPTIONS_DINNER_GUESTS;
        $subPlantilla->assign("DINNER_GUESTS", $dinnerguests);
        $subPlantilla->assign("TOTAL_GUESTS", $row["valor"]);

          
        $result = $db->callProcedure("CALL ed_pr_total_number_of_dinner_courses()");
	    $row = $result->fetch_assoc();
	    $max_courses = $row["number_of_courses"];
        // $max_courses = 3;
        // $subPlantilla->assign("TOTAL_COURSES", $max_courses);
 		
		// Closing dinner choices

		  for ($x = 0; $x <= $total_guests; $x++) {
            
          $result = $db->callProcedure("CALL ed_pr_closing_dinner_signups($id_attendee,$x)");
      	  $row = $result->fetch_assoc();
			  
			  $subPlantilla->assign("GUEST_NAME" . $x, "<div class='form-row'><div class='col-12 col-md-6'><input type='text' class='form-control' name='GuestNombre" . $x . "' id='GuestNombre" . $x . "' value='" . $row["diner_name"] . "' placeholder='Name of guest n. " . $x . "' autocomplete='given-name'" . $dinner_choices_event . "></div></div>");
            
            for ($y = 1; $y <= $max_courses; $y++) {
              
                $chosendish = $row["dish" . $y];
                $combostyle = "class='form-control' style='color:slategray;'";
              
              	if (($row["dish" . $y]==-1) && $all_metm_choices_closed) {
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
              
                $subPlantilla->assign("COMBO_DISH" . $y . $x,$combodish);
                }  
     		   }
			}

		$subPlantilla->assign("CONFERENCE_DINNER_OPT_OUT", $dinner_option);
		
		if($three_four_time_band_incompatibility) {
				$blocktodisable = 0;
          
          		$bandresult = $db->callProcedure("CALL ed_pr_get_number_events_in_band(3)");
				$bandrow = $bandresult->fetch_assoc();
				$eventsinbandthree = $bandrow["eventsinband"] + 1;
                $bandresult = $db->callProcedure("CALL ed_pr_get_number_events_in_band(4)");
				$bandrow = $bandresult->fetch_assoc();
				$eventsinbandfour = $bandrow["eventsinband"] + 1; 
          
          		$bandthreejava = "onclick=disableSection('cmbOffMetms3','cmbOffMetms4'," . $eventsinbandfour . ");";
				$subPlantilla->assign("OFF_METM_BAND3_EXCL", $bandthreejava);
                $bandfourjava = "onclick=disableSection('cmbOffMetms4','cmbOffMetms3'," . $eventsinbandthree . ");" . $choirwarningjava;
				$subPlantilla->assign("OFF_METM_BAND4_EXCL", $bandfourjava);
				$result = $db->callProcedure("CALL ed_pr_inscripcion_offmetm_get_event($id_attendee,3)");
				$row = $result->fetch_assoc();
				if($row["off_metm_event"]) {
					$blocktodisable = 4;
					}
				$result = $db->callProcedure("CALL ed_pr_inscripcion_offmetm_get_event($id_attendee,4)");
				$row = $result->fetch_assoc();
				if($row["off_metm_event"]) {
					$blocktodisable = 3;
					}
		} else {
          		$bandfourchoir = "onclick=" . $choirwarningjava;
          		$subPlantilla->assign("OFF_METM_BAND4_EXCL", $bandfourchoir);
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

        if (strpos($off_metm_combo, "style='font-weight: bold'") !== false) {
           		$no_off_metm = false;
          		$subPlantilla->assign("OFFMETM_HEAD" . $x, "<i>" . $head[$x-1] . "</i>");
           } 

		$subPlantilla->assign("OFF_METM_BAND" . $x,$off_metm_combo);
        
    }
      // $no_off_metm = true;
      if ($no_off_metm) {
          $subPlantilla->assign("OFF_METM_BAND1","<strong>" . STATIC_CONFERENCE_OPTIONS_NONE . "</strong>");
          $subPlantilla->assign("OFFMETM_HEAD1", "");
          $subPlantilla->assign("OFFMETM_HEAD2", "");
          $subPlantilla->assign("OFFMETM_HEAD3", "");
          $subPlantilla->assign("OFFMETM_HEAD4", "");
          }
      
      if(!$offmetm_choices_closed) {
      //Combo dietary preferences

 $combodiet = generalUtils::construirCombo($db,
    "CALL ed_pr_get_dietary_preferences()",
    "cmbDiet",
    "cmbDiet",
    $dietaryorientation,
    "dietary_preference",
    "id_diet",
    "",
    "",
    "",
    'class="form-control" style="color:slategray;"');   
              
$subPlantilla->assign("COMBO_DIET",$combodiet);

      $subPlantilla->assign("COMBO_ALLERGIES", generalUtils::construirMultiCombo($db, "CALL ed_pr_get_allergies()", "CALL ed_pr_get_attendee_allergies($id_attendee)", "allergy_name", "allergy_name", "cmbAllergy", "cmbAllergy", "allergy_name", "id_allergy", STATIC_CONFERENCE_CHOOSE_ALLERGIES_PROMPT, -1, 'class="form-control multiSelect"'));
      }  
	
    } else { 
	
	  if ($_SESSION["met_user"]["id"]) {
        // You are a MET member, you are logged in, but you have not registered for the conference yet
        $subPlantilla = new XTemplate("html/forms/register_for_conference_first.html");		  
		
		} else { 
	      // You are not logged in
			$subPlantilla = new XTemplate("html/forms/form_nonmember_conference_login.html");
		}	
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
    $plantilla->parse("contenido_principal.script_multiselect");
	//Parse the validation script in index.html
	$plantilla->parse("contenido_principal.validate_confedit_form");
	
	//Exportamos plantilla secundaria a la principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
    //Parse inner page content with lefthand menu
    $plantilla->parse("contenido_principal.menu_left");

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
              
				if($dato[$opcionValor]==$valorActual){
					$combo.="<input type='radio' $evento checked name='".$nombreCheck."' id='".$idCheck.$counter."' value='$dato[$opcionValor]'><span $evento5>$dato[$opcionTexto]<br></span>";
				}else{
					$combo.="<span $evento><input type='radio' $block $evento2 name='".$nombreCheck."' id='".$idCheck.$counter."' value='$dato[$opcionValor]'> $evento3 $dato[$opcionTexto] $evento4<br></span>";
				}//end else
			}//end while
  			if($counter==1){
              $combo = str_replace("type='radio'", "type='checkbox'", $combo);
             } 
			return $combo;
		}//end function
?>