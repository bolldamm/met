<?php
	/**
	 * 
	 * Script que muestra y realiza la creacion de un menu
	 * @Author eData
	 * 
	 */

 	if(!gestorGeneralUtils::tieneUsuarioRolSeo()){
 		generalUtils::redirigir("index.php");
 	}

	//Si hemos enviado el formulario...
	if(count($_POST)){		
		//Obtenemos idiomas
		$resultadoIdioma=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_idioma_obtener()");
		while($datoIdioma=$db->getData($resultadoIdioma)){
			$resultadoMetaTag=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_metatag_obtener()");
			while($datoMetaTag=$db->getData($resultadoMetaTag)){
				$descripcion=generalUtils::escaparCadena($_POST["txtMetatag_".$datoMetaTag["id_metatag"]."_".$datoIdioma["id_idioma"]]);
				
				//Insertamos la descripcion del metatag miembro por cada idioma
				$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_menu_metatag_guardar(".$_POST["hdnIdMenu"].",".$datoMetaTag["id_metatag"].",".$datoIdioma["id_idioma"].",'".$descripcion."')");
			}
			
			//Guardamos seo title y seo url
			$titulo=generalUtils::escaparCadena($_POST["txtTitulo_".$datoIdioma["id_idioma"]]);
			$url=generalUtils::escaparCadena($_POST["txtUrl_".$datoIdioma["id_idioma"]]);
			$canonical=generalUtils::escaparCadena($_POST["txtCanonical_".$datoIdioma["id_idioma"]]);
			$resultadoMenuSeo=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_menu_seo_idioma_guardar(".$_POST["hdnIdMenu"].",".$datoIdioma["id_idioma"].",'".$titulo."','".$url."','".$canonical."')");
		}
				
		//Volvemos a listar menu
		if($_POST["hdnVolver"]==0){
			generalUtils::redirigir("main_app.php?section=menu&action=edit&id_menu=".$_POST["hdnIdMenu"]);
		}else{
			generalUtils::redirigir("main_app.php?section=menu_seo&action=create&id_menu=".$_POST["hdnIdMenu"]);
		}
	}


	
	if(!isset($_GET["id_menu"]) || !is_numeric($_GET["id_menu"])){
		generalUtils::redirigir("index.php");
	}
	
	
	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/menu/menu_seo/manage_menu_seo.html");
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;

	//Obtenemos todos los menus padres del menu actual, incluido el actual
	$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_menu_breadcumb(null,".$_GET["id_menu"].",".$_SESSION["user"]["language_id"].",'','')");
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
		$vectorMigas[$contadorMigas]["url"]=STATIC_BREADCUMB_MENU_EDIT_MENU_LINK."&id_menu=".$vectorIdMenu[$i];
		$vectorMigas[$contadorMigas]["texto"]=$vectorNombreMenu[$i];
		
		$contadorMigas++;
		
		//SubMenu
		$vectorMigas[$contadorMigas]["url"]=STATIC_BREADCUMB_INICIO_LINK."&id_padre=".$vectorIdMenu[$i];
		$vectorMigas[$contadorMigas]["texto"]=STATIC_BREADCUMB_MENU_SUB_MENU_TEXT;
			
		$contadorMigas++;
	}
	
	$vectorMigas[$contadorMigas-1]["url"]=STATIC_BREADCUMB_MENU_SEO_CREATE_MENU_SEO_LINK."&id_menu=".$_GET["id_menu"];
	$vectorMigas[$contadorMigas-1]["texto"]=STATIC_BREADCUMB_MENU_SEO_CREATE_MENU_TEXT;
	
	
	
	require "includes/load_breadcumb.inc.php";
	
	//Obtenemos bloque multidioma	
	$resultadoIdioma=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_idioma_obtener()");
	$i=0;
	//Generamos contenido multidioma
	while($datoIdioma=$db->getData($resultadoIdioma)){
		$subPlantilla->assign("IDIOMA_ID",$datoIdioma["id_idioma"]);
		$subPlantilla->assign("IDIOMA_DESCRIPCION",$datoIdioma["nombre"]);
		$plantilla->assign("IDIOMA_ID",$datoIdioma["id_idioma"]);

		//Menu seo
		$resultadoMenuSeo=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_menu_seo_obtener_concreto(".$_GET["id_menu"].",".$datoIdioma["id_idioma"].")");
		$datoMenuSeo=$db->getData($resultadoMenuSeo);
		$subPlantilla->assign("MENU_SEO_TITULO",$datoMenuSeo["seo_title"]);
		$subPlantilla->assign("MENU_SEO_URL",$datoMenuSeo["seo_url"]);
		$subPlantilla->assign("MENU_SEO_CANONICAL",$datoMenuSeo["seo_canonical"]);

		//Obtenemos metas
		$resultadoMetaTag=$db->callProcedure("CALL ed_sp_menu_metatag_obtener(".$_GET["id_menu"].",".$datoIdioma["id_idioma"].")");
		while($datoMetaTag=$db->getData($resultadoMetaTag)){
			$subPlantilla->assign("METATAG_ID",$datoMetaTag["id_metatag"]);
			$subPlantilla->assign("METATAG_TITULO",$datoMetaTag["nombre"]);
			$subPlantilla->assign("METATAG_DESCRIPCION",$datoMetaTag["descripcion"]);
			
			$subPlantilla->parse("contenido_principal.item_contenido_idioma.item_metatag");
			
		}
		
		
		$subPlantilla->parse("contenido_principal.item_contenido_idioma");
		$i++;
	}
	$plantilla->parse("contenido_principal.carga_inicial.editor_finder");
	
	//Menu id
	$subPlantilla->assign("ID_MENU",$_GET["id_menu"]);
	
	
	
	$subPlantilla->assign("ACTION","create");	
	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
		
	//Incluimos script editor
	$plantilla->parse("contenido_principal.editor_script");
	
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