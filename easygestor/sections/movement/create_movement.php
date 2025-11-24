<?php
	/**
	 * 
	 * Script que muestra y realiza la creacion de un menu
	 * @Author eData
	 * 
	 */


	//Si hemos enviado el formulario...
	if(count($_POST)){
		//Datos generales del movimiento
		if(isset($_POST["hdnPagado"])){
			$pagado=$_POST["hdnPagado"];
		}else{
			$pagado=0;
		}
		
		//Iniciar transaccion
		$db->startTransaction();
				
		//Insercion movimiento
		$descripcion=generalUtils::escaparCadena($_POST["txtaDescripcion"]);
        $comment = generalUtils::escaparCadena($_POST["txtaComment"]);
		
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_movimiento_insertar(".$_POST["cmbTipo"].",".$_POST["cmbSubConcepto"].",".$_POST["hdnNonTaxable"].",".$_POST["cmbTipoPago"].",".$_SESSION["user"]["id"].",'".generalUtils::escaparCadena($_POST["txtConceptoPersonalizado"])."','".generalUtils::escaparCadena($_POST["txtPersona"])."','".generalUtils::conversionFechaFormato($_POST["txtFecha"])."','".$descripcion."','".$_POST["txtImporte"]."',".$pagado.",'".$comment."')");		
		$dato=$db->getData($resultado);
		$idMovimiento=$dato["id_movimiento"];


		
		//Cerrar transaccion
		$db->endTransaction();
		
		//Volvemos a listar menu
		generalUtils::redirigir("main_app.php?section=movement&action=view");
	}

	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/movement/manage_movement.html");
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_MOVEMENT_VIEW_MOVEMENT_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_MOVEMENT_VIEW_MOVEMENT_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_MOVEMENT_CREATE_MOVEMENT_LINK;
	$vectorMigas[2]["texto"]=STATIC_BREADCUMB_MOVEMENT_CREATE_MOVEMENT_TEXT;
		
	require "includes/load_breadcumb.inc.php";
	
	$plantilla->assign("TEXTAREA_ID","txtaDescripcion");
	$plantilla->assign("TEXTAREA_TOOLBAR","Minimo");
	$plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");
	
	$plantilla->parse("contenido_principal.carga_inicial.editor_finder");
	
	
	
	$subPlantilla->assign("ACTION","create");
	
	//Atributos del checkbox
	$subPlantilla->assign("MOVIMIENTO_PAGADO","0");
	$subPlantilla->assign("PAGADO_CLASE","unChecked");
	
	//<<<07/12/2016>>>
	$subPlantilla->assign("MOVIMIENTO_TAXABLE","0");
	$subPlantilla->assign("TAXABLE_CLASE","unChecked");
	
	//Display subconcepto
	$subPlantilla->assign("DISPLAY_SUBCONCEPTO","style='display:none;'");
	//Combo tipo
	$subPlantilla->assign("COMBO_TIPO",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_tipo_movimiento_obtener_combo(".$_SESSION["user"]["language_id"].")","cmbTipo","cmbTipo",0,"nombre","id_tipo_movimiento","",0,""));
	
	//Combo concepto
	$subPlantilla->assign("COMBO_CONCEPTO",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_concepto_movimiento_obtener_combo(null,".$_SESSION["user"]["language_id"].")","cmbConcepto","cmbConcepto",0,"nombre","id_concepto_movimiento",STATIC_GLOBAL_COMBO_DEFAULT,0,"onchange='obtenerComboSubConcepto(this)'"));
	
	//Combo tipo pago
	$subPlantilla->assign("COMBO_TIPO_PAGO",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_tipo_pago_movimiento_obtener_combo(".$_SESSION["user"]["language_id"].")","cmbTipoPago","cmbTipoPago",0,"nombre","id_tipo_pago_movimiento",STATIC_GLOBAL_COMBO_DEFAULT,0,""));
	
	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
		
	//Date pickers
	$plantilla->assign("INPUT_ID","txtFecha");
	$plantilla->parse("contenido_principal.carga_inicial.autoload_datepicker");
	
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