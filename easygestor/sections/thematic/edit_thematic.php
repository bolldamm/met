<?php
	/**
	 * 
	 * Script que muestra y realiza la edicion de un menu
	 * @Author eData
	 * 
	 */
	
	//Si hemos enviado el formulario...
	if(count($_POST)){
		$idTematica=$_POST["hdnIdTematica"];
		
		if(isset($_POST["hdnIdPadre"]) && is_numeric($_POST["hdnIdPadre"])){
			$parametroPadre="&id_padre=".$_POST["hdnIdPadre"];
		}

		//Guardamos la informacion multidioma del tipo area
		require "language_thematic.php";
		
		
		//Volvemos a listar menu
		if($_POST["hdnVolver"]==0){
			generalUtils::redirigir("main_app.php?section=thematic&action=view".$parametroPadre);
		}else{
			generalUtils::redirigir("main_app.php?section=thematic&action=edit&id_tematica=".$idTematica);
		}
	}

	
	if(!isset($_GET["id_tematica"]) || !is_numeric($_GET["id_tematica"])){
		generalUtils::redirigir("main_app.php?section=thematic&action=view");
	}
	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/thematic/manage_thematic.html");
		
	//Obtenemos bloque multidioma	
	$resultadoIdioma=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_idioma_obtener()");
	$i=0;
	//Generamos contenido multidioma
	while($datoIdioma=$db->getData($resultadoIdioma)){
		//Sacamos la informacion de la tematica en cada idioma
		$resultadoTematica=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_tematica_obtener_concreta(".$_GET["id_tematica"].",".$datoIdioma["id_idioma"].")");
		$datoTematica=$db->getData($resultadoTematica);
		$subPlantilla->assign("IDIOMA_ID",$datoIdioma["id_idioma"]);
		$subPlantilla->assign("IDIOMA_DESCRIPCION",$datoIdioma["nombre"]);
		if($i==0){
			$nombreTematica=$datoTematica["nombre"];
			$idPadre=$datoTematica["id_padre"];
			$subPlantilla->assign("STYLE_DISPLAY","display:");
		}else{
			$subPlantilla->assign("STYLE_DISPLAY","display:none;");
		}
		$subPlantilla->assign("TEMATICA_NOMBRE",$datoTematica["nombre"]);
		$subPlantilla->parse("contenido_principal.item_contenido_idioma");
		$i++;
	}
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_THEMATIC_VIEW_THEMATIC_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_THEMATIC_VIEW_THEMATIC_TEXT;
	
	if($idPadre==""){
		$vectorMigas[2]["url"]=STATIC_BREADCUMB_THEMATIC_EDIT_THEMATIC_LINK."&id_tematica=".$_GET["id_tematica"];
		$vectorMigas[2]["texto"]=$nombreTematica;
		$mostrarTematica=false;
	}else{
		$mostrarTematica=false;
		$subPlantilla->assign("ID_PADRE",$idPadre);
		$subPlantilla->assign("PARAMETRO_ID_PADRE","&id_padre=".$idPadre);
		
		//Obtenemos todos las tematicas padres de la tematica actual, incluido el actual
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_tematica_breadcumb(null,".$_GET["id_tematica"].",".$_SESSION["user"]["language_id"].",'','')");
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
			
			$contadorMigas++;
			
			if($i<$totalVectorIdTematica-1){
				//Subtipo tematica
				$vectorMigas[$contadorMigas]["url"]=STATIC_BREADCUMB_THEMATIC_VIEW_THEMATIC_LINK."&id_padre=".$vectorIdTematica[$i];
				$vectorMigas[$contadorMigas]["texto"]=STATIC_BREADCUMB_THEMATIC_SUB_THEMATIC_TEXT;
				
				$contadorMigas++;
			}
		}
	}
	
	require "includes/load_breadcumb.inc.php";
	
	
	//Parametros hidden formulario
	$subPlantilla->assign("ID_TEMATICA",$_GET["id_tematica"]);
	$subPlantilla->assign("ACTION","edit");

	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
	
	//Incluimos save & clsoe
	$subPlantilla->parse("contenido_principal.item_button_close");
		
	
	//Incluimos proceso onload
	$plantilla->parse("contenido_principal.carga_inicial");
	
	if($mostrarTematica){
		//Subtematica
		$subPlantilla->parse("contenido_principal.item_subtematica");
	}
	
	
	//Contruimos plantilla secundaria
	$subPlantilla->parse("contenido_principal");
	
	//Exportamos plantilla secundaria a la plantilla principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
	//Construimos plantilla principal
	$plantilla->parse("contenido_principal");
	
	//Mostramos plantilla principal por pantalla
	$plantilla->out("contenido_principal");
?>