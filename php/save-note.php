<?php
// Simple wrapper to allow saving notes via POST using save-entity.php logic
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
session_start();
require_once __DIR__ . '/csrf.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify_request()) {
    if ($isAjax) { header('Content-Type: application/json; charset=utf-8'); http_response_code(403); echo json_encode(['error' => 'Invalid CSRF token']); exit; }
    $_SESSION['flash'] = 'Invalid request (CSRF token mismatch)'; header('Location: ../index.php'); exit;
}
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
// Require either a logged-in user or admin to submit notes
if (!isset($_SESSION['user']) && !isset($_SESSION['admin_logged'])) {
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized: Please sign in to add notes.']);
        exit;
    } else {
        // redirect to index login modal
        $_SESSION['flash'] = 'Please sign in to add notes.';
        header('Location: ../index.php?auth=1');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST['file'] = 'notes';
    // If a logged-in user is creating the note, store their name as author if not supplied
    if (!isset($_POST['author']) && isset($_SESSION['user'])) {
        $_POST['author'] = $_SESSION['user']['name'];
    }
    include __DIR__ . '/save-entity.php';
    exit;
}
// If GET, show info
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['error' => 'Use POST to save notes.']);
?>