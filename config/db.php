<?php
/**
 * Database Configuration File
 * Handles MySQL connection for PC Parts E-Commerce System
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pc_parts_store');

// Create connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to UTF8
    $conn->set_charset("utf8");
    
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

/**
 * Sanitize input to prevent SQL injection
 */
function sanitize($input, $conn) {
    return $conn->real_escape_string(trim($input));
}

/**
 * Fetch all rows from query result
 */
function fetchAll($result) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

/**
 * Fetch single row from query result
 */
function fetchOne($result) {
    return $result->fetch_assoc();
}

/**
 * Fetch single value from query result
 */
function fetchValue($result) {
    $row = $result->fetch_assoc();
    return $row ? reset($row) : null;
}
?>
