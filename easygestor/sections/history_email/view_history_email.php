<?php
	/**
	 * 
	 * Listamos todos las areas en el sistema
	 * @Author eData
	 * 
	 */

	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/history_email/view_history_email.html");
	
	$mostrarPaginador=true;
	$valorDefecto=0;
	$campoOrden="fecha_envio";
	$direccionOrden="DESC";
	$numeroRegistrosPagina=DEFAULT_FILTER_NUMBER;
	
	/* 
	 * $matrizOrden, la primera dimension de la matriz nos es el indice numerico, que es el que se usara como value del combobox.
	 * la parte asociativa "descripcion" nos indica la palabra que vera el usuario del gestor y la parte valor es el campo de la base de datos
	 * 
	 */
	$matrizOrden[0]["descripcion"]=STATIC_ORDER_HISTORY_EMAIL_SENT_DATE;
	$matrizOrden[0]["valor"]="fecha_envio";
	$matrizOrden[1]["descripcion"]=STATIC_ORDER_HISTORY_EMAIL_NAME;
	$matrizOrden[1]["valor"]="nombre_completo";
	$matrizOrden[2]["descripcion"]=STATIC_ORDER_HISTORY_EMAIL_SUBJECT;
	$matrizOrden[2]["valor"]="asunto";
	$matrizOrden[3]["descripcion"]=STATIC_ORDER_HISTORY_EMAIL_EMAIL_TYPE;
	$matrizOrden[3]["valor"]="nombre";


	//Gestion del campo de orden y filtro de numero de registros
	$campoOrdenDefecto="";
	require "includes/load_filter_list.inc.php";
	
	if($campoOrden!="fecha_envio"){
		$direccionOrden="ASC";
	}
	
	//Buscador
	$idTipo=0;
	$fechaDesde="";
	$fechaHasta="";
	$keyword="";
	$filtroPaginador="";
	if(isset($_GET["cmbTipo"])){
		$idTipo=$_GET["cmbTipo"];
		
		$filtroPaginador="cmbTipo=".$idTipo;
		
		if($_GET["from"]!=""){
			$fechaDesde=generalUtils::conversionFechaFormato($_GET["from"],"-","-");
			$subPlantilla->assign("VIEW_HISTORY_EMAIL_DATE_FROM_SEARCH_VALUE",$_GET["from"]);
			$filtroPaginador.="&from=".$_GET["from"];
		}
		if($_GET["to"]!=""){
			$fechaHasta=generalUtils::conversionFechaFormato($_GET["to"],"-","-");
			$subPlantilla->assign("VIEW_HISTORY_EMAIL_DATE_TO_SEARCH_VALUE",$_GET["to"]);
			$filtroPaginador.="&to=".$_GET["to"];
		}
		if($_GET["keyword"]!=""){
            $keyword=$_GET["keyword"];
			$subPlantilla->assign("VIEW_HISTORY_EMAIL_KEYWORD_TO_SEARCH_VALUE",$_GET["keyword"]);
			$filtroPaginador.="&keyword=".$_GET["keyword"];
		}
		
		$filtroPaginador.="&";
	}
	
	
	/**
	 * Total number of pages to be displayed on screen
	 * @var int
	 */
	$totalPaginasMostrar=4;

	$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_sp_correo_electronico_listar(".$idTipo.",".$_SESSION["user"]["language_id"].",'".$fechaDesde."','".$fechaHasta."','".$keyword."','".$campoOrden."','".$direccionOrden."',";

	$urlActual="main_app.php?section=history_email&action=view&hdnOrden=".$valorDefecto."&hdnRegistros=".$numeroRegistrosPagina."&".$filtroPaginador;

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
		$vectorHistoryEmail["ID"]=$dato["id_correo_electronico"];
        $encodedTo = rawurlencode($dato["correo_electronico"] ?? '');
        $subject = rawurlencode($dato["asunto"] ?? '');
        //$body = rawurlencode(strip_tags($dato["cuerpo"])); //Not used (can't pass HTML formatted body in mailto link)
        $vectorHistoryEmail["SUBJECT"]="<a href='mailto:".$encodedTo."?subject=".$subject."' target='_blank'>".$dato["asunto"]."</a>";
		$vectorHistoryEmail["NAME"]=$dato["nombre_completo"];
		$vectorHistoryEmail["EMAIL_TYPE"]=$dato["nombre"];
		$fecha=explode(" ",$dato["fecha_envio"]);
		
		$vectorHistoryEmail["SENT_DATE"]=generalUtils::conversionFechaFormato($fecha[0])." ".$fecha[1];
		$subPlantilla->assign("HISTORY_EMAIL",$vectorHistoryEmail);
		$subPlantilla->parse("contenido_principal.item_history_email");
		$i++;
	}
	
	
	//Date picker FROM
	$plantilla->assign("INPUT_ID","txtFechaDesde");
	$plantilla->parse("contenido_principal.carga_inicial.autoload_datepicker");
	
	//Date picker TO
	$plantilla->assign("INPUT_ID","txtFechaHasta");
	$plantilla->parse("contenido_principal.carga_inicial.autoload_datepicker");
	
	//Combo tipo
	$subPlantilla->assign("COMBO_TIPO_EMAIL",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_tipo_correo_electronico_obtener_combo(".$_SESSION["user"]["language_id"].")","cmbTipo","cmbTipo",$idTipo,"nombre","id_tipo_correo_electronico",STATIC_GLOBAL_COMBO_DEFAULT,0,""));
	
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_HISTORY_EMAIL_VIEW_HISTORY_EMAIL_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_HISTORY_EMAIL_VIEW_HISTORY_EMAIL_TEXT;
	
	
	
	require "includes/load_breadcumb.inc.php";
	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
	
	//Contruimos plantilla secundaria
	$subPlantilla->parse("contenido_principal");
	
	$plantilla->parse("contenido_principal.carga_inicial");
	
	//Exportamos plantilla secundaria a la plantilla principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
	//Construimos plantilla principal
	$plantilla->parse("contenido_principal");
	
	//Mostramos plantilla principal por pantalla
	$plantilla->out("contenido_principal");
?>