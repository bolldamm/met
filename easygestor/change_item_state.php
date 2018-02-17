<?php
	require "includes/load_main_components.inc.php";
	require "includes/load_validate_user.inc.php";
	require "config/constants.php";
	require "config/dictionary/".$_SESSION["user"]["language_dictio"];
	require "config/sections.php";
	
	//Si hemos enviado form por post
	if(count($_POST)>0){
		
		if($_SESSION["user"]["rol_global"]!=1){
			/**
			 * 
			 * Incluimos la informacion sobre las secciones,acciones y las rutas de acceso al script(Ahora podremos controlar permisos mas facilmente)
			 * 
			 */
			require "config/controller.php";
			$idSeccion=$controller[$_POST["section"]][ID_SECTION];
			
			//Si somos una seccion a controlar...
			if($idSeccion!=-1 && !gestorGeneralUtils::tienePermisoUsuarioSeccion($db, $idSeccion, 2)){
				die();
			}
		}
		
		$parametroAdicional="";
		//Esta variable nos indica si es necesareo controlar el permiso a la hora de habilitar
		$esPermiso=false;
		switch($_POST["section"]){
			case SECTION_MENU:
				$parametroAdicional="&hdnIdPosicion=".$_POST["hdnIdPosicion"];
				$store="ed_sp_menu_modificar_estado";
				$esPermiso=true;
				break;
			case SECTION_NEW:
				$store="ed_sp_noticia_modificar_estado";
				break;
			case SECTION_CONFERENCE:
				$store="ed_sp_conferencia_modificar_estado";
				break;
			case SECTION_WORKSHOP:
				$store="ed_sp_taller_modificar_estado";
				break;
			case SECTION_JOB:
				$store="ed_sp_oferta_trabajo_modificar_estado";
				break;
			case SECTION_SUCCESS:
				$store="ed_sp_caso_exito_modificar_estado";
				break;
			case SECTION_ACCESS_BUTTON:
				$store="ed_sp_boton_acceso_modificar_estado";
				break;
			case SECTION_DIARY:
				$store="ed_sp_agenda_modificar_estado";
				break;
			case SECTION_CONCEPT:
				$store="ed_sp_concepto_movimiento_modificar_estado";
				break;
			case SECTION_PAYMENT_TYPE:
				$store="ed_sp_tipo_pago_movimiento_modificar_estado";
				break;
		}
		
		/**
		 * 
		 * La cadena de los id separado por el caracter, se ha pasado a un array.
		 * @var array
		 * 
		 */
		$vectorItem=array_filter(explode(",",$_POST["hdnId"]));
		/**
		 * 
		 * Total items del array
		 * @var int
		 * 
		 */
		$totalItem=count($vectorItem);

		//Recorremos array
		for($i=0;$i<$totalItem;$i++){
			$esDenegado=false;
			if($esPermiso){
				if(gestorGeneralUtils::tienePermisoUsuarioMenu($db, $vectorItem[$i], 2)){
					$esDenegado=false;
				}else{
					$esDenegado=true;
				}
			}
			if(!$esDenegado){
				$resultado=$db->callProcedure("CALL ".$store."(".$vectorItem[$i].")");
			}
		}
		if(isset($_POST["id_padre"]) && $_POST["id_padre"]!=""){
			$parametroPadre="&id_padre=".$_POST["id_padre"];
		}else{
			$parametroPadre="";
		}
	
		generalUtils::redirigir("main_app.php?section=".$_POST["section"]."&action=view&pagina=".$_POST["pagina"]."&hdnOrden=".$_POST["hdnOrden"]."&hdnRegistros=".$_POST["hdnRegistros"].$parametroPadre.$parametroAdicional);
	}
?>