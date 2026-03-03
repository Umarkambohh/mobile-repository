<?php
/**
 * Login Page
 * Mobile Repository System
 * Main login page with authentication form
 */

// Start session
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Include error messages from authentication
$error = '';
if (isset($_GET['error'])) {
    switch($_GET['error']) {
        case 'invalid':
            $error = "Invalid username or password!";
            break;
        case 'empty':
            $error = "Please enter both userid and password!";
            break;
        case 'db_error':
            $error = "Database connection error. Please try again later.";
            break;
        default:
            $error = "Login failed. Please try again.";
    }
}

// Handle logout message
$logout_msg = '';
if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
    $logout_msg = "You have been logged out successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mobile Repository System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <h2>📱 Mobile Repository</h2>
            <p class="text-center" style="margin-bottom: 20px; color: #718096;">
                Please login to access the system
            </p>
            
            <!-- Display Logout Message -->
            <?php if ($logout_msg): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($logout_msg); ?>
                </div>
            <?php endif; ?>
            
            <!-- Display Error Message -->
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <!-- Login Form -->
            <form action="authenticate.php" method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        required 
                        placeholder="Enter your username"
                        autocomplete="username"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        placeholder="Enter your password"
                        autocomplete="current-password"
                    >
                </div>
                
                <button type="submit" class="btn" style="width: 100%;">
                    Login
                </button>
            </form>
            
            <!-- <div class="mt-30 text-center">
                <small style="color: #718096;">
                    Default Login: admin / admin123
                </small>
            </div> -->
        </div>
    </div>
</body>
</html>
