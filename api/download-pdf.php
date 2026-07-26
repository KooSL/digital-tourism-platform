<?php
if (!isset($_GET['file'])) {
    exit('File not found');
}

$file = basename($_GET['file']);
$path = "../uploads/pdf/" . $file;

if (!file_exists($path)) {
    exit('File does not exist');
}

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Content-Length: ' . filesize($path));
readfile($path);
exit;
