<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Add CORS headers for cross-domain requests
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
header("Access-Control-Allow-Origin: $origin");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get all environment variables
$env_vars = getenv();

// Filter out sensitive information
$safe_env_vars = [];
foreach ($env_vars as $key => $value) {
    // Only include DB_ variables but mask passwords
    if (strpos($key, 'DB_') === 0) {
        if ($key === 'DB_PASSWORD') {
            $safe_env_vars[$key] = !empty($value) ? '******' : '(empty)';
        } else {
            $safe_env_vars[$key] = $value;
        }
    }
}

// Add server information
$server_info = [
    'php_version' => PHP_VERSION,
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
    'server_protocol' => $_SERVER['SERVER_PROTOCOL'] ?? 'Unknown',
    'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'Unknown',
    'https' => isset($_SERVER['HTTPS']) ? 'On' : 'Off',
    'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
    'server_name' => $_SERVER['SERVER_NAME'] ?? 'Unknown',
    'server_port' => $_SERVER['SERVER_PORT'] ?? 'Unknown'
];

// Check if we're running in a Docker container
$in_docker = file_exists('/.dockerenv') ? true : false;

// Prepare response
$response = [
    'environment_variables' => $safe_env_vars,
    'server_info' => $server_info,
    'in_docker' => $in_docker,
    'timestamp' => date('Y-m-d H:i:s'),
    'timezone' => date_default_timezone_get()
];

// Output JSON response
echo json_encode($response, JSON_PRETTY_PRINT);