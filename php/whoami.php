<?php
// php/whoami.php - returns current session user
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');
$user = null;
$admin = false;
if (isset($_SESSION['user'])) $user = $_SESSION['user'];
if (isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true) $admin = true;
echo json_encode(['user' => $user, 'admin' => $admin]);
exit;
?>
