<?php
	/**
	 * 
	 * Pagina de inicio desde donde se inicia el portal
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */

	require "includes/load_main_components.inc.php";
	
	// Instanciamos la clase Xtemplate con la plantilla base
	$plantilla = new XTemplate("html/index.html");
	
	// Instanciamos la clase Xtemplate con la plantilla que va a contener la información del apartado
	$subPlantilla = new XTemplate("html/directory_admin_list.html");
		
	
	/**
	 * Asignamos el CSS que corresponde a este apartado
	 */
	$plantilla->assign("SECTION_FILE_CSS", "directory_list.css");
	
	require "includes/load_structure.inc.php";
	
	
	if(isset($_GET["filtro"]) && $_GET["filtro"]!=""){
		//El primer campo pertenece a la query y el segndo a la fecha
		$camposFiltro=explode("_",$_GET["filtro"]);
		if($camposFiltro[0]!=""){
			$_GET["c"]=$camposFiltro[0];
		}
		if($camposFiltro[1]!=""){
			$_GET["t"]=$camposFiltro[1];
		}
		if($camposFiltro[2]!=""){
			$_GET["ln"]=$camposFiltro[2];
		}
		if($camposFiltro[3]!=""){
			$_GET["pa"]=$camposFiltro[3];
		}
		if($camposFiltro[4]!=""){
			$_GET["pre"]=$camposFiltro[4];
		}
		if($camposFiltro[5]!=""){
			$_GET["p"]=$camposFiltro[5];
		}
		if($camposFiltro[6]!=""){
			$_GET["na"]=$camposFiltro[6];
		}
		if($camposFiltro[7]!=""){
			$_GET["ci"]=$camposFiltro[7];
		}
		if($camposFiltro[8]!=""){	
			$_GET["sl"]=$camposFiltro[8];
		}
		if($camposFiltro[9]!=""){	
			$_GET["edad"]=$camposFiltro[9];
		}
	}


	/**
	 * Información del buscador
	 */
	$descripcionBuscador = "";
	$urlBusqueda = "";
	$apellido = "";
	$idPais = 0;
	$idActividadProfesional = 0;
	$pagado=-1;
	$preferencia=-1;
	$nacionalidad = "";
	$ciudad = "";
	$idEdad=0;
	$sexo=-1;
	$filtro="#co_#t_#ln_#pa_#pre_#p_#na_#ci_#sl_#edad";
	$esPais=false;
	$esDescripcion=false;
	$esApellido=false;
	$esActividad=false;
	$esPreferencia=false;
	$esPagado=false;
	$esNacionalidad=false;
	$esCiudad=false;
	$esSituacionLaboral=false;
	$esEdad=false;
	$esSexo=true;
	//Descripcion
	if(isset($_GET["t"]) && trim($_GET["t"]) != "" && $_GET["t"] != STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_TEXT_SEARCH) {
		$descripcionBuscador = generalUtils::escaparCadena($_GET["t"]);
		$subPlantilla->assign("QUERY_VALUE", generalUtils::reeamplazarAntiBarras(htmlspecialchars($_GET["t"])));
		if(trim($_GET["t"]) != "") {
			$urlBusqueda .= "&t=".$_GET["t"];
			$esDescripcion=true;
		}
	}else{
		$subPlantilla->assign("QUERY_VALUE", STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_TEXT_SEARCH);
	}
	
	if(isset($_GET["ln"]) && trim($_GET["ln"]) != "" && $_GET["ln"] != STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_LAST_NAME_SEARCH) {
		$apellido = generalUtils::escaparCadena($_GET["ln"]);
		$subPlantilla->assign("LASTNAME_VALUE", generalUtils::reeamplazarAntiBarras(htmlspecialchars($_GET["ln"])));
		if(trim($_GET["ln"]) != "") {
			$urlBusqueda .= "&ln=".$_GET["ln"];
			$esApellido=true;
		}
	}else{
		$subPlantilla->assign("LASTNAME_VALUE", STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_LAST_NAME_SEARCH);
	}
	
	//Pais
	if(isset($_GET["co"]) && is_numeric($_GET["co"]) && $_GET["co"] > 0) {
		$idPais = $_GET["co"];
		$urlBusqueda .= "&co=".$_GET["co"];
		$esPais=true;
	}
	
	//Actividad profesional
	if(isset($_GET["p"]) && is_numeric($_GET["p"]) && $_GET["p"] > 0) {
		$idActividadProfesional = $_GET["p"];
		$urlBusqueda .= "&p=".$_GET["p"];
		$esActividad=true;
	}
	
	//Preferencia
	if(isset($_GET["pre"]) && is_numeric($_GET["pre"]) && $_GET["pre"] > -1) {
		$preferencia = $_GET["pre"];
		$urlBusqueda .= "&pre=".$_GET["pre"];
		$esPreferencia=true;
	}
	
	//Sexo
	if(isset($_GET["genero"]) && is_numeric($_GET["genero"]) && $_GET["genero"] > -1) {
		$sexo = $_GET["genero"];
		$urlBusqueda .= "&genero=".$_GET["genero"];
		$esSexo=true;
	}
	
	//Edad
	if(isset($_GET["edad"]) && is_numeric($_GET["edad"]) && $_GET["edad"] > 0) {
		$idEdad = $_GET["edad"];
		$urlBusqueda .= "&edad=".$_GET["edad"];
		$esEdad=true;
	}
	
	//Pagado
	if(isset($_GET["pa"]) && is_numeric($_GET["pa"]) && $_GET["pa"] > -1) {
		$pagado = $_GET["pa"];
		$urlBusqueda .= "&pa=".$_GET["pa"];
		$esPagado=true;
	}
	
	if(isset($_GET["na"]) && trim($_GET["na"]) != "" && $_GET["na"] != STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_NATIONALITY) {
		$nacionalidad = generalUtils::escaparCadena($_GET["na"]);
		$subPlantilla->assign("NATIONALITY_VALUE", generalUtils::reeamplazarAntiBarras(htmlspecialchars($_GET["na"])));
		if(trim($_GET["na"]) != "") {
			$urlBusqueda .= "&na=".$_GET["na"];
			$esNacionalidad=true;
		}
	}else{
		$subPlantilla->assign("NATIONALITY_VALUE", STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_NATIONALITY);
	}
	
	if(isset($_GET["ci"]) && trim($_GET["ci"]) != "" && $_GET["ci"] != STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_CITY) {
		$ciudad = generalUtils::escaparCadena($_GET["ci"]);
		$subPlantilla->assign("CITY_VALUE", generalUtils::reeamplazarAntiBarras(htmlspecialchars($_GET["ci"])));
		if(trim($_GET["ci"]) != "") {
			$urlBusqueda .= "&ci=".$_GET["ci"];
			$esCiudad=true;
		}
	}else{
		$subPlantilla->assign("CITY_VALUE", STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_CITY);
	}
	
	$totalSituacionLaboral=0;
	if(isset($_GET["sl"])) {
			//Listamos situaciones laborales
			$resultadoSituacionLaboral = $db->callProcedure("CALL ed_sp_web_situacion_laboral_obtener(".$_SESSION["id_idioma"].")");
			$cadenaSituacionLaboral = "";
			while($datoSituacionLaboral = $db->getData($resultadoSituacionLaboral)){
				if(isset($_GET["chkSituacion_".$datoSituacionLaboral["id_situacion_laboral"]])){
					$cadenaSituacionLaboral.=$datoSituacionLaboral["id_situacion_laboral"].",";
				}
			}
			
			$cadenaSituacionLaboral=substr($cadenaSituacionLaboral, 0,strlen($cadenaSituacionLaboral)-1);
			if($cadenaSituacionLaboral!=""){
				$_GET["sl"]=$cadenaSituacionLaboral;
			}else{
				//Si no hay situacion seleccionada y no estamos con paginador,entonces reseteamos
				if(!isset($_GET["pagina"])){
					$_GET["sl"]="";
				}
			}


		if(trim($_GET["sl"]) != "") {
			$urlBusqueda .= "&sl=".$_GET["sl"];
			$esSituacionLaboral=true;
			$subPlantilla->assign("SITUACION_LABORAL_VALUE", $_GET["sl"]);
			$vectorSituacionLaboral=explode(",",$_GET["sl"]);
			$totalSituacionLaboral=count($vectorSituacionLaboral);
			$situacionLaboral="";
			$situacionLaboral=$_GET["sl"];
			$i=0;

			/*if($totalSituacionLaboral>0){
				$situacionLaboral="WHERE ";
			}
			while($i<$totalSituacionLaboral){
				$situacionLaboral.="id_situacion_laboral=".$vectorSituacionLaboral[$i];
				if($i<$totalSituacionLaboral-1){
					$situacionLaboral.=" AND ";
				}
				
				$i++;
			}		*/	
		}
	}else{
		$subPlantilla->assign("SITUACION_LABORAL_VALUE", "");
		$situacionLaboral="";
	}

	if($situacionLaboral==""){
		$situacionLaboral='""';
	}

	
	//$esDescripcion filtro por valores reales
	if($esDescripcion || $esApellido || $esPais || $esActividad || $esPreferencia || $esPagado || $esNacionalidad || $esCiudad || $esSituacionLaboral || $esEdad || $esSexo){
		$filtro=str_replace("#co",$_GET["co"],$filtro);
		$filtro=str_replace("#t",$descripcionBuscador,$filtro);
		$filtro=str_replace("#ln",$apellido,$filtro);
		$filtro=str_replace("#pa",$_GET["pa"],$filtro);
		$filtro=str_replace("#pre",$_GET["pre"],$filtro);
		$filtro=str_replace("#p",$_GET["p"],$filtro);
		$filtro=str_replace("#na",$nacionalidad,$filtro);
		$filtro=str_replace("#ci",$ciudad,$filtro);
		$filtro=str_replace("#sl",$_GET["sl"],$filtro);
		$filtro=str_replace("#edad",$_GET["edad"],$filtro);
	}else{
		$filtro="";
	}
	
	//Obtenemos la url asociada a noticias
	$resultadoMenuSeo=$db->callProcedure("CALL ed_sp_web_menu_seo_obtener(".$idMenu.",".$_SESSION["id_idioma"].")");
	$datoMenuSeo=$db->getData($resultadoMenuSeo);
	$vectorAtributosMenu["idioma"]=$_SESSION["siglas"];
	$vectorAtributosMenu["id_menu"]=$idMenu;
	$vectorAtributosMenu["seo_url"]=$datoMenuSeo["seo_url"];
	$urlActualAux=generalUtils::generarUrlAmigableMenu($vectorAtributosMenu);
	
	$subPlantilla->assign("CONTENIDO_DESCRIPCION",$datoMenuSeo["descripcion"]);


	//Listamos los miembros del directorio;
	$codeProcedure = "CALL ed_sp_web_usuario_web_admin_obtener_listado('".$descripcionBuscador."','".$apellido."','".$nacionalidad."', '".$ciudad."' , ".$idPais.", ".$idActividadProfesional.", ".$preferencia.", ".$idEdad.",".$sexo.", ".$pagado.",'".$situacionLaboral."',".$totalSituacionLaboral.", ";
	$totalItemsPagina = 30;
	$totalPaginasMostrar = 8;

	
	if($filtro!=""){
		$urlBusqueda="-".$filtro;
	}

	$exExcel=false;
	if(isset($_GET["hdnExcel"]) && $_GET["hdnExcel"]==1){
		$esExcel=true;
		//Plantilla principal excel
		$plantillaExcel=new XTemplate("html/excel/index.html");
		
		//Subplantilla excel
		$subPlantillaExcel=new XTemplate("html/excel/excel_directory_admin_list.html");
	}
	
	$urlActual= $urlActualAux."-";
	//$urlActual = "directory_list.php.php?menu=".$idMenu.$urlBusqueda."&";
	require "includes/load_paginator.php";


	$resultMiembros = $db->callProcedure($codeProcedure);
	$cont = 1;
	$totalMiembros = $db->getNumberRows($resultMiembros);
	

	
	if($db->getNumberRows($resultMiembros) > 0) {
		while($dataMiembro = $db->getData($resultMiembros)) {
			$imagen = "files/members/thumb/".(($dataMiembro["imagen"] == "") ? "default.jpg" : $dataMiembro["imagen"]);
			$subPlantilla->assign("ITEM_MIEMBRO_NOMBRE", $dataMiembro["nombre"].(($dataMiembro["apellidos"] == "") ? "" : " ".$dataMiembro["apellidos"]));
			$subPlantilla->assign("ITEM_MIEMBRO_IMAGEN", $imagen);
			
			$subPlantilla->assign("ITEM_MIEMBRO_MAIL", $dataMiembro["correo_electronico"]);

			
			
			//Plantilla excel
			if($esExcel){
				$subPlantillaExcel->assign("DIRECTORY_MEMBER_EXCEL_NAME_VALUE",$dataMiembro["nombre"]." ".$dataMiembro["apellidos"]);
				$subPlantillaExcel->assign("DIRECTORY_MEMBER_EXCEL_EMAIL_VALUE",$dataMiembro["correo_electronico"]);
				$subPlantillaExcel->assign("DIRECTORY_MEMBER_EXCEL_COUNTRY_VALUE",$dataMiembro["pais"]);
				$subPlantillaExcel->assign("DIRECTORY_MEMBER_EXCEL_PROFESSION_VALUE","");
				$subPlantillaExcel->assign("DIRECTORY_MEMBER_EXCEL_WORK_SITUATION_VALUE","");
				$subPlantillaExcel->assign("DIRECTORY_MEMBER_EXCEL_AGE_VALUE",$dataMiembro["edad"]);
				
				if($dataMiembro["genero"]==0){
					$subPlantillaExcel->assign("DIRECTORY_MEMBER_EXCEL_SEX_VALUE",STATIC_FORM_MEMBERSHIP_FEMALE);
				}else{
					$subPlantillaExcel->assign("DIRECTORY_MEMBER_EXCEL_SEX_VALUE",STATIC_FORM_MEMBERSHIP_MALE);
				}
				$subPlantillaExcel->parse("contenido_principal.item_member");
			}

						
			
			//Url detalle
			$vectorAtributosDetalle["idioma"]=$_SESSION["siglas"];
			$vectorAtributosDetalle["id_menu"]=$_GET["menu"];
			$vectorAtributosDetalle["id_detalle"]=$dataMiembro["id_usuario_web"];
			$vectorAtributosDetalle["seo_url"]=$dataMiembro["nombre"]." ".$dataMiembro["apellidos"];
			$subPlantilla->assign("ITEM_MIEMBRO_URL", generalUtils::generarUrlAmigableDetalle($vectorAtributosDetalle));
		

			
			$subPlantilla->parse("contenido_principal.listado_miembros.item_fila.item_miembro");
			
			if($cont % 2 == 0 || $cont==$totalMiembros){
				$subPlantilla->parse("contenido_principal.listado_miembros.item_fila");
			}
			
			$cont++;
			
		}
		//Total miembros
		$subPlantilla->assign("TOTAL_MIEMBROS_ENCONTRADOS",$totalRegistros);
		
		$subPlantilla->parse("contenido_principal.listado_miembros");
	}else{
		$subPlantilla->parse("contenido_principal.no_miembros");
	}

	
	
	$subPlantilla->assign("MENU_ID", $idMenu);
	
	//Combo de paises
	$subPlantilla->assign("COMBO_PAISES", generalUtils::construirCombo($db, "CALL ed_sp_web_usuario_web_admin_pais_obtener_combo()", "co", "cmbPais", $idPais, "nombre_original", "id_pais", STATIC_FORM_MEMBERSHIP_INSTIT_COUNTRY, 0, "class='inputText left' style='width:272px;'"));
	
	//Combo actividad profesional
	$subPlantilla->assign("COMBO_ACTIVIDAD_PROFESIONAL", generalUtils::construirCombo($db, "CALL ed_sp_web_usuario_web_actividad_profesional_obtener_combo(".$_SESSION["id_idioma"].")", "p", "cmbActividadProfesional", $idActividadProfesional, "descripcion", "id_actividad_profesional", STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_PROFESSIONAL_ACTIVITY, 0, "class='inputText left' style='width:272px;'"));

	//Combo edad
	$subPlantilla->assign("COMBO_EDAD",generalUtils::construirCombo($db, "CALL ed_sp_web_edad_usuario_web_admin_obtener_combo(".$_SESSION["id_idioma"].")", "edad", "cmbEdad", $idEdad, "nombre", "id_edad_usuario_web", STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_AGE, 0, "class='inputText left' style='width:272px;'")); 
	
	
	//Combo sexo
	$matriz[1]["descripcion"]=STATIC_FORM_MEMBERSHIP_MALE;
	$matriz[0]["descripcion"]=STATIC_FORM_MEMBERSHIP_FEMALE;
	
	$subPlantilla->assign("COMBO_SEXO",generalUtils::construirComboMatriz($matriz,"genero","cmbGenero",$sexo,STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_SEX,-1,"class='inputText left' style='width:272px;'"));
	
	/**define("STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_AGE", "Age");
	define("STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_SEX", "Sex");**/
	
	
	//Combo pagado
	unset($matriz);
	$matriz[2]["descripcion"]=STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_PENDING_PAYMENT;
	$matriz[1]["descripcion"]=STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_PAYED;
	$matriz[0]["descripcion"]=STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_NO_PAYED;
	
	$subPlantilla->assign("COMBO_PAGADO",generalUtils::construirComboMatriz($matriz,"pa","cmbPagado",$pagado,STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_STATUS_PAYMENT,-1,"class='inputText left' style='width:272px;'"));
	
	unset($matriz);
	$matriz[1]["descripcion"]=STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_PREFERENCE_PUBLIC;
	$matriz[0]["descripcion"]=STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_PREFERENCE_MEMBERS_ONLY;
	
	$subPlantilla->assign("COMBO_PREFERENCIA",generalUtils::construirComboMatriz($matriz,"pre","cmbPreferencia",$preferencia,STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_PREFERENCE,-1,"class='inputText left' style='width:272px;'"));
		
	
	//Listamos situaciones laborales
	$resultadoSituacionLaboral = $db->callProcedure("CALL ed_sp_web_situacion_laboral_obtener(".$_SESSION["id_idioma"].")");
	
	if($esSituacionLaboral){
		$vectorSituacionLaboral=explode(",",$_GET["sl"]);
		
	}
	while($dataSituacionLaboral = $db->getData($resultadoSituacionLaboral)) {
		$subPlantilla->assign("SITUACION_LABORAL_ID", $dataSituacionLaboral["id_situacion_laboral"]);
		$subPlantilla->assign("SITUACION_LABORAL_NOMBRE", $dataSituacionLaboral["nombre"]);
		
		if($esSituacionLaboral && in_array($dataSituacionLaboral["id_situacion_laboral"], $vectorSituacionLaboral)){
			$subPlantilla->assign("CHECKED_SITUACION","checked");
		}else{
			$subPlantilla->assign("CHECKED_SITUACION","");
		}
		
		
		$subPlantilla->parse("contenido_principal.item_situacion_laboral");
	}
	
	//Cargamos el breadcrumb
	require "includes/load_breadcrumb.inc.php";
	
	//Cargamos los menus hijos del lateral derecho
	require "includes/load_menu_left.inc.php";
		
	//Cargamos el slider en caso de que tenga imagenes
	require "includes/load_slider.inc.php";
	
	$subPlantilla->parse("contenido_principal");
	
	/**
	 * Realizamos todos los parse realcionados con este apartado
	 */
	$plantilla->parse("contenido_principal.css_form");
	$plantilla->parse("contenido_principal.control_superior");
	$plantilla->parse("contenido_principal.bloque_ready");
	
	
	//Exportamos plantilla secundaria a la principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
	//Mostramos el excel por pantalla
	if($esExcel){
		$subPlantillaExcel->parse("contenido_principal");
		$plantillaExcel->assign("CONTENIDO",$subPlantillaExcel->text("contenido_principal"));

        //Parse inner page content with lefthand menu
        $plantilla->parse("contenido_principal.menu_left");

        $plantillaExcel->parse("contenido_principal");
		
		$fichero="members.xls";
	
		header("Content-type: application/vnd.ms-excel");
		//header("Content-Type: application/force-download");
		header("Content-Disposition: attachment; filename=$fichero");
		header("Content-Transfer-Encoding: binary");
		
		$plantillaExcel->out("contenido_principal");
	}else{
		//Parseamos y sacamos informacion por pantalla
		$plantilla->parse("contenido_principal");
		$plantilla->out("contenido_principal");
	}
?>