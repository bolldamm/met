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
		
		if(isset($_POST["hdnVisible"])){
			$visible=$_POST["hdnVisible"];
		}else{
			$visible=0;
		}
		
		
		//Iniciar transaccion
		$db->startTransaction();
		//Insercion menu
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_tipo_pago_movimiento_insertar(".$visible.")");		
		$dato=$db->getData($resultado);
		$idTipoPago=$dato["id_tipo_pago_movimiento"];
		

		//Guardamos la informacion multidioma del menu
		require "language_payment_type.php";

			
		//Cerrar transaccion
		$db->endTransaction();
		
		//Volvemos a listar menu
		generalUtils::redirigir("main_app.php?section=payment_type&action=view");
	}

	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/movement/payment_type/manage_payment_type.html");
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_MOVEMENT_VIEW_MOVEMENT_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_MOVEMENT_VIEW_MOVEMENT_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_PAYMENT_TYPE_VIEW_PAYMENT_TYPE_LINK;
	$vectorMigas[2]["texto"]=STATIC_BREADCUMB_PAYMENT_TYPE_VIEW_PAYMENT_TYPE_TEXT;
	
	$posicionReferencia=3;
	$parametroPadre="";
	
	
	
	$vectorMigas[$posicionReferencia]["url"]=STATIC_BREADCUMB_PAYMENT_TYPE_CREATE_PAYMENT_TYPE_LINK;
	$vectorMigas[$posicionReferencia]["texto"]=STATIC_BREADCUMB_PAYMENT_TYPE_CREATE_PAYMENT_TYPE_TEXT;
	
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
	$subPlantilla->assign("TIPO_PAGO_WEB_VISIBLE","0");
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