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
	$subPlantilla=new XTemplate("html/sections/job/view_job.html");
	
	$mostrarPaginador=true;
	$valorDefecto=1;
	$campoOrden="fecha";
	$direccionOrden="DESC";
	$numeroRegistrosPagina=DEFAULT_FILTER_NUMBER;

	/**
	 * 
	 * $matrizOrden, la primera dimension de la matriz nos es el indice numerico, que es el que se usara como value del combobox.
	 * la parte asociativa "descripcion" nos indica la palabra que vera el usuario del gestor y la parte valor es el campo de la base de datos
	 * 
	 */
	$matrizOrden[0]["descripcion"]=STATIC_ORDER_JOB_TITLE_FIELD;
	$matrizOrden[0]["valor"]="titulo";
	$matrizOrden[1]["descripcion"]=STATIC_ORDER_JOB_DATE_FIELD;
	$matrizOrden[1]["valor"]="fecha";
	$matrizOrden[2]["descripcion"]=STATIC_ORDER_JOB_MAIL_FIELD;
	$matrizOrden[2]["valor"]="correo_electronico";
	
		
	
	//Gestion del campo de orden y filtro de numero de registros
	$campoOrdenDefecto="";
	require "includes/load_filter_list.inc.php";
	if($campoOrden!="fecha"){
		$direccionOrden="ASC";
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
	$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_sp_oferta_trabajo_listar(".$_SESSION["user"]["language_id"].",'".$campoOrden."','".$direccionOrden."',";
	$urlActual="main_app.php?section=job&action=view&hdnOrden=".$valorDefecto."&hdnRegistros=".$numeroRegistrosPagina."&";
	
	//Paginador
	require "includes/load_paginator.inc.php";
	
	//Pagina actual
	$subPlantilla->assign("PAGINA_ACTUAL",$paginaActual);
	
	$resultado=$db->callProcedure($codeProcedure);
	$i=0;
	while($dato=$db->getData($resultado)){
		if($dato["activo"]==0){
			$subPlantilla->assign("STATE_STYLE","class='disabled' title='".STATIC_VIEW_JOB_ITEM_DISABLED."'");
		}else{
			$subPlantilla->assign("STATE_STYLE","");
		}
		if($i%2==0){
			$subPlantilla->assign("TR_STYLE","class='dark'");
		}else{
			$subPlantilla->assign("TR_STYLE","class='light'");
		}
		$vectorJob["ID"]=$dato["id_oferta_trabajo"];
		$vectorJob["TITLE"]=$dato["titulo"];
		$vectorJob["EMAIL"]=$dato["correo_electronico"];
		$vectorJob["DATE"]=generalUtils::conversionFechaFormato($dato["fecha"]);

		if($dato["id_usuario_web"]!=""){
			$vectorJob["USER"]="<a href='main_app.php?section=member&action=edit&id_miembro=".$dato["id_usuario_web"]."&origen=1'>".$dato["correo_usuario"]."</a>";
		}else{
			$vectorJob["USER"]="";
		}
		
		$subPlantilla->assign("JOB",$vectorJob);
		$subPlantilla->parse("contenido_principal.item_job");
		$i++;
	}
	
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_JOB_VIEW_JOB_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_JOB_VIEW_JOB_TEXT;

	
	
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