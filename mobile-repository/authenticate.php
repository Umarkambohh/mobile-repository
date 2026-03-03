<?php
/**
 * Authentication Script
 * Mobile Repository System
 * Handles user login validation and session management
 */

// Start session
session_start();

// Include database connection
require_once 'db.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

// Get form data
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validate input
if (empty($username) || empty($password)) {
    header("Location: login.php?error=empty");
    exit();
}

try {
    // Get database connection
    $conn = $database->getConnection();
    
    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    
    // Fetch user
    $user = $stmt->fetch();
    
    // Verify user exists and password is correct
    if ($user && password_verify($password, $user['password'])) {
        // Password is correct, create session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['login_time'] = time();
        
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        // Redirect to dashboard
        header("Location: dashboard.php");
        exit();
        
    } else {
        // Invalid credentials
        header("Location: login.php?error=invalid");
        exit();
    }
    
} catch (PDOException $e) {
    // Log error for debugging
    error_log("Authentication Error: " . $e->getMessage());
    
    // Redirect with database error
    header("Location: login.php?error=db_error");
    exit();
    
} catch (Exception $e) {
    // Handle other exceptions
    error_log("General Authentication Error: " . $e->getMessage());
    
    // Redirect with general error
    header("Location: login.php?error=invalid");
    exit();
}
?>
