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
	$subPlantilla=new XTemplate("html/sections/invoice/view_invoice.html");
	
	$mostrarPaginador=true;
	$valorDefecto=4;
	$campoOrden="id_factura";
	$direccionOrden="DESC";
	$numeroRegistrosPagina=DEFAULT_FILTER_NUMBER;

	/**
	 * 
	 * $matrizOrden, la primera dimension de la matriz nos es el indice numerico, que es el que se usara como value del combobox.
	 * la parte asociativa "descripcion" nos indica la palabra que vera el usuario del gestor y la parte valor es el campo de la base de datos
	 * 
	 */
	$matrizOrden[0]["descripcion"]=STATIC_ORDER_INVOICE_DATE;
	$matrizOrden[0]["valor"]="fecha_factura";
	$matrizOrden[1]["descripcion"]=STATIC_ORDER_INVOICE_NUMBER;
	$matrizOrden[1]["valor"]="numero_factura";
	$matrizOrden[2]["descripcion"]=STATIC_ORDER_INVOICE_NIF;
	$matrizOrden[2]["valor"]="nif_cliente_factura";
	$matrizOrden[3]["descripcion"]=STATIC_ORDER_INVOICE_NAME;
	$matrizOrden[3]["valor"]="nombre_cliente_factura";
	$matrizOrden[4]["descripcion"]=STATIC_ORDER_INVOICE_LAST_INSERTED_FIELD;
	$matrizOrden[4]["valor"]="id_factura";
	$matrizOrden[5]["descripcion"] = "Paid status";
	$matrizOrden[5]["valor"] = "es_pagado";
//	$matrizOrden[6]["descripcion"] = "Sent status";
//	$matrizOrden[6]["valor"] = "enviado";

	
	
	//Gestion del campo de orden y filtro de numero de registros
	$campoOrdenDefecto="";
	require "includes/load_filter_list.inc.php";
	if($campoOrden!="fecha_factura" && $campoOrden!="id_factura"){
		$direccionOrden="ASC";
	}
	
	$fechaDesde=date("Y")."-01-01";
	$fechaHasta=date("Y")."-12-31";
	$persona="";
	$filtroPaginador="";
	if(isset($_GET["from"])){
		if($_GET["from"]!=""){
			$fechaDesde=generalUtils::conversionFechaFormato($_GET["from"],"-","-");
			$subPlantilla->assign("VIEW_INVOICE_DATE_FROM_SEARCH_VALUE",$_GET["from"]);
			$filtroPaginador.="&from=".$_GET["from"];
		}
		if($_GET["to"]!=""){
			$fechaHasta=generalUtils::conversionFechaFormato($_GET["to"],"-","-");
			$subPlantilla->assign("VIEW_INVOICE_DATE_TO_SEARCH_VALUE",$_GET["to"]);
			$filtroPaginador.="&to=".$_GET["to"];
		}
        if ($_GET["txtPersona"] != "") {
    		$persona = $_GET["txtPersona"];
    		$subPlantilla->assign("VIEW_INVOICE_PERSON_SEARCH_VALUE", "checked");
    		$filtroPaginador .= "&txtPersona=" . $_GET["txtPersona"];
		
		$filtroPaginador.="&";
	}else{
		$subPlantilla->assign("VIEW_INVOICE_DATE_FROM_SEARCH_VALUE","01-01-".date("Y"));
		$subPlantilla->assign("VIEW_INVOICE_DATE_TO_SEARCH_VALUE","31-12-".date("Y"));
	}
}

/**
	 * 
	 * El total de paginas que mostraremos por pantalla
	 * @var int
	 * 
	 */
	$totalPaginasMostrar=4;
	
	/**
	 * 
	 * Almacenamos la cadena que representa la llamada al store procedure
	 * @var string
	 * 
	 */
	$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_sp_factura_listar('".$fechaDesde."','".$fechaHasta."','".generalUtils::escaparCadena($persona)."','".$campoOrden."','".$direccionOrden."',";
	$urlActual="main_app.php?section=invoice&action=view&hdnOrden=".$valorDefecto."&hdnRegistros=".$numeroRegistrosPagina."&".$filtroPaginador;
	
	//Paginador
	require "includes/load_paginator.inc.php";
	
	//Pagina actual
	$subPlantilla->assign("PAGINA_ACTUAL",$paginaActual);
	
	$resultado=$db->callProcedure($codeProcedure);
	$i=0;
	while($dato=$db->getData($resultado)){
		if($i%2==0){
			$subPlantilla->assign("TR_STYLE","class='dark'");
		}else{
			$subPlantilla->assign("TR_STYLE","class='light'");
		}
			
		
		$vectorFactura["ID"]=$dato["id_factura"];
		$vectorFactura["DATE"]=generalUtils::conversionFechaFormato($dato["fecha_factura"],"-","-");;
		$vectorFactura["NUMBER"]=$dato["numero_factura"];
		$vectorFactura["NIF"]=$dato["nif_cliente_factura"];
		$vectorFactura["NAME"]=$dato["nombre_cliente_factura"];
        $vectorFactura["PAID"] = ($dato["es_pagado"]==1 ? STATIC_VIEW_MOVEMENT_PAYED_YES : STATIC_VIEW_MOVEMENT_PAYED_NO);
        $vectorFactura["SENT"] = ($dato["hash_generado"] ? ($dato["enviado"]==1 ? STATIC_VIEW_MOVEMENT_PAYED_YES : STATIC_VIEW_MOVEMENT_PAYED_NO) : "");
//      $vectorFactura["SENT"] = ($dato["hash_generado"] ? $dato["enviado"] : "");
		$vectorFactura["HASH"]=$dato["hash_generado"];

		$subPlantilla->assign("INVOICE",$vectorFactura);
	
		if($dato["hash_generado"]!=""){
			$subPlantilla->parse("contenido_principal.item_factura.item_pdf");
			
			$enlaceFactura="main_app.php?section=invoice&action=send&id_factura=".$dato["id_factura"];
			$subPlantilla->assign("ENLACE_FACTURA",$enlaceFactura);
			
			$subPlantilla->parse("contenido_principal.item_factura.item_send");
		}
	
		$subPlantilla->parse("contenido_principal.item_factura");
		$i++;
	}

	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_INVOICE_VIEW_INVOICE_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_INVOICE_VIEW_INVOICE_TEXT;
	
	//Date picker FROM
	$plantilla->assign("INPUT_ID","txtFechaDesde");
	$plantilla->parse("contenido_principal.carga_inicial.autoload_datepicker");
	
	//Date picker TO
	$plantilla->assign("INPUT_ID","txtFechaHasta");
	$plantilla->parse("contenido_principal.carga_inicial.autoload_datepicker");

	
	require "includes/load_breadcumb.inc.php";
	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";

	
	$plantilla->parse("contenido_principal.carga_inicial");
	
	//Contruimos plantilla secundaria
	$subPlantilla->parse("contenido_principal");
	
	//Exportamos plantilla secundaria a la plantilla principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
	//Construimos plantilla principal
	$plantilla->parse("contenido_principal");
	
	//Mostramos plantilla principal por pantalla
	$plantilla->out("contenido_principal");
?>