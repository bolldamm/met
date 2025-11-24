<?php
	/**
	 * 
	 * Esta clase contiene los metodos encargados de las tareas mas comunes unicamente para el gestor.
	 * @author eData S.L.
	 * @version 4.0
	 */
	class gestorGeneralUtils{
		
		/**
		 * 
		 * Obtenemos el combo con los valores que representan el maximo de valores por pagina
		 *
		 */
		static function construirComboNumeroRegistros($todosRegistrosTexto,$valorActual){
			$vectorNumeroRegistros=Array(10,20,MAX_FILTER_NUMBER,-1);
			$totalNumeroRegistros=count($vectorNumeroRegistros);
			$select="<select name='cmbNumeroRegistro' onchange='configurarNumeroRegistros(this)'>";
			for($i=0;$i<$totalNumeroRegistros;$i++){
				if($vectorNumeroRegistros[$i]==-1){
					$textoOpcion=$todosRegistrosTexto;
				}else{
					$textoOpcion=$vectorNumeroRegistros[$i];
				}
				if($vectorNumeroRegistros[$i]==$valorActual){
					$selected="SELECTED";
				}else{
					$selected="";
				}
				$select.="<option value='".$vectorNumeroRegistros[$i]."' ".$selected.">".$textoOpcion."</option>";
			}
			$select.="</select>";
			
			return $select;
		}
		
		
		/**
		 * 
		 * Nos indica si un usuario tiene el permiso que se pide para el menu en cuestion
		 *
		 */
		static function tienePermisoUsuarioMenu($db,$idMenu,$idPermiso){
			//Si somos administradores si tenemos permiso...
//			if($_SESSION["user"]["rol_global"]==1){
//				$esMenuEditable=true;
//			}else{
//				//Miramos si el usuario logueado tiene permisos
//				$resultadoPermisoUsuario=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_permiso_usuario_menu_obtener(".$_SESSION["user"]["id"].",".$idMenu.",".$idPermiso.")");
//				if($db->getNumberRows($resultadoPermisoUsuario)==1){
//					$esMenuEditable=true;
//				}else{
//					$esMenuEditable=false;
//				}
//			}
          // I have added $esMenuEditable=true; below
			$esMenuEditable=true;
			return $esMenuEditable;
		}
		
		/**
		 * 
		 * Nos indica si un usuario tiene el permiso que se pide para la seccion en cuestion
		 *
		 */
		static function tienePermisoUsuarioSeccion($db,$idSeccion,$idPermiso){
			$esSeccionEditable=true;
			//Simos admins no hace falta mirarlo
			if($_SESSION["user"]["rol_global"]!=1){
				//Miramos si el usuario logueado tiene permisos
				$resultadoPermisoUsuario=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_easygestor_seccion_usuario_obtener_concreto(".$_SESSION["user"]["id"].",".$idSeccion.",".$idPermiso.")");
				if($db->getNumberRows($resultadoPermisoUsuario)==1){
					$esSeccionEditable=true;
				}else{
					$esSeccionEditable=false;
				}
			}

			return $esSeccionEditable;
		}
		
		/**
		 * 
		 * Nos indica si un usuario tiene asociado el rol necesario para el seo
		 *
		 */
		static function tieneUsuarioRolSeo(){
			return ($_SESSION["user"]["rol_id"]==3);
		}
		
		/**
		 * 
		 * Dado un valor y un array nos indica en que posicion esta
		 * 
		 * 
		 */
		function getKeyArrayByValue($valor,$array){
			$esEncontrado=false;
			$i=0;
			$totalElementos=count($array);
			$valorDevuelto=-1;
			while(!$esEncontrado && $i<$totalElementos){
				if($array[$i]==$valor){
					$valorDevuelto=$i;
					$esEncontrado=true;
				}
				$i++;
			}
			return $valorDevuelto;
		}
		
	}//end class
	
?>