<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/models/Task.php';
require_once __DIR__ . '/app/controllers/TaskController.php';

$database   = new Database();
$db         = $database->connect();
$task       = new Task($db);
$controller = new TaskController($task);

$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = rtrim($uri, '/');
$params = $_GET;
$body   = json_decode(file_get_contents('php://input'), true) ?? [];

if ($method === 'GET' && preg_match('/^\/api\/tasks\/report$/', $uri)) {
    $response = $controller->dailyReport($params['date'] ?? null);
} elseif ($method === 'GET' && preg_match('/^\/api\/tasks$/', $uri)) {
    $response = $controller->listTasks($params['status'] ?? null);
} elseif ($method === 'POST' && preg_match('/^\/api\/tasks$/', $uri)) {
    $response = $controller->createTask($body);
} elseif ($method === 'PATCH' && preg_match('/^\/api\/tasks\/(\d+)\/status$/', $uri, $matches)) {
    $response = $controller->updateStatus($matches[1], $body);
} elseif ($method === 'DELETE' && preg_match('/^\/api\/tasks\/(\d+)$/', $uri, $matches)) {
    $response = $controller->deleteTask($matches[1]);
} else {
    http_response_code(404);
    $response = ['error' => 'Route not found'];
}

echo json_encode($response, JSON_PRETTY_PRINT);