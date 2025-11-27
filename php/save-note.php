<?php
// Simple wrapper to allow saving notes via POST using save-entity.php logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST['file'] = 'notes';
    include __DIR__ . '/save-entity.php';
    exit;
}
// If GET, show info
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['error' => 'Use POST to save notes.']);
?>