<?php

require __DIR__ . '/vendor/autoload.php';

$client = new \Google_Client();
$client->setApplicationName('Google Sheets API PHP Quickstart');
$client->setScopes(Google_Service_Sheets::SPREADSHEETS);
$client->setAuthConfig(__DIR__ . '/credentials.json');
$client->setAccessType('offline');

$service = new Google_Service_Sheets($client); 
$spreadsheetId = "1ls_2SPIzClGAWovZ7-Xau0IO6OQT_xqNr8QxuwbTmUQ";

$rangeGrades = "Semester_1!A4:F27";
$rangeTotalClasses = "Semester_1!C2";
$response = $service->spreadsheets_values->get($spreadsheetId, $rangeGrades);
$valuesGrades = $response->getValues();
$response = $service->spreadsheets_values->get($spreadsheetId, $rangeTotalClasses);
$totalClasses = $response->getValues()[0][0];
$maxClassAbsences = round (25 * $totalClasses / 100);

if(empty($valuesGrades)){
    print "No data.\r\n";
} else {       
    echo "\r\n============================================\r\n\r\n"; 
    foreach($valuesGrades as $row){
        $gradesAvg = round (($row[3] + $row[4] + $row[5])/3);
        echo $row[0] . " - " . $row[1] . " - AVG: " . $gradesAvg . " - ";
        if($row[2] > $maxClassAbsences ){
            echo " Failed by absences.\r\n";
        } else {            
            if($gradesAvg >= 70){
                echo " Pass.\r\n";
            } else {
                $msp = 100 - $gradesAvg;
                echo " Final Exam. [Minimum Score to Pass:" . $msp . "]\r\n";
            }
        }
        
    }
}
