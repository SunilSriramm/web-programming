<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if ($requestUri === '/workway' || $requestUri === '/workway/') {
    $requestUri = '/';
} elseif (str_starts_with($requestUri, '/workway/')) {
    $requestUri = substr($requestUri, strlen('/workway')) ?: '/';
}
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

function jsonResponse(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($requestUri === '/' && $method === 'GET') {
    jsonResponse(200, [
        'project' => 'WORKWAY',
        'language' => 'PHP',
        'status' => 'ready',
        'message' => 'PHP support has been added to this project.',
        'routes' => [
            'GET /' => 'Project status and overview',
            'GET /api/health' => 'Health check endpoint',
            'POST /api/login' => 'Example login endpoint for PHP-based auth flow'
        ]
    ]);
}

if ($requestUri === '/api/health' && $method === 'GET') {
    jsonResponse(200, [
        'status' => 'ok',
        'service' => 'workway-php',
        'language' => 'PHP',
        'timestamp' => gmdate('c')
    ]);
}

if ($requestUri === '/api/login' && $method === 'POST') {
    $rawBody = file_get_contents('php://input');
    $input = json_decode($rawBody, true);
    $input = is_array($input) ? $input : [];

    $username = trim((string)($input['username'] ?? ''));
    $password = (string)($input['password'] ?? '');

    if ($username === '' || $password === '') {
        jsonResponse(400, [
            'success' => false,
            'message' => 'username and password are required.'
        ]);
    }

    $validUser = in_array($username, ['ADMIN001', 'USER001', 'ELECHEAD01', 'WORKER001'], true);
    $validPassword = $password === 'Admin@123' || $password === 'User@123' || $password === 'Head@123' || $password === 'Worker@123';

    if (!$validUser || !$validPassword) {
        jsonResponse(401, [
            'success' => false,
            'message' => 'invalid credentials.'
        ]);
    }

    jsonResponse(200, [
        'success' => true,
        'message' => 'PHP login successful.',
        'user' => [
            'username' => $username,
            'role' => $username === 'ADMIN001' ? 'ADMIN' : ($username === 'USER001' ? 'USER' : ($username === 'ELECHEAD01' ? 'DEPARTMENT_HEAD' : 'WORKER'))
        ],
        'token' => 'php-demo-token-for-' . strtolower($username)
    ]);
}

jsonResponse(404, [
    'success' => false,
    'message' => 'Route not found.',
    'requested_path' => $requestUri
]);
