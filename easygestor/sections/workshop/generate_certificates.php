<?php
	/**
	 * 
	 * Script to generate PDF workshop attendance certificate
	 * @Author Mike
	 * 
	 */

	require_once('../vendor/autoload.php');

    use \Spipu\Html2Pdf\Html2Pdf;
    use \Spipu\Html2Pdf\Exception\Html2PdfException;
    use \Spipu\Html2Pdf\Exception\ExceptionFormatter;

	//Get certificate details
	$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_pr_get_workshop_certificates('".$_GET['id_taller']."','".$_GET['fecha_taller']."')";

	$resultado=$db->callProcedure($codeProcedure);
	$i = 0;

	while($dato=$db->getData($resultado)){
      
    //Get HTML certificate template
	$plantillaPdf=new XTemplate("html/sections/workshop/generate_certificate.html");
      
    //Assign certificate details to placeholders in template

    $plantillaPdf->assign("FULL_NAME", $dato['nombre']." ".$dato['apellidos']);
    $plantillaPdf->assign("WORKSHOP_NAME", $dato['nombre_largo']);
    $plantillaPdf->assign("WORKSHOP_SEASON", $dato['season']);
    $plantillaPdf->assign("WORKSHOP_DURATION", $dato['duration']);
    $plantillaPdf->assign("WORKSHOP_FACILITATOR", $dato['facilitator']);
    $plantillaPdf->assign("WORKSHOP_DATE", generalUtils::conversionFechaFormato($dato['fecha']));
      
	// $email = $dato['correo_electronico'];
    //  $dato['numero_inscripcion'];
      //  $dato['hash_generado'];
      
      //The following line is essential: don't remove it
      $plantillaPdf->parse("contenido_principal");
      
	//Convert HTML to PDF, else output error message
    try {
      // P = portrait / L = landscape
        $html2pdf = new Html2Pdf('L', 'A4', 'en');
        $html2pdf->setDefaultFont('freesans');
        $html2pdf->writeHTML($plantillaPdf->text("contenido_principal"));
      	$fileName = $_GET['id_taller']."-".$_GET['fecha_taller']."-".$dato['numero_inscripcion'];
        // $hashGenerado=md5(uniqid(time()) . $fileName);

        //Update attendee
      if (!isset($dato['hash_generado'])) {
        $hashGenerado=md5(uniqid(time()) . $fileName);
		$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_pr_update_workshop_certificates(".$dato['id_inscripcion_taller'].",'".$hashGenerado."','".$fileName."',".$_GET['fecha_taller'].")";
      	$db->callProcedure($codeProcedure);  
      }
      
      //Output PDF to file (F), then move to /files/customers/workshops folder
      
        $html2pdf->Output(__DIR__.$fileName.".pdf","F");
        rename(__DIR__.$fileName.".pdf","../files/customers/workshops/".$fileName.".pdf");
      	$html2pdf->clean();
      	unset($plantillaPdf);

    } catch (Html2PdfException $e) {
        $html2pdf->clean();

        $formatter = new ExceptionFormatter($e);
        echo $formatter->getHtmlMessage();
        exit;
    }
	$i++;
}
	// echo "Generate certificates does not work yet";
	// echo "<br>Workshop number: ".$_GET["id_taller"];
	// echo "<br>Workshop date: ".$_GET["fecha_taller"];
	generalUtils::redirigir($_SERVER['HTTP_REFERER']);
?>