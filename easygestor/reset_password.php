<?php
	/**
	 * 
	 * Scripts que presenta el formulario de recordar password
	 * @author eData
	 * @version 4.0
	 * 
	 */
	require "includes/load_main_components.inc.php";
	require "config/dictionary/default.php";
	
	
	//Si hemos enviado el formulario...
	if(count($_POST)>0){
		$hashGenerado=generalUtils::filtrarInyeccionSQL($_POST["hdnHashGenerado"]);
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_usuario_restablecer_clave('".generalUtils::escaparCadena($_POST["txtPassword"])."','".$hashGenerado."')");
		$dato=$db->getData($resultado);
		
		//Redirigimos
		generalUtils::redirigir("success_reset_password.php");		
	}
	
	/**
	 * 
	 * Nos indicara si todo el proceso de creacion del formulario para resetear password cumple con todas las condiciones previstas
	 * @var boolean
	 * 
	 */
	$esCorrecto=true;
	
	/**
	 * 
	 * Instancia de XTemplate de la pantalla principal, previa a la autentication en el gestor
	 * @var object
	 * 
	 */
	$plantilla=new XTemplate("html/index.html");
		
	//Establecemos titulo
	$plantilla->assign("WEB_TITLE",STATIC_RESET_PASSWORD_WEB_TITLE);
	
	//Incluimos archivo para password_validator
	$plantilla->parse("contenido_principal.script_password_validator");
	$plantilla->parse("contenido_principal.carga_inicial.password_validator");
	
	/**
	 * 
	 * Instancia de XTemplate de la pantalla reset password
	 * @var object
	 * 
	 */
	$subPlantilla=new XTemplate("html/reset_password.html");
		
	
	//Si no se ha pasado el codigo... o no tiene la longitud de 32 caracteres de md5...
	if(!isset($_GET["c"]) || strlen($_GET["c"])<32){
		$esCorrecto=false;	
	}else{
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_usuario_validar_hash('".$_GET["c"]."')");
		$dato=$db->getData($resultado);
		
		//Si nos  ha devuelto valor blanco...
		if($dato["codigo"]==""){
			$esCorrecto=false;
		}
		
	}
	if(!$esCorrecto){
		$subPlantilla->assign("NOTIFY_LAYOUT_CLASS","layoutResetPasswordNotifyDeprecated");
		
		//Construimos el proceso de carga inicial
		$plantilla->assign("ITEM_EASYNOTIFY",STATIC_RESET_PASSWORD_PASSWORD_DEPRECATED_LINK);
		$plantilla->assign("TYPE_EASYNOTIFY",1);
		$plantilla->parse("contenido_principal.carga_inicial.autoload_easynotify");
	}else{
		$subPlantilla->assign("HASH_GENERADO",$_GET["c"]);
		$subPlantilla->assign("NOTIFY_LAYOUT_CLASS","layoutResetPasswordNotify");
		$subPlantilla->parse("contenido_principal.form_reset_password");
	}
	
	
	//Construimos bloque $subPlantilla
	$subPlantilla->parse("contenido_principal");
	
	//Exportamos $subPlantilla a $plantilla
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
	//Construimos el bloque carga inicial
	$plantilla->parse("contenido_principal.carga_inicial");
	
	//Construimos bloque $plantilla
	$plantilla->parse("contenido_principal");
	
	//Mostramos bloque $plantilla
	$plantilla->out("contenido_principal");
?>