<?php
	require "../includes/load_main_components.inc.php";
	$esAjax=true;
	require "../includes/load_validate_user.inc.php";
	require "../config/dictionary/".$_SESSION["user"]["language_dictio"];
		
	
	switch($_GET["c"]){
		case 1:
			//Obtenemos combo con subcategoria
			echo generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_categoria_media_obtener_combo(".$_GET["id"].",".$_SESSION["user"]["language_id"].")","cmbSubCategoria","cmbSubCategoria",0,"nombre","id_categoria_media",STATIC_GLOBAL_COMBO_DEFAULT,0,"");
			break;
		case 2:
			//Obtenemos combo menus
			if($_GET["idMenu"]==""){
				$idMenu=-1;
			}else{
				$idMenu=$_GET["idMenu"];
			}
			echo generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_menu_obtener_combo(".$_GET["id"].",".$_SESSION["user"]["language_id"].",".$idMenu.")","cmbMenu".$contadorMenu,"cmbMenu".$contadorMenu,0,"nombre","id_menu",STATIC_GLOBAL_COMBO_DEFAULT,0,"onchange='obtenerComboMenu(this)'");
			break;
		case 3:
			//Obtenemos modalidad menu
			echo generalUtils::construirCombo($db, "CALL ".OBJECT_DB_ACRONYM."_sp_tipo_usuario_web_modalidad_obtener_combo(".$_GET["id"].",".$_SESSION["user"]["language_id"].")", "cmbModalidadUsuario", "cmbModalidadUsuario", 0, "nombre", "id_modalidad_usuario_web", STATIC_GLOBAL_COMBO_DEFAULT, 0, "onchange='obtenerBloqueModalidad(this)'");
			break;
		case 4:
			//Obtenemos un subconcepto
			echo generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_concepto_movimiento_obtener_combo(".$_GET["id"].",".$_SESSION["user"]["language_id"].")","cmbSubConcepto","cmbSubConcepto",0,"nombre","id_concepto_movimiento",STATIC_GLOBAL_COMBO_DEFAULT,0,"");
			break;
	}
?>