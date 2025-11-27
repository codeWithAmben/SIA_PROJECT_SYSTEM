<?php
// php/user-login.php -- simple user login (non-admin)
session_start();
require_once __DIR__ . '/csrf.php';
header('Content-Type: application/json; charset=utf-8');
// Prepare AJAX detection and return before csrf check
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$return = isset($_POST['return']) ? $_POST['return'] : '../index.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify_request()) {
    if ($isAjax) { http_response_code(403); echo json_encode(['error' => 'Invalid CSRF token']); exit; }
    $_SESSION['flash'] = 'Invalid request (CSRF token mismatch)'; header('Location: ' . $return); exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$identifier = '';
if (isset($_POST['email'])) $identifier = trim($_POST['email']);
elseif (isset($_POST['username'])) $identifier = trim($_POST['username']);
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Basic sanitization and length checks
$identifier = substr($identifier, 0, 250);
$password = substr($password, 0, 1024);
if (strlen($password) < 6 || strlen($identifier) < 2) {
    if ($isAjax) { http_response_code(400); echo json_encode(['error' => 'Missing or invalid credentials']); exit; }
    $_SESSION['flash'] = 'Invalid credentials'; header('Location: ' . $return); exit;
}

if (!$identifier || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing email/username or password']);
    exit;
}

$path = __DIR__ . '/../data/users.xml';
if (!file_exists($path)) {
    http_response_code(400);
    echo json_encode(['error' => 'No users registered yet']);
    exit;
}

libxml_use_internal_errors(true);
$xml = simplexml_load_file($path);
if ($xml === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to parse users']);
    exit;
}

// Load config to see admin settings
$config = include __DIR__ . '/config.php';

// Admin identifier handling: if identifier matches admin username or admin user email (from users.xml), try admin auth
$isAdminLogin = false;
$adminUserEntry = null;
foreach ($xml->user as $u) {
    if (isset($u->role) && strtolower((string)$u->role) === 'admin' && isset($u->email)) {
        if (strtolower((string)$u->email) === strtolower($identifier)) {
            $isAdminLogin = true;
            $adminUserEntry = $u;
            break;
        }
    }
}
if (!$isAdminLogin && isset($config['admin_user']) && strtolower($identifier) === strtolower($config['admin_user'])) {
    $isAdminLogin = true;
}

// If admin login attempt, validate against config admin credentials
if ($isAdminLogin) {
    $ok = false;
    // Check admin password on users.xml (if present first)
    if ($adminUserEntry && isset($adminUserEntry->password)) {
        $adminPw = (string)$adminUserEntry->password;
        if ($adminPw && password_verify($password, $adminPw)) $ok = true;
    }
    // fallback to config password
    if (!$ok) {
        if (isset($config['admin_pass_hash']) && strpos($config['admin_pass_hash'], '$2y$') === 0) {
            if (password_verify($password, $config['admin_pass_hash'])) $ok = true;
        } else {
            if ($password === $config['admin_pass_hash']) $ok = true;
        }
    }
    if ($ok) {
        $_SESSION['admin_logged'] = true;
        // Also populate a `user` session object if found
        if ($adminUserEntry) {
            $_SESSION['user'] = [
                'id' => (string)$adminUserEntry->id,
                'name' => (string)$adminUserEntry->name,
                'email' => (string)$adminUserEntry->email,
                'role' => (string)$adminUserEntry->role
            ];
        } else {
            // fallback user info
            $_SESSION['user'] = [ 'id' => '0', 'name' => $config['admin_user'], 'email' => '', 'role' => 'Admin' ];
        }
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        $return = isset($_POST['return']) ? $_POST['return'] : '../pages/admin.php';
        // If admin logged in via index form which set return to index, redirect to admin dashboard
        if (!$isAjax && $return === '../index.php') $return = '../pages/admin.php';
        session_regenerate_id(true);
        if ($isAjax) {
            echo json_encode(['success' => true, 'message' => 'Admin logged in', 'user' => $_SESSION['user']]);
            exit;
        } else {
            $_SESSION['flash'] = 'Welcome, ' . $_SESSION['user']['name'] . '! You are logged in as Admin.';
            header('Location: ' . $return);
            exit;
        }
    } else {
        // admin password mismatch; deny
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        $return = isset($_POST['return']) ? $_POST['return'] : '../index.php';
        if ($isAjax) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid admin credentials']);
            exit;
        } else {
            $_SESSION['flash'] = 'Invalid admin credentials'; header('Location: ' . $return); exit;
        }
    }
}

// Normal user login flow
foreach ($xml->user as $u) {
    // support both matching by email or by name (username)
    if ((isset($u->email) && strtolower((string)$u->email) === strtolower($identifier)) || (isset($u->name) && strtolower((string)$u->name) === strtolower($identifier))) {
        $pw = isset($u->password) ? (string)$u->password : '';
        if ($pw && password_verify($password, $pw)) {
            $_SESSION['user'] = [
                'id' => (string)$u->id,
                'name' => (string)$u->name,
                'email' => (string)$u->email,
                'role' => (string)$u->role
            ];
            // If AJAX (XHR) request, return JSON; otherwise redirect back with a flash
            $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            $return = isset($_POST['return']) ? $_POST['return'] : '../index.php';
            if ($isAjax) {
                echo json_encode(['success' => true, 'message' => 'Logged in', 'user' => $_SESSION['user']]);
                exit;
            } else {
                session_regenerate_id(true);
                $_SESSION['flash'] = 'Welcome, ' . $_SESSION['user']['name'] . '! You are logged in.';
                header('Location: ' . $return);
                exit;
            }
        }
    }
}

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$return = isset($_POST['return']) ? $_POST['return'] : '../index.php';
// Not found or invalid
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$return = isset($_POST['return']) ? $_POST['return'] : '../index.php';
if ($isAjax) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
    exit;
} else {
    $_SESSION['flash'] = 'Invalid email or password';
    header('Location: ' . $return);
    exit;
}

?>
