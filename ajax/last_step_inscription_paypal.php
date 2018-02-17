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
				$idEstadoInscripcion=INSCRIPCION_ESTADO_INSCRIPCION_CONFIRMADA;

				
			
				
				//Datos devueltos de paypal			
				$txnId = $_POST["txn_id"];
				$payerId=$_POST["payer_id"];
				if(isset($_POST["mc_fee"]) && $_POST["mc_fee"]!=""){
					$comision=$_POST["mc_fee"];
				}else{
					$comision=0;
				}
				
				//Obtengo los datos basicos de la inscripcion
				$resultadoInscripcion=$db->callProcedure("CALL ed_sp_web_inscripcion_obtener_concreta(".$idInscripcion.")");
				$datosInscripcion=$db->getData($resultadoInscripcion);
				$importe=$datosInscripcion["importe"];
				$idUsuarioWeb=$datosInscripcion["id_usuario_web"];
				$idUsuarioWebCorreo=$idUsuarioWeb;
				$emailUsuario=$datosInscripcion["correo_electronico"];
				//$esFactura=$datosInscripcion["es_factura"];
				$esFactura=1;
				$fechaInscripcion=$datosInscripcion["fecha_inscripcion"];
				$fechaFinalizacion=$datosInscripcion["fecha_finalizacion"];
				
				//Billing information
				$nifFactura=generalUtils::escaparCadena($datosInscripcion["nif_cliente_factura"]);
				$nombreClienteFactura=generalUtils::escaparCadena($datosInscripcion["nombre_cliente_factura"]);
				$nombreEmpresaFactura=generalUtils::escaparCadena($datosInscripcion["nombre_empresa_factura"]);
				$direccionFactura=generalUtils::escaparCadena($datosInscripcion["direccion_factura"]);
				$codigoPostalFactura=generalUtils::escaparCadena($datosInscripcion["codigo_postal_factura"]);
				$ciudadFactura=generalUtils::escaparCadena($datosInscripcion["ciudad_factura"]);
				$provinciaFactura=generalUtils::escaparCadena($datosInscripcion["provincia_factura"]);
				$paisFactura=generalUtils::escaparCadena($datosInscripcion["pais_factura"]);
				

				if($datosInscripcion["id_modalidad_usuario_web"]==MODALIDAD_USUARIO_INDIVIDUAL){
					$nombreUsuario=$datosInscripcion["nombre"];
					$apellidosUsuario=$datosInscripcion["apellidos"];
					$idConceptoMovimiento=MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP;
					
					//Miramos si somos jubilados o estudiantes para determinar si el concepto movimiento asociado tiene que ser distinto o no
								
					if($datosInscripcion["id_situacion_adicional"]==SITUACION_ADICIONAL_JUBILADO){
						$idConceptoMovimiento=MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP_RETIRED;
					}else if($datosInscripcion["id_situacion_adicional"]==SITUACION_ADICIONAL_ESTUDIANTE){
						$idConceptoMovimiento=MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP_STUDENT;
					}
					
					
				}else if ($datosInscripcion["id_modalidad_usuario_web"]==MODALIDAD_USUARIO_INSTITUTIONAL){
					$nombreUsuario=$datosInscripcion["nombre_representante"];
					$apellidosUsuario=$datosInscripcion["apellidos_representante"];
					$idConceptoMovimiento=MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP_INSTITUTIONAL;
				}

				//Iniciamos transaccion para guardar datos en bases de datos
				$db->startTransaction();
		

				//Guardamos en base de datos el id de transaccion paypal
				$resultado=$db->callProcedure("CALL ed_sp_web_inscripcion_paypal_guardar(".$idInscripcion.",'".$txnId."','".$payerId."')");
				
				
				//Actualizar estado
				$idEstado=INSCRIPCION_ESTADO_INSCRIPCION_CONFIRMADA;
				$resultado=$db->callProcedure("CALL ed_sp_web_inscripcion_estado_actualizar(".$idEstado.",'".$numeroPedidoInscripcion."')");
				
	
				//Actualizar pagado
				$pagado=1;
				$resultado=$db->callProcedure("CALL ed_sp_web_inscripcion_pagado_actualizar(".$pagado.",'".$numeroPedidoInscripcion."')");
				
				require "../includes/load_send_mail_inscription.inc.php";
				
				//Finalizar transaccion
				$db->endTransaction();
			}
		}
		fclose ($fp);
	}
?>