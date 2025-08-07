<?php
header('Content-Type: application/json');
require_once 'config.php';
require_once 'logger.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Enhanced logging for deployment troubleshooting
log_info("Auth Request", ['post' => $_POST, 'method' => $_SERVER['REQUEST_METHOD']]);
log_info("Database Connection Info", ['host' => $host, 'db' => $dbname, 'user' => $username]);

// Add CORS headers for cross-domain requests
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
header("Access-Control-Allow-Origin: $origin");
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

$action = $_POST['action'] ?? '';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // Log connection attempt
    error_log("Attempting database connection");
    
    switch ($action) {
        case 'login':
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            error_log("Login attempt for email: " . $email);
            
            if (!$email || !$password) {
                error_log("Missing email or password");
                echo json_encode(['success' => false, 'message' => 'Email and password are required']);
                exit;
            }
            
            $stmt = $pdo->prepare("SELECT id, name, email, password, is_admin FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            error_log("User found: " . ($user ? 'Yes' : 'No'));
            
            if ($user && password_verify($password, $user['password'])) {
                error_log("Password verification successful");
                unset($user['password']); // Don't send password back to client
                echo json_encode(['success' => true, 'user' => $user]);
            } else {
                error_log("Password verification failed");
                echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
            }
            break;
            
        case 'register':
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (!$name || !$email || !$password) {
                echo json_encode(['success' => false, 'message' => 'Name, email and password are required']);
                exit;
            }
            
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Email already registered']);
                exit;
            }
            
            // Hash password and create user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, 0)");
            $stmt->execute([$name, $email, $hashedPassword]);
            
            echo json_encode(['success' => true, 'message' => 'Registration successful']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>