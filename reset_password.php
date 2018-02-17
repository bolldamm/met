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
	$subPlantilla = new XTemplate("html/reset_password.html");
	
	/**
	 * Asignamos el CSS que corresponde a este apartado
	 */
	$plantilla->assign("SECTION_FILE_CSS", "remember_password.css");
		
	/**
	 * Cargamos la información que ha de aparecer siempre como la cabecera, el pie u otro contenido
	 */
	require "includes/load_structure.inc.php";
	
	/**** INICIO: breadcrumb ****/
	$breadCrumbUrlDetalle = $_SERVER["REQUEST_URI"];
	$breadCrumbDescripcionDetalle = STATIC_RESET_PASSWORD_TITLE;
	/**** FINAL: breadcrumb ****/


	//Cargamos el breadcrumb
	$idMenu=null;
	require "includes/load_breadcrumb.inc.php";
	
	//Si hemos enviado el formulario...
	if(count($_POST) > 0){
		$hashGenerado = generalUtils::filtrarInyeccionSQL(generalUtils::escaparCadena($_POST["hdnHashGenerado"]));
		$resultado = $db->callProcedure("CALL ed_sp_web_usuario_web_reset_clave('".generalUtils::escaparCadena($_POST["txtPassword"])."','".$hashGenerado."')");
		$dato=$db->getData($resultado);
		
		if($dato["codigo"] == 1) {
			//Presentamos un mensaje informando de que se ha modificado correctamente
			$subPlantilla->parse("contenido_principal.clave_modificada");
		}else{
			$subPlantilla->parse("contenido_principal.clave_no_modificada");
		}
	}else{
		
		$esCorrecto = true;
		
		//Si no se ha pasado el codigo... o no tiene la longitud de 32 caracteres de md5...
		if(!isset($_GET["c"]) || strlen($_GET["c"]) < 32){
			$esCorrecto = false;
		}else{
			$subPlantilla->assign("HASH_GENERADO", $_GET["c"]);
			$resultado=$db->callProcedure("CALL ed_sp_web_usuario_web_validar_hash('".$_GET["c"]."')");
			$dato=$db->getData($resultado);
	
			//Si nos  ha devuelto valor blanco...
			if($dato["codigo"] == ""){
				$esCorrecto = false;
			}
		}
			
		if($esCorrecto) {
			$subPlantilla->parse("contenido_principal.form_reset_password");
			$plantilla->parse("contenido_principal.formulario_reiniciar_clave");
		}else{
			$subPlantilla->parse("contenido_principal.error_validar_hash");
		}
		
		$plantilla->parse("contenido_principal.control_superior");
		
	}
	
	
	
	/**
	 * 
	 * En muchos casos nos encntramos en que hay algun script o estilo css que no deaseamos
	 * ejecturar en una pagina determinada.
	 * Dese aquí parsearemos todos aquellos ficheros que se encuentren dentro del tag <header>
	 * que deseamos que se ejecuten.
	 * 
	 */
	
	$subPlantilla->parse("contenido_principal");
	//Exportamos plantilla secundaria a la principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));

    //Parse inner page content with lefthand menu
    $plantilla->parse("contenido_principal.menu_left");

    //Parseamos y sacamos informacion por pantalla
	$plantilla->parse("contenido_principal");
	$plantilla->out("contenido_principal");
?>