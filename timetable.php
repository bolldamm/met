<?php
  /**
   * 
   * Pagina openbox de contenido libre
   * @author Edata S.L.
   * @copyright Edata S.L.
   * @version 1.0
   */
  require "includes/load_main_components.inc.php";
  
  // Instanciamos la clase Xtemplate con la plantilla base
  $plantilla = new XTemplate("html/index.html");
  
  // Instanciamos la clase Xtemplate con la plantilla que va a contener la información del apartado
  $subPlantilla = new XTemplate("html/timetable.html"); 
  
  if(isset($_GET["menu"]) && is_numeric($_GET["menu"])) {
    $idMenu = $_GET["menu"];
    
    //Cargamos el breadcrumb
    require "includes/load_breadcrumb.inc.php";
    
    
    //Cargamos toda la información de openbox
    $resultOpenbox = $db->callProcedure("CALL ed_sp_web_openbox_obtener(".$_SESSION["id_idioma"].", ".$_GET["menu"].")");
    if($db->getNumberRows($resultOpenbox) > 0) {

      $dataOpenbox = $db->getData($resultOpenbox);
      $idPadre=$dataOpenbox["id_padre"];
      $subPlantilla->assign("APARTADO_CONTENIDO", $dataOpenbox["descripcion"]);
      $subPlantilla->assign("APARTADO_TITULO", $dataOpenbox["nombre"]);
      
      $plantilla->assign("SIGLAS_IDIOMA",$_SESSION["siglas"]);
      $plantilla->assign("SIGLAS_IDIOMA_MAYUSCULA",strtoupper($_SESSION["siglas"]));
      
      $iMonth       = (int) date("m");
      $totalMonth   = 12 - $iMonth;
      $iNumeroMonth = 1;

      for($i = $iMonth; $i <= 12;$i++) {

        $plantilla->assign("CONTADOR_MES",($i-1));
        $subPlantilla->assign("CONTADOR_MES",($i-1));
        
        $mesCompleto = ($i<=9) ? '0'.$i : $i;
        
        $sDatePrint = ($mesCompleto == date("m")) ? "new Date().Today" : '"'.date("Y")."-".$mesCompleto."-01".'"';
        
        $plantilla->assign("DIA_ACTUAL",$sDatePrint);
        $plantilla->assign("ANYO_ACTUAL",date("Y"));
        $plantilla->parse("contenido_principal.bloque_calendar.bloque_calendar_script");
        
        $subPlantilla->parse("contenido_principal.row_calendar_html.calendar_html");

        if($iNumeroMonth > 0 && $iNumeroMonth%3==0){
          $subPlantilla->parse("contenido_principal.row_calendar_html");  
        }//end if
        $iNumeroMonth++;
        
      }//end for

      $totalMonth = ($totalMonth == 0) ? 11 : $totalMonth;

      for($i = 1; $i <= $totalMonth;$i++) {
        
        $plantilla->assign("CONTADOR_MES",($i-1));
        $subPlantilla->assign("CONTADOR_MES",($i-1));
        
        $mesCompleto = ($i<=9) ? '0'.$i : $i;

        $sYear = (int) date("Y");
        $sYear++;
        $plantilla->assign("ANYO_ACTUAL",$sYear);
        $sDatePrint = ($mesCompleto == date("m")) ? "new Date().Today" : '"'.$sYear."-".$mesCompleto."-01".'"';
        
        $plantilla->assign("DIA_ACTUAL",$sDatePrint);

        $plantilla->parse("contenido_principal.bloque_calendar.bloque_calendar_script");
        
        $subPlantilla->parse("contenido_principal.row_calendar_html.calendar_html");  
        if($iNumeroMonth > 0 && $iNumeroMonth%3==0){
          $subPlantilla->parse("contenido_principal.row_calendar_html");  
        }//end if
        $iNumeroMonth++;
        
      }//end for
    
      $plantilla->parse("contenido_principal.script_calendar");
      $plantilla->parse("contenido_principal.bloque_calendar");

      //Cargamos las imagenes del menú openbox
      $resultImagenes=$db->callProcedure("CALL ed_sp_web_menu_archivo_obtener(".$_GET["menu"].")");
      $dataImagenes=$db->getData($resultImagenes);
      
      
      if($dataImagenes["nombre"]==""){
        $resultImagenes=$db->callProcedure("CALL ed_sp_web_menu_archivo_obtener(".$explodeMenuId[0].")");
        $dataImagenes=$db->getData($resultImagenes);
      }
      
      $plantilla->assign("IMAGEN_CABECERA", "files/menu/".$dataImagenes["nombre"]);
      $plantilla->parse("contenido_principal.imagen_cabecera");

    } else { generalUtils::redirigir(CURRENT_DOMAIN); }
    
    
  } else { generalUtils::redirigir(CURRENT_DOMAIN); }
  


  require "includes/load_structure.inc.php";
  
  require "includes/load_menu_left.inc.php";
  
  
  $subPlantilla->parse("contenido_principal");
  
  $plantilla->parse("contenido_principal.script_slider");
  $plantilla->parse("contenido_principal.bloque_ready.bloque_slider");
  
  $plantilla->parse("contenido_principal.bloque_ready");
  

  
  //Exportamos plantilla secundaria a la principal
  $plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));

    //Parse inner page content with lefthand menu
    $plantilla->parse("contenido_principal.menu_left");

    //Parseamos y sacamos informacion por pantalla
  $plantilla->parse("contenido_principal");
  $plantilla->out("contenido_principal");