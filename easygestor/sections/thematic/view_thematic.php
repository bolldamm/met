<?php
	/**
	 * 
	 * Listamos todos los tipos area existentes en el sistema
	 * @Author eData
	 * 
	 */


	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/thematic/view_thematic.html");

	$mostrarPaginador=true;
	$valorDefecto=-1;
	$campoOrden="nombre";
	$direccionOrden="ASC";
	$numeroRegistrosPagina=DEFAULT_FILTER_NUMBER;

	/**
	 * 
	 * $matrizOrden, la primera dimension de la matriz nos es el indice numerico, que es el que se usara como value del combobox.
	 * la parte asociativa "descripcion" nos indica la palabra que vera el usuario del gestor y la parte valor es el campo de la base de datos
	 * 
	 */
	$matrizOrden[0]["descripcion"]=STATIC_ORDER_THEMATIC_NAME_FIELD;
	$matrizOrden[0]["valor"]="nombre";		
	
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
	
	/**
	 * 
	 * Almacenamos la cadena que representa la llamada al store procedure
	 * @var string
	 * 
	 */
	if(!isset($_GET["id_padre"]) || !is_numeric($_GET["id_padre"]) || $_GET["id_padre"]==-1){
		$idPadre=-1;
	}else{
		$idPadre=$_GET["id_padre"];
		$subPlantilla->assign("TEMATICA_ID_PADRE",$idPadre);
		$subPlantilla->assign("PARAMETRO_ID_PADRE","&id_padre=".$idPadre);
	}
	$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_sp_tematica_listar(".$idPadre.",".$_SESSION["user"]["language_id"].",'".$campoOrden."','".$direccionOrden."',";
	$urlActual="main_app.php?section=thematic&action=view&hdnOrden=".$valorDefecto."&hdnRegistros=".$numeroRegistrosPagina."&";
	
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
		$vectorTipoArea["ID"]=$dato["id_tematica"];
		$vectorTipoArea["NAME"]=$dato["nombre"];
		$subPlantilla->assign("TEMATICA",$vectorTipoArea);
		$subPlantilla->parse("contenido_principal.item_tematica");
		$i++;
	}
	
	//Si no hemos seleccionado un filtro de orden...
	if($valorDefecto==-1){
		//Carga inicial
		$i=0;
		$vectorTablaOrden[$i]["ID_CONTAINER"]="tblList";
		$vectorTablaOrden[$i]["ID_CONTAINER_BODY"]="tblListItem";
		$vectorTablaOrden[$i]["NAME"]="thematic";
		
		require "includes/load_order_table.inc.php";
		
		//Construimos 
		$plantilla->parse("contenido_principal.carga_inicial");
	}
	
	
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_THEMATIC_VIEW_THEMATIC_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_THEMATIC_VIEW_THEMATIC_TEXT;
	
	//Si hemos pasado como parametro el id_padre
	if($idPadre!=-1){
		//Obtenemos todas las tematicas padres de la tematica actual, incluido el actual
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_tematica_breadcumb(null,".$idPadre.",".$_SESSION["user"]["language_id"].",'','')");
		$dato=$db->getData($resultado);
		//Generamos vector con los id tematica de los datos separados por |
		$vectorIdTematica=explode("|",$dato["id_tematica"]);
		//Generamos vector con los nombre tematica de los datos separados por |
		$vectorNombreTematica=explode("|",$dato["nombre_tematica"]);
		
		//Invertimos ambos vectores
		$vectorIdTematica=array_reverse($vectorIdTematica);
		$totalVectorIdTematica=count($vectorIdTematica);
		$vectorNombreTematica=array_reverse($vectorNombreTematica);
		
		$contadorMigas=2;
		for($i=0;$i<$totalVectorIdTematica;$i++){
			$vectorMigas[$contadorMigas]["url"]=STATIC_BREADCUMB_THEMATIC_EDIT_THEMATIC_LINK."&id_tematica=".$vectorIdTematica[$i];
			$vectorMigas[$contadorMigas]["texto"]=$vectorNombreTematica[$i];
			
			//Incrementamos
			$contadorMigas++;
			
			//Sub tematica
			$vectorMigas[$contadorMigas]["url"]=STATIC_BREADCUMB_THEMATIC_VIEW_THEMATIC_LINK."&id_padre=".$vectorIdTematica[$i];
			$vectorMigas[$contadorMigas]["texto"]=STATIC_BREADCUMB_THEMATIC_SUB_THEMATIC_TEXT;
			
			//Incrementamos
			$contadorMigas++;
		}
	}
	
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