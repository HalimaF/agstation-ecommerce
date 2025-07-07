<?php
header('Content-Type: application/json');
echo json_encode([
    'status' => 'OK',
    'timestamp' => time(),
    'date' => date('Y-m-d H:i:s'),
    'php_version' => PHP_VERSION,
    'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
]);
?>
