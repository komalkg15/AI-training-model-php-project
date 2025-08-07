<?php
// Include database configuration
require_once 'config.php';
require_once 'logger.php';

// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Check if test user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute(['test@example.com']);
    $existingUser = $stmt->fetch();
    
    if ($existingUser) {
        echo "Test user already exists with ID: {$existingUser['id']}\n";
    } else {
        // Create test user
        $name = 'Test User';
        $email = 'test@example.com';
        $password = 'password123'; // Simple password for testing
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user with is_admin column
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, 0)");
        $result = $stmt->execute([$name, $email, $hashedPassword]);
        
        if ($result) {
            $userId = $pdo->lastInsertId();
            echo "Test user created successfully with ID: {$userId}\n";
            echo "Email: {$email}\n";
            echo "Password: {$password}\n";
        } else {
            echo "Failed to create test user\n";
        }
    }
    
    // Create admin user if it doesn't exist
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute(['admin@example.com']);
    $existingAdmin = $stmt->fetch();
    
    if ($existingAdmin) {
        echo "Admin user already exists with ID: {$existingAdmin['id']}\n";
    } else {
        // Create admin user
        $name = 'Admin User';
        $email = 'admin@example.com';
        $password = 'admin123'; // Simple password for testing
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert admin user with is_admin = 1
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, 1)");
        $result = $stmt->execute([$name, $email, $hashedPassword]);
        
        if ($result) {
            $userId = $pdo->lastInsertId();
            echo "Admin user created successfully with ID: {$userId}\n";
            echo "Email: {$email}\n";
            echo "Password: {$password}\n";
        } else {
            echo "Failed to create admin user\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>