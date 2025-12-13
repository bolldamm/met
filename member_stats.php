<?php

require "includes/load_main_components.inc.php";

$plantilla = new XTemplate("html/index.html");

$subPlantilla = new XTemplate("html/member_stats.html");

$plantilla->assign("SECTION_FILE_CSS", "openbox.css");

require "includes/load_structure.inc.php";

$subPlantilla->assign("CURRENT_DATE", date("j F Y"));


if (isset($_GET["menu"]) && is_numeric($_GET["menu"])) {

    //Get number of paid-up individual members
    $memberCount = 0;
    $resultMembers = $db->callProcedure("CALL ed_sp_web_usuario_web_statistics_paidup_members()");
    $rows = $db->getData($resultMembers);
    $memberCount = $rows["total"];
    $subPlantilla->assign("TOTAL_PAIDUP_MEMBERS", $memberCount);

    //Get countries of residence
    $resultCountriesOfResidence = $db->callProcedure("CALL ed_sp_web_usuario_web_statistics_countries_of_residence()");
    $totalCountries = $db->getNumberRows($resultCountriesOfResidence);
    $percentCounter = 0;
    while ($rowCountries = $db->getData($resultCountriesOfResidence)) {
        $percent = round(($rowCountries["country_total"] / $memberCount) * 100, 1);
        $percentCounter += $percent;
        $subPlantilla->assign("MEMBER_STATISTICS_COUNTRY", $rowCountries["nombre_original"]);
        $subPlantilla->assign("MEMBER_STATISTICS_COUNTRY_NUMBER", $rowCountries["country_total"]);
        $subPlantilla->assign("MEMBER_STATISTICS_COUNTRY_PERCENT", $percent);
        $subPlantilla->parse("contenido_principal.item_countries");
    }

    //Get professions
    $resultProfessions = $db->callProcedure("CALL ed_sp_web_usuario_web_statistics_professions()");
    while ($rowProfessions = $db->getData($resultProfessions)) {
        $subPlantilla->assign("MEMBER_STATISTICS_PROFESSION", $rowProfessions["descripcion"]);
        $subPlantilla->assign("MEMBER_STATISTICS_PROFESSION_NUMBER", $rowProfessions["total"]);
        $subPlantilla->assign("MEMBER_STATISTICS_PROFESSION_PERCENT", $percent = round(($rowProfessions["total"] / $memberCount) * 100, 1));
        $subPlantilla->parse("contenido_principal.item_professions");
    }

    //Get only translators (and nothing else)
    $resultEditorOnlyz = $db->callProcedure("CALL ed_sp_web_usuario_web_statistics_professions_editor_onlyz()");
    $onlyEditorsz = $db->getData($resultEditorOnlyz);
    $subPlantilla->assign("MEMBER_STATISTICS_EDITOR_ONLYZ", $onlyEditorsz["total"]);

    //Get only editors (and nothing else)
    $resultTranslatorOnlyz = $db->callProcedure("CALL ed_sp_web_usuario_web_statistics_professions_translator_onlyz()");
    $onlyTranslatorsz = $db->getData($resultTranslatorOnlyz);
    $subPlantilla->assign("MEMBER_STATISTICS_TRANSLATOR_ONLYZ", $onlyTranslatorsz["total"]);

    //Get only translators
    $resultEditorOnly = $db->callProcedure("CALL ed_sp_web_usuario_web_statistics_professions_editor_only()");
    $onlyEditors = $db->getData($resultEditorOnly);
    $subPlantilla->assign("MEMBER_STATISTICS_EDITOR_ONLY", $onlyEditors["total"]);

    //Get only editors
    $resultTranslatorOnly = $db->callProcedure("CALL ed_sp_web_usuario_web_statistics_professions_translator_only()");
    $onlyTranslators = $db->getData($resultTranslatorOnly);
    $subPlantilla->assign("MEMBER_STATISTICS_TRANSLATOR_ONLY", $onlyTranslators["total"]);

    //Get editors AND translators
    $resultTE = $db->callProcedure("CALL ed_sp_web_usuario_web_statistics_professions_TE()");
    $tE = $db->getData($resultTE);
    $subPlantilla->assign("MEMBER_STATISTICS_EDITOR_AND_TRANSLATOR", $tE["total"]);

    //Get work situations
    $resultWorkSituations = $db->callProcedure("CALL ed_sp_web_usuario_web_statistics_work_situations()");
    $totalWorkSituations = $db->getNumberRows($resultWorkSituations);
    $percentCounter = 0;
    while ($rowWorkSituation = $db->getData($resultWorkSituations)) {
        $percent = round(($rowWorkSituation["work_situation_total"] / $memberCount) * 100, 1);
        $percentCounter += $percent;
        $subPlantilla->assign("MEMBER_STATISTICS_WORK_SITUATION", $rowWorkSituation["nombre"]);
        $subPlantilla->assign("MEMBER_STATISTICS_WORK_SITUATION_NUMBER", $rowWorkSituation["work_situation_total"]);
        $subPlantilla->assign("MEMBER_STATISTICS_WORK_SITUATION_PERCENT", $percent);
        $subPlantilla->parse("contenido_principal.item_work_situations");
    }

    //Get source languages
    $resultSourceLanguages = $db->callProcedure("CALL ed_sp_web_usuario_web_statistics_source_languages()");
    $totalSourceLanguages = $db->getNumberRows($resultSourceLanguages);
    $percentCounter = 0;
    while ($rowSourceLanguage = $db->getData($resultSourceLanguages)) {
        $percent = round(($rowSourceLanguage["source_language_total"] / $memberCount) * 100, 1);
        $percentCounter += $percent;
        $subPlantilla->assign("MEMBER_STATISTICS_SOURCE_LANGUAGE", $rowSourceLanguage["nombre"]);
        $subPlantilla->assign("MEMBER_STATISTICS_SOURCE_LANGUAGE_NUMBER", $rowSourceLanguage["source_language_total"]);
        $subPlantilla->assign("MEMBER_STATISTICS_SOURCE_LANGUAGE_PERCENT", $percent);
        $subPlantilla->parse("contenido_principal.item_source_languages");
    }

    //Get target languages
    $resultTargetLanguages = $db->callProcedure("CALL ed_sp_web_usuario_web_statistics_target_languages()");
    $totalTargetLanguages = $db->getNumberRows($resultTargetLanguages);
    $percentCounter = 0;
    while ($rowTargetLanguage = $db->getData($resultTargetLanguages)) {
        $percent = round(($rowTargetLanguage["target_language_total"] / $memberCount) * 100, 1);
        $percentCounter += $percent;
        $subPlantilla->assign("MEMBER_STATISTICS_TARGET_LANGUAGE", $rowTargetLanguage["nombre"]);
        $subPlantilla->assign("MEMBER_STATISTICS_TARGET_LANGUAGE_NUMBER", $rowTargetLanguage["target_language_total"]);
        $subPlantilla->assign("MEMBER_STATISTICS_TARGET_LANGUAGE_PERCENT", $percent);
        $subPlantilla->parse("contenido_principal.item_target_languages");
    }

    //Get areas of expertise
    $resultAreas = $db->callProcedure("CALL ed_sp_web_usuario_web_statistics_areas_of_expertise()");
    $totalAreas = $db->getNumberRows($resultAreas);
    $percentCounter = 0;
    while ($rowArea = $db->getData($resultAreas)) {
        $percent = round(($rowArea["area_total"] / $memberCount) * 100, 1);
        $percentCounter += $percent;
        $subPlantilla->assign("MEMBER_STATISTICS_AREA_OF_EXPERTISE", $rowArea["nombre"]);
        $subPlantilla->assign("MEMBER_STATISTICS_AREA_OF_EXPERTISE_NUMBER", $rowArea["area_total"]);
        $subPlantilla->assign("MEMBER_STATISTICS_AREA_OF_EXPERTISE_PERCENT", $percent);
        $subPlantilla->parse("contenido_principal.item_areas_of_expertise");
    }

    //Get profile view preferences
    $resultPreferences = $db->callProcedure("CALL ed_sp_web_usuario_web_statistics_profile_view_preferences()");
    $totalPreferences = $db->getNumberRows($resultPreferences);
    $percentCounter = 0;

    while ($rowPreference = $db->getData($resultPreferences)) {
        $percent = round(($rowPreference["preference_total"] / $memberCount) * 100, 1);
        $percentCounter += $percent;
        $public = $rowPreference["preference_total"];
        $subPlantilla->assign("MEMBER_STATISTICS_PROFILE_VIEW_PREFERENCE", "Public");
        $subPlantilla->assign("MEMBER_STATISTICS_PROFILE_VIEW_PREFERENCE_NUMBER", $public);
        $subPlantilla->assign("MEMBER_STATISTICS_PROFILE_VIEW_PREFERENCE_PERCENT", $percent);
    }
    $subPlantilla->parse("contenido_principal.item_profile_view_preferences");

    $subPlantilla->assign("MEMBER_STATISTICS_PROFILE_VIEW_PREFERENCE_NUMBERZ", $memberCount - $public);
    $subPlantilla->assign("MEMBER_STATISTICS_PROFILE_VIEW_PREFERENCE_PERCENTZ", 100 - $percent);


} else {
    generalUtils::redirigir(CURRENT_DOMAIN);
}

//Load breadcrumbs
require "includes/load_breadcrumb.inc.php";

//Do not load left-hand menu
//require "includes/load_menu_left.inc.php";

//Load slider (images)
require "includes/load_slider.inc.php";

$subPlantilla->parse("contenido_principal");

$plantilla->parse("contenido_principal.bloque_ready");
$plantilla->parse("contenido_principal.control_superior");

$plantilla->assign("CONTENIDO", $subPlantilla->text("contenido_principal"));

//Parse inner page content full width, without lefthand menu
//$plantilla->parse("contenido_principal.menu_left");
$plantilla->parse("contenido_principal.full_width_content");

$plantilla->parse("contenido_principal");
$plantilla->out("contenido_principal");



