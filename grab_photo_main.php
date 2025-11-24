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
  
  // Maximum allowed file size in bytes (e.g., 1 MB)
$max_file_size = 1 * 1024 * 1024;

// Allowed image extensions
$allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

// Validate upload
if (
    !isset($_FILES['fileImagen']) ||
    $_FILES['fileImagen']['error'] !== UPLOAD_ERR_OK
) {
    echo json_encode(['success' => false, 'error' => 'Upload error']);
    exit;
}

    $file = $_FILES['fileImagen']['name'];
    $tmp_file = $_FILES['fileImagen']['tmp_name'];
    $file_size = $_FILES['fileImagen']['size'];
//	$ext = pathinfo($file, PATHINFO_EXTENSION);
	
// 1. Check file size
if ($file_size > $max_file_size) {
    echo json_encode(['success' => false, 'error' => 'File too large']);
    exit;
}

// 2. Get extension & validate
$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
if (!in_array($ext, $allowed_exts)) {
    echo json_encode(['success' => false, 'error' => 'Invalid extension']);
    exit;
}

// 3. Check MIME type using FileInfo
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime  = finfo_file($finfo, $tmp_file);
finfo_close($finfo);

$allowed_mimes = [
    'image/jpeg',
    'image/png',
    'image/gif',
    'image/webp'
];

if (!in_array($mime, $allowed_mimes)) {
    echo json_encode(['success' => false, 'error' => 'Invalid MIME type']);
    exit;
}

// 4. Verify actual image content
$image_info = @getimagesize($tmp_file);
if ($image_info === false) {
    echo json_encode(['success' => false, 'error' => 'File is not a valid image']);
    exit;
}

// 5. Strip EXIF data (for JPEGs) to prevent hidden payloads
if ($mime === 'image/jpeg') {
    $img = imagecreatefromjpeg($tmp_file);
    if ($img === false) {
        echo json_encode(['success' => false, 'error' => 'Error processing JPEG']);
        exit;
    }
    imagejpeg($img, $tmp_file, 100); // Overwrite without EXIF
    imagedestroy($img);

} elseif ($mime === 'image/png') {
    $img = imagecreatefrompng($tmp_file);
    if ($img === false) {
        echo json_encode(['success' => false, 'error' => 'Error processing PNG']);
        exit;
    }
    imagepng($img, $tmp_file, 0); // Overwrite without metadata, 0 = no compression
    imagedestroy($img);

} elseif ($mime === 'image/gif') {
    $img = imagecreatefromgif($tmp_file);
    if ($img === false) {
        echo json_encode(['success' => false, 'error' => 'Error processing GIF']);
        exit;
    }
    imagegif($img, $tmp_file); // Overwrite without metadata
    imagedestroy($img);
}


   $pic = $_FILES['fileImagen'];
   $aleatorio=$_POST['valorimagen'];
		
		$pic['name']=$aleatorio;
	
	$data = array('success' => false);
	
	
	//Validamos si la copio correctamente 
//	if(copy($pic['tmp_name'],'files/METM_attendees/'.$pic['name'])){
    if(move_uploaded_file($pic['tmp_name'],'files/METM_attendees/'.$pic['name'])){
		$data = array('success' => true);
		$imagen = $pic['name'];
	}
	
	//Codificamos el array a JSON (Esta sera la respuesta AJAX) 
	echo json_encode($data);
    
/*
// 6. Create safe filename
$aleatorio = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_POST['valorimagen']);
$new_name  = $aleatorio . '.' . $ext;

// 7. Ensure target directory exists
$target_dir = 'files/METM_attendees';
// if (!is_dir($target_dir)) {
//    mkdir($target_dir, 0755, true);
//}

// 8. Move file securely
if (move_uploaded_file($tmp_file, $target_dir . '/' . $new_name)) {
    echo json_encode(['success' => true, 'file' => $new_name]);
} else {
    echo json_encode(['success' => false, 'error' => 'Could not save file']);
}
*/
?>