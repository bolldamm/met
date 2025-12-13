  <?php
    if(function_exists('curl_init')) // Comprobamos si hay soporte para cURL
    {
      $ch = curl_init();
     // curl_setopt($ch, CURLOPT_URL,"http://www.metmeetings.org/easygestor/main_app.php?section=invoice&action=generate&id_factura=980");
      curl_setopt($ch, CURLOPT_URL,"http://www.google.es");
      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      $resultado = curl_exec ($ch);
      curl_close ($ch);
      //print_r($resultado);
      echo "entra";
  }
  else
    echo "No hay soporte para cURL";
?>
