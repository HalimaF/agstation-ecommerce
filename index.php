<?php
// Simple health check endpoint
if ($_SERVER['REQUEST_URI'] === '/health') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok', 'timestamp' => time()]);
    exit;
}

// Basic info endpoint
if ($_SERVER['REQUEST_URI'] === '/info') {
    header('Content-Type: application/json');
    echo json_encode([
        'app' => 'AG Station',
        'version' => '1.0.0',
        'php_version' => PHP_VERSION,
        'environment' => $_ENV['APP_ENV'] ?? 'production'
    ]);
    exit;
}

// Root index.php for Railway deployment
// This file handles basic routing for the application

$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Remove leading slash
$path = ltrim($path, '/');

// Default to frontend if no path specified
if (empty($path)) {
    include __DIR__ . '/frontend/index.php';
    exit;
}

// Route to appropriate files
if (strpos($path, 'admin/') === 0) {
    $file = $path;
} elseif (strpos($path, 'frontend/') === 0) {
    $file = $path;
} elseif (strpos($path, 'auth/') === 0) {
    $file = $path;
} elseif (strpos($path, 'user/') === 0) {
    $file = $path;
} elseif (strpos($path, 'assets/') === 0) {
    $file = $path;
} else {
    // Default to frontend for other paths
    $file = 'frontend/' . $path;
}

// Check if file exists
if (file_exists(__DIR__ . '/' . $file)) {
    // Handle different file types
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    
    switch ($ext) {
        case 'css':
            header('Content-Type: text/css');
            break;
        case 'js':
            header('Content-Type: application/javascript');
            break;
        case 'png':
            header('Content-Type: image/png');
            break;
        case 'jpg':
        case 'jpeg':
            header('Content-Type: image/jpeg');
            break;
        case 'gif':
            header('Content-Type: image/gif');
            break;
        case 'webp':
            header('Content-Type: image/webp');
            break;
        case 'php':
            include __DIR__ . '/' . $file;
            exit;
        default:
            break;
    }
    
    if ($ext !== 'php') {
        readfile(__DIR__ . '/' . $file);
    }
} else {
    // File not found - redirect to frontend
    header("HTTP/1.0 404 Not Found");
    include __DIR__ . '/frontend/index.php';
}
?>
