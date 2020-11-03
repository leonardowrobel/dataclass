<?php

require __DIR__ . '/vendor/autoload.php';

$client = new \Google_Client();
$client->setApplicationName('Google Sheets API PHP Quickstart');
$client->setScopes(Google_Service_Sheets::SPREADSHEETS);
$client->setAuthConfig(__DIR__ . '/credentials.json');
$client->setAccessType('offline');

$service = new Google_Service_Sheets($client); 
$spreadsheetId = "1ls_2SPIzClGAWovZ7-Xau0IO6OQT_xqNr8QxuwbTmUQ";

$range = "Semester_1!A4:F27";
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();

if(empty($values)){
    print "No data\n";
} else {    
    foreach($values as $row){
        echo $row[0] . '\n';
    }
}
