<?php

// Route static files from public/
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve static CSS and JS files
$staticFile = __DIR__ . '/public' . $uri;
if (preg_match('/\.(css|js|png|jpg|ico)$/', $uri) && file_exists($staticFile)) {
    return false;
}

// Route API requests
if (strpos($uri, '/api') !== false) {
    require __DIR__ . '/api.php';
    exit();
}

// Serve frontend
require __DIR__ . '/public/index.php';