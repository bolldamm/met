<?php

require "includes/load_main_components.inc.php";

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$spreadsheet = new Spreadsheet();
$fichero = "Membership statistics.xls";

$sheet1 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Country');
$spreadsheet->addSheet($sheet1, 0);
$sheet2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Profession');
$spreadsheet->addSheet($sheet2, 1);
$sheet3 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Work situation');
$spreadsheet->addSheet($sheet3, 2);
//$sheet4 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'View preference');
//$spreadsheet->addSheet($sheet4, 3);
$sheet5 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Source language');
$spreadsheet->addSheet($sheet5, 3);
$sheet6 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Target language');
$spreadsheet->addSheet($sheet6, 4);
$sheet7 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Area of expertise');
$spreadsheet->addSheet($sheet7, 5);

//COUNTRY OF RESIDENCE
$spreadsheet->setActiveSheetIndexByName('Country');
$spreadsheet->getActiveSheet()->setCellValue("A1", "Country");
$spreadsheet->getActiveSheet()->setCellValue("B1", "Number");
$spreadsheet->getActiveSheet()->getStyle('A1:B1')->getFont()->setBold(true);
$firstRow = 2;
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(40);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$spreadsheet->getActiveSheet()->getStyle('B1')
    ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

$resultCountryStats = $db->callProcedure("CALL ed_sp_web_usuario_web_statistics_countries_of_residence()");
while ($oneCountry = $db->getData($resultCountryStats)) {
    $vectorCountryStats["COUNTRY_NAME"] = $oneCountry["nombre_original"];
    $vectorCountryStats["COUNTRY_NUMBER"] = $oneCountry["country_total"];

    $spreadsheet->getActiveSheet()->setCellValue("A" . $firstRow, $vectorCountryStats["COUNTRY_NAME"]);
    $spreadsheet->getActiveSheet()->setCellValue("B" . $firstRow, $vectorCountryStats["COUNTRY_NUMBER"]);
    $firstRow++;
}//end while

//PROFESSION
$spreadsheet->setActiveSheetIndexByName('Profession');
$spreadsheet->getActiveSheet()->setCellValue("A1", "Profession");
$spreadsheet->getActiveSheet()->setCellValue("B1", "Number");
$spreadsheet->getActiveSheet()->getStyle('A1:B1')->getFont()->setBold(true);
$firstRow = 2;
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(40);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$spreadsheet->getActiveSheet()->getStyle('B1')
    ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

$resultProfessionStats = $db->callProcedure("CALL ed_sp_web_usuario_web_statistics_professions()");
while ($oneProfession = $db->getData($resultProfessionStats)) {
    $vectorProfessionStats["PROFESSION_NAME"] = $oneProfession["descripcion"];
    $vectorProfessionStats["PROFESSION_NUMBER"] = $oneProfession["total"];

    $spreadsheet->getActiveSheet()->setCellValue("A" . $firstRow, $vectorProfessionStats["PROFESSION_NAME"]);
    $spreadsheet->getActiveSheet()->setCellValue("B" . $firstRow, $vectorProfessionStats["PROFESSION_NUMBER"]);
    $firstRow++;
}//end while

//WORK SITUATION
$spreadsheet->setActiveSheetIndexByName('Work situation');
$spreadsheet->getActiveSheet()->setCellValue("A1", "Work situation");
$spreadsheet->getActiveSheet()->setCellValue("B1", "Number");
$spreadsheet->getActiveSheet()->getStyle('A1:B1')->getFont()->setBold(true);
$firstRow = 2;
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(40);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$spreadsheet->getActiveSheet()->getStyle('B1')
    ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

$resultWorkSituationStats = $db->callProcedure("CALL ed_sp_web_usuario_web_statistics_work_situations()");
while ($oneWorkSituation = $db->getData($resultWorkSituationStats)) {
    $vectorWorkSituationStats["WORK_SITUATION_NAME"] = $oneWorkSituation["nombre"];
    $vectorWorkSituationStats["WORK_SITUATION_NUMBER"] = $oneWorkSituation["work_situation_total"];

    $spreadsheet->getActiveSheet()->setCellValue("A" . $firstRow, $vectorWorkSituationStats["WORK_SITUATION_NAME"]);
    $spreadsheet->getActiveSheet()->setCellValue("B" . $firstRow, $vectorWorkSituationStats["WORK_SITUATION_NUMBER"]);
    $firstRow++;
}//end while

/*
//PROFILE VIEW PREFERENCE
$spreadsheet->setActiveSheetIndexByName('View preference');
$spreadsheet->getActiveSheet()->setCellValue("A1", "View preference");
$spreadsheet->getActiveSheet()->setCellValue("B1", "Number");
$spreadsheet->getActiveSheet()->getStyle('A1:B1')->getFont()->setBold(true);
$firstRow = 2;
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(40);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);

$resultViewPreferenceStats = $db->callProcedure("CALL ed_sp_web_usuario_web_statistics_profile_view_preferences()");
while ($oneViewPreference = $db->getData($resultViewPreferenceStats)) {
    $vectorViewPreferenceStats["VIEW_PREFERENCE_NAME"] = $oneViewPreference["nombre"];
    $vectorViewPreferenceStats["VIEW_PREFERENCE_NUMBER"] = $oneViewPreference["work_situation_total"];

    $spreadsheet->getActiveSheet()->setCellValue("A" . $firstRow, $vectorViewPreferenceStats["VIEW_PREFERENCE_NAME"]);
    $spreadsheet->getActiveSheet()->setCellValue("B" . $firstRow, $vectorViewPreferenceStats["VIEW_PREFERENCE_NUMBER"]);
    $firstRow++;
}//end while
*/

//SOURCE LANGUAGES
$spreadsheet->setActiveSheetIndexByName('Source language');
$spreadsheet->getActiveSheet()->setCellValue("A1", "Source language");
$spreadsheet->getActiveSheet()->setCellValue("B1", "Number");
$spreadsheet->getActiveSheet()->getStyle('A1:B1')->getFont()->setBold(true);
$firstRow = 2;
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(40);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$spreadsheet->getActiveSheet()->getStyle('B1')
    ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

$resultSourceLanguageStats = $db->callProcedure("CALL ed_sp_web_usuario_web_statistics_source_languages()");
while ($oneSourceLanguage = $db->getData($resultSourceLanguageStats)) {
    $vectorSourceLanguageStats["SOURCE_LANGUAGE_NAME"] = $oneSourceLanguage["nombre"];
    $vectorSourceLanguageStats["SOURCE_LANGUAGE_NUMBER"] = $oneSourceLanguage["source_language_total"];

    $spreadsheet->getActiveSheet()->setCellValue("A" . $firstRow, $vectorSourceLanguageStats["SOURCE_LANGUAGE_NAME"]);
    $spreadsheet->getActiveSheet()->setCellValue("B" . $firstRow, $vectorSourceLanguageStats["SOURCE_LANGUAGE_NUMBER"]);
    $firstRow++;
}//end while

//TARGET LANGUAGES
$spreadsheet->setActiveSheetIndexByName('Target language');
$spreadsheet->getActiveSheet()->setCellValue("A1", "Target language");
$spreadsheet->getActiveSheet()->setCellValue("B1", "Number");
$spreadsheet->getActiveSheet()->getStyle('A1:B1')->getFont()->setBold(true);
$firstRow = 2;
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(40);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$spreadsheet->getActiveSheet()->getStyle('B1')
    ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

$resultTargetLanguageStats = $db->callProcedure("CALL ed_sp_web_usuario_web_statistics_target_languages()");
while ($oneTargetLanguage = $db->getData($resultTargetLanguageStats)) {
    $vectorTargetLanguageStats["TARGET_LANGUAGE_NAME"] = $oneTargetLanguage["nombre"];
    $vectorTargetLanguageStats["TARGET_LANGUAGE_NUMBER"] = $oneTargetLanguage["target_language_total"];

    $spreadsheet->getActiveSheet()->setCellValue("A" . $firstRow, $vectorTargetLanguageStats["TARGET_LANGUAGE_NAME"]);
    $spreadsheet->getActiveSheet()->setCellValue("B" . $firstRow, $vectorTargetLanguageStats["TARGET_LANGUAGE_NUMBER"]);
    $firstRow++;
}//end while

//AREAS OF EXPERTISE
$spreadsheet->setActiveSheetIndexByName('Area of expertise');
$spreadsheet->getActiveSheet()->setCellValue("A1", "Area of expertise");
$spreadsheet->getActiveSheet()->setCellValue("B1", "Number");
$spreadsheet->getActiveSheet()->getStyle('A1:B1')->getFont()->setBold(true);
$firstRow = 2;
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(40);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$spreadsheet->getActiveSheet()->getStyle('B1')
    ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

$resultAreaStats = $db->callProcedure("CALL ed_sp_web_usuario_web_statistics_areas_of_expertise()");
while ($oneArea = $db->getData($resultAreaStats)) {
    $vectorAreaStats["AREA_NAME"] = $oneArea["nombre"];
    $vectorAreaStats["AREA_NUMBER"] = $oneArea["area_total"];

    $spreadsheet->getActiveSheet()->setCellValue("A" . $firstRow, $vectorAreaStats["AREA_NAME"]);
    $spreadsheet->getActiveSheet()->setCellValue("B" . $firstRow, $vectorAreaStats["AREA_NUMBER"]);
    $firstRow++;
}//end while



// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);
// Rename worksheet
//$spreadsheet->getActiveSheet()->setTitle('Countries');
// Redirect output to a client’s web browser (Xls)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename=' . $fichero);
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0
$writer = IOFactory::createWriter($spreadsheet, 'Xls');
$writer->save('php://output');
exit;

