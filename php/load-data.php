<?php
header('Content-Type: application/json; charset=utf-8');

$allowed = ['animals','crops','users','tasks','notes'];
$file = isset($_GET['file']) ? preg_replace('/[^a-z]/', '', $_GET['file']) : '';
if (!$file || !in_array($file, $allowed)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file parameter']);
    exit;
}

$path = __DIR__ . '/../data/' . $file . '.xml';
if (!file_exists($path)) {
    echo json_encode([]);
    exit;
}

libxml_use_internal_errors(true);
$xml = simplexml_load_file($path);
if ($xml === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to parse XML']);
    exit;
}

$xmlCopy = $xml;
// Remove sensitive fields for users
if ($file === 'users') {
    foreach ($xmlCopy->user as $u) {
        if (isset($u->password)) unset($u->password[0]);
    }
}
$json = json_encode($xmlCopy);
if ($json === false) {
    echo json_encode(['error' => 'Failed to convert to JSON']);
    exit;
}

header('Content-Type: application/json');
echo $json;
