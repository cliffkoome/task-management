<?php

// Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Autoload classes
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/models/Task.php';
require_once __DIR__ . '/app/controllers/TaskController.php';

// Database connection
$database = new Database();
$db       = $database->connect();

// Task instances
$task       = new Task($db);
$controller = new TaskController($task);

// Get request method and URI
$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = rtrim($uri, '/');

// Remove base path if needed
$uri = str_replace('/task-management', '', $uri);

// Get query params
$params = $_GET;

// Get request body
$body = json_decode(file_get_contents('php://input'), true) ?? [];

// Route matching
// GET /api/tasks/report
if ($method === 'GET' && preg_match('/^\/api\/tasks\/report$/', $uri)) {
    $response = $controller->dailyReport($params['date'] ?? null);
}

// GET /api/tasks
elseif ($method === 'GET' && preg_match('/^\/api\/tasks$/', $uri)) {
    $response = $controller->listTasks($params['status'] ?? null);
}

// POST /api/tasks
elseif ($method === 'POST' && preg_match('/^\/api\/tasks$/', $uri)) {
    $response = $controller->createTask($body);
}

// PATCH /api/tasks/{id}/status
elseif ($method === 'PATCH' && preg_match('/^\/api\/tasks\/(\d+)\/status$/', $uri, $matches)) {
    $response = $controller->updateStatus($matches[1], $body);
}

// DELETE /api/tasks/{id}
elseif ($method === 'DELETE' && preg_match('/^\/api\/tasks\/(\d+)$/', $uri, $matches)) {
    $response = $controller->deleteTask($matches[1]);
}

// 404 fallback
else {
    http_response_code(404);
    $response = ['error' => 'Route not found'];
}

echo json_encode($response, JSON_PRETTY_PRINT);