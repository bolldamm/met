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
	$subPlantilla=new XTemplate("html/sections/workshop/view_workshop.html");
	
	$mostrarPaginador=true;
	$valorDefecto=1;
	$campoOrden="activo";
	$direccionOrden="ASC";
	$numeroRegistrosPagina=DEFAULT_FILTER_NUMBER;

	/**
	 * 
	 * $matrizOrden, la primera dimension de la matriz nos es el indice numerico, que es el que se usara como value del combobox.
	 * la parte asociativa "descripcion" nos indica la palabra que vera el usuario del gestor y la parte valor es el campo de la base de datos
	 * 
	 */
	$matrizOrden[0]["descripcion"]=STATIC_ORDER_WORKSHOP_NAME_FIELD;
	$matrizOrden[0]["valor"]="nombre";
	$matrizOrden[1]["descripcion"]=STATIC_ORDER_WORKSHOP_ENABLED_FIELD;
	$matrizOrden[1]["valor"]="activo";
	$matrizOrden[2]["descripcion"]=STATIC_ORDER_WORKSHOP_ID_FIELD;
	$matrizOrden[2]["valor"]="id_taller";
	

	
	//Gestion del campo de orden y filtro de numero de registros
	$campoOrdenDefecto="";
	require "includes/load_filter_list.inc.php";
	if($campoOrden=="activo" || $campoOrden=="id_taller"){
		$direccionOrden="DESC";
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
	$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_sp_taller_listar(".$_SESSION["user"]["language_id"].",'".$campoOrden."','".$direccionOrden."',";
	$urlActual="main_app.php?section=workshop&action=view&hdnOrden=".$valorDefecto."&hdnRegistros=".$numeroRegistrosPagina."&";
	
	//Paginador
	require "includes/load_paginator.inc.php";
	
	//Pagina actual
	$subPlantilla->assign("PAGINA_ACTUAL",$paginaActual);
	
	$resultado=$db->callProcedure($codeProcedure);
	$i=0;
	while($dato=$db->getData($resultado)){
		if($dato["activo"]==0){
			$subPlantilla->assign("STATE_STYLE","class='disabled' title='".STATIC_VIEW_WORKSHOP_ITEM_DISABLED."'");
		}else{
			$subPlantilla->assign("STATE_STYLE","");
		}
		if($i%2==0){
			$subPlantilla->assign("TR_STYLE","class='dark'");
		}else{
			$subPlantilla->assign("TR_STYLE","class='light'");
		}
		
        $subPlantilla->assign("WORKSHOP_ALLOW",0);

		/*
		if($dato["total_usuarios"]==0){
			$subPlantilla->assign("WORKSHOP_ALLOW",1);
		}else{
			$subPlantilla->assign("WORKSHOP_ALLOW",0);
		}
        */

		$vectorTaller["ID"]=$dato["id_taller"];
		//$vectorTaller["START_DATE"]=generalUtils::conversionFechaFormato($dato["fecha_inicio"]); //not used
		//$vectorTaller["END_DATE"]=generalUtils::conversionFechaFormato($dato["fecha_fin"]); //not used
		$vectorTaller["NAME"]=$dato["nombre"];
		$vectorTaller["REGISTERED"]=$dato["inscritos"];
		
		
		
		$subPlantilla->assign("WORKSHOP",$vectorTaller);
		$subPlantilla->parse("contenido_principal.item_taller");
		$i++;
	}
	
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_WORKSHOP_VIEW_WORKSHOP_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_WORKSHOP_VIEW_WORKSHOP_TEXT;

	
	
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