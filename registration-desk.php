<?php
	/**
	 * 
	 * Pagina de inicio desde donde se inicia el portal
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */

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
	
	// Instanciamos la clase Xtemplate con la plantilla que va a contener la información del apartado
	$subPlantilla = new XTemplate("html/registration-desk.html");
	
	$subPlantilla->assign("ID_MENU",$_GET["menu"]);
	
	/**
	 * Asignamos el CSS que corresponde a este apartado
	 */
	$plantilla->assign("SECTION_FILE_CSS", "openbox.css");
	$plantilla->parse("contenido_principal.css_seccion");
	
	require "includes/load_structure.inc.php";
	
	if(isset($_GET["menu"]) && is_numeric($_GET["menu"])) {
		//Cargamos toda la información de openbox
		$resultOpenbox = $db->callProcedure("CALL ed_sp_web_openbox_obtener(".$_SESSION["id_idioma"].", ".$_GET["menu"].")");
		if($db->getNumberRows($resultOpenbox) > 0) {
			$dataOpenbox = $db->getData($resultOpenbox);
			$subPlantilla->assign("OPENBOX_DESCRIPCION", $dataOpenbox["descripcion"]);
		}
	}
	
	//Cargamos el breadcrumb
	require "includes/load_breadcrumb.inc.php";
	
	//Cargamos el slider en caso de que tenga imagenes
	require "includes/load_slider.inc.php";
	
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

for ($x = 1; $x <= 4; $x++) {
    $result = $db->callProcedure("CALL ed_pr_get_conference_attendee_diets($x,$idConferencia)");
	$row = $result->fetch_assoc();
	// $total_diet = $row["total_diet"];
  	$subPlantilla->assign("DIET_".$x, $diet[$x - 1] . ": " . $row["total_diet"]);
}

$resultReg = $db->callProcedure("CALL ed_sp_obtener_conferencia_reg_times(".$idConferencia.")");
$regData = "";
while ($dataReg = $db->getData($resultReg)) {
  
  	$resultFigure = $db->callProcedure("CALL ed_sp_obtener_conferencia_reg_figures(".$dataReg['RegDay'].",".$dataReg['RegMonth'].",".$dataReg['RegHour'].",".$idConferencia.")");
	$rowFigure = $resultFigure ->fetch_assoc();
  
  if($dataReg['RegDay']){
    $displayHour = $dataReg['RegHour'] - STATIC_CONFERENCE_TMIE_OFFSET_HOURS;
  $regData = $regData . "From " . $displayHour . ":00 to " . $displayHour .":59 on ". $dataReg['RegDay'] . "/" . $dataReg['RegMonth'] . ": ".$rowFigure["TotNum"]."<br />";
  } else {
  $regData = $regData . "Not yet registered: ".$rowFigure["TotNum"]."<br />";
  }
  
}

$subPlantilla->assign("REG_DATA", $regData);

$resultCourses = $db->callProcedure("CALL ed_pr_total_number_of_dinner_courses()");
$rowCourses = $resultCourses->fetch_assoc();
$max_courses = $rowCourses["number_of_courses"];
$dinnerData = "";

// Population part

$populate = $db->callProcedure("CALL ed_pr_get_inscripcion_conferencia_for_closing_dinner_populate($idConferencia)");

while($dato=$db->getData($populate)){
  for ($x = 0; $x <= $dato['valor']; $x++) {    
            $idAttendee = $dato["id_inscripcion_conferencia"];    
        
        for ($y = 1; $y <= $max_courses; $y++) {  

        // get dishes
        
      	$resultDish = $db->callProcedure("CALL ed_pr_closing_dinner_choices_per_course($idAttendee,$x,$y)");
      	$rowDish = $resultDish ->fetch_assoc();
      	$chosendish = $rowDish["dish"];
        
		if (!isset($chosendish)) {
          $chosendish=-1;
		  $db->callProcedure("CALL ed_pr_closing_dinner_choices_insert($idAttendee,$x,$y,$chosendish)");
		}        
    }
  }
}

// End of population part


for ($x = 1; $x <= $max_courses; $x++) {
  $dinnerData = $dinnerData ."<strong>Course " . $x ."</strong><br />";
  
   $resultDinner = $db->callProcedure("CALL ed_pr_closing_dinner_dish_stats(-1,$x)");
   $rowDinner = $resultDinner ->fetch_assoc();
   $defaultFigure = $rowDinner["Figure"];
  
   $resultDinnerg = $db->callProcedure("CALL ed_pr_closing_dinner_dish_gluten_stats(-1,$x)");
   $rowDinnerg = $resultDinnerg ->fetch_assoc();
   $defaultFigureg = $rowDinnerg["gluten"];
  
   $result2 = $db->callProcedure("CALL ed_pr_get_closing_dinner_default_dish($x)");
   $row2 = $result2->fetch_assoc();
   $defaultDish = $row2["ID"];
 
   $resultDish = $db->callProcedure("CALL ed_pr_closing_dinner_dish_obtener_combo(".$x.")");

while ($dataDish = $db->getData($resultDish)) {
  
   $thisDish = $dataDish['ID'];
  
   $resultDinner = $db->callProcedure("CALL ed_pr_closing_dinner_dish_stats($thisDish,$x)");
   $rowDinner = $resultDinner ->fetch_assoc();
  
   $resultDinnerg = $db->callProcedure("CALL ed_pr_closing_dinner_dish_gluten_stats($thisDish,$x)");
   $rowDinnerg = $resultDinnerg ->fetch_assoc();

  if($thisDish == $defaultDish) {
       $figure = $rowDinner["Figure"] + $defaultFigure;
       $figureg = $rowDinnerg["gluten"] + $defaultFigureg;
       $dinnerData = $dinnerData . $dataDish['dish'] . ": " . $figure . "<br />(" . $figureg . " of which gluten-free and " . $defaultFigure . " of which by default)<br />";
  } else {
       $figure = $rowDinner["Figure"];
       $figureg = $rowDinnerg["gluten"];
       $dinnerData = $dinnerData . $dataDish['dish'] . ": " . $figure . "<br />(" . $figureg . " of which gluten-free)<br />";
  }
 }
}

$subPlantilla->assign("DINNER_DATA", $dinnerData);

$resultWS = $db->callProcedure("CALL ed_pr_number_workshop_attendees($idConferencia)");

// Array to store quantities
    $quantities = array();
	
// Fetch quantities and store them in the array
    while ($row = $resultWS->fetch_assoc()) {
        $quantities[] = $row['n_attendees'];
    }
	
// Assign quantities to output
$subPlantilla->assign("TOT_WS1_A", $quantities[0]);
$subPlantilla->assign("TOT_WS2_A", $quantities[1]);

$resultWSF = $db->callProcedure("CALL ed_pr_number_workshop_facilitators($idConferencia)");

// Array to store quantities
    $quantitiesf = array();
	
// Fetch quantities and store them in the array
    while ($row = $resultWSF->fetch_assoc()) {
        $quantitiesf[] = $row['n_fac'];
    }
	
// Assign quantities to output
$subPlantilla->assign("TOT_WS1_F", $quantitiesf[0]);
$subPlantilla->assign("TOT_WS2_F", $quantitiesf[1]);

$resultCB = $db->callProcedure("CALL ed_pr_get_coffee_breaks");

// Array to store quantities
    $quantitiescb = array();
	
// Fetch quantities and store them in the array
    while ($row = $resultCB->fetch_assoc()) {
        $quantitiecb[] = $row['coffee_break_name'];
    }
	
// Assign quantities to output
for ($i = 0; $i < count($quantitiecb); $i++) {
    $subPlantilla->assign("CB_" . ($i + 1), $quantitiecb[$i]);
}

$resultECB = $db->callProcedure("CALL ed_pr_number_coffee_break_extras");
$rowCBE = $resultECB ->fetch_assoc();
for ($i = 1; $i <= 2; $i++) {
    ${"ws{$i}extras"} = $rowCBE["tcb{$i}"];
}
$faextras = $rowCBE["tcb3"];
$smextras = $rowCBE["tcb4"];
$saextras = $rowCBE["tcb5"];

// $ws1extras = "0";
$subPlantilla->assign("TOT_WS1_E", $ws1extras);
$totws1 = $quantities[0] + $quantitiesf[0] + $ws1extras;
$subPlantilla->assign("TOT_WS1", $totws1);

// $ws2extras = "0";
$subPlantilla->assign("TOT_WS2_E", $ws2extras);
$totws2 = $quantities[1] + $quantitiesf[1] + $ws2extras;
$subPlantilla->assign("TOT_WS2", $totws2);

$resultQR = $db->callProcedure("CALL ed_pr_count_click()");
$rowQR = $resultQR ->fetch_assoc();
$QRTotal = $rowQR["NumberOfClicks"];

$subPlantilla->assign("CLICK_TOTAL", $QRTotal);

$resultCountries = $db->callProcedure("CALL ed_pr_pais_inscripcion($idConferencia)");
$CountryData = "";

while ($dataCountry = $db->getData($resultCountries)) {
  $CountryData = $CountryData . $dataCountry["pais_factura"] . ": " . $dataCountry["number"] . "<br />";
}
$subPlantilla->assign("COUNTRY_DATA", $CountryData);

$resultTypes = $db->callProcedure("CALL ed_pr_tipo_inscripcion($idConferencia)");
$rowTypes = $resultTypes ->fetch_assoc();

  $nonMember = $rowTypes["total"] - $rowTypes["sister"] - $rowTypes["member"];
  $TypeData = "Member: " . $rowTypes["member"] . "<br />" . "Sister: " . $rowTypes["sister"] . "<br />" . "Non-member: " . $nonMember . "<br />" . "Total: " . $rowTypes["total"];
$subPlantilla->assign("C_ATTENDEE_CB", $rowTypes["total"]);
$subPlantilla->assign("TYPE_DATA", $TypeData);

// $faextras = "0";
$subPlantilla->assign("TOT_FA_E", $faextras);
$totfa = $rowTypes["total"] + $faextras;
$subPlantilla->assign("TOT_FA", $totfa);

// $smextras = "0";
$subPlantilla->assign("TOT_SM_E", $smextras);
$totsm = $rowTypes["total"] + $smextras;
$subPlantilla->assign("TOT_SM", $totsm);

// $saextras = "0";
$subPlantilla->assign("TOT_SA_E", $saextras);
$totsa = $rowTypes["total"] + $saextras;
$subPlantilla->assign("TOT_SA", $totsa);

$resultFTs = $db->callProcedure("CALL ed_pr_get_total_first_timers($idConferencia)");
$rowFTs = $resultFTs ->fetch_assoc();
$totFTs = $rowFTs["total"];

$subPlantilla->assign("TOT_FT", $totFTs);

$resultKHelper = $db->callProcedure("CALL ed_pr_get_total_speakers($idConferencia,5)");
$rowKHelper = $resultKHelper ->fetch_assoc();
$totKHelper = $rowKHelper["total"];

$subPlantilla->assign("TOT_K_HELP", $totKHelper);

$resultHelper = $db->callProcedure("CALL ed_pr_get_total_speakers($idConferencia,1)");
$rowHelper = $resultHelper ->fetch_assoc();
$totHelper = $rowHelper["total"];

$subPlantilla->assign("TOT_HELP", $totHelper);

$resultSpeaker = $db->callProcedure("CALL ed_pr_get_total_speakers($idConferencia,2)");
$rowSpeaker = $resultSpeaker ->fetch_assoc();
$totSpeaker = $rowSpeaker["total"];

$subPlantilla->assign("TOT_SPEAKER", $totSpeaker);

$resultStudent = $db->callProcedure("CALL ed_pr_get_total_speakers($idConferencia,3)");
$rowStudent = $resultStudent ->fetch_assoc();
$totStudent = $rowStudent["total"];

$subPlantilla->assign("TOT_STUDENT", $totStudent);

$resultKeynote = $db->callProcedure("CALL ed_pr_get_total_speakers($idConferencia,6)");
$rowKeynote = $resultKeynote ->fetch_assoc();
$totKeynote = $rowKeynote["total"];

$subPlantilla->assign("TOT_KEYNOTE", $totKeynote);

$resultReceptionG = $db->callProcedure("CALL ed_pr_total_reception_guests($idConferencia)");
$rowReceptionG = $resultReceptionG ->fetch_assoc();
$totReceptionG = $rowReceptionG["total"];

$subPlantilla->assign("TOT_RG", $totReceptionG);

		$subPlantilla->parse("contenido_principal");
	/**
	 * Realizamos todos los parse realcionados con este apartado
	 */
	$plantilla->parse("contenido_principal.css_form");
	$plantilla->parse("contenido_principal.bloque_ready");
	$plantilla->parse("contenido_principal.control_superior");
	
	
	//Exportamos plantilla secundaria a la principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
    //Parse inner page content with lefthand menu
    $plantilla->parse("contenido_principal.full_width_content");

	//Parseamos y sacamos informacion por pantalla
	$plantilla->parse("contenido_principal");
	$plantilla->out("contenido_principal");
?>