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

// Log environment variables
error_log("Environment variables: DB_HOST=" . getenv('DB_HOST') . ", DB_NAME=" . getenv('DB_NAME') . ", DB_USER=" . getenv('DB_USER'));

// Database configuration
$host = getenv('DB_HOST') ? getenv('DB_HOST') : 'localhost';
$dbname = getenv('DB_NAME') ? getenv('DB_NAME') : 'ai_training';
$username = getenv('DB_USER') ? getenv('DB_USER') : 'root';
$password = getenv('DB_PASSWORD') ? getenv('DB_PASSWORD') : '';

// Response array
$response = [
    'success' => false,
    'message' => '',
    'server_info' => [
        'php_version' => PHP_VERSION,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'host' => $host,
        'database' => $dbname,
        'username' => $username,
        'password_set' => !empty($password) ? 'Yes' : 'No',
        'environment' => getenv('ENVIRONMENT') ? getenv('ENVIRONMENT') : 'local'
    ],
    'connection_test' => null,
    'tables' => []
];

try {
    // Attempt database connection with timeout
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 5, // 5 seconds timeout
    ];
    
    // First try to connect to the server without specifying a database
    try {
        $serverPdo = new PDO("mysql:host=$host", $username, $password, $options);
        $response['server_connection'] = 'Passed';
        
        // Now try to connect to the specific database
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, $options);
            $response['success'] = true;
            $response['message'] = 'Database connection successful';
            $response['connection_test'] = 'Passed';
            $response['database_exists'] = true;
        } catch (PDOException $e) {
            // Database doesn't exist or can't be accessed
            $response['message'] = 'Server connection successful, but database connection failed: ' . $e->getMessage();
            $response['database_exists'] = false;
            $response['connection_test'] = 'Failed at database level';
            $response['error_details'] = $e->getMessage();
            
            // Try to create the database
            $serverPdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
            $response['database_creation_attempted'] = true;
            
            // Try connecting again
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, $options);
            $response['success'] = true;
            $response['message'] = 'Database created and connected successfully';
            $response['connection_test'] = 'Passed after database creation';
        }
    } catch (PDOException $e) {
        // Server connection failed
        throw new PDOException('Server connection failed: ' . $e->getMessage());
    }
    
    // Get list of tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        // Get row count for each table
        $countStmt = $pdo->query("SELECT COUNT(*) FROM $table");
        $count = $countStmt->fetchColumn();
        
        $response['tables'][] = [
            'name' => $table,
            'row_count' => $count
        ];
    }
    
} catch (PDOException $e) {
    $response['message'] = 'Database connection failed: ' . $e->getMessage();
    $response['connection_test'] = 'Failed';
    error_log("Database connection error: " . $e->getMessage());
}

// Output response
echo json_encode($response, JSON_PRETTY_PRINT);
?>