<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Add CORS headers for cross-domain requests
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
header("Access-Control-Allow-Origin: $origin");
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Initialize response
$response = [
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'checks' => []
];

// Check PHP version
$php_version = PHP_VERSION;
$php_version_check = version_compare($php_version, '7.4.0', '>=');
$response['checks']['php_version'] = [
    'status' => $php_version_check ? 'ok' : 'fail',
    'message' => "PHP version: $php_version" . ($php_version_check ? '' : ' (7.4.0+ recommended)')
];

// Check if config.php exists
$config_exists = file_exists(__DIR__ . '/config.php');
$response['checks']['config_file'] = [
    'status' => $config_exists ? 'ok' : 'fail',
    'message' => $config_exists ? 'config.php exists' : 'config.php not found'
];

// Check database connection (without exposing credentials)
try {
    require_once 'config.php';
    $response['checks']['database'] = [
        'status' => 'ok',
        'message' => 'Database connection successful'
    ];
} catch (Exception $e) {
    $response['checks']['database'] = [
        'status' => 'fail',
        'message' => 'Database connection failed'
    ];
    $response['status'] = 'degraded';
}

// Check if required PHP extensions are loaded
$required_extensions = ['pdo', 'pdo_mysql', 'json'];
$missing_extensions = [];

foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing_extensions[] = $ext;
    }
}

$response['checks']['php_extensions'] = [
    'status' => empty($missing_extensions) ? 'ok' : 'fail',
    'message' => empty($missing_extensions) 
        ? 'All required extensions loaded' 
        : 'Missing extensions: ' . implode(', ', $missing_extensions)
];

if (!empty($missing_extensions)) {
    $response['status'] = 'degraded';
}

// Check write permissions for log directory
$log_dir = __DIR__ . '/logs';
$log_dir_exists = is_dir($log_dir) || mkdir($log_dir, 0755, true);
$log_dir_writable = $log_dir_exists && is_writable($log_dir);

$response['checks']['log_directory'] = [
    'status' => $log_dir_writable ? 'ok' : 'fail',
    'message' => $log_dir_writable 
        ? 'Log directory is writable' 
        : 'Log directory is not writable'
];

if (!$log_dir_writable) {
    $response['status'] = 'degraded';
}

// Output the response
echo json_encode($response, JSON_PRETTY_PRINT);