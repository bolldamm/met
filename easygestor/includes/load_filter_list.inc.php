<?php
	//Si hemos seleccionado orden, entonces cambiamos valor
	if(isset($_GET["hdnOrden"]) && $_GET["hdnOrden"]!=-1 && isset($matrizOrden[$_GET["hdnOrden"]]["valor"])){
		$campoOrden=$matrizOrden[$_GET["hdnOrden"]]["valor"];
		$valorDefecto=$_GET["hdnOrden"];
		//Mostrar menu
	}else{
		//Solo si ponemos el campo orden por defecto, si no ponemos orden como defecto es porque no existe este campo en este mantenimiento
		if($valorDefecto==-1){
			//Solo drag and drop cuando es el orden original
			$plantilla->assign("ORDEN_ALERT_TITULO",STATIC_GLOBAL_ERROR_INTERNAL);
			$plantilla->assign("ORDEN_ALERT_TEXTO",STATIC_ORDER_ERROR);
			
			$plantilla->parse("contenido_principal.script_table_drag_and_drop");
		}
		//$valorDefecto=-1;
	}
	$subPlantilla->assign("ORDEN_SELECCIONADO",$valorDefecto);
	
	//Filtro de registros
	if(isset($_GET["hdnRegistros"]) && is_numeric($_GET["hdnRegistros"])){
		$numeroRegistrosPagina=$_GET["hdnRegistros"];
		if($_GET["hdnRegistros"]==-1){
			//No habra paginador
			$mostrarPaginador=false;
		}else if($_GET["hdnRegistros"]>MAX_FILTER_NUMBER || $_GET["hdnRegistros"]<-1){
			//No podemos poner mas registros de los permitidos en el combo
			$numeroRegistrosPagina=DEFAULT_FILTER_NUMBER;
		}
	}
		
	$subPlantilla->assign("NUMERO_REGISTROS_SELECCIONADOS",$numeroRegistrosPagina);
	
	
	if(!isset($campoOrdenDefecto)){
		$campoOrdenDefecto=STATIC_ORDER_ORIGINAL_FIELD;
	}
	
	
	//Combo orden
	$subPlantilla->assign("COMBO_ORDEN",generalUtils::construirComboMatriz($matrizOrden,"cmbOrden","cmbOrden",$valorDefecto,$campoOrdenDefecto,-1,"onchange='configurarCampoOrden(this)'"));
	
	//Combo numero registros
	$subPlantilla->assign("COMBO_NUMERO_REGISTROS",gestorGeneralUtils::construirComboNumeroRegistros(STATIC_FILTER_ROW_ALL,$numeroRegistrosPagina));
?>