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
	$subPlantilla=new XTemplate("html/sections/eletter/history_eletter/view_history_eletter.html");
	
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
	$matrizOrden[0]["descripcion"]=STATIC_ORDER_HISTORY_ELETTER_SENT_DATE;
	$matrizOrden[0]["valor"]="fecha_envio";
	$matrizOrden[1]["descripcion"]=STATIC_ORDER_HISTORY_ELETTER_EMAIL_TOTAL_SENT;
	$matrizOrden[1]["valor"]="total_enviado";
	$matrizOrden[2]["descripcion"]=STATIC_ORDER_HISTORY_ELETTER_EMAIL_TOTAL_READ;
	$matrizOrden[2]["valor"]="total_leido";
	
	

	//Gestion del campo de orden y filtro de numero de registros
	$campoOrdenDefecto="";
	require "includes/load_filter_list.inc.php";
	

	/**
	 * 
	 * El total de paginas que mostraremos por pantalla
	 * @var int
	 * 
	 */
	$totalPaginasMostrar=4;
	$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_sp_eletter_listar(".$_GET["id_eletter"].",'".$campoOrden."','".$direccionOrden."',";

	$urlActual="main_app.php?section=history_eletter&action=view&hdnOrden=".$valorDefecto."&hdnRegistros=".$numeroRegistrosPagina."&";

	$subPlantilla->assign("ID_ELETTER",$_GET["id_eletter"]);
	
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
		$vectorHistorialEletter["ID"]=$dato["id_eletter"];
		$vectorHistorialEletter["SUBJECT"]=$dato["asunto"];
		$vectorHistorialEletter["TOTAL_SENT"]=$dato["total_enviado"];
		$vectorHistorialEletter["TOTAL_READ"]=$dato["total_leido"];
		
		$fechaEnvio=explode(" ",$dato["fecha_envio"]);
		$vectorHistorialEletter["DATE"]=generalUtils::conversionFechaFormato($fechaEnvio[0])." ".$fechaEnvio[1];
		$subPlantilla->assign("HISTORY_ELETTER",$vectorHistorialEletter);
		$subPlantilla->parse("contenido_principal.item_history_eletter");
		$i++;
	}
	
	
	$resultadoEletter=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_novedad_obtener_concreta(".$_GET["id_eletter"].")");
	$datoEletter=$db->getData($resultadoEletter);
	$titulo=$datoEletter["titulo"];
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_ELETTER_VIEW_ELETTER_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_ELETTER_VIEW_ELETTER_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_ELETTER_EDIT_ELETTER_LINK."&id_eletter=".$_GET["id_eletter"];
	$vectorMigas[2]["texto"]=$titulo;
	$vectorMigas[3]["url"]=STATIC_BREADCUMB_HISTORY_ELETTER_VIEW_HISTORY_ELETTER_LINK."&id_eletter=".$_GET["id_eletter"];
	$vectorMigas[3]["texto"]=STATIC_BREADCUMB_HISTORY_ELETTER_VIEW_HISTORY_ELETTER_TEXT;
	
	

	
	
	
	require "includes/load_breadcumb.inc.php";
	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
	
	//Contruimos plantilla secundaria
	$subPlantilla->parse("contenido_principal");
	
	//Exportamos plantilla secundaria a la plantilla principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
	//Construimos plantilla principal
	$plantilla->parse("contenido_principal");
	
	//Mostramos plantilla principal por pantalla
	$plantilla->out("contenido_principal");
?>