<?php

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

//Store name of existing image or empty variable
if (isset($_POST["hdnNombreImagen_1"]) && $_POST["hdnNombreImagen_1"] != "default.jpg") {
    $fileName = $_POST["hdnNombreImagen_1"];
} else {
    $fileName = "";
}

//If delete button has been clicked, delete image and empty variable
if ($_POST["hdnEliminarImagen_1"] == 1) {
    unlink($_SERVER['DOCUMENT_ROOT'] . "/files/METM_attendees/" . $fileName);
    $fileName = "";
}

//If a new image has been added
if ($_FILES["fileAttendeePhoto"]["tmp_name"]) {
    $imageFileExtension = generalUtils::obtenerExtensionFichero($_FILES["fileAttendeePhoto"]["name"]);
    $imageFileName = generate_string($permitted_chars, 10) . "-" . date("his");
    $fileName = $imageFileName . "." . $imageFileExtension;
    $target = $_SERVER['DOCUMENT_ROOT'] . "/files/METM_attendees/" . $fileName;
    move_uploaded_file($_FILES['fileAttendeePhoto']['tmp_name'], $target);

}

if (isset($_POST["listPermission"])) {
    $showOnList = "1";
} else {
    $showOnList = "0";
}

$BadgeText = generalUtils::escaparCadena($_POST["txtBadge"]);
$BadgeText = str_replace ( "\n", "<br />", $BadgeText );

//Update attendee photo in ed_tb_inscripcion_conferencia
$resultado = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_update_attendee_list_details(" . $registrationId . ",'" . $fileName . "'," . $showOnList . ",'" . $BadgeText . "')");


