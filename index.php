<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');

// API routes
if (strpos($uri, '/api') === 0) {
    require __DIR__ . '/api.php';
    exit();
}

// Frontend
require __DIR__ . '/public/index.php';