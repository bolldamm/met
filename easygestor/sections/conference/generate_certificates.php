<?php
	/**
	 * 
	 * Script to generate METM attendance certificates
	 * @Author Mike
	 * 
	 */

	require_once('../vendor/autoload.php');

    use \Spipu\Html2Pdf\Html2Pdf;
    use \Spipu\Html2Pdf\Exception\Html2PdfException;
    use \Spipu\Html2Pdf\Exception\ExceptionFormatter;

	//Get certificate details
$idEstadoInscripcion = 2;
$nombre = "";
$idTipoUsuario = 0;
$campoOrden = "ic.id_inscripcion_conferencia";
$direccionOrden = "ASC";

	$codeProcedure = "CALL " . OBJECT_DB_ACRONYM . "_sp_inscripcion_conferencia_listar(" . $_GET["id_conferencia"] . "," . $idEstadoInscripcion . ",'" . $nombre . "','" . $idTipoUsuario . "'," . $_SESSION["user"]["language_id"] . ",'" . $campoOrden . "','" . $direccionOrden . "','')";

	$resultado=$db->callProcedure($codeProcedure);

    //Get details of specified conference
    $resultadoConferencia = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_conferencia_obtener_concreta(" . $_GET["id_conferencia"] . "," . $_SESSION["user"]["language_id"] . ")");
    $datoConferencia = $db->getData($resultadoConferencia);

	 while($dato=$db->getData($resultado)){
       
       if($dato["asistido"]) {
         
         $workshopString ="";

         // Get workshops attended
       $resultWorkshops = $db->callProcedure("CALL ed_sp_inscripcion_conferencia_linea_taller_attended_obtener(" . $dato["id_inscripcion_conferencia"] . "," . $_SESSION["user"]["language_id"] . ")");

         while ($oneWorkshop = $db->getData($resultWorkshops)) {
//              if ($oneWorkshop["es_mini"]) {
//                $workOrmini = "minisession";
//              } else {
//                $workOrmini = "workshop";
//              }
              $workshopString = $workshopString."<p style='font-weight:bold;font-size:12px;'>".$oneWorkshop["nombre_largo"]."<br>(".$oneWorkshop["duration"]." presented by ".$oneWorkshop["facilitator"].")</p>";
         }
         
         if($workshopString) {
         $workshopString = ",<br>and attended the following pre-conference workshop(s):<br>".$workshopString; 
         } else {
           $workshopString = "<br>";
         }
          
    //Get HTML certificate template
	 $plantillaPdf=new XTemplate("html/sections/conference/generate_certificate.html");
      
    //Assign certificate details to placeholders in template
         
     $plantillaPdf->assign("FULL_NAME", $dato['nombre_inscrito']." ".$dato['apellidos_inscrito']);
     $plantillaPdf->assign("CERTIFICATE_HEAD", $datoConferencia['certificate_text']);
     $plantillaPdf->assign("CERTIFICATE_DURATION", $datoConferencia['certificate_duration']);
     $plantillaPdf->assign("CERTIFICATE_VENUE", $datoConferencia['certificate_venue']);
     $plantillaPdf->assign("CERTIFICATE_WORKSHOPS", $workshopString);
      
      //  $dato['hash_generado'];
      
      //The following line is essential: don't remove it
      $plantillaPdf->parse("contenido_principal");
      
	//Convert HTML to PDF, else output error message
     try {
      // P = portrait / L = landscape
         $html2pdf = new Html2Pdf('L', 'A4', 'en');
         $html2pdf->setDefaultFont('freesans');
         $html2pdf->writeHTML($plantillaPdf->text("contenido_principal"));
       	 $fileName = $_GET["id_conferencia"]."-".$dato['numero_inscripcion'];

        //Update attendee
       if (!isset($dato['hash_generado'])) {
         $hashGenerado=md5(uniqid(time()) . $fileName);
	 	$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_pr_update_conference_certificates('".$dato['numero_inscripcion']."','".$hashGenerado."','".$fileName."')";
        // echo $codeProcedure."<br>";
       	$db->callProcedure($codeProcedure);  
       }
      
      //Output PDF to file (F), then move to /files/customers/conferences folder
      
         $html2pdf->Output(__DIR__.$fileName.".pdf","F");
         rename(__DIR__.$fileName.".pdf","../files/customers/conferences/".$fileName.".pdf");
       	$html2pdf->clean();
       	unset($plantillaPdf);

     } catch (Html2PdfException $e) {
         $html2pdf->clean();

         $formatter = new ExceptionFormatter($e);
         echo $formatter->getHtmlMessage();
         exit;
     }

 		}
     }
	generalUtils::redirigir($_SERVER['HTTP_REFERER']);
?>