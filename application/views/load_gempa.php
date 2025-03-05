<?php
header("Content-Type: application/json");

// Load file XML
$url = "https://bmkg-content-inatews.storage.googleapis.com/live30event.xml";
$xmlContent = file_get_contents($url);

if ($xmlContent === false) {
    echo json_encode(["error" => "Gagal memuat data XML dari BMKG"]);
    exit;
}

$xml = simplexml_load_string($xmlContent);

if ($xml === false) {
    echo json_encode(["error" => "Format XML tidak valid"]);
    exit;
}

$gempaData = [];
foreach ($xml->gempa as $gempa) {
    $gempaData[] = [
        "eventid" => (string) $gempa->eventid,
        "latitude" => (float) $gempa->lintang,
        "longitude" => (float) $gempa->bujur,
        "depth" => (int) $gempa->dalam,
        "magnitude" => (float) $gempa->mag,
        "area" => (string) $gempa->area,
        "time" => (string) $gempa->waktu
    ];
}

// Output JSON
echo json_encode($gempaData);