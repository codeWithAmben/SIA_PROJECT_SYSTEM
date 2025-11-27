<?php
// Simple auth helpers
if (session_status() === PHP_SESSION_NONE) session_start();

function is_admin_logged_in() {
    return isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true;
}

function require_admin() {
    if (!is_admin_logged_in()) {
        // Redirect to site root login modal with return URL
        $return = isset($_SERVER['REQUEST_URI']) ? urlencode($_SERVER['REQUEST_URI']) : '';
        // pages include this file; redirect to index with auth modal
        header('Location: ../index.php?auth=1&return=' . $return);
        exit;
    }
}

function login_admin() {
    $_SESSION['admin_logged'] = true;
}

function logout_admin() {
    unset($_SESSION['admin_logged']);
    session_destroy();
}
?>