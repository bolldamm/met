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
	$subPlantilla=new XTemplate("html/sections/member/view_member.html");
	
	$mostrarPaginador=true;
	$valorDefecto=0;
	//Edited by Stephen on 10-01-2016
        /*$campoOrden="tipo_usuario";*/
	$campoOrden="id_usuario_web";
	$direccionOrden="ASC";
	$numeroRegistrosPagina=DEFAULT_FILTER_NUMBER;

	/**
	 * 
	 * $matrizOrden, la primera dimension de la matriz nos es el indice numerico, que es el que se usara como value del combobox.
	 * la parte asociativa "descripcion" nos indica la palabra que vera el usuario del gestor y la parte valor es el campo de la base de datos
	 * 
	 */
	$matrizOrden[0]["descripcion"]=STATIC_ORDER_MEMBER_TYPE_MEMBER_FIELD;
	$matrizOrden[0]["valor"]="tipo_usuario";
	$matrizOrden[1]["descripcion"]=STATIC_ORDER_MEMBER_MODALITY_MEMBER_FIELD;
	$matrizOrden[1]["valor"]="modalidad_usuario";
	$matrizOrden[2]["descripcion"]=STATIC_ORDER_MEMBER_EMAIL_ADDRESS_FIELD;
	$matrizOrden[2]["valor"]="correo_electronico";
	$matrizOrden[3]["descripcion"]=STATIC_ORDER_MEMBER_NAME_FIELD;
	$matrizOrden[3]["valor"]="nombre_completo";
	$matrizOrden[4]["descripcion"]=STATIC_ORDER_MEMBER_ID_FIELD;
	$matrizOrden[4]["valor"]="id_usuario_web";
	

	
	
	//Gestion del campo de orden y filtro de numero de registros
	$campoOrdenDefecto="";
	require "includes/load_filter_list.inc.php";

	
	if($campoOrden=="id_usuario_web"){
		$direccionOrden="DESC";
	}
	
	//Buscador
	$idModalidadUsuario=0;
	$idSituacionAdicional=0;
	$pagado=1;
	$idTipoUsuario=0;
	$correoElectronico="";
	$nombre="";
	$fecha=0;
	$nominee=0;
	$filtroPaginador="";
	if(isset($_GET["cmbModalidadUsuario"])){
		$idModalidadUsuario=$_GET["cmbModalidadUsuario"];
		$idTipoUsuario=$_GET["cmbTipoUsuario"];


		$filtroPaginador="cmbModalidadUsuario=".$idModalidadUsuario;
		$filtroPaginador.="&cmbTipoUsuario=".$idTipoUsuario;

		//filtro correo electronico
		if($_GET["txtEmail"]!=""){
			$correoElectronico=$_GET["txtEmail"];
			$subPlantilla->assign("VIEW_MEMBER_EMAIL_ADDRESS_SEARCH_VALUE",$correoElectronico);
			$filtroPaginador.="&txtEmail=".$_GET["txtEmail"];
		}
		
		//filtro nombre
		define("VIEW_MEMBER_NAME_SEARCH_VALUE", "");
        if($_GET["txtNombre"]!=""){
			$nombre=$_GET["txtNombre"];
			$subPlantilla->assign("VIEW_MEMBER_NAME_SEARCH_VALUE",$nombre);
			$filtroPaginador.="&txtNombre=".$_GET["txtNombre"];
		}
		
		//filtro pagado
		if(isset($_GET["cmbPagado"]) && $_GET["cmbPagado"]!="-1"){
			$pagado=$_GET["cmbPagado"];
			$filtroPaginador.="&cmbPagado=".$_GET["cmbPagado"];
		}
		
		//filtro año
		if(isset($_GET["cmbYear"]) && $_GET["cmbYear"]!="-1"){
			$fecha=$_GET["cmbYear"];
			$filtroPaginador.="&cmbYear=".$_GET["cmbYear"];
		}
		
		//filtro situacion adicional
		if(isset($_GET["cmbSituacionAdicional"]) && $_GET["cmbSituacionAdicional"]!="0"){
			$idSituacionAdicional=$_GET["cmbSituacionAdicional"];
			$filtroPaginador.="&cmbSituacionAdicional=".$_GET["cmbSituacionAdicional"];
		}

      	//filtro nominee
		if(isset($_GET["chkNominee"])){
			$nominee='1';
			$filtroPaginador.="&chkNominee=".$_GET["chkNominee"];
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

	
	$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_sp_usuario_web_listar(".$idModalidadUsuario.",".$idTipoUsuario.",".$idSituacionAdicional.",".$nominee.",'".generalUtils::escaparCadena($correoElectronico)."','".generalUtils::escaparCadena($nombre)."',".$_SESSION["user"]["language_id"].",".$pagado.",".$fecha.",'".$campoOrden."','".$direccionOrden."',";
	$urlActual="main_app.php?section=member&action=view&hdnOrden=".$valorDefecto."&hdnRegistros=".$numeroRegistrosPagina."&".$filtroPaginador;
		


	//Paginador
	if(isset($_GET["excel"]) && $_GET["excel"]==1){
		$numeroRegistrosPagina=-1;
	}
	require "includes/load_paginator.inc.php";
	
	$subPlantilla->assign("MEMBERS_FOUND_VALUE",$totalRegistros);
	
	//Exportamos a excel utilizando la clase phpxecel
	if(isset($_GET["excel"]) && $_GET["excel"]==1){
		require "classes/excel/PHPExcel.php";
		
		//Declaramos excel
		$fichero="met_members.xls";
		$objPHPExcel = new PHPExcel();
		
		$resultado=$db->callProcedure($codeProcedure);
		$objPHPExcel->getActiveSheet()->setCellValue("A1",STATIC_VIEW_MEMBER_TYPE_MEMBER);
		$objPHPExcel->getActiveSheet()->setCellValue("B1",STATIC_VIEW_MEMBER_LASTNAME);
		$objPHPExcel->getActiveSheet()->setCellValue("C1",STATIC_VIEW_MEMBER_NAME);
		$objPHPExcel->getActiveSheet()->setCellValue("D1",STATIC_VIEW_MEMBER_COUNTRY);
		$filaInicial=2;
		
		$objPHPExcel->getActiveSheet()->getStyle("A1:D1")->getFont()->setBold(true);
		
		while($dato=$db->getData($resultado)){
			$objPHPExcel->getActiveSheet()->setCellValue("A".$filaInicial, $dato["correo_electronico"]);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$filaInicial, $dato["apellidos"]);
			$objPHPExcel->getActiveSheet()->setCellValue("C".$filaInicial, $dato["nombre"]);
			$objPHPExcel->getActiveSheet()->setCellValue("D".$filaInicial, $dato["pais"]);
			$filaInicial++;
		}
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment;filename=".$fichero);
		header("Cache-Control: max-age=0");
						
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');

		
		exit();
	}
	
	
	//Pagina actual
	$subPlantilla->assign("PAGINA_ACTUAL",$paginaActual);
	
	
	//Si esta seleccionado pagado... mostramos el combo year
	if($pagado==1){
		$subPlantilla->assign("DISPLAY_YEAR","");
	}else{
		$subPlantilla->assign("DISPLAY_YEAR","style='display:none;'");
	}

	$esExcel=false;
	
	if(isset($_GET["excel"]) && $_GET["excel"]==1){
		$esExcel=true;
		$plantillaExcel=new XTemplate("html/excel/index.html");
		$subPlantillaExcel=new XTemplate("html/excel/view_member.html");
	}
	
	$resultado=$db->callProcedure($codeProcedure);
	$i=0;

	while($dato=$db->getData($resultado)){
		if($i%2==0){
			$subPlantilla->assign("TR_STYLE","class='dark'");
		}else{
			$subPlantilla->assign("TR_STYLE","class='light'");
		}
		
		$vectorMiembro["ID"]=$dato["id_usuario_web"];
		$vectorMiembro["TYPE"]=$dato["tipo_usuario"];
		$vectorMiembro["MODALITY"]=$dato["modalidad_usuario"];
		$vectorMiembro["EMAIL_ADDRESS"]=$dato["correo_electronico"];
		$vectorMiembro["NAME"]=$dato["nombre_completo"];
		

		if($dato["observaciones"]!=""){
			$vectorMiembro["COMMENT_EXCEL"]=$dato["observaciones"];
			$vectorMiembro["COMMENT"]="<a href='main_app.php?section=conference_registered&action=edit&id_inscripcion=".$dato["id_inscripcion_conferencia"]."&id_conferencia=".$dato["id_conferencia"]."' title='".htmlspecialchars($dato["observaciones"])."'><img src='images/comment.png' /></a>";
		}else{
			$vectorMiembro["COMMENT"]="";
			$vectorMiembro["COMMENT_EXCEL"]="";
		}//end else
		
		if($esExcel){
			$subPlantillaExcel->assign("MEMBER",$vectorMiembro);
			$subPlantillaExcel->parse("contenido_principal.item_miembro");
		}else{
			$subPlantilla->assign("MEMBER",$vectorMiembro);
			$subPlantilla->parse("contenido_principal.item_miembro");
		}
		$i++;
	}
	
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_MEMBER_VIEW_MEMBER_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_MEMBER_VIEW_MEMBER_TEXT;	
	
	
	//Combo modalidad usuario
	$subPlantilla->assign("COMBO_MODALIDAD_USUARIO",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_modalidad_usuario_buscador_obtener_combo(".$_SESSION["user"]["language_id"].")","cmbModalidadUsuario","cmbModalidadUsuario",$idModalidadUsuario,"nombre","id_modalidad_usuario_web",STATIC_GLOBAL_COMBO_DEFAULT,0,""));

	//Combo tipo usuario
	$subPlantilla->assign("COMBO_TIPO_USUARIO",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_tipo_usuario_web_buscador_obtener_combo(".$_SESSION["user"]["language_id"].")","cmbTipoUsuario","cmbTipoUsuario",$idTipoUsuario,"nombre","id_tipo_usuario_web",STATIC_GLOBAL_COMBO_DEFAULT,0,""));
	
	//Combo situacion adicional
	$subPlantilla->assign("COMBO_SITUACION_ADICIONAL", generalUtils::construirCombo($db, "CALL ".OBJECT_DB_ACRONYM."_sp_situacion_adicional_obtener_combo(".$_SESSION["user"]["language_id"].")", "cmbSituacionAdicional", "cmbSituacionAdicional", $idSituacionAdicional, "nombre", "id_situacion_adicional", STATIC_GLOBAL_COMBO_DEFAULT, 0, "" ));
	
//Combo pagado
	$matriz[2]["descripcion"]=STATIC_VIEW_MEMBER_PENDING_PAYMENT_SEARCH;
	$matriz[1]["descripcion"]=STATIC_VIEW_MEMBER_PAYED_SEARCH;
	$matriz[0]["descripcion"]=STATIC_VIEW_MEMBER_NO_PAYED_SEARCH;
	
	
	$subPlantilla->assign("COMBO_PAGADO",generalUtils::construirComboMatriz($matriz,"cmbPagado","cmbPagado",$pagado,STATIC_GLOBAL_COMBO_DEFAULT,-1,"onchange='tratarPagado(this)'"));
	
	
	//Combo year
	$fechaActual=date("Y");
	$matrizYear[0]["descripcion"]=$fechaActual;
	$matrizYear[1]["descripcion"]=$fechaActual-1;
	$matrizYear[2]["descripcion"]=$fechaActual-2;
	
	
	$subPlantilla->assign("COMBO_ANYO",generalUtils::construirComboMatriz($matrizYear,"cmbYear","cmbYear",$fecha,STATIC_GLOBAL_COMBO_DEFAULT,-1,""));
	

	require "includes/load_breadcumb.inc.php";
	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
	
	if(!$esExcel){
		$plantilla->parse("contenido_principal.carga_inicial");
		
		//Contruimos plantilla secundaria
		$subPlantilla->parse("contenido_principal");
		
		//Exportamos plantilla secundaria a la plantilla principal
		$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
		
		//Construimos plantilla principal
		$plantilla->parse("contenido_principal");
		
		//Mostramos plantilla principal por pantalla
		$plantilla->out("contenido_principal");
	}else{
		$fichero="miembros.xls";
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