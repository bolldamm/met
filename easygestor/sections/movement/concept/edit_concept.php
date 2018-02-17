<?php
	/**
	 * 
	 * Script que muestra y realiza la edicion de un menu
	 * @Author eData
	 * 
	 */
	
	//Si hemos enviado el formulario...
	if(count($_POST)){
		$idConcepto=$_POST["hdnIdConcepto"];

		if(isset($_POST["hdnVisible"])){
			$visible=$_POST["hdnVisible"];
		}else{
			$visible=0;
		}
		
		if(isset($_POST["hdnIdPadre"]) && is_numeric($_POST["hdnIdPadre"])){			
			$parametroPadre="&id_padre=".$_POST["hdnIdPadre"];
		}else{
			$parametroPadre="";
		}
		
		
		$db->startTransaction();
		
		//Insercion menu
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_concepto_movimiento_editar(".$idConcepto.",".$visible.")");		

		//Guardamos la informacion multidioma del tipo area
		require "language_concept.php";
		
		$db->endTransaction();
		
		//Volvemos a listar menu
		if($_POST["hdnVolver"]==0){
			generalUtils::redirigir("main_app.php?section=concept&action=view".$parametroPadre);
		}else{
			generalUtils::redirigir("main_app.php?section=concept&action=edit&id_concepto=".$_GET["id_concepto"]);
		}
	}

	
	if(!isset($_GET["id_concepto"]) || !is_numeric($_GET["id_concepto"])){
		generalUtils::redirigir("main_app.php?section=movement&action=view");
	}
	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/movement/concept/manage_concept.html");
		
	//Obtenemos bloque multidioma	
	$resultadoIdioma=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_idioma_obtener()");
	$i=0;
	//Generamos contenido multidioma
	while($datoIdioma=$db->getData($resultadoIdioma)){
		//Sacamos la informacion de la tematica en cada idioma
		$resultadoConcepto=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_concepto_movimiento_obtener_concreta(".$_GET["id_concepto"].",".$datoIdioma["id_idioma"].")");
		$datoConcepto=$db->getData($resultadoConcepto);
		$subPlantilla->assign("IDIOMA_ID",$datoIdioma["id_idioma"]);
		$subPlantilla->assign("IDIOMA_DESCRIPCION",$datoIdioma["nombre"]);
		if($i==0){
			$nombreConcepto=$datoConcepto["nombre"];
			$idPadre=$datoConcepto["id_padre"];
			$subPlantilla->assign("STYLE_DISPLAY","display:");
			
			//La primera vez, miramos si la noticia esta activo o no
			if($datoConcepto["activo"]==1){
				$subPlantilla->assign("WEB_VISIBLE_CLASE","checked");
			}else{
				$subPlantilla->assign("WEB_VISIBLE_CLASE","unChecked");
			}
			$subPlantilla->assign("CONCEPTO_WEB_VISIBLE",$datoConcepto["activo"]);
			
		}else{
			$subPlantilla->assign("STYLE_DISPLAY","display:none;");
		}
		$subPlantilla->assign("CONCEPTO_NOMBRE",$datoConcepto["nombre"]);
		$subPlantilla->parse("contenido_principal.item_contenido_idioma");
		$i++;
	}
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_MOVEMENT_VIEW_MOVEMENT_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_MOVEMENT_VIEW_MOVEMENT_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_CONCEPT_VIEW_CONCEPT_LINK;
	$vectorMigas[2]["texto"]=STATIC_BREADCUMB_CONCEPT_VIEW_CONCEPT_TEXT;
	
	
	if($idPadre==""){
		$vectorMigas[3]["url"]=STATIC_BREADCUMB_CONCEPT_EDIT_CONCEPT_LINK."&id_concepto=".$_GET["id_concepto"];
		$vectorMigas[3]["texto"]=$nombreConcepto;
		$mostrarConcepto=true;	
	}else{
		$mostrarConcepto=false;
		$subPlantilla->assign("ID_PADRE",$idPadre);
		$subPlantilla->assign("PARAMETRO_ID_PADRE","&id_padre=".$idPadre);
		
		//Obtenemos todos las tematicas padres de la tematica actual, incluido el actual
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_concepto_movimiento_breadcumb(null,".$_GET["id_concepto"].",".$_SESSION["user"]["language_id"].",'','')");
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
			
			$contadorMigas++;
			
			if($i<$totalVectorIdConcepto-1){
				//Subtipo tematica
				$vectorMigas[$contadorMigas]["url"]=STATIC_BREADCUMB_CONCEPT_VIEW_CONCEPT_LINK."&id_padre=".$vectorIdConcepto[$i];
				$vectorMigas[$contadorMigas]["texto"]=STATIC_BREADCUMB_CONCEPT_SUBCONCEPT_TEXT;
				
				$contadorMigas++;
			}
		}
	}
	
	
	require "includes/load_breadcumb.inc.php";
	
	
	//Parametros hidden formulario
	$subPlantilla->assign("ID_CONCEPTO",$_GET["id_concepto"]);
	$subPlantilla->assign("ACTION","edit");
	
	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
	$subPlantilla->parse("contenido_principal.item_button_close");
		
	
	//Incluimos proceso onload
	$plantilla->parse("contenido_principal.carga_inicial");
	
	
	if($mostrarConcepto){
		//Subtematica
		$subPlantilla->parse("contenido_principal.item_subconcepto");
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