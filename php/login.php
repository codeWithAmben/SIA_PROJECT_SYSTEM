<?php
// Deprecated: redirect to unified index login modal
session_start();
$return = isset($_POST['return']) ? $_POST['return'] : (isset($_GET['return']) ? $_GET['return'] : '../index.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // still accept POST but redirect back to index with auth modal open
    header('Location: ../index.php?auth=1&return=' . urlencode($return));
    exit;
} else {
    header('Location: ../index.php?auth=1');
    exit;
}
?>