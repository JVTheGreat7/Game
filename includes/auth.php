<?php
/**
 * Authentication Functions
 * Handles user login, registration, and session management
 */

session_start();

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user info from session
 */
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'user_id' => $_SESSION['user_id'],
            'full_name' => $_SESSION['full_name'],
            'email' => $_SESSION['email'],
            'role' => $_SESSION['role']
        ];
    }
    return null;
}

/**
 * Require login - redirect if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /PC/auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit();
    }
}

/**
 * Require specific role
 */
function requireRole($role) {
    requireLogin();
    if ($_SESSION['role'] !== $role) {
        die('Access Denied: You do not have permission to access this page.');
    }
}

/**
 * Require admin or staff
 */
function requireStaffOrAdmin() {
    requireLogin();
    if (!in_array($_SESSION['role'], ['admin', 'staff'])) {
        die('Access Denied: Staff or Admin access required.');
    }
}

/**
 * Login user
 */
function loginUser($conn, $email, $password) {
    $email = sanitize($email, $conn);
    
    // Fetch user from database
    $query = "SELECT user_id, full_name, email, password_hash, role FROM users WHERE email = '$email'";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password_hash'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            return true;
        }
    }
    
    return false;
}

/**
 * Register new user
 */
function registerUser($conn, $full_name, $email, $password, $password_confirm) {
    // Validate inputs
    if (empty($full_name) || empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'All fields are required'];
    }
    
    if ($password !== $password_confirm) {
        return ['success' => false, 'message' => 'Passwords do not match'];
    }
    
    if (strlen($password) < 6) {
        return ['success' => false, 'message' => 'Password must be at least 6 characters'];
    }
    
    // Check if email already exists
    $email = sanitize($email, $conn);
    $check_query = "SELECT user_id FROM users WHERE email = '$email'";
    $check_result = $conn->query($check_query);
    
    if ($check_result && $check_result->num_rows > 0) {
        return ['success' => false, 'message' => 'Email already registered'];
    }
    
    // Hash password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
    // Insert user
    $full_name = sanitize($full_name, $conn);
    $insert_query = "INSERT INTO users (full_name, email, password_hash, role) VALUES ('$full_name', '$email', '$password_hash', 'customer')";
    
    if ($conn->query($insert_query)) {
        return ['success' => true, 'message' => 'Registration successful! Please login.'];
    } else {
        return ['success' => false, 'message' => 'Registration failed: ' . $conn->error];
    }
}

/**
 * Logout user
 */
function logoutUser() {
    $_SESSION = [];
    session_destroy();
}
?>
