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
	$subPlantilla=new XTemplate("html/sections/workshop/workshop_date/view_workshop_date.html");
	
	$mostrarPaginador=true;
	$valorDefecto=0; // default sort is by name
	$campoOrden="nombre_completo"; // default sort is by name
	$direccionOrden="DESC";
	//$numeroRegistrosPagina=DEFAULT_FILTER_NUMBER;
	$numeroRegistrosPagina=50; // default is 50 per page (= show all)

	/**
	 * 
	 * $matrizOrden, la primera dimension de la matriz nos es el indice numerico, que es el que se usara como value del combobox.
	 * la parte asociativa "descripcion" nos indica la palabra que vera el usuario del gestor y la parte valor es el campo de la base de datos
	 * 
	 */
	$matrizOrden[0]["descripcion"]=STATIC_ORDER_WORKSHOP_DATE_NAME_FIELD;
	$matrizOrden[0]["valor"]="nombre_completo";
	$matrizOrden[1]["descripcion"]=STATIC_ORDER_WORKSHOP_DATE_EMAIL_FIELD;
	$matrizOrden[1]["valor"]="correo_electronico";
	$matrizOrden[2]["descripcion"]=STATIC_ORDER_WORKSHOP_DATE_INSCRIPTION_NUMBER_FIELD;
	$matrizOrden[2]["valor"]="numero_inscripcion";
	$matrizOrden[3]["descripcion"]=STATIC_ORDER_WORKSHOP_DATE_PAID_FIELD;
	$matrizOrden[3]["valor"]="pagado";
	$matrizOrden[4]["descripcion"]=STATIC_ORDER_WORKSHOP_DATE_ASSISTED_FIELD;
	$matrizOrden[4]["valor"]="asistido";
	$matrizOrden[5]["descripcion"]=STATIC_ORDER_WORKSHOP_DATE_USER_TYPE_FIELD;
	$matrizOrden[5]["valor"]="tipo_usuario";


	
	
	//Gestion del campo de orden y filtro de numero de registros
	$campoOrdenDefecto="";
	require "includes/load_filter_list.inc.php";


	if($campoOrden!="numero_inscripcion" && $campoOrden!="pagado"){
		$direccionOrden="ASC";
	}
	
	
	$idFechaTaller=0;
	$pagado=-1;
	$asistido=-1;
	$idTipoUsuario=0;
	$correoElectronico="";
	$nombre="";
	$numeroInscripcion="";
	

	
	$filtroPaginador="";
	if(isset($_GET["cmbFecha"])){
		
		//filtro correo electronico
		$filtroPaginador="txtEmail=".$_GET["txtEmail"];
		$correoElectronico=$_GET["txtEmail"];
		$subPlantilla->assign("VIEW_WORKSHOP_DATE_EMAIL_ADDRESS_SEARCH_VALUE",$correoElectronico);

		
		//filtro nombre
		$filtroPaginador.="&txtNombre=".$_GET["txtNombre"];
		$nombre=$_GET["txtNombre"];
		$subPlantilla->assign("VIEW_WORKSHOP_DATE_NAME_SEARCH_VALUE",$nombre);
		
		
		//filtro numero inscripcion
		$filtroPaginador.="&txtNumeroInscripcion=".$_GET["txtNumeroInscripcion"];
		$numeroInscripcion=$_GET["txtNumeroInscripcion"];
		$subPlantilla->assign("VIEW_WORKSHOP_DATE_INSCRIPTION_NUMBER_SEARCH_VALUE",$numeroInscripcion);

		//Filtro fecha
		$idFechaTaller=$_GET["cmbFecha"];
		$filtroPaginador.="&cmbFecha=".$_GET["cmbFecha"];
		
		//Filtrado asistido
		if(isset($_GET["cmbPagado"])){
			$pagado=$_GET["cmbPagado"];
			$filtroPaginador.="&cmbPagado=".$_GET["cmbPagado"];
		}
		
		if(isset($_GET["cmbAsistido"])){
			//Filtrado asistido
			$asistido=$_GET["cmbAsistido"];
			$filtroPaginador.="&cmbAsistido=".$_GET["cmbAsistido"];
		}
		
		if(isset($_GET["cmbTipoUsuario"])){
			//Filtrado asistido
			$idTipoUsuario=$_GET["cmbTipoUsuario"];
			$filtroPaginador.="&cmbTipoUsuario=".$_GET["cmbTipoUsuario"];
		}


		$filtroPaginador.="&";
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
	
	$resultadoTipoWorkshop=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_taller_fecha_obtener_concreta(".$idFechaTaller.")");
	$datoTipoWorkshop=$db->getData($resultadoTipoWorkshop);
	
	if($datoTipoWorkshop["es_conferencia"]==0){
		$esConferencia=0;
		$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_sp_inscripcion_taller_concreto_listar('".$nombre."','".$correoElectronico."','".$numeroInscripcion."','".$idFechaTaller."',".$pagado.",".$asistido.",".$idTipoUsuario.",'".$campoOrden."','".$direccionOrden."',";


		$subPlantilla->assign("TR_STYLE","class='light'");
	}else{
		$esConferencia=1;
		$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_sp_inscripcion_conferencia_concreto_listar('".$nombre."','".$correoElectronico."','".$numeroInscripcion."','".$idFechaTaller."',".$pagado.",".$asistido.",'".$campoOrden."','".$direccionOrden."',";
		$subPlantilla->assign("TR_STYLE","class='light'");

	}
	$urlActual="main_app.php?section=workshop_date&action=view&id_taller=".$_GET["id_taller"]."&hdnOrden=".$valorDefecto."&hdnRegistros=".$numeroRegistrosPagina."&".$filtroPaginador;


	

	//Paginador
	require "includes/load_paginator.inc.php";

	
	
	//Obtener nombre del workshop en concreto
	$resultadoTaller=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_taller_obtener_concreta(".$_GET["id_taller"].",".$_SESSION["user"]["language_id"].")");
	$datoTaller=$db->getData($resultadoTaller);

	
	
	
	//Pagina actual
	$subPlantilla->assign("PAGINA_ACTUAL",$paginaActual);
	
	$esExcel=false;
	
	if(isset($_GET["excel"]) && $_GET["excel"]==1){
		$esExcel=true;
		$plantillaExcel=new XTemplate("html/excel/index.html");
		$subPlantillaExcel=new XTemplate("html/excel/view_workshop_date.html");
	}else{
	
		$subPlantilla->parse("contenido_principal.boton_cancel_inscripcion");

	}
	
	$resultado=$db->callProcedure($codeProcedure);
	$i=0;
	while($dato=$db->getData($resultado)){
		$nombreTaller=$datoTaller["nombre"];
		if($i%2==0){
			$subPlantilla->assign("TR_STYLE","class='dark'");
		}else{
			$subPlantilla->assign("TR_STYLE","class='light'");
		}

		if($esConferencia==1){
			$vectorTaller["ID"]=$dato["id_inscripcion_conferencia"]."-".$dato["id_inscripcion_conferencia_linea"];
			$subPlantilla->assign("ID_DESTINO","2");
			$subPlantilla->assign("ID_DESTINO_PAGADO","5");
			

			if($dato["observaciones"]!=""){
				$vectorTaller["COMMENT_EXCEL"]=$dato["observaciones"];
				$vectorTaller["COMMENT"]="<a href='main_app.php?section=conference_registered&action=edit&id_inscripcion=".$dato["id_inscripcion_conferencia"]."&id_conferencia=".$dato["id_conferencia"]."' title='".htmlspecialchars($dato["observaciones"])."'><img src='images/comment.png' /></a>";
			}else{
				$vectorTaller["COMMENT"]="";
				$vectorTaller["COMMENT_EXCEL"]="";
			}//end else
		
			
		}else{
			$vectorTaller["ID"]=$dato["id_inscripcion_taller"]."-".$dato["id_inscripcion_taller_linea"];
			$subPlantilla->assign("ID_DESTINO","1");
			$subPlantilla->assign("ID_DESTINO_PAGADO","4");
		}
		

		//Segun seamos(sister association,logueados o no miembors)
		if($dato["id_usuario_web"]!=""){
			$tipoUsuario=STATIC_VIEW_WORKSHOP_DATE_USER_TYPE_MEMBER;
			$vectorTaller["TYPE"]="<img src='images/add_close_user_icon.png' title='".$tipoUsuario."'>";
		}else{
			if($dato["id_asociacion_hermana"]==""){
				$tipoUsuario=STATIC_VIEW_WORKSHOP_DATE_USER_TYPE_NON_MEMBER;
				$vectorTaller["TYPE"]="<img src='images/add_remove_user_icon.png' title='".$tipoUsuario."'>";
			}else{
				$tipoUsuario=STATIC_VIEW_WORKSHOP_DATE_USER_TYPE_SISTER_ASSOCIATION;
				$vectorTaller["TYPE"]="<img src='images/female_user_icon.png' title='".$tipoUsuario."'>";
			}
		}
			
		$vectorTaller["TYPE_NAME"]=$tipoUsuario;
		$vectorTaller["INSCRIPCION_NUMBER"]=$dato["numero_inscripcion"];
		$vectorTaller["NAME"]=$dato["nombre_completo"];
		$vectorTaller["EMAIL"]=$dato["correo_electronico"];
		$vectorTaller["WORKSHOP"]=$datoTaller["nombre"];
		
		if($dato["pagado"]==1){
			$vectorTaller["PAID"]=STATIC_GLOBAL_BUTTON_YES;
		}else if($dato["pagado"]==0){
			$vectorTaller["PAID"]=STATIC_GLOBAL_BUTTON_NO;
			
		}
		
		if($dato["asistido"]==1){
			$vectorTaller["ASSISTED"]=STATIC_GLOBAL_BUTTON_YES;
		}else if($dato["asistido"]==0){
			$vectorTaller["ASSISTED"]=STATIC_GLOBAL_BUTTON_NO;
			
		}
		$vectorTaller["PAID_NUMERIC"]=$dato["pagado"];
		$vectorTaller["ASSISTED_NUMERIC"]=$dato["asistido"];
		
		
		if($esExcel){
			$subPlantillaExcel->assign("WORKSHOP_DATE",$vectorTaller);
			$subPlantillaExcel->parse("contenido_principal.item_taller");
		}else{
			$subPlantilla->assign("WORKSHOP_DATE",$vectorTaller);
			$subPlantilla->parse("contenido_principal.item_taller");
		}

		$i++;
	}
	
	
	//Combo tipo usuario
	$subPlantilla->assign("COMBO_FECHA",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_taller_fecha_obtener_combo(".$_GET["id_taller"].")","cmbFecha","cmbFecha",$idFechaTaller,"fecha_formateada","id_taller_fecha",STATIC_GLOBAL_COMBO_DEFAULT,0,""));
	
	
	$matriz[1]["descripcion"]=STATIC_GLOBAL_BUTTON_YES;
	$matriz[0]["descripcion"]=STATIC_GLOBAL_BUTTON_NO;
	
	
	$subPlantilla->assign("COMBO_ASISTIDO",generalUtils::construirComboMatriz($matriz,"cmbAsistido","cmbAsistido",$asistido,STATIC_GLOBAL_COMBO_DEFAULT,-1,""));
	
	$subPlantilla->assign("COMBO_PAGADO",generalUtils::construirComboMatriz($matriz,"cmbPagado","cmbPagado",$pagado,STATIC_GLOBAL_COMBO_DEFAULT,-1,""));
	
	
	$matriz[3]["descripcion"]=STATIC_VIEW_WORKSHOP_DATE_USER_TYPE_NON_MEMBER;
	$matriz[2]["descripcion"]=STATIC_VIEW_WORKSHOP_DATE_USER_TYPE_SISTER_ASSOCIATION;
	$matriz[1]["descripcion"]=STATIC_VIEW_WORKSHOP_DATE_USER_TYPE_MEMBER;
	$matriz[0]["descripcion"]=STATIC_GLOBAL_COMBO_DEFAULT;
	
	$subPlantilla->assign("COMBO_USER_TYPE",generalUtils::construirComboMatriz($matriz,"cmbTipoUsuario","cmbTipoUsuario",$idTipoUsuario,"",-1,""));
	
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_WORKSHOP_VIEW_WORKSHOP_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_WORKSHOP_VIEW_WORKSHOP_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_WORKSHOP_EDIT_WORKSHOP_LINK."&id_taller=".$_GET["id_taller"];
	$vectorMigas[2]["texto"]=$datoTaller["nombre"];
	$vectorMigas[3]["url"]=STATIC_BREADCUMB_WORKSHOP_DATE_VIEW_WORKSHOP_DATE_LINK."&id_taller=".$_GET["id_taller"];
	$vectorMigas[3]["texto"]=STATIC_BREADCUMB_WORKSHOP_DATE_VIEW_WORKSHOP_DATE_TEXT;

	
	//Id taller
	$subPlantilla->assign("ID_TALLER",$_GET["id_taller"]);
	
	require "includes/load_breadcumb.inc.php";
	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
	
	if(!$esExcel){
		
		//Contruimos plantilla secundaria
		$subPlantilla->parse("contenido_principal");
		
		//Incluimos proceso onload
		$plantilla->parse("contenido_principal.carga_inicial");
		
		//Exportamos plantilla secundaria a la plantilla principal
		$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
		
		//Construimos plantilla principal
		$plantilla->parse("contenido_principal");
		
		//Mostramos plantilla principal por pantalla
		$plantilla->out("contenido_principal");
	}else{
		$fichero="workshop-inscriptions-".generalUtils::toAscii($nombreTaller).".xls";
		header("Content-type: application/vnd.ms-excel");
		//header("Content-Type: application/force-download");
		header("Content-Disposition: attachment; filename=$fichero");
		header("Content-Transfer-Encoding: binary");
		
		//Mostramos excel
		$subPlantillaExcel->parse("contenido_principal");
		$plantillaExcel->assign("CONTENIDO",$subPlantillaExcel->text("contenido_principal"));
		$plantillaExcel->parse("contenido_principal");
		$plantillaExcel->out("contenido_principal");
	}
?>