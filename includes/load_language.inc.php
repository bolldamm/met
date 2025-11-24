<?php
	/**
	 * 
	 * Especificamos en el caso de ser un portal multidioma cual será su idioma
	 * predeterminado. Si ha escogido otro idioma debemos comprobar si existe
	 * en la base de datos como activo y seleccionar su diccionario.
	 * 
	 */
	if(isset($_GET["idioma"])){
		$resultadoIdioma = $db->callProcedure("CALL ed_sp_web_idioma_obtener_concreto('".$_GET["idioma"]."')");
		if($db->getNumberRows($resultadoIdioma) == 1) {
			$datoIdioma = $db->getData($resultadoIdioma);
			$_SESSION["id_idioma"] = $datoIdioma["id_idioma"];
			$_SESSION["siglas"] = $datoIdioma["siglas"];
			$_SESSION["diccio"] = $datoIdioma["diccionario"];
		}
	}else{
		if(!isset($_SESSION["id_idioma"])) {
			$_SESSION["id_idioma"] = 3;
			$_SESSION["siglas"] = "en";
			$_SESSION["diccio"] = "en_EN.php";
		}
	}
?>