<?php
// Simple CSRF helper for generating and validating tokens
if (session_status() === PHP_SESSION_NONE) session_start();

function csrf_get_token() {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time']) || (time() - $_SESSION['csrf_token_time']) > 7200) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    $t = csrf_get_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($t, ENT_QUOTES, 'UTF-8') . '">';
}

function csrf_verify($token) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['csrf_token']) || !$token) return false;
    if (hash_equals($_SESSION['csrf_token'], $token)) return true;
    return false;
}

// Accept token from headers or POST
function csrf_verify_request() {
    $headToken = null;
    if (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) $headToken = $_SERVER['HTTP_X_CSRF_TOKEN'];
    $postToken = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : (isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : null);
    $token = $postToken ?: $headToken;
    return csrf_verify($token);
}

?>
