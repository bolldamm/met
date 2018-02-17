<?php
	/**
	 * 
	 * Dado un procedimiento que nos devuelve informacion,
	 * procederemos a mostrar los resultados paginados
	 * @author eData
	 * 
	 */

	/**
	 * 
	 * Instancia de la plantilla paginador
	 * @var object
	 * 
	 */
	$plantillaPaginador=new XTemplate("html/paginador.html");
	
	$resultado=$db->callProcedure($codeProcedure."'')");
	/**
	 * 
	 * Almacenamos el total de registros que hay en la base de datos
	 * @var int
	 */
	$totalRegistros=$db->getNumberRows($resultado);
	
	/**
	 * 
	 * A partir del total de registros y numero de registros que pueden salir por pagina
	 * obtenemos el total de paginas que existirian en sistema
	 * @var int
	 * 
	 */
	$totalPaginas=ceil($totalRegistros/$numeroRegistrosPagina);
	
	/**
	 * 
	 * El numero de registro por el cual tiene que empezar el limit de la consulta
	 * @var int
	 * 
	 */
	$limiteInicioRegistro=0;
	$paginaActual=1;
	if(isset($_GET["pagina"]) && $_GET["pagina"]>0){
		$limiteInicioRegistro=($_GET["pagina"]-1)*$numeroRegistrosPagina;
		$paginaActual=$_GET["pagina"];
		if($_GET["pagina"]>$totalPaginas){
			$limiteInicioRegistro=0;
			$paginaActual=1;
		}
	}
	//A partir de aqui obtenemos el intervalo de paginas que deben salir en el paginador
	$primeraPagina=1;
	
	//Si el total de paginas es menor igual a $totalPaginasMostrar, mostraremos por pantalla el total de paginas
	if($totalPaginas<=$totalPaginasMostrar){
		$ultimaPagina=$totalPaginas;
	}else{
		//Si hay mas paginas de las que se pueden mostrar... ponemos hasta $totalPaginasMostrar
		$ultimaPagina=$totalPaginasMostrar;
		
		//Si el usuario nos ha pasado una pagina superior a $totalPaginasMostrar
		if($paginaActual>=$totalPaginasMostrar){
			$ultimaPagina=$paginaActual+1;
			
			//Si hemos excedido la ultima pagina, asignamos a ultima pagina el valor maximo
			if($ultimaPagina>$totalPaginas){
				$ultimaPagina=$totalPaginas;
			}
			$primeraPagina=($ultimaPagina-$totalPaginasMostrar)+1;
		}
	}
	
	//Si hay mas de una pagina, entonces mostraremos paginador
	if($ultimaPagina>1){
		
		//Si estamos en primera pagina deshabilitamos el previous
		if($paginaActual==1){
			$plantillaPaginador->parse("paginador_parte_superior.boton_anterior_disabled");
			$plantillaPaginador->parse("paginador_parte_inferior.boton_anterior_disabled");
		}else{
			$plantillaPaginador->assign("PREVIOUS_PAGE",$urlActual."pagina=".($paginaActual-1));
			$plantillaPaginador->parse("paginador_parte_superior.boton_anterior");
			$plantillaPaginador->parse("paginador_parte_inferior.boton_anterior");
		}

		//Si estamos en ultima pagina deshabilitamos el next
		if($paginaActual==$totalPaginas){
			$plantillaPaginador->parse("paginador_parte_superior.boton_siguiente_disabled");
			$plantillaPaginador->parse("paginador_parte_inferior.boton_siguiente_disabled");
		}else{
			$plantillaPaginador->assign("NEXT_PAGE",$urlActual."pagina=".($paginaActual+1));
			$plantillaPaginador->parse("paginador_parte_superior.boton_siguiente");
			$plantillaPaginador->parse("paginador_parte_inferior.boton_siguiente");
		}
		
		//Construimos la navegacion superior e inferior
		$plantillaPaginador->parse("paginador_parte_inferior");

		//Mostramos por pantalla los menus de navegacion
		$subPlantilla->assign("PAGINADOR_NAVEGACION_INFERIOR",$plantillaPaginador->text("paginador_parte_inferior"));
		
		//Asignamos la informacion del paginador
		$plantillaPaginador->assign("PAGINA_ACTUAL",$paginaActual);
		$plantillaPaginador->assign("TOTAL_PAGINAS",$totalPaginas);
		
		//Construimos informacion del paginador
		$plantillaPaginador->parse("paginador_informacion");
		
		//Mostramos por pantalla informacion
		$subPlantilla->assign("PAGINADOR_INFORMACION",$plantillaPaginador->text("paginador_informacion"));
		

		for($i=$primeraPagina;$i<=$ultimaPagina;$i++){
			$vectorPagina[$i]["PAGE"]=$i;
			$vectorPagina[$i]["URL"]=$urlActual."pagina=".$i;
			if($i==$paginaActual){
				$vectorPagina[$i]["CLASS"]="class='active'";
			}else{
				$vectorPagina[$i]["CLASS"]="";
			}
			$plantillaPaginador->assign("PAGINATOR",$vectorPagina[$i]);
			$plantillaPaginador->parse("paginador_bloque.item_paginador");
		}
		$plantillaPaginador->parse("paginador_bloque");
		$subPlantilla->assign("PAGINADOR",$plantillaPaginador->text("paginador_bloque"));
	}
	
	$plantillaPaginador->parse("paginador_parte_superior");
	$subPlantilla->assign("PAGINADOR_NAVEGACION_SUPERIOR",$plantillaPaginador->text("paginador_parte_superior"));
	
	//Si queremos mostrar todos los registros por pantalla
	if($numeroRegistrosPagina==-1){
		$limite="''";
	}else{
		$limite="'LIMIT ".$limiteInicioRegistro.",".$numeroRegistrosPagina."'";
	}
	
	//Establecemos el limit
	$codeProcedure=$codeProcedure.$limite.")";
?>