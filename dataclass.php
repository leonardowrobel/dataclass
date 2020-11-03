<?php

// Google config
require __DIR__ . '/vendor/autoload.php';

$client = new \Google_Client();
$client->setApplicationName('Google Sheets API PHP Quickstart');
$client->setScopes(Google_Service_Sheets::SPREADSHEETS);
$client->setAuthConfig(__DIR__ . '/credentials.json');
$client->setAccessType('offline');

$service = new Google_Service_Sheets($client);
$spreadsheetId = "1ls_2SPIzClGAWovZ7-Xau0IO6OQT_xqNr8QxuwbTmUQ";

// Especify where(range) to read on spreadsheet
$rangeGrades = "Semester_1!A4:F27";
// Especify where to find total classes on spreadsheet
$rangeTotalClasses = "Semester_1!C2";
// Get data and set local variables
$response = $service->spreadsheets_values->get($spreadsheetId, $rangeGrades);
$valuesGrades = $response->getValues();
$response = $service->spreadsheets_values->get($spreadsheetId, $rangeTotalClasses);
$totalClasses = $response->getValues()[0][0];
$maxClassAbsences = round(25 * $totalClasses / 100);

// Especify where(range) to edit on spreadsheet
$rangeResults = "Semester_1!G4:H27";
$valuesResults = array();

if (empty($valuesGrades)) {
    // 
    print "No data.\r\n";
} else {
    // Business logic
    foreach ($valuesGrades as $row) {
        $gradesAvg = round(($row[3] + $row[4] + $row[5]) / 3);
        $status = "";
        $msp = "";        
        if ($row[2] > $maxClassAbsences) {            
            $status = "Reprovado";
        } else {
            if ($gradesAvg >= 70) {                
                $status = "Aprovado";
            } else {
                $msp = 100 - $gradesAvg;
                $status = "Final";                
            }
        }
        $valuesResults[] = [$status, $msp];
    }
    // Test generated data 
    /* foreach($valuesResults as $row){   
        echo " - " . $row[0] . " - " . $row[1] . "\r\n";
    } */ 
    
    // Preparing to edit remote spreadsheet
    $body = new Google_Service_Sheets_ValueRange([
        'values' => $valuesResults
    ]);
    $params = [
        'valueInputOption' => 'RAW'
    ];
    // Execute the changes on remote spreadsheet
    $result = $service->spreadsheets_values->update(
        $spreadsheetId,
        $rangeResults,
        $body,
        $params
    );
}
