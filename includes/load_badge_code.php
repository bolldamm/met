<?php

     /**
	 * 
	 * Badge code
	 * @author Mike
	 * @version 1.0
	 */


$badgeJavascript = "

 /*!
 * Name:  Badge functions
 * Author:  Mike
 * Version: 1
 * Date:    16th December 2021
 */
  
function annotate(y,x){
  var typed= document.getElementById(y).value;
  var len = typed.length;
  
  if (y == 'fname') {  
    
        if (!len) {
          typed = '".STATIC_FORM_MEMBERSHIP_FIRST_NAME."';
          var len = typed.length;
        }
        if (len<9) {
		  var fsize = 60;
		} else {
		  var fsize = ((len * -2) + 76);
		} 
  
} else if (y == 'fsurname') {
  
        if (!len) {
          typed = '".STATIC_FORM_MEMBERSHIP_LAST_NAMES."';
          var len = typed.length;
        }
        if (len<13) {
		  var fsize = 48;
		} else if (len<26) {
		  var fsize = ((len * -2.4) + 77);
		} else {
          var fsize = 16;
        }
  
  } else if (y == 'fline') {
    var fsize = 16;
    if (!len) {
      typed = '".STATIC_FORM_CONFERENCE_REGISTER_BADGE_BODY."';
    } 
  } else if (y == 'pline') {
    var fsize = 16;
    if (!len) {
      typed = '"."&nbsp"."';
    } 
  } else {
    var fsize = 16;
    if (!len) {
      typed = '".STATIC_FORM_CONFERENCE_REGISTER_BADGE_BODY_EXAMPLE_1."';
    }
  }
  
  if (len<=".STATIC_CONFERENCE_BADGE_LENGTH.") {
    
  document.getElementById(x).innerHTML= typed;
  document.getElementById(x).style.fontSize = fsize + 'px';
    
  }
}  
  
  function speaker(){
    document.getElementById('slash').innerHTML= '';

    var e = document.getElementById('cmbSpeaker');
    var f = document.getElementById('cmbCouncil');
    var g = document.getElementById('chkFirsttimer');

    if(e.value>0) {
      // Speaker/Helper
    document.getElementById('line3').innerHTML= RateArray[e.value];
    document.getElementById('line3').style.color = ColourArray[e.value];      
      if (g.checked == true){
        // also first-timer 
    	document.getElementById('slash').innerHTML= ' / ';
      } else {
        // not also first-timer
      document.getElementById('slash').innerHTML= '';
      }
    } else {
      // Not Speaker/Helper
      document.getElementById('line3').innerHTML= '';
    }
    if(f.value>0) {
      // Council member
    document.getElementById('council').innerHTML= '".STATIC_CONFERENCE_COUNCIL_LEAD."' + CouncilArray[f.value] + '".STATIC_CONFERENCE_COUNCIL_TRAIL."';
    document.getElementById('council').style.color = CouncilColourArray[f.value];
      if(e.value>0) {
        // also speaker/Helper 
    	document.getElementById('slash').innerHTML= ' / ';
      } else {
        // not also speaker/helper
      document.getElementById('slash').innerHTML= '';
      }
    } else {
      // Not Council member
    document.getElementById('council').innerHTML= '';
    }
  }

function firsttimer(){
  
      document.getElementById('slash').innerHTML= '';
      document.getElementById('council').innerHTML= '';
      var g = document.getElementById('chkFirsttimer');
      var e = document.getElementById('cmbSpeaker');
  
    if (g.checked == true){
       document.getElementById('council').innerHTML= CouncilArray['1'];
       document.getElementById('council').style.color = CouncilColourArray['1'];
       if(e.value>0) {
        // also speaker/Helper 
    	document.getElementById('slash').innerHTML= ' / ';
      } else {
        // not also speaker/helper
      document.getElementById('slash').innerHTML= '';
      }

  } else {
    document.getElementById('council').innerHTML= '';
  }
 }";

$badgeTemplate = "
        <div style='width: 94mm !important; min-height:65mm; max-height:65mm !important; border: 1px solid silver;margin-left: auto; margin-right: auto; overflow: hidden; padding: 4mm 3mm 5mm 2mm; font-style: bold; vertical-align: middle;white-space:nowrap;'>
        <h1 style='font-family: Segoe UI, Calibri; line-height: 70px; color: #2A4E6E; text-align:center;' id='name' style='font-size:60px; white-space:nowrap;'>".STATIC_FORM_MEMBERSHIP_FIRST_NAME."</h1>
        <h2 style='font-family: Segoe UI, Calibri; line-height: 60px; color: #2A4E6E; text-align:center;' id='surname' style='font-size:48px; white-space:nowrap;' >".STATIC_FORM_MEMBERSHIP_LAST_NAMES."</h2>
        <p style='text-align: left;	font-size:16px;	line-height: 18px; color: #239991; white-space:nowrap;'><img style='height:20mm; float: left; margin: 0 0 0 0;' src='https://www.metmeetings.org/documentacion/images/MET_logo_for_email_signature2.jpg'>
        <span id='line1' style='font-family: Segoe UI, Calibri; line-height: 16px; font-size:16px !important;'>".STATIC_FORM_CONFERENCE_REGISTER_BADGE_BODY."</span><br />
        <span id='line2' style='font-family: Segoe UI, Calibri; line-height: 16px; font-size:16px !important;'>".STATIC_FORM_CONFERENCE_REGISTER_BADGE_BODY_EXAMPLE_1."</span><br />
        <span id='council' style='font-family: Segoe UI, Calibri; line-height: 16px; font-size:16px;'></span><span id='slash' style='font-family: Segoe UI, Calibri; line-height: 16px; font-size:16px;color:black;'></span><span id='line3' style='font-family: Segoe UI, Calibri; line-height: 16px; font-size:16px;'></span><br />
        <span id='line4' style='font-family: Segoe UI, Calibri; line-height: 16px; font-size:16px !important;'>"."&nbsp"."</span></p>
</div>";

        $record = $db->callProcedure("CALL ed_pr_metm_special_rate_met(0)");
		$sRate = array();
		$colour = array();
		while($row = $record->fetch_assoc()){
			$sRate[$row["id_type"]] = $row["name_type"];
			$colour[$row["id_type"]] = $row["colour"];
		}
      
        $record = $db->callProcedure("CALL ed_pr_first_timer_or_council(0)");
		$councilMember = array();
      	$councilColor = array();
		while($row = $record->fetch_assoc()){
			$councilMember[$row["id_role"]] = $row["name_role"];
			$councilColor[$row["id_role"]] = $row["colour"];          
		}

     $jsonsCouncil = json_encode($councilMember);
     $jsonsCouncilcol = json_encode($councilColor);
     $jsonsRate = json_encode($sRate);
     $jsonColour = json_encode($colour);
     $perhapsfirsttimer = 'display:block;';

if ($_SESSION["met_user"]["tipoUsuario"] == TIPO_USUARIO_CONSEJO && $id_member) {
      $perhapsfirsttimer = 'display:none;';
      //Assign council drop downs to placeholders in form template
       $comboCouncil = generalUtils::construirCombo($db,
                        "CALL ed_pr_first_timer_or_council(1)",
                        "cmbCouncil",
                        "cmbCouncil",
                        $FTorCouncil,
                        "name_role",
                        "id_role",
                        "Please select current council role...",
                        -1,
                        "",
                        'class="form-control" style="color:slategray;" onchange="speaker()"');
						
       $comboCouncil2 = generalUtils::construirCombo($db,
                        "CALL ed_pr_first_timer_or_council(1)",
                        "cmbCouncil2",
                        "cmbCouncil2",
                        $badgelines[4],
                        "name_role",
                        "id_role",
                        "Please select new council role...",
                        -1,
                        "",
                        'class="form-control" style="color:slategray;"');
} 

if($id_member && !$_SESSION["met_user"]["tipoUsuario"] == TIPO_USUARIO_CONSEJO) {
    $resultFT = $db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_usuario_web_obtener(" . $id_member . ",3)");
    $datoFT = $db->getData($resultFT);

    if ( ! $datoFT) {
    //    Database contains no entries
          $perhapsfirsttimer = 'display:block;';
    } else {
          $perhapsfirsttimer = 'display:none;';
    }  
}

?>