<?php
	 /*	require "classes/phpThumb/ThumbLib.inc.php";
		$thumb=PhpThumbFactory::create($_FILES["fileImagen"]["tmp_name"]);
		$extensionImagen=generalUtils::obtenerExtensionFichero($_FILES["fileImagen"]["name"]);
		$id_foto=$_POST['valorimagen'];
		$nombreImagen=$id_foto.".".$extensionImagen;
				
		$thumb->resize(WIDTH_SIZE_MEMBER_INDIVIDUAL, HEIGHT_SIZE_MEMBER_INDIVIDUAL);
		$thumb->save("files/METM_attendees/".$nombreImagen);
		
		//Redimension la imagen
		$thumb->resize(WIDTH_SIZE_MEMBER_INDIVIDUAL_THUMB, HEIGHT_SIZE_MEMBER_INDIVIDUAL_THUMB);
		$thumb->save("files/METM_attendees/thumb/".$nombreImagen);
		
		$data = array('success' => true);
		echo json_encode($data);*/

   //obtenemos el archivo a subir
   
    $file = $_FILES['fileImagen']['name'];
	$ext = pathinfo($file, PATHINFO_EXTENSION);
	
	
   $pic = $_FILES['fileImagen'];
   $aleatorio=$_POST['valorimagen'];
		
		$pic['name']=$aleatorio;
	
	$data = array('success' => false);
	
	
	//Validamos si la copio correctamente 
	if(copy($pic['tmp_name'],'files/METM_attendees/'.$pic['name'])){
		$data = array('success' => true);
		$imagen = $pic['name'];
	}
	
	//Codificamos el array a JSON (Esta sera la respuesta AJAX) 
	echo json_encode($data);
?>
	