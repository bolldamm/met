<?php
	/**
	 * 
	 * Script que muestra y realiza la edicion de un menu
	 * @Author eData
	 * 
	 */
	
	//Si hemos enviado el formulario...
	if(count($_POST)){
		$idTipoPago=$_POST["hdnIdTipoPago"];

		if(isset($_POST["hdnVisible"])){
			$visible=$_POST["hdnVisible"];
		}else{
			$visible=0;
		}
		
		$db->startTransaction();
		
		//Insercion menu
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_tipo_pago_movimiento_editar(".$idTipoPago.",".$visible.")");	
		

		//Guardamos la informacion multidioma del tipo area
		require "language_payment_type.php";
		
		$db->endTransaction();
		
		//Volvemos a listar menu
		if($_POST["hdnVolver"]==0){
			generalUtils::redirigir("main_app.php?section=payment_type&action=view");
		}else{
			generalUtils::redirigir("main_app.php?section=payment_type&action=edit&id_tipo_pago=".$idTipoPago);
		}
	}

	
	if(!isset($_GET["id_tipo_pago"]) || !is_numeric($_GET["id_tipo_pago"])){
		generalUtils::redirigir("main_app.php?section=movement&action=view");
	}
	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/movement/payment_type/manage_payment_type.html");
		
	//Obtenemos bloque multidioma	
	$resultadoIdioma=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_idioma_obtener()");
	$i=0;
	//Generamos contenido multidioma
	while($datoIdioma=$db->getData($resultadoIdioma)){
		//Sacamos la informacion de la tematica en cada idioma
		$resultadoTipoPago=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_tipo_pago_movimiento_obtener_concreta(".$_GET["id_tipo_pago"].",".$datoIdioma["id_idioma"].")");
		$datoTipoPago=$db->getData($resultadoTipoPago);
		$subPlantilla->assign("IDIOMA_ID",$datoIdioma["id_idioma"]);
		$subPlantilla->assign("IDIOMA_DESCRIPCION",$datoIdioma["nombre"]);
		if($i==0){
			$nombreTipoPago=$datoTipoPago["nombre"];
			$subPlantilla->assign("STYLE_DISPLAY","display:");
			
			//La primera vez, miramos si la noticia esta activo o no
			if($datoTipoPago["activo"]==1){
				$subPlantilla->assign("WEB_VISIBLE_CLASE","checked");
			}else{
				$subPlantilla->assign("WEB_VISIBLE_CLASE","unChecked");
			}
			$subPlantilla->assign("TIPO_PAGO_WEB_VISIBLE",$datoTipoPago["activo"]);
		}else{
			$subPlantilla->assign("STYLE_DISPLAY","display:none;");
		}
		$subPlantilla->assign("TIPO_PAGO_NOMBRE",$datoTipoPago["nombre"]);
		$subPlantilla->parse("contenido_principal.item_contenido_idioma");
		$i++;
	}
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_MOVEMENT_VIEW_MOVEMENT_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_MOVEMENT_VIEW_MOVEMENT_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_PAYMENT_TYPE_VIEW_PAYMENT_TYPE_LINK;
	$vectorMigas[2]["texto"]=STATIC_BREADCUMB_PAYMENT_TYPE_VIEW_PAYMENT_TYPE_TEXT;
	$vectorMigas[3]["url"]=STATIC_BREADCUMB_PAYMENT_TYPE_EDIT_PAYMENT_TYPE_LINK."&id_tipo_pago=".$_GET["id_tipo_pago"];
	$vectorMigas[3]["texto"]=$nombreTipoPago;
	
	
	require "includes/load_breadcumb.inc.php";
	
	
	//Parametros hidden formulario
	$subPlantilla->assign("ID_TIPO_PAGO",$_GET["id_tipo_pago"]);
	$subPlantilla->assign("ACTION","edit");


	//Informacion del usuario
	require "includes/load_information_user.inc.php";
	$subPlantilla->parse("contenido_principal.item_button_close");
		
	
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