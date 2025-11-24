<?php
	/**
	 * 
	 * Listamos todos los menus existentes en el sistema
	 * @Author eData
	 * 
	 */
	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/movement/view_movement.html");
	
	$mostrarPaginador=true;
	$valorDefecto=6;
	$campoOrden="id_movimiento";
	$direccionOrden="DESC";
	$numeroRegistrosPagina=DEFAULT_FILTER_NUMBER;

	/**
	 * 
	 * $matrizOrden, la primera dimension de la matriz nos es el indice numerico, que es el que se usara como value del combobox.
	 * la parte asociativa "descripcion" nos indica la palabra que vera el usuario del gestor y la parte valor es el campo de la base de datos
	 * 
	 */
	$matrizOrden[0]["descripcion"]=STATIC_ORDER_MOVEMENT_DATE_FIELD;
	$matrizOrden[0]["valor"]="fecha_movimiento";
	$matrizOrden[1]["descripcion"]=STATIC_ORDER_MOVEMENT_TYPE_FIELD;
	$matrizOrden[1]["valor"]="tipo";
	$matrizOrden[2]["descripcion"]=STATIC_ORDER_MOVEMENT_PAYMENT_TYPE_FIELD;
	$matrizOrden[2]["valor"]="tipo_pago";
	$matrizOrden[3]["descripcion"]=STATIC_ORDER_MOVEMENT_PERSON_FIELD;
	$matrizOrden[3]["valor"]="nombre_persona";
	$matrizOrden[4]["descripcion"]=STATIC_ORDER_MOVEMENT_IMPORT_FIELD;
	$matrizOrden[4]["valor"]="importe";
	$matrizOrden[5]["descripcion"]=STATIC_ORDER_MOVEMENT_PAYED_FIELD;
	$matrizOrden[5]["valor"]="es_pagado";
	$matrizOrden[6]["descripcion"]=STATIC_ORDER_MOVEMENT_LAST_INSERTED_FIELD;
	$matrizOrden[6]["valor"]="id_movimiento";		
	
		
	
	//Gestion del campo de orden y filtro de numero de registros
	$campoOrdenDefecto="";
	require "includes/load_filter_list.inc.php";
	if($campoOrden!="fecha_movimiento" && $campoOrden!="id_movimiento" ){
		$direccionOrden="ASC";
	}
	
	
	//Buscador remote
	$idTipo=0;
	$idConcepto=0;
	$idSubConcepto=0;
	$idConceptoQuery=0;
	$idTipoPago=0;
	$pagado=-1;
	$non_taxable=-1;
	$fechaDesde=date("Y")."-01-01";
	$fechaHasta=date("Y")."-12-31";
	$importeDesde="-1";
	$importeHasta="-1";
	$persona="";
        $description="";
	$filtroPaginador="";
	if(isset($_GET["cmbTipo"])){
		$_GET["txtImporteDesde"]=str_replace(",",".",$_GET["txtImporteDesde"]);
		$_GET["txtImporteHasta"]=str_replace(",",".",$_GET["txtImporteHasta"]);
		
		$idTipo=$_GET["cmbTipo"];
		$idConcepto=$_GET["cmbConcepto"];
		$idConceptoQuery=$_GET["cmbConcepto"];
		

		
		//Subconcepto
		if(isset($_GET["cmbSubConcepto"]) && $_GET["cmbSubConcepto"]!="0"){
			$idSubConcepto=$_GET["cmbSubConcepto"];
			$idConceptoQuery=$_GET["cmbSubConcepto"];
		}

		$idTipoPago=$_GET["cmbTipoPago"];
		$pagado=$_GET["cmbPagado"];
		$non_taxable=$_GET["cmbTaxable"];
		$filtroPaginador="cmbTipo=".$idTipo;
		$filtroPaginador.="&cmbConcepto=".$idConcepto;
		$filtroPaginador.="&cmbSubConcepto=".$idSubConcepto;
		$filtroPaginador.="&cmbPagado=".$pagado;
		$filtroPaginador.="&cmbTipoPago=".$idTipoPago;
        $filtroPaginador.="&cmbTaxable=".$non_taxable;
		
		if($_GET["from"]!=""){
			$fechaDesde=generalUtils::conversionFechaFormato($_GET["from"],"-","-");
			$subPlantilla->assign("VIEW_MOVEMENT_DATE_FROM_SEARCH_VALUE",$_GET["from"]);
			$filtroPaginador.="&from=".$_GET["from"];
		}
		if($_GET["to"]!=""){
			$fechaHasta=generalUtils::conversionFechaFormato($_GET["to"],"-","-");
			$subPlantilla->assign("VIEW_MOVEMENT_DATE_TO_SEARCH_VALUE",$_GET["to"]);
			$filtroPaginador.="&to=".$_GET["to"];
		}
		if(is_numeric($_GET["txtImporteDesde"])){
			$importeDesde=$_GET["txtImporteDesde"];
			$subPlantilla->assign("VIEW_MOVEMENT_IMPORT_FROM_SEARCH_VALUE",$importeDesde);
			$filtroPaginador.="&txtImporteDesde=".$_GET["txtImporteDesde"];
		}
		if(is_numeric($_GET["txtImporteHasta"])){
			$importeHasta=$_GET["txtImporteHasta"];
			$subPlantilla->assign("VIEW_MOVEMENT_IMPORT_TO_SEARCH_VALUE",$importeHasta);
			$filtroPaginador.="&txtImporteHasta=".$_GET["txtImporteHasta"];
		}
		if($_GET["txtPersona"]!=""){
			$persona=$_GET["txtPersona"];
			$subPlantilla->assign("VIEW_MOVEMENT_PERSON_SEARCH_VALUE",$persona);
			$filtroPaginador.="&txtPersona=".$_GET["txtPersona"];
		}
		
		if($_GET["txtDescription"]!=""){
			$description=$_GET["txtDescription"];
			$subPlantilla->assign("VIEW_MOVEMENT_DESCRIPTION_SEARCH_VALUE",$description);
			$filtroPaginador.="&txtDescription=".$_GET["txtDescription"];
		}

		$filtroPaginador.="&";
	}else{
		$subPlantilla->assign("VIEW_MOVEMENT_DATE_FROM_SEARCH_VALUE","01-01-".date("Y"));
		$subPlantilla->assign("VIEW_MOVEMENT_DATE_TO_SEARCH_VALUE","31-12-".date("Y"));
	}
	
	/**
	 * 
	 * El total de paginas que mostraremos por pantalla
	 * @var int
	 * 
	 */
	$totalPaginasMostrar=4;
	
	/*
     *
	 * 
	 * Almacenamos la cadena que representa la llamada al store procedure
	 * @var string
	 * Added generalUtils::escaparCadena($description)
	 */
	$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_sp_movimiento_listar(".$idTipo.",".$idConceptoQuery.",".$idTipoPago.",".$pagado.",'".$fechaDesde."','".$fechaHasta."','".$importeDesde."','".$importeHasta."','".generalUtils::escaparCadena($persona)."','".generalUtils::escaparCadena($description)."',".$_SESSION["user"]["language_id"].",'".$campoOrden."','".$direccionOrden."','".$non_taxable."',";

	$urlActual="main_app.php?section=movement&action=view&hdnOrden=".$valorDefecto."&hdnRegistros=".$numeroRegistrosPagina."&".$filtroPaginador;
	
	//Paginador
	require "includes/load_paginator.inc.php";
	
	//Pagina actual
	$subPlantilla->assign("PAGINA_ACTUAL",$paginaActual);
	
	$esExcel=false;
	if(isset($_GET["excel"]) && $_GET["excel"]==1){
		$esExcel=true;
		$plantillaExcel=new XTemplate("html/excel/index.html");
		$subPlantillaExcel=new XTemplate("html/excel/view_movement.html");
	}
	
	$resultado=$db->callProcedure($codeProcedure);

	$i=0;
	$totalIncoming=0;
	$totalOutgoing=0;
	
	//Conceptos con detalle
	$vectorConceptosDetalle=Array(2,46,55,70,71,72,77);
	$aMovimientosRepetidos = array(); 
	while($dato=$db->getData($resultado)){
		if (!in_array($dato['id_movimiento'], $aMovimientosRepetidos)) {
		    /*  Not used
			if($dato["activo"]==0){
				$subPlantilla->assign("STATE_STYLE","class='disabled' title='".STATIC_VIEW_MOVEMENT_ITEM_DISABLED."'");
			}else{
				$subPlantilla->assign("STATE_STYLE","");
			}
			*/

			//Miremos el id concreto
			$idInscripcion="-";
			if(in_array($dato["id_concepto_movimiento"],$vectorConceptosDetalle)){
				//$nombreClase="detailMovement"; //Doesn't seem to be used
				
				switch($dato["id_concepto_movimiento"]){
					/* MEMBERSHIP */
					case 2:
					case 70:
					case 71:
					case 72:
					case 77:
						$resultadoInscripcion=$db->callProcedure("CALL ed_sp_movimiento_inscripcion_obtener_concreta(".$dato["id_movimiento"].")");
						$datoInscripcion = $db->getData($resultadoInscripcion);
						$idInscripcion=$datoInscripcion["id_inscripcion"];
						break;
					/* METM REGISTRATION*/
					case 46:
						$resultadoInscripcion=$db->callProcedure("CALL ed_sp_movimiento_inscripcion_conferencia_obtener_concreta(".$dato["id_movimiento"].",".$_SESSION["user"]["language_id"].")");
						$datoInscripcion=$db->getData($resultadoInscripcion);
						$idInscripcion=$datoInscripcion["id_inscripcion_conferencia"];
						break;
					/* WORKSHOP REGISTRATION*/
					case 55:
						$resultadoInscripcion=$db->callProcedure("CALL ed_sp_movimiento_inscripcion_taller_obtener_concreta(".$dato["id_movimiento"].",".$_SESSION["user"]["language_id"].")");
						$datoInscripcion=$db->getData($resultadoInscripcion);
						$idInscripcion=$datoInscripcion["id_inscripcion_taller"];
						break;
				}
			}



            if($i%2==0){
                $subPlantilla->assign("TR_STYLE","class='dark'");
            }else{
                $subPlantilla->assign("TR_STYLE","class='light'");
            }
			/*if($i%2==0){
				$subPlantilla->assign("TR_STYLE","class='dark ".$nombreClase."'");
			}else{
				$subPlantilla->assign("TR_STYLE","class='light ".$nombreClase."'");
			}*/
			
			$vectorMovimiento["ID"]=$dato["id_movimiento"];
			$vectorMovimiento["REG_ID"]=$idInscripcion;
			$vectorMovimiento["TYPE"]=$dato["tipo"];
			$vectorMovimiento["CONCEPT"]=$dato["concepto"];
			$vectorMovimiento["TIPO_PAGO"]=$dato["tipo_pago"];
			$vectorMovimiento["TAXABLE"]=($dato["non_taxable"]==1 ? STATIC_VIEW_MOVEMENT_PAYED_NO : STATIC_VIEW_MOVEMENT_PAYED_YES);
			$vectorMovimiento["DATE"]=generalUtils::conversionFechaFormato($dato["fecha_movimiento"],"-","-");
			$importeAux=$dato["importe"];
			$dato["importe"]=str_replace(".",",",$dato["importe"]);
			
			if($dato["id_tipo_movimiento"]==2){
				//$dato["importe"]=$dato["importe"]*(-1);
				$vectorMovimiento["IMPORT_OUT"]=$dato["importe"];
				$vectorMovimiento["IMPORT_IN"]=0;
				
				$totalIncoming+=0;
				$totalOutgoing+=$importeAux;
				
			}else{
				$vectorMovimiento["IMPORT_OUT"]=0;
				$vectorMovimiento["IMPORT_IN"]=$dato["importe"];
				
				$totalIncoming+=$importeAux;
				$totalOutgoing+=0;
				
			}
			if($dato["comments"]!=""){
				$vectorMovimiento["COMMENT_EXCEL"]=$dato["comments"];
				$vectorMovimiento["COMMENT"]="<a href='#' title='".htmlspecialchars($dato["comments"])."'><img src='images/comment.png' /></a>";
			}else{
				$vectorMovimiento["COMMENT"]="";
				$vectorMovimiento["COMMENT_EXCEL"]="";
			}//end else

			$vectorMovimiento["IMPORT"]=$dato["importe"];
			$vectorMovimiento["PERSON"]=$dato["nombre_persona"];
			$vectorMovimiento["DESCRIPTION"]=$dato["concepto_personalizado"];
			$vectorMovimiento["PAYED"]=($dato["es_pagado"]==1 ? STATIC_VIEW_MOVEMENT_PAYED_YES : STATIC_VIEW_MOVEMENT_PAYED_NO);
			$vectorMovimiento["HASH"]=$dato["hash_generado"];
			$vectorMovimiento["EMAIL"]=$dato["correo_electronico"];

			if($esExcel){
				$subPlantillaExcel->assign("MOVEMENT",$vectorMovimiento);
				$subPlantillaExcel->parse("contenido_principal.item_movimiento");
			}else{
				$subPlantilla->assign("MOVEMENT",$vectorMovimiento);
				if($dato["hash_generado"]!=""){
					$subPlantilla->parse("contenido_principal.item_movimiento.item_pdf");
				}
				
				$subPlantilla->assign("MOVEMENT_UNCHECKED","class='unChecked'");
				$subPlantilla->parse("contenido_principal.item_movimiento");
			}
			$i++;
		}
		$aMovimientosRepetidos[] = $dato['id_movimiento'];
	}
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_MOVEMENT_VIEW_MOVEMENT_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_MOVEMENT_VIEW_MOVEMENT_TEXT;	
	
	
	//Date picker FROM
	$plantilla->assign("INPUT_ID","txtFechaDesde");
	$plantilla->parse("contenido_principal.carga_inicial.autoload_datepicker");
	
	//Date picker TO
	$plantilla->assign("INPUT_ID","txtFechaHasta");
	$plantilla->parse("contenido_principal.carga_inicial.autoload_datepicker");
	
	//Combo tipo
	$subPlantilla->assign("COMBO_TIPO",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_tipo_movimiento_buscador_obtener_combo(".$_SESSION["user"]["language_id"].")","cmbTipo","cmbTipo",$idTipo,"nombre","id_tipo_movimiento",STATIC_GLOBAL_COMBO_DEFAULT,0,""));
	
	//Combo concepto
	$subPlantilla->assign("COMBO_CONCEPTO",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_concepto_movimiento_obtener_combo(null,".$_SESSION["user"]["language_id"].")","cmbConcepto","cmbConcepto",$idConcepto,"nombre","id_concepto_movimiento",STATIC_GLOBAL_COMBO_DEFAULT,0,"onchange='obtenerComboSubConcepto(this)'","style='width:100px;'"));
	
	//Combo subconcepto
	if($idConcepto!=0){
		$subPlantilla->assign("COMBO_SUBCONCEPTO",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_concepto_movimiento_obtener_combo(".$idConcepto.",".$_SESSION["user"]["language_id"].")","cmbSubConcepto","cmbSubConcepto",$idSubConcepto,"nombre","id_concepto_movimiento",STATIC_GLOBAL_COMBO_DEFAULT,0,"","style='width:100px;'"));
	}else{
		$subPlantilla->assign("DISPLAY_SUBCONCEPTO","style='display:none;'");
	}
	
	//Combo tipo pago
	$subPlantilla->assign("COMBO_TIPO_PAGO",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_tipo_pago_movimiento_buscador_obtener_combo(".$_SESSION["user"]["language_id"].")","cmbTipoPago","cmbTipoPago",$idTipoPago,"nombre","id_tipo_pago_movimiento",STATIC_GLOBAL_COMBO_DEFAULT,0,"style='width:100px;'"));
	
	//Combo pagado
	$matriz[1]["descripcion"]=STATIC_GLOBAL_BUTTON_YES;
	$matriz[0]["descripcion"]=STATIC_GLOBAL_BUTTON_NO;
	
	$subPlantilla->assign("COMBO_PAGADO",generalUtils::construirComboMatriz($matriz,"cmbPagado","cmbPagado",$pagado,STATIC_GLOBAL_COMBO_DEFAULT,-1,""));
	
	$matriztax[0]["descripcion"]=STATIC_GLOBAL_BUTTON_YES;
	$matriztax[1]["descripcion"]=STATIC_GLOBAL_BUTTON_NO;
	
	$subPlantilla->assign("COMBO_TAXABLE",generalUtils::construirComboMatriz($matriztax,"cmbTaxable","cmbTaxable",$non_taxable,STATIC_GLOBAL_COMBO_DEFAULT,-1,""));
	
	
	
	require "includes/load_breadcumb.inc.php";
	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
	
	if(!$esExcel){
		if(($totalIncoming+$totalOutgoing)>0){
			//Total pagina actual
			$subPlantilla->assign("MOVEMENT_UNCHECKED","");
			$subPlantilla->assign("ALINEAR_PERSON_COLUMN","text-align:right;");
			$vectorMovimiento["TYPE"]="";
			$vectorMovimiento["CONCEPT"]="";
			$vectorMovimiento["REG_ID"]="";
			$vectorMovimiento["TIPO_PAGO"]="";
			$vectorMovimiento["DATE"]="";
			$vectorMovimiento["DESCRIPTION"]="";
			$vectorMovimiento["PERSON"]="<strong>".STATIC_VIEW_MOVEMENT_TOTAL_CURRENT_PAGE."</strong>";
			$vectorMovimiento["PAYED"]="";
			$vectorMovimiento["HASH"]="";
			$vectorMovimiento["IMPORT_OUT"]="<strong>".number_format($totalOutgoing,2,",",".")."</strong>";
			$vectorMovimiento["IMPORT_IN"]="<strong>".number_format($totalIncoming,2,",",".")."</strong>";
			$vectorMovimiento["EMAIL"]="";
			$difTotal=number_format(($totalIncoming-$totalOutgoing),2,",",".");
			if($difTotal<0){
				$vectorMovimiento["PAYED"]="<span style='color:#f10000;font-weight:bold;'>".$difTotal."</span>";
			}else{
				$vectorMovimiento["PAYED"]="<span style='color:#2551d1;font-weight:bold;'>".$difTotal."</span>";
			}
			
			$subPlantilla->assign("MOVEMENT",$vectorMovimiento);
			
			$subPlantilla->parse("contenido_principal.item_movimiento");
		}
		
		$plantilla->parse("contenido_principal.carga_inicial.bloque_tooltip");
		$plantilla->parse("contenido_principal.carga_inicial");
		
		//Contruimos plantilla secundaria
		$subPlantilla->parse("contenido_principal");
		
		//Exportamos plantilla secundaria a la plantilla principal
		$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
		
		//Construimos plantilla principal
		$plantilla->parse("contenido_principal");
		
		//Mostramos plantilla principal por pantalla
		$plantilla->out("contenido_principal");
	}else{
		$fichero="movimientos.xls";
		header("Content-type: application/vnd.ms-excel");
		//header("Content-Type: application/force-download");
		header("Content-Disposition: attachment; filename=$fichero");
		header("Content-Transfer-Encoding: binary");
		
		//Mostramos excel
		$subPlantillaExcel->parse("contenido_principal");
		$plantillaExcel->assign("CONTENIDO",$subPlantillaExcel->text("contenido_principal"));
		$plantillaExcel->parse("contenido_principal");
		$plantillaExcel->out("contenido_principal");
	}
?>