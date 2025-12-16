<?php
	/**
	 * 
	 * Esta clase contiene los metodos encargados de las tareas mas comunes.
	 * @author eData S.L.
	 * @version 4.0
	 */
	class generalUtils{
		
		/**
		 * 
		 * Encargada de eliminar las comillas simples de una cadena.
		 * @param string $cadena
		 * @return string
		 * 
		 */
		static function filtrarInyeccionSQL($cadena){
			return str_replace("'","",$cadena);
		}
		
		/**
		 * 
		 * Agregamos antibarras para escapar cadenas con comillas simples o comillas dobles.
		 * @param string $cadena
		 * @return string
		 * 
		 */
		static function escaparCadena($cadena){
			$cadena=str_replace("\\","",$cadena);
			return addslashes($cadena);
		}
		
		/**
		 * 
		 * Quitamos antibarras
		 * @param string $cadena
		 * @return string
		 * 
		 */
		static function reeamplazarAntiBarras($cadena){
			return str_replace("\\","",$cadena);
		}
		
		/**
		 * 
		 * Realizamos redireccion a la pagina deseada.
		 * @param string $url
		 * 
		 */
		static function redirigir($url){
			header("Location:".$url);
			exit();
		}
		
		static function esMiembroCaducado($fechaFinalizacion){
			$fechaActual=date("d/m/Y");
			$fechaPrevia=generalUtils::conversionFechaFormato($fechaFinalizacion,"-","/");
			if(generalUtils::compararFechas($fechaActual,$fechaPrevia)==3){
				return true;
			}else{
				return false;
			}
		}
		
		/**
		 * 
		 * Dado un email, verificamos que cumple con el formato estandar
		 * @param string $email
		 * @return string
		 * 
		 */
		static function validarEmail($email){
			return preg_match('#^[a-z0-9.!\#$%&\'*+-/=?^_`{|}~]+@([0-9.]+|([^\s\'"<>]+\.+[a-z]{2,6}))$#si', $email);
		}
				
		/**
		 * 
		 * Obfuscates email addresses
		 * @param string $email
		 * 
		 * 
		 */
		static function hideEmail($email){
  $character_set = '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz';
  $key = str_shuffle($character_set); $cipher_text = ''; $id = 'e'.rand(1,999999999);
  for ($i=0;$i<strlen($email);$i+=1) $cipher_text.= $key[strpos($character_set,$email[$i])];
  $script = 'var a="'.$key.'";var b=a.split("").sort().join("");var c="'.$cipher_text.'";var d="";';
  $script.= 'for(var e=0;e<c.length;e++)d+=b.charAt(a.indexOf(c.charAt(e)));';
  $script.= 'document.getElementById("'.$id.'").innerHTML="<a href=\\"mailto:"+d+"\\">"+d+"</a>"';
  $script = "eval(\"".str_replace(array("\\",'"'),array("\\\\",'\"'), $script)."\")"; 
  $script = '<script type="text/javascript">/*<![CDATA[*/'.$script.'/*]]>*/</script>';
  return '<span id="'.$id.'">[javascript protected email address]</span>'.$script;
}
		
		/**
		 * 
		 * Generamos un combo, donde las opciones son generadas por los resultados de un store procedure pasado como parametro
		 * @param object $db
		 * @param string $procedure
		 * @param string $nombreCombo
		 * @param string $idCombo
		 * @param string $valorActual
		 * @param string $opcionTexto
		 * @param string $opcionValor
		 * @param string $opcionDefectoTexto
		 * @param string $opcionDefectoValor
		 * @param string $evento
		 * @return string
		 * 
		 */
		static function construirCombo($db,$procedure,$nombreCombo,$idCombo,$valorActual,$opcionTexto,$opcionValor,$opcionDefectoTexto,$opcionDefectoValor,$evento,$clase=""){
			$combo="<select name='".$nombreCombo."' id='".$idCombo."' ".$evento." ".$clase.">";
            if($opcionDefectoTexto!=""){
                //$combo.="<option value='".$opcionDefectoValor."'>".$opcionDefectoTexto."</option>";
                //Otherwise get error on Submit
                $combo.="<option selected value='".$opcionDefectoValor."'>".$opcionDefectoTexto."</option>";
                //Adding disabled prevents HTML5 validation for required fields (no "-1" value when none selected)
                //Adding hidden makes the "-1" option unavailable once a first selection has been made
                //$combo.="<option disabled value='".$opcionDefectoValor."' selected hidden>".$opcionDefectoTexto."</option>";
            }
			$resultado=$db->callProcedure($procedure);
			while($dato=$db->getData($resultado)){
				if($dato[$opcionValor]==$valorActual){
					$seleccionado="SELECTED";
				}else{
					$seleccionado="";
				}//end else
				$combo.="<option value='".$dato[$opcionValor]."' ".$seleccionado.">".$dato[$opcionTexto]."</option>";
			}//end while
			$combo.="</select>";
			
			return $combo;
		}//end function

		/**
		 * Generate a combo with data attributes on each option
		 * Similar to construirCombo but supports adding data-* attributes from result columns
		 *
		 * @param object $db Database connection
		 * @param string $procedure Stored procedure to call
		 * @param string $nombreCombo Name attribute for select
		 * @param string $idCombo ID attribute for select
		 * @param mixed $valorActual Currently selected value
		 * @param string $opcionTexto Column name for option display text
		 * @param string $opcionValor Column name for option value
		 * @param string $opcionDefectoTexto Default option text (placeholder)
		 * @param mixed $opcionDefectoValor Default option value
		 * @param string $evento Event attributes
		 * @param array $dataAttributes Array of column names to add as data-* attributes (e.g., ['is_eu'] becomes data-is-eu)
		 * @param string $clase Class attribute
		 * @return string Generated HTML select element
		 */
		static function construirComboConDataAttr($db,$procedure,$nombreCombo,$idCombo,$valorActual,$opcionTexto,$opcionValor,$opcionDefectoTexto,$opcionDefectoValor,$evento,$dataAttributes=[],$clase=""){
			$combo="<select name='".$nombreCombo."' id='".$idCombo."' ".$evento." ".$clase.">";
			if($opcionDefectoTexto!=""){
				$combo.="<option selected value='".$opcionDefectoValor."'>".$opcionDefectoTexto."</option>";
			}
			$resultado=$db->callProcedure($procedure);
			while($dato=$db->getData($resultado)){
				if($dato[$opcionValor]==$valorActual){
					$seleccionado="SELECTED";
				}else{
					$seleccionado="";
				}
				// Build data attributes string
				$dataAttrStr = "";
				foreach($dataAttributes as $attr){
					if(isset($dato[$attr])){
						// Convert underscore to hyphen for HTML data attribute convention
						$htmlAttr = str_replace('_', '-', $attr);
						$dataAttrStr .= " data-".$htmlAttr."='".$dato[$attr]."'";
					}
				}
				$combo.="<option value='".$dato[$opcionValor]."' ".$seleccionado.$dataAttrStr.">".$dato[$opcionTexto]."</option>";
			}
			$combo.="</select>";

			return $combo;
		}

		 /**
                 * Generate a multi-select combo
		 */
		static function construirMultiCombo($db,$allOptionsProcedure,$selectedOptionsProcedure,$columnNameAll,$columnNameSelected,$nombreCombo,$idCombo,$opcionTexto,$opcionValor,$opcionDefectoTexto,$opcionDefectoValor,$evento,$clase=""){
			$combo="<select multiple name='".$nombreCombo."[]' id='".$idCombo."' ".$evento." ".$clase." data-placeholder='".$opcionDefectoTexto."'>";
			//if($opcionDefectoTexto!=""){
			//	$combo.="<option value='".$opcionDefectoValor."'>".$opcionDefectoTexto."</option>";
			//}
                        $selected = array('1','2');
			$allOptions=$db->callProcedure($allOptionsProcedure);
			$selectedOptions=$db->callProcedure($selectedOptionsProcedure);
			while($datoAll=$db->getData($allOptions)){
                        $options[] = $datoAll["$columnNameAll"];
                        
 			while($datoSelected=$db->getData($selectedOptions)){
                        if ($datoSelected != '') {
                            $selected[] = $datoSelected["$columnNameSelected"];
                        } else {
                            $selected[] = '';
                        }
                        }
                        foreach($options as $option) { 
                            $seleccionado = in_array($option,$selected) ? "selected " : "";
                        }	
                        $combo.="<option value='".$datoAll[$opcionValor]."' ".$seleccionado.">".$datoAll[$opcionTexto]."</option>";
                        }
			$combo.="</select>";
			
			return $combo;
		}//end function
		
		/**
		 * 
		 * Generamos un combo a partir de una matriz de valores
		 * @param array $matriz
		 * @param string $nombreCombo
		 * @param string $idCombo
		 * @param string $valorActual
		 * @param string $opcionDefectoTexto
		 * @param string $opcionDefectoValor
		 * @param string $evento
		 * @return string
		 * 
		 */
		static function construirComboMatriz($matriz,$nombreCombo,$idCombo,$valorActual,$opcionDefectoTexto,$opcionDefectoValor,$evento,$clase=""){
			$combo="<select name='".$nombreCombo."' id='".$idCombo."' ".$evento." ".$clase.">";
			
			//Solo si pasamos una opcion defecto, es cuando creariamos una opcion por defecto.
			if($opcionDefectoTexto!=""){
				$combo.="<option value='".$opcionDefectoValor."'>".$opcionDefectoTexto."</option>";
			}
			
			/**
			 * 
			 * Numero de elementos de la matriz
			 * @var int
			 * 
			 */
			$lenMatriz=count($matriz);
			
			//Recorremos matriz
			for($i=0;$i<$lenMatriz;$i++){
				if($i==$valorActual){
					$seleccionado="SELECTED";
				}else{
					$seleccionado="";
				}//end else
				$combo.="<option value='".$i."' ".$seleccionado.">".$matriz[$i]["descripcion"]."</option>";
			}
			$combo.="</select>";
			
			return $combo;
		}
				
		/**
		 * 
		 * Dado un nombre de fichero, obtenemos su extension
		 * @param string $fichero
		 * @return string
		 * 
		 */
		static function obtenerExtensionFichero($fichero){
			return substr($fichero, strrpos($fichero,".")+1);
		}
		
		/**
		 * 
		 * Dada un fichero comprobamos si es de tipo .jpg, jpeg o .gif,png
		 * 
		 */
		static function esImagenValida($fichero){
			$extension = strtolower(generalUtils::obtenerExtensionFichero($fichero));
			$extensionePermitidas = Array("jpg", "jpeg", "gif", "png");

			if(in_array($extension, $extensionePermitidas)) {
				return true;
			}else{
				return false;
			}
		}
		
		/**
		 * 
		 * Dada una fecha en formato aaaa-mm-dd o dd-mm-aaaa devolvemos la inversa
		 * @example Si pasamos 31-10-1985, devolvoremos 1985-10-31, si pasamos 1985-10-31, devolvremos 31-10-1985
		 * @param string $fecha
		 * @return string
		 */
		static function conversionFechaFormato($fecha,$delimitadorOrigen="-",$delimitadorDestino="-"){
			$fechaConcreta=explode($delimitadorOrigen,$fecha);
			
			return $fechaConcreta[2].$delimitadorDestino.$fechaConcreta[1].$delimitadorDestino.$fechaConcreta[0];
		}
		
		/**
		 * Formamos la url amigable para los menus
		 * 
		 */
		static function generarUrlAmigableMenu($vectorAtributos){			
			$urlAmigable=$vectorAtributos["idioma"]."/".generalUtils::toAscii($vectorAtributos["seo_url"]).":".$vectorAtributos["id_menu"];

			return $urlAmigable;
		}
		
		/**
		 * Formamos la url amigable para los detalles
		 * 
		 */
		static function generarUrlAmigableDetalle($vectorAtributos){			
			$urlAmigable=$vectorAtributos["idioma"]."/detail/".generalUtils::toAscii($vectorAtributos["seo_url"]).":".$vectorAtributos["id_menu"]."-".$vectorAtributos["id_detalle"];

			return $urlAmigable;
		}	
		
		/**
		 * Formamos la url amigable para profile
		 * 
		 */
		static function generarUrlAmigablePerfil($vectorAtributos){			
			$urlAmigable=$vectorAtributos["idioma"]."/profile/".generalUtils::toAscii($vectorAtributos["seo_url"]).":".$vectorAtributos["id_menu"]."-".$vectorAtributos["perfil"];

			return $urlAmigable;
		}	
		
		/** 
		 * Obtener url menu con contenido.Si el menu leido, no tiene contenido, 
		 * pondremos la url del menu hijo mas cercano que tenga url, en caso de que sea un menu copia, pondremos url del menu copia
		 * 
		*/
		static function generarUrlMenuContenido($db,$idModulo,$idMenu,$descripcion,$idIdioma,$vectorAtributos){
			if($idModulo==1 && $descripcion==""){
				if(isset($_SESSION["met_user"]["tipoUsuario"])){
					$idMenuTipo=$_SESSION["met_user"]["tipoUsuario"];
				}else{
					$idMenuTipo=-1;
				}
				$resultadoMenuDerivado=$db->callProcedure("CALL ed_sp_web_menu_hijo_contenido_obtener(".$idMenu.",".$idMenuTipo.",".$idIdioma.",1,'','','')");
				if($db->getNumberRows($resultadoMenuDerivado)>0){
					$datoMenuDerivado=$db->getData($resultadoMenuDerivado);
					$vectorAtributos["id_menu"]=$datoMenuDerivado["id_menu"];
					if($datoMenuDerivado["seo_url"]!=""){
						$vectorAtributos["seo_url"]=$datoMenuDerivado["seo_url"];
						//$vectorAtributos["seo_url"]=$datoMenuDerivado["url"];
					}

					if($datoMenuDerivado["id_modulo"]!=1){
						//Datos url seo
						$resultadoMenuSeo=$db->callProcedure("CALL ed_sp_web_menu_seo_obtener(".$datoMenuDerivado["id_menu"].",".$idIdioma.")");
						$datoMenuSeo=$db->getData($resultadoMenuSeo);
						$vectorAtributos["seo_url"]=$datoMenuSeo["seo_url"];
						//$vectorAtributos["seo_url"]=$datoMenuSeo["url"];
					}//end else if
				}//end if
			}//end if
			
			return $vectorAtributos;
		}
		
		
		/**
		 * Limpiamos cadena para que sea considerada amigable
		 */
		
		static function toAscii($cadena){
			setlocale(LC_ALL, 'en_US.UTF8');
			$cadenaLimpia = iconv("UTF-8", "ASCII//TRANSLIT", $cadena);
			$cadenaLimpia = preg_replace("/[^a-zA-Z0-9\/_| -]/", "", $cadenaLimpia);
			$cadenaLimpia = strtolower(trim($cadenaLimpia, "-"));
			$cadenaLimpia = preg_replace("/[\_| -]+/", "-", $cadenaLimpia);

			return $cadenaLimpia;
		}		
		
		
		/**
		 * Quitamos valor defecto de una variable de formulario (quitamos el placeholder en caso de que el valor sea el placaeholder)
		 * 
		 */
		static function skipPlaceHolder($placeHolder,$valor){
			if($placeHolder==$valor){
				$valor="";
			}
			return $valor;
		}
		
	/**
		 * 
		 * Dadas una fecha pasadada como el tipo de datos string y formato dd/mm/aaaa
		 * @param $fechaAConvertir 
		 * @return int
		 */
		static function formatearFechaComparacion($fechaAConvertir) {
			$fechaAConvertirTrozeada = explode ( "/", $fechaAConvertir );
			settype ($fechaAConvertirTrozeada [0],"int");
			settype ($fechaAConvertirTrozeada [1],"int");
			settype ($fechaAConvertirTrozeada [2],"int");
			
		
			$fechaAConvertir = mktime ( 0, 0, 0, $fechaAConvertirTrozeada [1], $fechaAConvertirTrozeada [0], $fechaAConvertirTrozeada [2] );
			return $fechaAConvertir;
		}
		
		
		/**
		 * 
		 * Dadas dos fechas en el formato dd/mm/aaaa nos devuelve su diferencia en dias
		 * @param string $fecha1
		 * @param string $fecha2
		 * @param boolean $abs
		 * @return int
		 */
		static function dateDiff($fecha1, $fecha2,$abs=true) {
			$fecha1 = generalUtils::formatearFechaComparacion ( $fecha1 );
			$fecha2 = generalUtils::formatearFechaComparacion ( $fecha2 );
			$segundosDiferencia = $fecha1 - $fecha2;
			$diasDiferencia = $segundosDiferencia / (60 * 60 * 24);
			
			if(!$abs){
				$diasDiferencia = floor ( $diasDiferencia  );
			}else{
				$diasDiferencia = floor ( abs ( $diasDiferencia ) );
			}
			
			
			return $diasDiferencia;
		}
		
		
		/**
		 * Comparamos fechas
		 */
		static function compararFechas($fechaPrimera, $fechaSegunda) {
			$fechaPrimera = generalUtils::formatearFechaComparacion($fechaPrimera);
			$fechaSegunda = generalUtils::formatearFechaComparacion($fechaSegunda);
		
			//Comparamos
			if ($fechaPrimera < $fechaSegunda) {
				$valor = 1;
			} elseif ($fechaPrimera == $fechaSegunda) {
				$valor = 2;
			} else {
				$valor = 3;
			}
		
			return $valor;
	
		}
		
		
		/**
		 * Agregamos http a una url siempre que no la tenga...
		 */
		static function agregarProtocoloUrl($url) {
		    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
		        $url = "http://" . $url;
		    }
		    return $url;
		}
		
	}//end class
	
