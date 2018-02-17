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
	$subPlantilla=new XTemplate("html/sections/movement/concept/view_concept.html");
	
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
	$matrizOrden[0]["descripcion"]=STATIC_ORDER_CONCEPT_NAME_FIELD;
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
		$urlPadre="";
	}else{
		$idPadre=$_GET["id_padre"];
		$urlPadre="&id_padre=".$idPadre;
		$subPlantilla->assign("CONCEPTO_ID_PADRE",$idPadre);
		$subPlantilla->assign("PARAMETRO_ID_PADRE","&id_padre=".$idPadre);
	}
	

	$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_sp_concepto_movimiento_listar(".$idPadre.",".$_SESSION["user"]["language_id"].",'".$campoOrden."','".$direccionOrden."',";
	$urlActual="main_app.php?section=concept&action=view&hdnOrden=".$valorDefecto."&hdnRegistros=".$numeroRegistrosPagina.$urlPadre."&";


	//Paginador
	require "includes/load_paginator.inc.php";

	
	//Pagina actual
	$subPlantilla->assign("PAGINA_ACTUAL",$paginaActual);
	

	$resultado=$db->callProcedure($codeProcedure);
	$i=0;
	while($dato=$db->getData($resultado)){
		if($dato["activo"]==0){
			$subPlantilla->assign("STATE_STYLE","class='disabled' title='".STATIC_VIEW_CONCEPT_ITEM_DISABLED."'");
		}else{
			$subPlantilla->assign("STATE_STYLE","");
		}
		if($i%2==0){
			$subPlantilla->assign("TR_STYLE","class='dark'");
		}else{
			$subPlantilla->assign("TR_STYLE","class='light'");
		}
		if($dato["total_movimientos"]==0){
			$subPlantilla->assign("CONCEPTO_ALLOW",1);
		}else{
			$subPlantilla->assign("CONCEPTO_ALLOW",0);
		}
		$vectorConcepto["ID"]=$dato["id_concepto_movimiento"];
		$vectorConcepto["NAME"]=$dato["nombre"];
		$subPlantilla->assign("CONCEPTO",$vectorConcepto);
		$subPlantilla->parse("contenido_principal.item_concepto");
		$i++;
	}
	
	//Si no hemos seleccionado un filtro de orden...

	//Construimos 
	$plantilla->parse("contenido_principal.carga_inicial");
	
			
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_MOVEMENT_VIEW_MOVEMENT_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_MOVEMENT_VIEW_MOVEMENT_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_CONCEPT_VIEW_CONCEPT_LINK;
	$vectorMigas[2]["texto"]=STATIC_BREADCUMB_CONCEPT_VIEW_CONCEPT_TEXT;
	
	
	
	//Si hemos pasado como parametro el id_padre
	if($idPadre!=-1){
		//Obtenemos todas las tematicas padres de la tematica actual, incluido el actual
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_concepto_movimiento_breadcumb(null,".$idPadre.",".$_SESSION["user"]["language_id"].",'','')");
		$dato=$db->getData($resultado);
		//Generamos vector con los id tematica de los datos separados por |
		$vectorIdConcepto=explode("|",$dato["id_concepto_movimiento"]);
		//Generamos vector con los nombre tematica de los datos separados por |
		$vectorNombreConcepto=explode("|",$dato["nombre_concepto"]);
		
		//Invertimos ambos vectores
		$vectorIdConcepto=array_reverse($vectorIdConcepto);
		$totalVectorIdConcepto=count($vectorIdConcepto);
		$vectorNombreConcepto=array_reverse($vectorNombreConcepto);

		
		$contadorMigas=3;
		for($i=0;$i<$totalVectorIdConcepto;$i++){
			$vectorMigas[$contadorMigas]["url"]=STATIC_BREADCUMB_CONCEPT_EDIT_CONCEPT_LINK."&id_concepto=".$vectorIdConcepto[$i];
			$vectorMigas[$contadorMigas]["texto"]=$vectorNombreConcepto[$i];
			
			//Incrementamos
			$contadorMigas++;
			
			//Sub tematica
			$vectorMigas[$contadorMigas]["url"]=STATIC_BREADCUMB_CONCEPT_VIEW_CONCEPT_LINK."&id_padre=".$vectorIdConcepto[$i];
			$vectorMigas[$contadorMigas]["texto"]=STATIC_BREADCUMB_CONCEPT_SUBCONCEPT_TEXT;
			
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