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
	$subPlantilla=new XTemplate("html/sections/menu/view_menu.html");
	
	$mostrarPaginador=true;
	$valorDefecto=-1;
	$campoOrden="orden";
	$direccionOrden="ASC";
	$numeroRegistrosPagina=DEFAULT_FILTER_NUMBER;

	if(!isset($_GET["hdnIdPosicion"])){
		$idPosicion=1;
	}else{
		$idPosicion=$_GET["hdnIdPosicion"];
	}
	
	//Posicion menu
	$subPlantilla->assign("MENU_ID_POSICION",$idPosicion);
	
	/**
	 * 
	 * $matrizOrden, la primera dimension de la matriz nos es el indice numerico, que es el que se usara como value del combobox.
	 * la parte asociativa "descripcion" nos indica la palabra que vera el usuario del gestor y la parte valor es el campo de la base de datos
	 * 
	 */
	$matrizOrden[0]["descripcion"]=STATIC_ORDER_MENU_TITLE_FIELD;
	$matrizOrden[0]["valor"]="titulo";
	$matrizOrden[1]["descripcion"]=STATIC_ORDER_MENU_MODULE_FIELD;
	$matrizOrden[1]["valor"]="modulo";
	$matrizOrden[2]["descripcion"]=STATIC_ORDER_MENU_TYPE_USER_FIELD;
	$matrizOrden[2]["valor"]="id_tipo_usuario_web";
		
	//Combo posicion
	$subPlantilla->assign("COMBO_POSICION",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_posicion_obtener_combo(".$_SESSION["user"]["language_id"].")","cmbPosicion","cmbPosicion",$idPosicion,"descripcion","id_posicion","",0,"onchange='configurarPosicionMenu(this)'","class='selectBox'"));
	
	//Gestion del campo de orden y filtro de numero de registros
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
		$subPlantilla->parse("contenido_principal.item_filter_position");
	}else{
		$idPadre=$_GET["id_padre"];
		$urlPadre="&id_padre=".$idPadre;
		$subPlantilla->assign("MENU_ID_PADRE",$idPadre);
		$subPlantilla->assign("PARAMETRO_ID_PADRE",$urlPadre);
	}
	$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_sp_menu_listar(".$idPadre.", ".$idPosicion.",".$_SESSION["user"]["language_id"].",'".$campoOrden."','".$direccionOrden."',";
	$urlActual="main_app.php?section=menu&action=view&hdnOrden=".$valorDefecto."&hdnRegistros=".$numeroRegistrosPagina.$urlPadre."&hdnIdPosicion=".$idPosicion."&";
	
	
	
	//Paginador
	require "includes/load_paginator.inc.php";
	
	//Pagina actual
	$subPlantilla->assign("PAGINA_ACTUAL",$paginaActual);
	
	$resultado=$db->callProcedure($codeProcedure);
	$i=0;
	$asignarPrimerOrden=false;
	while($dato=$db->getData($resultado)){
      
      if (($dato["id_tipo_usuario_web"] < 3) || ($_SESSION["user"]["rol_id"] <> 2)) {
		if($dato["activo"]==0){
			$subPlantilla->assign("STATE_STYLE","class='disabled' title='".STATIC_VIEW_MENU_ITEM_DISABLED."'");
		}else{
			$subPlantilla->assign("STATE_STYLE","");
		}
		if($dato["id_posicion"]==3){
			$drag="nodrag nodrop negrita ";
		}else{
			$drag="";
		}
		
		//Miramos si tiene permisos de escritura
		if(!gestorGeneralUtils::tienePermisoUsuarioMenu($db, $dato["id_menu"], 2)){
			$subPlantilla->assign("PRIVILEGE_ALLOW",0);
		}else{
			$subPlantilla->assign("PRIVILEGE_ALLOW",1);
		}
		// Forcing PRIVILEGE_ALLOW on it's own is not enough to allow editors
        //$subPlantilla->assign("PRIVILEGE_ALLOW",1);
		
		if($i%2==0){
			$subPlantilla->assign("TR_STYLE","class='".$drag."dark'");
		}else{
			$subPlantilla->assign("TR_STYLE","class='".$drag."light'");
		}
		
			
		$vectorMenu["ID"]=$dato["id_menu"];
		$vectorMenu["TITLE"]=$dato["titulo"];
		$vectorMenu["MODULE"]=$dato["modulo"];
		$vectorMenu["ID_TYPE_MEMBER"]=($dato["id_tipo_usuario_web"] == "") ? STATIC_MANAGE_MENU_TIPE_MEMBER_PUBLIC : $dato["tipo_usuario"];
		$vectorMenu["ORDER"]=$dato["orden"];
		$vectorMenu["POSICION"]=$dato["id_posicion"];
		

		
		//Si todavia no hemos asignado el orden y no estamos mostrando home(no es draggable), entonces ponemos el orden de referencia
		if(!$asignarPrimerOrden /*&& $dato["id_posicion"]!=3*/){
			$asignarPrimerOrden=true;
			$plantilla->assign("PRIMER_ORDEN",$dato["orden"]);
		}
		
		
		$subPlantilla->assign("MENU",$vectorMenu);
		$subPlantilla->parse("contenido_principal.item_menu");
		$i++;
      }
	}
	
	//Si no hemos seleccionado un filtro de orden...
	if(($_SESSION["user"]["rol_global"]==1 || ($idPadre!=-1 && gestorGeneralUtils::tienePermisoUsuarioMenu($db, $idPadre, 2))) && $valorDefecto==-1){
		//Carga inicial
		$i=0;
		$vectorTablaOrden[$i]["ID_CONTAINER"]="tblList";
		$vectorTablaOrden[$i]["ID_CONTAINER_BODY"]="tblListItem";
		$vectorTablaOrden[$i]["NAME"]="menu";
		
		require "includes/load_order_table.inc.php";
		
		//Construimos 
		$plantilla->parse("contenido_principal.carga_inicial");
	}
	
	

	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	
	//Si hemos pasado como parametro el id_padre
	if($idPadre!=-1){
		$resultadoMenu=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_menu_obtener_concreto(".$idPadre.",".$_SESSION["user"]["language_id"].")");
		$datoMenu=$db->getData($resultadoMenu);
		$idPosicion=$datoMenu["id_posicion"];

		//Si es menu 83...
		if($idPadre==83){
			$idPosicion=1;
		}
		
		//Obtenemos todos los menus padres del menu actual, incluido el actual
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_menu_breadcumb(null,".$idPadre.",".$_SESSION["user"]["language_id"].",'','')");
		$dato=$db->getData($resultado);
		//Generamos vector con los id menu de los datos separados por |
		$vectorIdMenu=explode("|",$dato["id_menu"]);
		//Generamos vector con los nombre menu de los datos separados por |
		$vectorNombreMenu=explode("|",$dato["nombre_menu"]);
		
		//Invertimos ambos vectores
		$vectorIdMenu=array_reverse($vectorIdMenu);
		$totalVectorIdMenu=count($vectorIdMenu);
		$vectorNombreMenu=array_reverse($vectorNombreMenu);
		
		$contadorMigas=1;
		for($i=0;$i<$totalVectorIdMenu;$i++){
			$vectorMigas[$contadorMigas]["url"]=STATIC_BREADCUMB_MENU_EDIT_MENU_LINK."&id_menu=".$vectorIdMenu[$i]."&posicion=".$idPosicion;
			$vectorMigas[$contadorMigas]["texto"]=$vectorNombreMenu[$i];
			
			$contadorMigas++;
			
			//SubMenu
			$vectorMigas[$contadorMigas]["url"]=STATIC_BREADCUMB_INICIO_LINK."&id_padre=".$vectorIdMenu[$i]."&hdnIdPosicion=".$idPosicion;
			$vectorMigas[$contadorMigas]["texto"]=STATIC_BREADCUMB_MENU_SUB_MENU_TEXT;
				
			$contadorMigas++;
		}
	}

	if($idPosicion!=3 && ($_SESSION["user"]["rol_global"]==1 || ($idPadre!=1 && gestorGeneralUtils::tienePermisoUsuarioMenu($db, $idPadre, 2)))){
		$subPlantilla->parse("contenido_principal.item_crear_menu");
		$subPlantilla->parse("contenido_principal.item_eliminar_menu");
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