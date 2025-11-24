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
	$subPlantilla=new XTemplate("html/sections/movement/payment_type/view_payment_type.html");
	
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
	$matrizOrden[0]["descripcion"]=STATIC_ORDER_PAYMENT_TYPE_NAME_FIELD;
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
	

	$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_sp_tipo_pago_movimiento_listar(".$_SESSION["user"]["language_id"].",'".$campoOrden."','".$direccionOrden."',";
	$urlActual="main_app.php?section=payment&action=view&hdnOrden=".$valorDefecto."&hdnRegistros=".$numeroRegistrosPagina."&";
	
	//Paginador
	require "includes/load_paginator.inc.php";

	
	//Pagina actual
	$subPlantilla->assign("PAGINA_ACTUAL",$paginaActual);
	

	$resultado=$db->callProcedure($codeProcedure);
	$i=0;
	while($dato=$db->getData($resultado)){
		if($dato["activo"]==0){
			$subPlantilla->assign("STATE_STYLE","class='disabled' title='".STATIC_VIEW_PAYMENT_TYPE_ITEM_DISABLED."'");
		}else{
			$subPlantilla->assign("STATE_STYLE","");
		}
		if($i%2==0){
			$subPlantilla->assign("TR_STYLE","class='dark'");
		}else{
			$subPlantilla->assign("TR_STYLE","class='light'");
		}
		if($dato["total_movimientos"]==0){
			$subPlantilla->assign("TIPO_PAGO_ALLOW",1);
		}else{
			$subPlantilla->assign("TIPO_PAGO_ALLOW",0);
		}
		$vectorTipoPago["ID"]=$dato["id_tipo_pago_movimiento"];
		$vectorTipoPago["NAME"]=$dato["nombre"];
		$subPlantilla->assign("TIPO_PAGO",$vectorTipoPago);
		$subPlantilla->parse("contenido_principal.item_tipo_pago");
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
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_PAYMENT_TYPE_VIEW_PAYMENT_TYPE_LINK;
	$vectorMigas[2]["texto"]=STATIC_BREADCUMB_PAYMENT_TYPE_VIEW_PAYMENT_TYPE_TEXT;
	
	
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