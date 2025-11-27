<?php
// Script: php/save-entity.php
// Usage: POST file=notes|animals|... and fields as POST params

header('Content-Type: application/json; charset=utf-8');
$allowed = ['animals','crops','users','tasks','notes'];
$file = isset($_POST['file']) ? preg_replace('/[^a-z]/', '', $_POST['file']) : '';
if (!$file || !in_array($file, $allowed)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file parameter']);
    exit;
}

$path = __DIR__ . '/../data/' . $file . '.xml';
if (!file_exists($path)) {
    // create structure with single root
    $root = $file;
    $xmlstr = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<$root>\n</$root>";
    file_put_contents($path, $xmlstr);
}

libxml_use_internal_errors(true);
$xml = simplexml_load_file($path);
if ($xml === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to parse XML']);
    exit;
}

// Determine item name (singular) by removing final 's' (very basic rule)
$rootName = $xml->getName();
$itemName = rtrim($rootName, 's');
$item = $xml->addChild($itemName);

// Add an id field by computing max id
$maxId = 0;
foreach ($xml->{$itemName} as $child) {
    if (isset($child->id)) {
        $id = intval($child->id);
        if ($id > $maxId) $maxId = $id;
    }
}
$newId = $maxId + 1;
$item->addChild('id', $newId);

// Now add other POST parameters
foreach ($_POST as $key => $value) {
    if (in_array($key, ['file'])) continue;
    // skip id; we already added
    if ($key === 'id') continue;
    // sanitize key to letters, numbers, underscore
    $k = preg_replace('/[^a-z0-9_]/i', '', $key);
    if ($k === '') continue;
    // If saving users, hash the password before saving for security
    if ($file === 'users' && strtolower($k) === 'password') {
        // avoid double-hashing if already a bcrypt hash
        if (!preg_match('/^\$2[ayb]\$/', $value)) {
            $value = password_hash($value, PASSWORD_DEFAULT);
        }
    }
    // add child
    $item->addChild($k, htmlspecialchars($value));
}

// Save XML back
$dom = new DOMDocument('1.0', 'utf-8');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML($xml->asXML());
$dom->save($path);

echo json_encode(['success' => true, 'id' => $newId, 'message' => 'Saved']);

?>