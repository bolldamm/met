<?php
	/**
	 * 
	 * Dado un procedimiento que nos devuelve informacion,
	 * procederemos a mostrar los resultados paginados
	 * @author eData
	 * @version 1.0
	 * 
	 */

	/**
	 * 
	 * Instancia de la plantilla paginador
	 * @var object
	 * 
	 */

	$plantillaPaginador = new XTemplate("html/includes/paginator.inc.html");
	
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
	
	$totalPaginas=ceil($totalRegistros/$totalItemsPagina);
		
	/**
	 * 
	 * El numero de registro por el cual tiene que empezar el limit de la consulta
	 * @var int
	 * 
	 */
	$limiteInicioRegistro = 0;
	$paginaActual = 1;
	if(isset($_GET["pagina"]) && $_GET["pagina"] > 0){
		$limiteInicioRegistro = ($_GET["pagina"]-1) * $totalItemsPagina;
		$paginaActual = $_GET["pagina"];
		if($_GET["pagina"] > $totalPaginas){
			$limiteInicioRegistro=0;
			$paginaActual = 1;
		}
	}
	
	//A partir de aqui obtenemos el intervalo de paginas que deben salir en el paginador
	$primeraPagina = 1;
	
	//Si el total de paginas es menor igual a $totalPaginasMostrar, mostraremos por pantalla el total de paginas
	if($totalPaginas <= $totalPaginasMostrar){
		$ultimaPagina = $totalPaginas;
	}else{
		//Si hay mas paginas de las que se pueden mostrar... ponemos hasta $totalPaginasMostrar
		$ultimaPagina = $totalPaginasMostrar;
		
		//Si el usuario nos ha pasado una pagina superior a $totalPaginasMostrar
		if($paginaActual >= $totalPaginasMostrar){
			$ultimaPagina = $paginaActual+1;
			
			//Si hemos excedido la ultima pagina, asignamos a ultima pagina el valor maximo
			if($ultimaPagina > $totalPaginas){
				$ultimaPagina = $totalPaginas;
			}
			$primeraPagina = ($ultimaPagina-$totalPaginasMostrar)+1;
		}
	}
	
	//Si hay mas de una pagina, entonces mostraremos paginador
	if($ultimaPagina > 1){
		
		//Si estamos en primera pagina deshabilitamos el previous
		if($paginaActual > 1){
			//$plantillaPaginador->assign("PREVIOUS_PAGE",$urlActual."pagina=".($paginaActual-1).$urlDinamica);
			$plantillaPaginador->assign("PREVIOUS_PAGE",$urlActual.($paginaActual-1).$urlBusqueda);
			$plantillaPaginador->parse("contenido_principal.paginator_previous_enabled");
		}
		
		//Si estamos en ultima pagina deshabilitamos el next
		if($paginaActual!=$totalPaginas){
			//$plantillaPaginador->assign("NEXT_PAGE",$urlActual."pagina=".($paginaActual+1).$urlDinamica);
			$plantillaPaginador->assign("NEXT_PAGE",$urlActual.($paginaActual+1).$urlBusqueda);
			$plantillaPaginador->parse("contenido_principal.paginator_next_enabled");
		}
		
		for($i=$primeraPagina;$i<=$ultimaPagina;$i++){
			$vectorPagina[$i]["PAGE"]=$i;
			$vectorPagina[$i]["URL"]=$urlActual.$i.$urlBusqueda;
			//$vectorPagina[$i]["URL"]=$urlActual."pagina=".$i.$urlDinamica;
			if($i==$paginaActual){
				$vectorPagina[$i]["CLASS"]="class='current'";
			}else{
				$vectorPagina[$i]["CLASS"]="";
			}
			$plantillaPaginador->assign("PAGINATOR",$vectorPagina[$i]);
			$plantillaPaginador->parse("contenido_principal.paginator_page_item");
		}
		//Color del paginador
		//$plantillaPaginador->assign("CLASS_COLOR_PAGER", $colorPaginador);
		$plantillaPaginador->parse("contenido_principal");
		$subPlantilla->assign("PAGINADOR",$plantillaPaginador->text("contenido_principal"));
	}
$esExcel=false;
	//Establecemos el limit
	if($esExcel){
		$codeProcedure=$codeProcedure."'')";
	}else{
		$codeProcedure=$codeProcedure."'LIMIT ".$limiteInicioRegistro.",".$totalItemsPagina."')";	
	}
?>