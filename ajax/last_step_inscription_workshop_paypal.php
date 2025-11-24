<?php
	$esRemoto=true;
	require "../includes/load_main_components.inc.php";
	
	
	// read the post from PayPal system and add 'cmd'
	$req = 'cmd=_notify-validate';
	foreach ($_POST as $key => $value) {
		$value = urlencode(stripslashes($value));
		$req .= "&$key=$value";
	}
	
	$paypalAddress="www.paypal.com";
	
	// post back to PayPal system to validate
	$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$header .= "Host: " . $paypalAddress . "\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
	$fp = fsockopen ("ssl://".$paypalAddress, 443, $errno, $errstr, 30);
	//$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
	
	
	// assign posted variables to local variables
	$payer_email = $_POST['payer_email'];
	$date=date("Y-m-d")." ".date("G:i:s");
			
	
	if (!$fp) {
	// HTTP ERROR
	} else {
		fputs ($fp, $header . $req);
		while (!feof($fp)) {
			$res = fgets ($fp, 1024);
			if (strpos($res, "VERIFIED")!==false){  	
				
				//Numero pedido
				$datosInscripcion=explode("-",$_POST["custom"]);
				$numeroPedidoInscripcion=$datosInscripcion[0];
				$idInscripcion=$datosInscripcion[1];
				$tipoInscripcion=$datosInscripcion[2];
				$metodoPago=INSCRIPCION_TIPO_PAGO_PAYPAL;
				
				//Datos devueltos de paypal			
				$txnId = $_POST["txn_id"];
				$payerId=$_POST["payer_id"];
				if(isset($_POST["mc_fee"]) && $_POST["mc_fee"]!=""){
					$comision=$_POST["mc_fee"];
				}else{
					$comision=0;
				}
				
				$idConceptoMovimiento=MOVIMIENTO_CONCEPTO_NEW_WORKSHOP;
					

				//Iniciamos transaccion para guardar datos en bases de datos
				$db->startTransaction();
		

				//Guardamos en base de datos el id de transaccion paypal
				$resultado=$db->callProcedure("CALL ed_sp_web_inscripcion_taller_paypal_guardar(".$idInscripcion.",'".$txnId."','".$payerId."')");
				
				
				//Actualizar estado
				$idEstado=INSCRIPCION_ESTADO_INSCRIPCION_CONFIRMADA;
				$resultado=$db->callProcedure("CALL ed_sp_web_inscripcion_taller_estado_actualizar(".$idEstado.",'".$numeroPedidoInscripcion."')");
				
	
				//Actualizar pagado
				$pagado=1;
				$resultado=$db->callProcedure("CALL ed_sp_web_inscripcion_taller_pagado_actualizar(".$pagado.",'".$numeroPedidoInscripcion."')");
				
				require "../includes/load_send_mail_inscription_workshop.inc.php";
				
				//Finalizar transaccion
				$db->endTransaction();
			}
		}
		fclose ($fp);
	}
?>