<?php
// Simple export: create a zip archive of data/*.xml and download
$dir = __DIR__ . '/../data';
$zipFilename = 'simple_farm2_data.zip';
$zipPath = sys_get_temp_dir() . '/' . $zipFilename;
$zip = new ZipArchive();
if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to create zip']);
    exit;
}
$files = glob($dir . '/*.xml');
foreach ($files as $file) {
    $basename = basename($file);
    $zip->addFile($file, $basename);
}
$zip->close();

header('Content-Type: application/zip');
header('Content-disposition: attachment; filename=' . $zipFilename);
header('Content-Length: ' . filesize($zipPath));
readfile($zipPath);
unlink($zipPath);
exit;
?>