<?php
// Edited original wo_for_parse.html:
// - line 198: changed id from location_name to store_id (semantic error in the original file)
// - line 207: changed id from store_id to location_name (semantic error in the original file)
// - removed id="location_address id" at line 217 (syntax error: same id used at line 216)

$doc = new DOMDocument();
$doc->loadHTMLFile("wo_for_parse.html");

$date_string = trim(preg_replace('/\s/', ' ', $doc->getElementById("scheduled_date")->textContent));
$date = date_create_from_format("F d, Y h:i a", $date_string);


$splitted_address = explode('--', preg_replace('/\s{2,}/', '--', trim($doc->getElementById("location_address")->textContent)));

// creating the header line for CSV
$CSV_lines[0] = array('Tracking number', 'PO Number', 'Scheduled', 'Customer', 'Trade', 'NTE ($)', 'Store ID', 'Street', 'City', 'State', 'ZIP Code', 'Phone number');

// creating the data line for CSV
$CSV_lines[1] = [
    trim($doc->getElementById("wo_number")->textContent),
    trim($doc->getElementById("po_number")->textContent),
    date_format($date, 'Y-m-d H:i'),
    trim($doc->getElementById("customer")->textContent),
    trim($doc->getElementById("trade")->textContent),
    substr(trim($doc->getElementById("nte")->textContent), 1),
    trim($doc->getElementById("store_id")->textContent),
    $splitted_address[0],
    substr($splitted_address[1], 0, -2),
    substr($splitted_address[1], -2),
    $splitted_address[2],
    preg_replace('/[^0-9]/', '', $doc->getElementById("location_phone")->textContent)
];

$fp = fopen('php://output', 'wb');

// solution 1: authomatically download csv file generated
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="wo.csv"');

foreach ($CSV_lines as $line)
    fputcsv($fp, $line, ',');

fclose($fp);

// solution 2: csv file created in the project folder (uncomment it and comment lines 38-44 to use the following)
/* $fp = fopen('wo.csv', 'wb');
foreach ($CSV_lines as $line)
    fputcsv($fp, $line, ',');

fclose($fp); */
