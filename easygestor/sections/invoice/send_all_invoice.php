<?php
	/**
	 * 
	 * Script to send all unsent PDF invoices
	 * @Author Mike
	 * 
	 */

echo "<!DOCTYPE html><html><style>#myProgress {  width: 100%;  background-color: #ddd; } #myBar {  width: 1%;  height: 30px;  background-color: #4CAF50; } </style><body>";
echo "<h1>Processing - please wait...</h1>";
echo "<div id='myProgress'><div id='myBar'></div></div>";
echo "<script type='text/javascript'>var i = 0; function move() {  if (i == 0) { i = 1; var elem = document.getElementById('myBar'); var width = 1; var id = setInterval(frame, 10); function frame() { if (width >= 100) { width = 1; i = 0; } else { width++; elem.style.width = width + '%'; } } } } move(); </script>";

$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_sp_factura_no_enviado()";
$resultadox=$db->callProcedure($codeProcedure);

if (mysqli_num_rows($resultadox)==0) {
  	$i = 0;
} else {
	$i = 2;
}

//Output names
while($datox=$db->getData($resultadox)){
  
  // Invoice sending routine

		$i++;
		$curlopturl = CURRENT_DOMAIN_EASYGESTOR . "main_app.php?section=invoice&action=send&send=1&id_factura=" . $datox['id_factura'];
   
            echo "<script type='text/javascript'>var element = document.createElement('iframe'); element.setAttribute('id', 'invoice_" . $datox['id_factura']  . "'); element.setAttribute('src', '" . $curlopturl . "'); element.style.display = 'none'; document.body.appendChild(element);</script>";

  // end of invoice sending routine
  
}

$i = $i * 1000;

echo "<script type='text/javascript'>setTimeout(function(){window.location = '" . CURRENT_DOMAIN_EASYGESTOR . "main_app.php?section=invoice&action=view&reload=" . rand() . "';}, " . $i . ");</script>"; 
echo "</body></html>";
?>