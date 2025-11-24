<?php
	/**
	 * 
	 * Script to generate METM dinner inserts
	 * @Author Mike
	 * 
	 */

    use \Spipu\Html2Pdf\Html2Pdf;
    use \Spipu\Html2Pdf\Exception\Html2PdfException;
    use \Spipu\Html2Pdf\Exception\ExceptionFormatter;
if (!isset($_GET["id_attendee"])) { 
  $htmlHead = "<html>
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title>METM dinner inserts</title>
        <style type='text/css'>

                    body {      
            padding:11mm 0 0 0;
            background: white;
            width: 210mm;
            font-size:16px;
            line-height: 18px;            
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
		
		p {
		text-align: left;
		font-size:16px;
		line-height: 18px;
        color: #239991;
		}

        #hp  {
        float: left;    
        margin: 0 10px 10px 0;
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
}
//Obtenemos la conferencia actual
$idConferencia = $_GET["id_conferencia"];

$result = $db->callProcedure("CALL ed_pr_total_number_of_dinner_courses()");
$row = $result->fetch_assoc();
$max_courses = $row["number_of_courses"];

$populate = $db->callProcedure("CALL ed_pr_get_inscripcion_conferencia_for_closing_dinner_populate($idConferencia)");
$hostName = $badgelines[0]."_".$badgelines[1];
$cont = 0;
$htmlTable = $htmlTable .  "<tr><td>";

while($item=$db->getData($populate)){
  
  $idAttendee = $item['id_inscripcion_conferencia'];
  if (($idAttendee == $_GET["id_attendee"]) || !isset($_GET["id_attendee"])) {
  $resultCourse = $db->callProcedure("CALL ed_pr_closing_dinner_choices_per_course($idAttendee,'0','1')");
  $rowCourse = $resultCourse->fetch_assoc();  
  
  $hostName = $item["nombre"] . " " . $item["apellidos"];
    
for ($x = 0; $x <= $item['valor']; $x++) {   
    $cont = $cont+1;

//  if (!$rowCourse["id_attendee"]) {  
//    $GuestNumber = $item['valor'];    
//    for ($y = 1; $y <= $max_courses; $y++) {  
//    	$db->callProcedure("CALL ed_pr_closing_dinner_choices_insert($idAttendee,$GuestNumber,$y,-1)");    
//    }
//  } 

  // badge construction
        $guestName = "";
  
        if($x){            
          $resultName = $db->callProcedure("CALL ed_pr_closing_dinner_guest_names($idAttendee,$x,1)");
      	  $rowName = $resultName->fetch_assoc();
			  
      	  $guestName = $rowName["guest_name"] . " " . $rowName["guest_surname"];
          
          if(!isset($rowName["guest_name"])) {
            $guestName = "Guest n. " . $x;
          }
          
          $htmlName = $guestName ."<br />" . $hostName . "'s guest";
        } else {
          $htmlName = $hostName;
        }
  
      for ($y = 1; $y <= $max_courses; $y++) {  

        // get dishes for badge
        
      	$resultDish = $db->callProcedure("CALL ed_pr_closing_dinner_choices_per_course($idAttendee,$x,$y)");
      	$rowDish = $resultDish ->fetch_assoc();
      	$chosendish = $rowDish["dish"];
        
		if (!isset($chosendish)) {
          $chosendish=-1;
		  $db->callProcedure("CALL ed_pr_closing_dinner_choices_insert($idAttendee,$x,$y,$chosendish)");
		}        
               
      	if ($chosendish==-1) {
      		$result2 = $db->callProcedure("CALL ed_pr_get_closing_dinner_default_dish($y)");
      		} else {
      		$result2 = $db->callProcedure("CALL ed_pr_closing_get_dinner_single_dish($chosendish)");
      		}
            $row2 = $result2->fetch_assoc();
      		$abbr = $row2["badge_abbreviation"];
        if ($max_courses == 1) {
      		$htmlTable = $htmlTable .  "<span id='dish'>" .  $abbr ."</span><br />";
        } else {        
      		$htmlTable = $htmlTable .  "<span id='dish'>" . $y . ". " . $abbr ."</span><br />";
        }
        
    }

//    If they don't want the no gluten badge, put this back
//    $htmlTable = $htmlTable . "<p><img id='hp' src='https://www.metmeetings.org/documentacion/images/gluten_free.png'>".$htmlName."</p>";
// end of the put-this-back
//    If they don't want the no gluten badge, remove this  
$insertLogo = "https://www.metmeetings.org/documentacion/images/MET_logo_for_email_signature2.jpg";  
$resultal = $db->callProcedure("CALL ed_pr_get_attendee_allergies($idAttendee,$x)");
while ($rowal = $db->getData($resultal)) {
if ($rowal["id_allergy"] == 5) {
	$insertLogo = "https://www.metmeetings.org/documentacion/images/gluten_free.png";
	break;
	} 
}
$htmlTable = $htmlTable . "<p><img id='hp' src='" . $insertLogo . "'>".$htmlName."</p>"; 
 // end of the remove-this
  
    if($cont % 2 == 0){
        // echo "Even"; 
      $htmlTable = $htmlTable . "</td></tr><tr><td>";
    }
    else{
            $htmlTable = $htmlTable . "</td><td>";
        // echo "Odd";
    }
 
   }
  }
}

if (!isset($_GET["id_attendee"])) {
for ($i = 1; $i <= 10; $i++) {
    $cont = $cont+1;
  
  for ($x = 1; $x <= $max_courses; $x++) {
    
        if ($max_courses == 1) {
      		    $htmlTable = $htmlTable .  "<span id='dish'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><br />";
        } else {        
      		    $htmlTable = $htmlTable .  "<span id='dish'>" . $x . ".&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><br />";
        }
    
  }
  $htmlTable = $htmlTable . "<p><img id='hp' src='https://www.metmeetings.org/documentacion/images/MET_logo_for_email_signature2.jpg'>&nbsp;</p>";
  
    if($cont % 2 == 0){
        // echo "Even"; 
      $htmlTable = $htmlTable . "</td></tr><tr><td>";
    }
    else{
            $htmlTable = $htmlTable . "</td><td>";
        // echo "Odd";
    }
  }
}


//    if($cont % 2 != 0){
        // echo "Ends odd"; 
      $htmlTable = $htmlTable . "</td></tr>";
//    }

$content = $htmlHead . $htmlTable . $htmlFooter;

// echo $content;

	//Convert HTML to PDF, else output error message
     try {
      // P = portrait / L = landscape
      //   $html2pdf = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8');
         $html2pdf = new Html2Pdf('P', 'A4', 'en');
         $html2pdf->setDefaultFont('freesans');
         $html2pdf->writeHTML($content);
         if (!isset($_GET["id_attendee"])) {       
       	   $fileName = $_GET["id_conferencia"]."-dinner-inserts.pdf";
       	 } else {
           $fileName = $hostName."_badge_and_dinner_inserts.pdf";
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

?>