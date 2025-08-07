<?php
/**
 * Simple logging utility for the AI Training Platform
 */

/**
 * Log a message to the application log file
 * 
 * @param string $message The message to log
 * @param string $level The log level (INFO, WARNING, ERROR, DEBUG)
 * @param array $context Additional context data to include in the log
 * @return bool True if the message was logged successfully, false otherwise
 */
function app_log($message, $level = 'INFO', $context = []) {
    // Create logs directory if it doesn't exist
    $logDir = __DIR__ . '/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    // Define log file path
    $logFile = $logDir . '/app_' . date('Y-m-d') . '.log';
    
    // Format the log message
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = empty($context) ? '' : ' ' . json_encode($context);
    $logMessage = "[$timestamp] [$level] $message$contextStr" . PHP_EOL;
    
    // Write to log file
    $result = file_put_contents($logFile, $logMessage, FILE_APPEND);
    
    return $result !== false;
}

/**
 * Log an error message
 * 
 * @param string $message The error message
 * @param array $context Additional context data
 * @return bool True if logged successfully
 */
function log_error($message, $context = []) {
    return app_log($message, 'ERROR', $context);
}

/**
 * Log a warning message
 * 
 * @param string $message The warning message
 * @param array $context Additional context data
 * @return bool True if logged successfully
 */
function log_warning($message, $context = []) {
    return app_log($message, 'WARNING', $context);
}

/**
 * Log an info message
 * 
 * @param string $message The info message
 * @param array $context Additional context data
 * @return bool True if logged successfully
 */
function log_info($message, $context = []) {
    return app_log($message, 'INFO', $context);
}

/**
 * Log a debug message
 * 
 * @param string $message The debug message
 * @param array $context Additional context data
 * @return bool True if logged successfully
 */
function log_debug($message, $context = []) {
    return app_log($message, 'DEBUG', $context);
}