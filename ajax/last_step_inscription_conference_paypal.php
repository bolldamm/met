<?php
	//This doesn't seem to do anything or to be needed
    $esRemoto=true;

	require "../includes/load_main_components.inc.php";
	
	// Read the post from PayPal system and add 'cmd'
	$req = 'cmd=_notify-validate';
	foreach ($_POST as $key => $value) {
		$value = urlencode(stripslashes($value));
		$req .= "&$key=$value";
	}
	
	$paypalAddress="www.paypal.com";
	
	// Post back to PayPal system to validate
	$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$header .= "Host: " . $paypalAddress . "\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
	$fp = fsockopen ("ssl://".$paypalAddress, 443, $errno, $errstr, 30);
	
	
	// Assign posted variables to local variables
	$payer_email = $_POST['payer_email'];
	$date=date("Y-m-d")." ".date("G:i:s");
			
	
	if (!$fp) {
	// HTTP ERROR
	} else {
	/*	fputs ($fp, $header . $req);
		while (!feof($fp)) {
			$res = fgets ($fp, 1024);*/
			if (($_POST["payment_status"]=="Completed")or($_POST["payment_status"]=="Processed")){	
				
				//Store registration ID, number and type and payment method in variables
				$datosInscripcion=explode("-",$_POST["custom"]);
				$numeroPedidoInscripcion=$datosInscripcion[0];
				$idInscripcion=$datosInscripcion[1];
				$tipoInscripcion=$datosInscripcion[2];
				$metodoPago=INSCRIPCION_TIPO_PAGO_PAYPAL;
				
				//Collect data returned by Paypal (transaction ID, payer ID and fee)
				$txnId = $_POST["txn_id"];
				$payerId=$_POST["payer_id"];
				if(isset($_POST["mc_fee"]) && $_POST["mc_fee"]!=""){
					$comision=$_POST["mc_fee"];
				}else{
					$comision=0;
				}

				//Store account name ("METM registration") in variable
				$idConceptoMovimiento=MOVIMIENTO_CONCEPTO_NEW_CONFERENCE;
					

				//Connect to database to update records
				$db->startTransaction();
		

				//Store registration ID, Paypal transaction ID and payer ID in the database
				$resultado=$db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_paypal_guardar(".$idInscripcion.",'".$txnId."','".$payerId."')");
				
				
				//Update payment status from "Pending" to "Confirmed"
				$idEstado=INSCRIPCION_ESTADO_INSCRIPCION_CONFIRMADA;
				$resultado=$db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_estado_actualizar(".$idEstado.",'".$numeroPedidoInscripcion."')");
				
	
				//Update conference registration status from "Unpaid" to "Paid"
				$pagado=1;
				$resultado=$db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_pagado_actualizar(".$pagado.",'".$numeroPedidoInscripcion."')");
				

				//Update workshop status from "Unpaid" to "Paid"
				$pagado=1;
				$resultado=$db->callProcedure("CALL ed_sp_inscripcion_conferencia_linea_pagado_actualizar(".$numeroPedidoInscripcion.",'".$pagado."')");

				
				//End database transaction and call script to send email
				$db->endTransaction();
				require "../includes/load_send_mail_inscription_conference.inc.php";
				
			}
//		}
		fclose ($fp);
	}
?>