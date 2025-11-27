<?php
// Script: php/save-entity.php
// Usage: POST file=notes|animals|... and fields as POST params

 $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if (session_status() === PHP_SESSION_NONE) session_start();
// where to redirect if not AJAX
$return = isset($_POST['return']) ? $_POST['return'] : '../index.php';
require_once __DIR__ . '/csrf.php';
// CSRF check for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify_request()) {
    if ($isAjax) { http_response_code(403); echo json_encode(['error' => 'Invalid CSRF token']); exit; }
    $_SESSION['flash'] = 'Invalid request (CSRF token mismatch)'; header('Location: ../index.php'); exit;
}
header('Content-Type: application/json; charset=utf-8');
$allowed = ['animals','crops','users','tasks','notes'];
$file = isset($_POST['file']) ? preg_replace('/[^a-z]/', '', $_POST['file']) : '';
// Authorization: notes can be saved by a logged user or admin; other datasets need admin.
if (!$file || !in_array($file, $allowed)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file parameter']);
    exit;
}

// Authorization checks before writing
if ($file === 'notes') {
    if (!isset($_SESSION['user']) && !isset($_SESSION['admin_logged'])) {
        if ($isAjax) { http_response_code(401); echo json_encode(['error' => 'Unauthorized']); exit; }
        $_SESSION['flash'] = 'Please sign in to add notes'; header('Location: ../index.php?auth=1'); exit;
    }
} else {
    // other files require admin
    if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
        if ($isAjax) { http_response_code(403); echo json_encode(['error' => 'Admin-only endpoint']); exit; }
        $_SESSION['flash'] = 'Admin access required'; header('Location: ../index.php?auth=1'); exit;
    }
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
$editId = isset($_POST['id']) ? intval($_POST['id']) : 0;
$item = null;
// If editing, find existing item
if ($editId > 0) {
    foreach ($xml->{$itemName} as $child) {
        if (isset($child->id) && intval($child->id) === $editId) {
            $item = $child;
            break;
        }
    }
}
// If not editing or not found, create new
if ($item === null) {
    $item = $xml->addChild($itemName);
}

// Add an id field by computing max id only if creating new
$maxId = 0;
foreach ($xml->{$itemName} as $child) {
    if (isset($child->id)) {
        $id = intval($child->id);
        if ($id > $maxId) $maxId = $id;
    }
}
$newId = $maxId + 1;
if ($editId > 0) {
    // ensure ID exists
    $existingId = isset($item->id) ? intval($item->id) : 0;
    if (!$existingId) {
        $item->addChild('id', $editId);
    }
    $newId = isset($item->id) ? intval($item->id) : $editId;
} else {
    $item->addChild('id', $newId);
}

// Basic input validation: max length limits for common fields
function sanitize_value($val) {
    $v = trim($val);
    if (strlen($v) > 4096) $v = substr($v,0,4096);
    return $v;
}

function abort_with_error($message, $code = 400) {
    global $isAjax, $return;
    if ($isAjax) { http_response_code($code); echo json_encode(['error' => $message]); exit; }
    $_SESSION['flash'] = $message; header('Location: ' . ($return ?? '../index.php')); exit;
}

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
    // Basic validations per file/field
    if ($file === 'users' && $k === 'email') {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) abort_with_error('Invalid email address');
    }
    if ($file === 'animals' && $k === 'age') {
        if (!is_numeric($value)) abort_with_error('Age must be a number');
    }
    if ($file === 'crops' && $k === 'yield') {
        if (!is_numeric($value)) abort_with_error('Yield must be a number');
    }

    // add child
    // If we're editing, remove any existing child with the same key to replace value
    if ($editId > 0 && isset($item->{$k})) {
        unset($item->{$k}[0]);
    }
    // sanitize values to avoid untrusted HTML
    $value = sanitize_value($value);
    $item->addChild($k, htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
}

// Save XML back
$dom = new DOMDocument('1.0', 'utf-8');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML($xml->asXML());
$dom->save($path);

$msg = ($editId > 0) ? 'Updated' : 'Saved';
echo json_encode(['success' => true, 'id' => $newId, 'message' => $msg]);

?>