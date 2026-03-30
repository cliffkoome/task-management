<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve static files (CSS, JS)
if (preg_match('/\.(css|js|png|jpg|ico|svg)$/', $uri)) {
    $file = __DIR__ . '/public' . $uri;
    if (file_exists($file)) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $mime = [
            'css' => 'text/css',
            'js'  => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'ico' => 'image/x-icon',
            'svg' => 'image/svg+xml',
        ];
        header('Content-Type: ' . ($mime[$ext] ?? 'text/plain'));
        readfile($file);
        exit();
    }
}

// API routes
if (strpos($uri, '/api') !== false) {
    require __DIR__ . '/api.php';
    exit();
}

// Frontend
require __DIR__ . '/public/index.php';