<?php
/**
 *
 * Presentamos por pantalla este formulario
 * @author Edata S.L.
 * @copyright Edata S.L.
 * @version 1.0
 */

//Combo titulos
$plantillaFormulario->assign("COMBO_TITULOS", generalUtils::construirCombo($db, "CALL ed_sp_web_tratamiento_usuario_web_obtener_combo(".$_SESSION["id_idioma"].")", "cmbTitulo", "cmbTitulo", -1, "nombre", "id_tratamiento_usuario_web", STATIC_FORM_MEMBERSHIP_TITLE, -1, 'class="form-control" style="color:lightslategray;"'));

//Combo paises
$plantillaFormulario->assign("COMBO_PAIS", generalUtils::construirCombo($db, "CALL ed_sp_web_pais_obtener_combo()", "cmbPais", "cmbPais", -1, "nombre_original", "id_pais", STATIC_FORM_MEMBERSHIP_COUNTRY_OF_RESIDENCE."*", -1, 'class="form-control" style="width:100%; color:lightslategray;" autocomplete="country-name" '));

//Combo años
$plantillaFormulario->assign("COMBO_ANYOS", generalUtils::construirCombo($db, "CALL ed_sp_web_edad_usuario_web_obtener_combo(".$_SESSION["id_idioma"].")", "cmbAnyos", "cmbAnyos", -1, "nombre", "id_edad_usuario_web", STATIC_FORM_MEMBERSHIP_AGE, -1, 'class="form-control" style="width:5em; color:lightslategray;"'));


//Combo situacion adicional
$plantillaFormulario->assign("COMBO_SITUACION_ADICIONAL", generalUtils::construirCombo($db, "CALL ed_sp_web_situacion_adicional_obtener_combo(".$_SESSION["id_idioma"].")", "cmbSituacionAdicional", "cmbSituacionAdicional", -1, "nombre", "id_situacion_adicional", "Standard", -1, 'class="form-control" style="width:100%; margin-top:1em; color:lightslategray;"' ));


//Listado de actividades profesionales
$resulActividadesProfesionales = $db->callProcedure("CALL ed_sp_web_actividad_profesional_obtener_listado(".$_SESSION["id_idioma"].")");
$validacion = "";
while($dataActividadProfesional = $db->getData($resulActividadesProfesionales)) {
    $nombreElemento = str_replace("-", "", $dataActividadProfesional["descripcion"]);
    $plantillaFormulario->assign("ITEM_ACTIVIDAD_PROFESIONAL_ID", $dataActividadProfesional["id_actividad_profesional"]);
    $plantillaFormulario->assign("ITEM_ACTIVIDAD_PROFESIONAL_NOMBRE", $dataActividadProfesional["descripcion"]);
    $plantillaFormulario->assign("ITEM_ACTIVIDAD_PROFESIONAL_NOMBRE_ELEMENTO", $nombreElemento);
    $validacion .= "!document.frmMembership.chk".$nombreElemento.".checked && ";

    $plantillaFormulario->parse("contenido_principal.item_actividad_profesional");
}

//Listamos situaciones laborales
$resultadoSituacionLaboral = $db->callProcedure("CALL ed_sp_web_situacion_laboral_obtener(".$_SESSION["id_idioma"].")");
while($dataSituacionLaboral = $db->getData($resultadoSituacionLaboral)) {
    $plantillaFormulario->assign("SITUACION_LABORAL_ID", $dataSituacionLaboral["id_situacion_laboral"]);
    $plantillaFormulario->assign("SITUACION_LABORAL_NOMBRE", $dataSituacionLaboral["nombre"]);

    $plantillaFormulario->parse("contenido_principal.item_situacion_laboral");
}
$plantillaFormulario->assign("USUARIO_MODALIDAD",MODALIDAD_USUARIO_INDIVIDUAL);

/**
 * Realizamos todos los parse relacionados con este apartado
 */
$plantilla->parse("contenido_principal.validate_membership_form");
?>