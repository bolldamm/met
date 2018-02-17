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
	$subPlantilla=new XTemplate("html/sections/success/view_success.html");
	
	$mostrarPaginador=true;
	$valorDefecto=-1;
	$campoOrden="orden";
	$direccionOrden="ASC";
	$numeroRegistrosPagina=DEFAULT_FILTER_NUMBER;

	/**
	 * 
	 * $matrizOrden, la primera dimension de la matriz nos es el indice numerico, que es el que se usara como value del combobox.
	 * la parte asociativa "descripcion" nos indica la palabra que vera el usuario del gestor y la parte valor es el campo de la base de datos
	 * 
	 */
	$matrizOrden[0]["descripcion"]=STATIC_ORDER_SUCCESS_TITLE_FIELD;
	$matrizOrden[0]["valor"]="titulo";
	
	//Gestion del campo de orden y filtro de numero de registros
	require "includes/load_filter_list.inc.php";
	if($valorDefecto==1) {
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
	
	$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_sp_casos_exito_listar(".$_SESSION["user"]["language_id"].",'".$campoOrden."','".$direccionOrden."',";
	
	
	$urlActual="main_app.php?section=success&action=view&hdnOrden=".$valorDefecto."&hdnRegistros=".$numeroRegistrosPagina."&";

	//Paginador
	require "includes/load_paginator.inc.php";
	
	//Pagina actual
	$subPlantilla->assign("PAGINA_ACTUAL",$paginaActual);
	
	$resultado=$db->callProcedure($codeProcedure);
	$i=0;
	while($dato=$db->getData($resultado)){
		if($dato["activo"]==0){
			$subPlantilla->assign("STATE_STYLE","class='disabled' title='".STATIC_VIEW_SUCCESS_ITEM_DISABLED."'");
		}else{
			$subPlantilla->assign("STATE_STYLE","");
		}
		if($i%2==0){
			$subPlantilla->assign("TR_STYLE","class='dark'");
		}else{
			$subPlantilla->assign("TR_STYLE","class='light'");
		}
		if($dato["resaltar"]==1){
			$subPlantilla->assign("FEATURED_STYLE","class='featured' title='".STATIC_ORDER_FEATURED_FIELD_ELEMENT."'");
		}else{
			$subPlantilla->assign("FEATURED_STYLE","");
		}
		$vectorProyecto["ID"]=$dato["id_caso_exito"];
		$vectorProyecto["TITLE"]=$dato["titulo"];
		$vectorProyecto["ORDER"]=$dato["orden"];
		if($i==0){
			$plantilla->assign("PRIMER_ORDEN",$dato["orden"]);
		}
		
		$subPlantilla->assign("CASOEXITO",$vectorProyecto);
		$subPlantilla->parse("contenido_principal.item_proyecto");
		$i++;
	}
	
	//Si no hemos seleccionado un filtro de orden...
	if($valorDefecto==-1){
		//Carga inicial
		$i=0;
		$vectorTablaOrden[$i]["ID_CONTAINER"]="tblList";
		$vectorTablaOrden[$i]["ID_CONTAINER_BODY"]="tblListItem";
		$vectorTablaOrden[$i]["NAME"]="success";
		
		require "includes/load_order_table.inc.php";
		
		//Construimos 
		$plantilla->parse("contenido_principal.carga_inicial");
	}
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_SUCCESS_VIEW_SUCCESS_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_SUCCESS_VIEW_TEXT;
	
	
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