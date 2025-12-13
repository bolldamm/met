<?php
// ==== CONFIGURATION ====
// Public directory for final safe images
$target_dir = "files/METM_attendees/"; // safer if outside webroot
// if (!is_dir($target_dir)) {
//     mkdir($target_dir, 0755, true);
// }

$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
$allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
$maxFileSize = 2 * 1024 * 1024; // 2MB limit

$permitted_chars = '0123456789';
function generate_string($input, $strength)
{
    $input_length = strlen($input);
    $random_string = '';
    for ($i = 0; $i < $strength; $i++) {
        $random_character = $input[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }
    return $random_string;
}

// ==== DEFAULT TO OLD PHOTO ON FAILURE ====
$photo_file = isset($_POST["OldPhoto"]) ? $_POST["OldPhoto"] : null;
$uploadOk = true;

// ==== VALIDATION ====
if (!isset($_FILES['fileImagen']) || !is_uploaded_file($_FILES['fileImagen']['tmp_name'])) {
    $uploadOk = false;
}

$imageFileType = strtolower(pathinfo($_FILES['fileImagen']['name'], PATHINFO_EXTENSION));

if (!in_array($imageFileType, $allowedExtensions)) {
    $uploadOk = false;
}

if ($_FILES["fileImagen"]["size"] > $maxFileSize) {
    $uploadOk = false;
}

if ($uploadOk) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES["fileImagen"]["tmp_name"]);
    finfo_close($finfo);

    if (!in_array($mime, $allowedMimes)) {
        $uploadOk = false;
    }
}

if ($uploadOk) {
    $check = getimagesize($_FILES["fileImagen"]["tmp_name"]);
    if ($check === false) {
        $uploadOk = false;
    }
}

// ==== PROCESS AND SAVE ====
if ($uploadOk) {
    $final_name = generate_string($permitted_chars, 10) . "-" . date("His") . "." . $imageFileType;
    $final_path = $target_dir . $final_name;

    // Create image from upload
    switch ($mime) {
        case 'image/jpeg':
            $img = imagecreatefromjpeg($_FILES["fileImagen"]["tmp_name"]);
            break;
        case 'image/png':
            $img = imagecreatefrompng($_FILES["fileImagen"]["tmp_name"]);
            break;
        case 'image/gif':
            $img = imagecreatefromgif($_FILES["fileImagen"]["tmp_name"]);
            break;
        default:
            $img = null;
            $uploadOk = false;
    }

    if ($img) {
        // Save reprocessed image (removes malicious payloads)
        switch ($mime) {
            case 'image/jpeg':
                imagejpeg($img, $final_path, 90);
                break;
            case 'image/png':
                imagepng($img, $final_path, 6);
                break;
            case 'image/gif':
                imagegif($img, $final_path);
                break;
        }
        imagedestroy($img);

        // Success: update photo reference
        $photo_file = $final_name;
    } else {
        $uploadOk = false;
    }
}

// ==== RESULT ====
// echo "Photo in use: " . htmlspecialchars($photo_file);
?>