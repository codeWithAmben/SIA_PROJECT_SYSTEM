<?php
// Deprecated: redirect to unified index modal login
session_start();
$return = isset($_GET['return']) ? urldecode($_GET['return']) : '/pages/admin.php';
header('Location: ../index.php?auth=1&return=' . urlencode($return));
exit;

?>
