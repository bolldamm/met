<?php
	/**
	 * 
	 * Script que muestra y realiza la creacion de un menu
	 * @Author eData
	 * 
	 */


	//Si hemos enviado el formulario...
	if(count($_POST)){
		//Datos generales del tipo
		$idPadre="null";
		$parametroPadre="";
		if(isset($_POST["hdnIdPadre"]) && is_numeric($_POST["hdnIdPadre"])){
			$idPadre=$_POST["hdnIdPadre"];
			$parametroPadre="&id_padre=".$_POST["hdnIdPadre"];
		}
		
		if(isset($_POST["hdnVisible"])){
			$visible=$_POST["hdnVisible"];
		}else{
			$visible=0;
		}
		
		//Iniciar transaccion
		$db->startTransaction();
		//Insercion menu
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_concepto_movimiento_insertar(".$idPadre.",".$visible.")");		
		$dato=$db->getData($resultado);
		$idConcepto=$dato["id_concepto_movimiento"];
		

		//Guardamos la informacion multidioma del menu
		require "language_concept.php";

			
		//Cerrar transaccion
		$db->endTransaction();
		
		//Volvemos a listar menu
		generalUtils::redirigir("main_app.php?section=concept&action=view".$parametroPadre);
	}

	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/movement/concept/manage_concept.html");
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_MOVEMENT_VIEW_MOVEMENT_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_MOVEMENT_VIEW_MOVEMENT_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_CONCEPT_VIEW_CONCEPT_LINK;
	$vectorMigas[2]["texto"]=STATIC_BREADCUMB_CONCEPT_VIEW_CONCEPT_TEXT;
	
	$posicionReferencia=3;
	$parametroPadre="";
	
	//Id padre
	if(isset($_GET["id_padre"]) && $_GET["id_padre"]!=-1){
		$subPlantilla->assign("PARAMETRO_ID_PADRE","&id_padre=".$_GET["id_padre"]);
		$subPlantilla->assign("ID_PADRE",$_GET["id_padre"]);
		
		//Obtenemos todos las tematicas padres de la actual, incluido el actual
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_concepto_movimiento_breadcumb(null,".$_GET["id_padre"].",".$_SESSION["user"]["language_id"].",'','')");
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
			$vectorMigas[$contadorMigas]["url"]=STATIC_BREADCUMB_CONCEPT_EDIT_CONCEPT_LINK."&id_tematica=".$vectorIdConcepto[$i];
			$vectorMigas[$contadorMigas]["texto"]=$vectorNombreConcepto[$i];
			
			$contadorMigas++;
			
			if($i<$totalVectorIdConcepto){
				//SubTipo area
				$vectorMigas[$contadorMigas]["url"]=STATIC_BREADCUMB_CONCEPT_VIEW_CONCEPT_LINK."&id_padre=".$vectorIdConcepto[$i];
				$vectorMigas[$contadorMigas]["texto"]=STATIC_BREADCUMB_CONCEPT_SUBCONCEPT_TEXT;
				
				$contadorMigas++;
			}
		}
		
		//Establecemos nueva posicion en el vector para las migas
		$posicionReferencia=$contadorMigas;
		$parametroPadre="&id_padre=".$_GET["id_padre"];
	}
	
	
	$vectorMigas[$posicionReferencia]["url"]=STATIC_BREADCUMB_CONCEPT_CREATE_CONCEPT_LINK.$parametroPadre;
	$vectorMigas[$posicionReferencia]["texto"]=STATIC_BREADCUMB_CONCEPT_CREATE_CONCEPT_TEXT;
	
	require "includes/load_breadcumb.inc.php";
	
	//Obtenemos bloque multidioma	
	$resultadoIdioma=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_idioma_obtener()");
	$i=0;
	//Generamos contenido multidioma
	while($datoIdioma=$db->getData($resultadoIdioma)){
		$subPlantilla->assign("IDIOMA_ID",$datoIdioma["id_idioma"]);
		$subPlantilla->assign("IDIOMA_DESCRIPCION",$datoIdioma["nombre"]);
		$plantilla->assign("IDIOMA_ID",$datoIdioma["id_idioma"]);
		if($i==0){
			$subPlantilla->assign("STYLE_DISPLAY","display:");
		}else{
			$subPlantilla->assign("STYLE_DISPLAY","display:none;");
		}
		$subPlantilla->parse("contenido_principal.item_contenido_idioma");
		$i++;
	}
	
	
	$subPlantilla->assign("ACTION","create");
	
	
	//Atributos del checkbox
	$subPlantilla->assign("CONCEPTO_WEB_VISIBLE","0");
	$subPlantilla->assign("WEB_VISIBLE_CLASE","unChecked");
	
	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
		
	//Incluimos proceso onload
	$plantilla->parse("contenido_principal.carga_inicial");
	
	//Contruimos plantilla secundaria
	$subPlantilla->parse("contenido_principal");
	
	//Exportamos plantilla secundaria a la plantilla principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
	//Construimos plantilla principal
	$plantilla->parse("contenido_principal");
	
	//Mostramos plantilla principal por pantalla
	$plantilla->out("contenido_principal");
?>