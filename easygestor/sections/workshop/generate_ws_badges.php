<?php
	/**
	 * 
	 * Script to generate Workshop badges
	 * @Author Mike
	 * 
	 */

	require_once('../vendor/autoload.php');

    use \Spipu\Html2Pdf\Html2Pdf;
    use \Spipu\Html2Pdf\Exception\Html2PdfException;
    use \Spipu\Html2Pdf\Exception\ExceptionFormatter;

function badgeBody($charSize, $firstName, $charSize2, $lastName, $firstLine, $secondLine, $otherInfo, $pronouns) {
  return "<tr><td><h1 style='".$charSize."'>".$firstName."</h1><h2 style='".$charSize2."'>".$lastName."</h2><p><img id='hp' src='https://www.metmeetings.org/documentacion/images/MET_logo_for_email_signature2.jpg'>".$firstLine."<br />".$secondLine."<br />".$otherInfo."<br />".$pronouns."</p></td>
	<td><h1 style='".$charSize."'>".$firstName."</h1><h2 style='".$charSize2."'>".$lastName."</h2><p><img id='hp' src='https://www.metmeetings.org/documentacion/images/MET_logo_for_email_signature2.jpg'>".$firstLine."<br />".$secondLine."<br />".$otherInfo."<br />".$pronouns."</p></td></tr>";
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

$inputDate = $_GET["ws-date"]; // Expected format: dd-mm-yyyy

// Convert to yyyy-mm-dd using DateTime
$dateObj = DateTime::createFromFormat('d-m-Y', $inputDate);
$wsdate = $dateObj->format('Y-m-d');

$codeProcedure = "CALL ed_pr_ws_badge('" . $wsdate . "')";

$resultMiembros = $db->callProcedure($codeProcedure);

if ($db->getNumberRows($resultMiembros) > 0) {
    while ($dataMiembro = $db->getData($resultMiembros)) {
      

      
      $firstName = htmlspecialchars($dataMiembro["nombre"]);
	  $lastName = htmlspecialchars($dataMiembro["apellidos"]); 

	  include '../includes/badge_char_size.php';
      
              $firstLine = "&nbsp;";
              $secondLine = "Workshop Attendee";
              $pronouns = "&nbsp;";
			  $otherInfo = "&nbsp;";
      
      $htmlTable = $htmlTable.badgeBody($charSize, $firstName, $charSize2, $lastName, $firstLine, $secondLine, $otherInfo, $pronouns);
	 
    }
} else {
	exit("<h1>There are no badges to print</h1><p>If this workshop is part of a METM, print out the conference badges. <br />Conference workshop badges cannot be printed separately.</p>");
}


	for ($i = 1; $i <= 10; $i++) {  
         $htmlTable = $htmlTable.badgeBody("font-size: 60px;", "&nbsp;", "font-size: 48px;", "&nbsp;", "&nbsp;", "&nbsp;", "&nbsp;", "&nbsp;"); 
	  }


$content = $htmlHead . $htmlTable . $htmlFooter;

//Convert HTML to PDF, else output error message
     try {
      // P = portrait / L = landscape
         $html2pdf = new Html2Pdf('P', 'A4', 'en');
         $html2pdf->setDefaultFont('freesans');
         $html2pdf->writeHTML($content);
  
       	 $fileName = $wsdate . "-badges.pdf";


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