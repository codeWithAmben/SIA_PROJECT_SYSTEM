<?php
// php/delete-entity.php - deletes an entity by file and id (admin-only)
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/csrf.php';
// CSRF check
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify_request()) {
    http_response_code(403); echo json_encode(['error' => 'Invalid CSRF token']); exit;
}
header('Content-Type: application/json; charset=utf-8');
$allowed = ['animals','crops','users','tasks','notes'];
$file = isset($_POST['file']) ? preg_replace('/[^a-z]/', '', $_POST['file']) : '';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if (!$file || !in_array($file, $allowed) || !$id) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid parameters']);
    exit;
}
// only admins can delete (notes can only be deleted by admin via this API)
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Admin-only endpoint']);
    exit;
}
$path = __DIR__ . '/../data/' . $file . '.xml';
if (!file_exists($path)) { http_response_code(404); echo json_encode(['error' => 'Not found']); exit; }
libxml_use_internal_errors(true);
$xml = simplexml_load_file($path);
if ($xml === false) { http_response_code(500); echo json_encode(['error' => 'Failed to parse XML']); exit; }
$itemName = rtrim($xml->getName(), 's');
$deleted = false;
foreach ($xml->{$itemName} as $i => $node) {
    if (isset($node->id) && intval($node->id) === $id) {
        // Remove this node
        $dom = dom_import_simplexml($node);
        $dom->parentNode->removeChild($dom);
        $deleted = true;
        break;
    }
}
if (!$deleted) { http_response_code(404); echo json_encode(['error' => 'Item not found']); exit; }
$dom = new DOMDocument('1.0', 'utf-8');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML($xml->asXML());
$dom->save($path);
echo json_encode(['success' => true, 'message' => 'Deleted']);
exit;
?>
