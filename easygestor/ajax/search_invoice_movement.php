<?php
	require "../includes/load_main_components.inc.php";
	$esAjax=true;
	require "../includes/load_validate_user.inc.php";
	require "../config/dictionary/".$_SESSION["user"]["language_dictio"];
	
	//Buscador
	$idTipo=0;
	$idConcepto=0;
	$idSubConcepto=0;
	$idConceptoQuery=0;
	$idTipoPago=0;
	$pagado=-1;
	$fechaDesde="";
	$fechaHasta="";
	$importeDesde="-1";
	$importeHasta="-1";
	$persona="";
	$filtroPaginador="";
	if(isset($_POST["cmbTipo"])){
		$_POST["txtImporteDesde"]=str_replace(",",".",$_POST["txtImporteDesde"]);
		$_POST["txtImporteHasta"]=str_replace(",",".",$_POST["txtImporteHasta"]);
		
		$idTipo=$_POST["cmbTipo"];
		$idTipoPago=$_POST["cmbTipoPago"];
		$pagado=$_POST["cmbPagado"];

		$idConcepto=$_POST["cmbConcepto"];
		$idConceptoQuery=$_POST["cmbConcepto"];

		//Subconcepto
		if(isset($_POST["cmbSubConcepto"]) && $_POST["cmbSubConcepto"]!="0"){
			$idSubConcepto=$_POST["cmbSubConcepto"];
			$idConceptoQuery=$_POST["cmbSubConcepto"];
		}
		

		if($_POST["from"]!=""){
			$fechaDesde=generalUtils::conversionFechaFormato($_POST["from"],"-","-");
		}
		if($_POST["to"]!=""){
			$fechaHasta=generalUtils::conversionFechaFormato($_POST["to"],"-","-");
		}
		if(is_numeric($_POST["txtImporteDesde"])){
			$importeDesde=$_POST["txtImporteDesde"];
		}
		if(is_numeric($_POST["txtImporteHasta"])){
			$importeHasta=$_POST["txtImporteHasta"];
		}
		if($_POST["txtPersona"]!=""){
			$persona=$_POST["txtPersona"];
		}

	}
	
	
	
	$plantilla=new XTemplate("../html/ajax/search_invoice_movement.html");
	
	if($_POST["hdnIdFacturaMovimiento"]==""){
		$idFactura=0;
	}else{
		$idFactura=$_POST["hdnIdFacturaMovimiento"];
	}

	$resultadoMovimiento=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_movimiento_factura_listar(".$idFactura.",".$idTipo.",".$idConceptoQuery.",".$idTipoPago.",".$pagado.",'".$fechaDesde."','".$fechaHasta."','".$importeDesde."','".$importeHasta."','".generalUtils::escaparCadena($persona)."',".$_SESSION["user"]["language_id"].")");
	$totalMovimiento=$db->getNumberRows($resultadoMovimiento);
	$i=1;


	
	while($datoMovimiento=$db->getData($resultadoMovimiento)){

		$vectorMovimiento["ID"]=$datoMovimiento["id_movimiento"];
		$vectorMovimiento["TYPE"]=$datoMovimiento["tipo"];
		$vectorMovimiento["CONCEPT"]=$datoMovimiento["concepto"];
		$vectorMovimiento["TIPO_PAGO"]=$datoMovimiento["tipo_pago"];
		$vectorMovimiento["DATE"]=generalUtils::conversionFechaFormato($datoMovimiento["fecha_movimiento"],"-","-");
		$datoMovimiento["importe"]=str_replace(".",",",$datoMovimiento["importe"]);
		
		if($datoMovimiento["id_tipo_movimiento"]==2){
			$dato["importe"]="-".$dato["importe"];
			$vectorMovimiento["IMPORT_OUT"]=$datoMovimiento["importe"];
			$vectorMovimiento["IMPORT_IN"]=0;
		}else{
			$vectorMovimiento["IMPORT_OUT"]=0;
			$vectorMovimiento["IMPORT_IN"]=$datoMovimiento["importe"];
			
		}
		$vectorMovimiento["IMPORT"]=$datoMovimiento["importe"];
		$vectorMovimiento["PERSON"]=$datoMovimiento["nombre_persona"];
		$vectorMovimiento["PAYED"]=($datoMovimiento["es_pagado"]==1 ? STATIC_VIEW_MOVEMENT_PAYED_YES : STATIC_VIEW_MOVEMENT_PAYED_NO);

		
		$plantilla->assign("MOVEMENT",$vectorMovimiento);
		$plantilla->parse("contenido_principal.item_movimiento");
		
		
		$i++;
	}
	$plantilla->parse("contenido_principal");
	
	$plantilla->out("contenido_principal");
?>