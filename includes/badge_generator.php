<?php
	/**
	 * 
	 * Script to generate METM badges
	 * @Author Mike
	 * 
	 */

    use \Spipu\Html2Pdf\Html2Pdf;
    use \Spipu\Html2Pdf\Exception\Html2PdfException;
    use \Spipu\Html2Pdf\Exception\ExceptionFormatter;

function badgeBody($charSize, $firstName, $charSize2, $lastName, $firstLine, $secondLine, $otherInfo, $pronouns) {
  return "<tr><td><h1 style='".$charSize."'>".$firstName."</h1><h2 style='".$charSize2."'>".$lastName."</h2><p><img id='hp' src='https://www.metmeetings.org/documentacion/images/MET_logo_for_email_signature2.jpg'>".$firstLine."<br />".$secondLine."<br />".$otherInfo."<br />".$pronouns."</p></td>
	<td><h1 style='".$charSize."'>".$firstName."</h1><h2 style='".$charSize2."'>".$lastName."</h2><p><img id='hp' src='https://www.metmeetings.org/documentacion/images/MET-dynamic-QR-code.png'>".$firstLine."<br />".$secondLine."<br />".$otherInfo."<br />".$pronouns."</p></td></tr>";
}

$htmlHead = "<html>
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title>METM Badges</title>
        
        <style type='text/css'>
        
        body {      
            padding:11mm 0 0 0;
            background: white;
            width: 210mm;
            font-size:16px;
            line-height: 18px;      
            white-space:nowrap;
            overflow: hidden; 
        }

        table { 
            page-break-after:always;
            width: 100%;
            height: auto;
            margin:0 auto;
            page-break-inside:avoid;                 
            border-collapse: collapse;
        }

        td img {
            height:20mm;
        }

        td {
            width: 94mm;
            height:60mm;
            padding: 0 1mm 0 1mm;
            font-style: bold; 
            text-align: center;
            vertical-align: middle; 
            border: 1px solid silver;	
        }


        tr    { 
            page-break-after:auto;
            page-break-inside:avoid; 
            min-height:65mm;
			max-height:65mm;
            margin:0;
            padding:0;
            page-break-inside:avoid;   
            border: 1px solid silver;			
        }
		
		h1 {		
<!--        font-family: freesans;-->
        line-height: 50px;
		color: #2A4E6E;
        white-space:nowrap;
        overflow: hidden; 
		}
		
		h2 {		
<!--        font-family: freesans;-->
        line-height: 50px;
		color: #2A4E6E;
        white-space:nowrap;
        overflow: hidden; 
		}
		
		p {
		text-align: left;
		font-size:16px;
		line-height: 18px;
        color: #239991;
        white-space:nowrap;
        overflow: hidden; 
		}

        #hp  {
        float: left;    
        margin: 0 10px 10px 0;
        }
		
		#council {
        text-align: left;
		font-size: 16px;
		line-height: 16px;
		}
        
        #dish {
        text-align: center;
		font-size: 32px;
		line-height: 40px;
        font-weight: bold;
		}

        </style>
    </head>
    <body>
    <table>";

$htmlTable = "";
$htmlFooter = "</table></body></html>";

//Obtenemos la conferencia actual
$numeroConferencia = $_GET["id_conferencia"];
// $numeroAttendee = $_GET["id_attendee"];

//Listamos los miembros del directorio
if ($numeroConferencia > 0) {
  $codeProcedure = "CALL ed_sp_obtener_listado_conferencia_completo('$numeroConferencia',2)";
} else {
  $codeProcedure = "CALL ed_pr_get_extra_badges(1)";
}

$resultMiembros = $db->callProcedure($codeProcedure);

if ($db->getNumberRows($resultMiembros) > 0) {
    while ($dataMiembro = $db->getData($resultMiembros)) {
      
      if (($dataMiembro["id_inscripcion_conferencia"] == $_GET["id_attendee"]) || !isset($_GET["id_attendee"])) {
      
      	// Split badge text into separate lines
        $badgetext = $dataMiembro["conference_badge"];
        $badgelines = explode("<br />", $badgetext);
        $badgelines = str_replace("&comma;", ",", $badgelines);
        if(isset($dataMiembro["first_timer_or_council"])) {
          $councilBod = $dataMiembro["first_timer_or_council"];
        } else {
          $councilBod = 0;
        }
      
      $resultFTCouncil = $db->callProcedure("CALL ed_pr_first_timer_or_council_name(".$councilBod.")");
      $FTCouncil = $resultFTCouncil->fetch_assoc();
      if ($FTCouncil["id_role"]>1) {
      $otherInfo = "<span id='council' style='color:".$FTCouncil['colour'].";'>Council (" . $FTCouncil['name_role'] . ")</span> ";
      } else if ($FTCouncil["id_role"]) {
       $otherInfo = "<span id='council' style='color:".$FTCouncil['colour'].";'>" . $FTCouncil['name_role'] . "</span> ";
      } else { 
        $otherInfo = "";
      }
      
      if ($badgelines[4] > 0) {
       $resultFTCouncil = $db->callProcedure("CALL ed_pr_first_timer_or_council_name(".$badgelines[4].")");
       $FTCouncil = $resultFTCouncil->fetch_assoc();
       $otherInfo2 = "<span id='council' style='color:".$FTCouncil['colour'].";'>Council (" . $FTCouncil['name_role'] . ")</span> ";
      } else {
        $otherInfo2 = "";        
      }
      
      $resultSpecialrate = $db->callProcedure("CALL ed_pr_metm_special_rate_name(".$dataMiembro["speaker"].")");
      $specialRate = $resultSpecialrate->fetch_assoc();
      if ($specialRate["name_type"]) {
        if($otherInfo){
          $otherInfo = $otherInfo."/ ";
        }
        if($otherInfo2){
          if($dataMiembro["speaker"]==2){
          $otherInfo2 = $otherInfo2."/ ";
          }
        }
      $otherInfo = $otherInfo."<span id='council' style='color:".$specialRate['colour'].";'>" . $specialRate["name_type"] . "</span> ";
      if($dataMiembro["speaker"]==2){
      	$otherInfo2 = $otherInfo2."<span id='council' style='color:".$specialRate['colour'].";'>" . $specialRate["name_type"] . "</span> ";
      }
      }    

      $firstName = $badgelines[0];
	  $lastName = $badgelines[1]; 

	  include 'badge_char_size.php';
      
              $firstLine = $badgelines[2];
              $secondLine = $badgelines[3];
              $pronouns = $badgelines[5];
      
      $htmlTable = $htmlTable.badgeBody($charSize, $firstName, $charSize2, $lastName, $firstLine, $secondLine, $otherInfo, $pronouns);
	 
      if (($dataMiembro["first_timer_or_council"]>1) || ($badgelines[4] > 0)) {
        if ($dataMiembro["first_timer_or_council"] !== $badgelines[4]) {          
       $htmlTable = $htmlTable.badgeBody($charSize, $firstName, $charSize2, $lastName, $firstLine, $secondLine, $otherInfo2, $pronouns);         
        }
      }
    }
  }
}

$id_attendee = $_GET["id_attendee"];
if (!isset($_GET["id_attendee"])) {
  $codeProcedure = "CALL ed_pr_get_guest_badges('$numeroConferencia')";
} else {
  $codeProcedure = "CALL ed_pr_get_guest_badges_attendee('$id_attendee')";
}

    	  // NEW CODE START
	  
//	$codeProcedure = "CALL ed_pr_get_guest_badges('$numeroConferencia')";
	$resultGuests = $db->callProcedure($codeProcedure);
	if ($db->getNumberRows($resultGuests) > 0) {
    while ($dataGuest = $db->getData($resultGuests)) {
         $firstName = $dataGuest["guest_name"];
		 $lastName = $dataGuest["guest_surname"];
		 $firstLine = $dataGuest["nombre"] . " " . $dataGuest["apellidos"] . "'s guest";	

		 include 'badge_char_size.php';
	  
		 $htmlTable = $htmlTable.badgeBody($charSize, $firstName, $charSize2, $lastName, $firstLine, "&nbsp;", "&nbsp;", "&nbsp;");
		 
	}
    }
	  
	  // NEW CODE END

if (!isset($_GET["id_attendee"])) {
  if ($numeroConferencia > 0) {
	for ($i = 1; $i <= 10; $i++) {  
         $htmlTable = $htmlTable.badgeBody("font-size: 60px;", "&nbsp;", "font-size: 48px;", "&nbsp;", "&nbsp;", "&nbsp;", "&nbsp;", "&nbsp;"); 
	  }
  }

$content = $htmlHead . $htmlTable . $htmlFooter;

//Convert HTML to PDF, else output error message
     try {
      // P = portrait / L = landscape
         $html2pdf = new Html2Pdf('P', 'A4', 'en');
         $html2pdf->setDefaultFont('freesans');
         $html2pdf->writeHTML($content);
       if (!isset($_GET["id_attendee"])) {       
       	 $fileName = $_GET["id_conferencia"]."-badges.pdf";
       } else {
         $fileName = $lastName."-badge.pdf";
       }

      //Force download of output PDF
       
        $html2pdf->Output($fileName, 'D');
       	$html2pdf->clean();

     } catch (Html2PdfException $e) {
         $html2pdf->clean();

         $formatter = new ExceptionFormatter($e);
         echo $formatter->getHtmlMessage();
         exit;
     }
}
?>