<?php
require 'db.php';

$allowed_tables = ['ProjectTBL'];
$allowed_cols   = ['ProfileImage', 'BackgroundImage'];

$table = $_GET['table'] ?? '';
$col   = $_GET['col']   ?? '';
$id    = (int)($_GET['id'] ?? 0);

if (!in_array($table, $allowed_tables) || !in_array($col, $allowed_cols) || $id <= 0) {
    http_response_code(400);
    exit;
}

$result = $conn->query("SELECT $col FROM $table WHERE ProjectID = $id LIMIT 1");
if (!$result) {
    http_response_code(500);
    exit;
}

$row = $result->fetch_assoc();
if (!$row || empty($row[$col])) {
    http_response_code(404);
    exit;
}

$data = $row[$col];

$mime = 'image/jpeg'; 
$header2 = substr($data, 0, 2);
$header4 = substr($data, 0, 4);
$header8 = substr($data, 0, 8);

if ($header4 === "\x89PNG") {
    $mime = 'image/png';
} elseif ($header2 === "\xFF\xD8") {
    $mime = 'image/jpeg';
} elseif (substr($data, 0, 6) === 'GIF87a' || substr($data, 0, 6) === 'GIF89a') {
    $mime = 'image/gif';
} elseif (substr($data, 0, 4) === 'RIFF' && substr($data, 8, 4) === 'WEBP') {
    $mime = 'image/webp';
}

header("Content-Type: $mime");
header("Content-Length: " . strlen($data));
header("Cache-Control: public, max-age=86400");
echo $data;
exit;