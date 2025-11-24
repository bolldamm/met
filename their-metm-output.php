<?php
	/**
	 * 
	 * Pagina de inicio desde donde se inicia el portal
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */
	
	// session_start();
	require "includes/load_main_components.inc.php";
	
	if($_SESSION["met_user"]["tipoUsuario"] < TIPO_USUARIO_EDITOR)
{
	if(!isset($_SESSION["registration_desk"]))
		{
			generalUtils::redirigir(CURRENT_DOMAIN);
		}
}
	
	// Instanciamos la clase Xtemplate con la plantilla base
	$plantilla = new XTemplate("html/index.html");
	
	$id_attendee = $_SESSION["conference_attendee"];
	unset($_SESSION["conference_attendee"]);

// MIKE'S CODE

$subPlantilla = new XTemplate("html/their-metm.html");

	// Get currrent conference ID
	$result = $db->callProcedure("CALL ed_pr_get_current_id_conferencia()");
	$row = $result->fetch_assoc();
	$idConferencia = $row["current_id_conferencia"];

// Get possible dietary preferences
$record = $db->callProcedure("CALL ed_pr_get_dietary_preferences()");
$diet = array();
while($row = $record->fetch_assoc()){
$diet[] = $row["dietary_preference"];
}

      // Get conference form freeze data
	  $result = $db->callProcedure("CALL ed_pr_get_conference_freeze_data($idConferencia)");
	  $row = $result->fetch_assoc();

if ($row["freeze_dinner_choices"]) {
  $subPlantilla->assign("DINNER", "checked");
  }
if ($row["freeze_all_metm_choices"]) {
  $subPlantilla->assign("FREEZE", "checked");
    }
if ($row["time_band_incompatibility"]) {
  $subPlantilla->assign("INCOMPATIBILITY", "checked");
      }
if ($row["freeze_offmetm_choices"]) {
  $subPlantilla->assign("OFFMETM", "checked");
      }
if ($row["choir_overlap_warning"]) {
  $subPlantilla->assign("CHOIR", "checked");
      }
if ($row["disable_raffle"]) {
  $subPlantilla->assign("RAFFLE", "checked");
      }


for ($x = 1; $x <= 4; $x++) {
    $result = $db->callProcedure("CALL ed_pr_get_conference_attendee_diets($x,$idConferencia)");
	$row = $result->fetch_assoc();
	// $total_diet = $row["total_diet"];
  	$subPlantilla->assign("DIET_".$x, $diet[$x - 1] . ": " . $row["total_diet"]);
}

$comboattendee = generalUtils::construirCombo($db,
    "CALL ed_pr_get_conference_attendee_names($idConferencia)",
    "cmbAttendee",
    "cmbAttendee",
    "",
    "apellidos",
    "id_inscripcion_conferencia",
    "Conference attendee",
    "-1",
    "",
    'class="form-control" style="color:slategray;width:50%;"');

$subPlantilla->assign("COMBO_ATTENDEE",$comboattendee);

// $id_attendee = $_SESSION["conference_user"];

if ($id_attendee > 0) {
  $dinner_choices_event = "disabled";
  $result = $db->callProcedure("CALL ed_pr_inscripcion_conferencia_get_options($id_attendee)");
  $row = $result->fetch_assoc();
  $notgoingtodinner = $row["es_dinner"];
  $firstname = str_replace("'", "&apos;", $row["nombre"]);
  $subPlantilla->assign("CONFERENCE_NAME", $firstname);
  $surname = str_replace("'", "&apos;", $row["apellidos"]);
  $subPlantilla->assign("CONFERENCE_SURNAME", $surname);
  $subPlantilla->assign("CONFERENCE_EMAIL", $row["correo_electronico"]);
  $subPlantilla->assign("CONFERENCE_TELEPHONE", $row["telefono"]);
  $offmetm_choices_closed = true;
  $subPlantilla->assign("OFFMETM_FOOTNOTE", DYNAMIC_OFFMETM_FOOTNOTE);
  
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
 		
		// Closing dinner choices

		  for ($x = 0; $x <= $total_guests; $x++) {
            
          $result = $db->callProcedure("CALL ed_pr_closing_dinner_signups($id_attendee,$x)");
      	  $row = $result->fetch_assoc();
			  
			  $subPlantilla->assign("GUEST_NAME" . $x, "<div class='form-row'><div class='col-12 col-md-6'><input type='text' class='form-control' name='GuestNombre" . $x . "' id='GuestNombre" . $x . "' value='" . $row["diner_name"] . "' placeholder='Name of guest n. " . $x . "' autocomplete='given-name'" . $dinner_choices_event . "></div></div>");
            
            for ($y = 1; $y <= $max_courses; $y++) {
              
                $chosendish = $row["dish" . $y];
                $combostyle = "class='form-control' style='color:slategray;'";
              
              	if ($row["dish" . $y]==-1) {
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
  
  	  $resultnew = $db->callProcedure("CALL ed_pr_total_number_of_offmetm_time_bands()");
      $rownew = $resultnew->fetch_assoc();
  
  		for ($x = 1; $x <= $rownew["number_of_bands"]; $x++) {
			$subPlantilla->assign("OFFMETM_HEAD".$x, "<i>" . $head[$x-1] . "</i>");
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
  
  } else {
  $subPlantilla->assign("FORM_EVENT2", "style='display:none'");
}

unset($_SESSION["conference_user"]);

// END OF MIKE'S CODE
	
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
	
	//Exportamos plantilla secundaria a la principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
    //Parse inner page content with lefthand menu
    $plantilla->parse("contenido_principal.full_width_content");

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