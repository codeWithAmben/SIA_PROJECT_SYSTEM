<?php
// php/register.php
// Basic registration endpoint: POST name, email, password, role (optional)
session_start();
header('Content-Type: application/json; charset=utf-8');
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$return = isset($_POST['return']) ? $_POST['return'] : '../index.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$role = isset($_POST['role']) ? trim($_POST['role']) : 'User';

if (!$name || !$email || !$password) {
    if ($isAjax) { http_response_code(400); echo json_encode(['error' => 'Missing required fields']); exit; }
    $_SESSION['flash'] = 'Missing required registration fields'; header('Location: ' . $return); exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    if ($isAjax) { http_response_code(400); echo json_encode(['error' => 'Invalid email address']); exit; }
    $_SESSION['flash'] = 'Invalid email address'; header('Location: ' . $return); exit;
}

$path = __DIR__ . '/../data/users.xml';
if (!file_exists($path)) {
    $xmlstr = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<users>\n</users>";
    file_put_contents($path, $xmlstr);
}

libxml_use_internal_errors(true);
$xml = simplexml_load_file($path);
if ($xml === false) {
    if ($isAjax) { http_response_code(500); echo json_encode(['error' => 'Failed to parse XML']); exit; }
    $_SESSION['flash'] = 'System error (failed to parse users store)'; header('Location: ' . $return); exit;
}

// Prevent duplicate email
foreach ($xml->user as $u) {
    if (isset($u->email) && strtolower((string)$u->email) === strtolower($email)) {
        if ($isAjax) { http_response_code(409); echo json_encode(['error' => 'Email already registered']); exit; }
        $_SESSION['flash'] = 'Email already registered'; header('Location: ' . $return); exit;
    }
}

// compute new id
$maxId = 0;
foreach ($xml->user as $child) {
    if (isset($child->id)) {
        $id = intval($child->id);
        if ($id > $maxId) $maxId = $id;
    }
}
$newId = $maxId + 1;

// Create user entry
$user = $xml->addChild('user');
$user->addChild('id', $newId);
$user->addChild('name', htmlspecialchars($name));
$user->addChild('email', htmlspecialchars($email));
$user->addChild('role', htmlspecialchars($role));
// Hash password for storage
$hash = password_hash($password, PASSWORD_DEFAULT);
$user->addChild('password', htmlspecialchars($hash));

// Save XML
$dom = new DOMDocument('1.0', 'utf-8');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML($xml->asXML());
$dom->save($path);

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$return = isset($_POST['return']) ? $_POST['return'] : '../index.php';
if ($isAjax) {
    echo json_encode(['success' => true, 'id' => $newId, 'message' => 'Account created']);
    exit;
} else {
    $_SESSION['flash'] = 'Thanks for registering. You can now sign in.';
    header('Location: ' . $return);
    exit;
}

?>
