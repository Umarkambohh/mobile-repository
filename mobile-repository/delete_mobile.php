<?php
/**
 * Delete Mobile Script
 * Mobile Repository System
 * Handles deleting mobile phone records
 */

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require_once 'db.php';

// Get mobile ID from URL
$mobile_id = $_GET['id'] ?? '';

// Validation
if (empty($mobile_id) || !is_numeric($mobile_id)) {
    header("Location: view_all.php?error=invalid_id");
    exit();
}

$mobile_id = (int)$mobile_id;

try {
    // Get database connection
    $conn = $database->getConnection();
    
    // Check if mobile exists and get details for confirmation
    $stmt = $conn->prepare("SELECT id, name, brand, price FROM mobiles WHERE id = :id");
    $stmt->bindParam(':id', $mobile_id);
    $stmt->execute();
    
    $mobile = $stmt->fetch();
    
    if (!$mobile) {
        header("Location: view_all.php?error=not_found");
        exit();
    }
    
    // Handle confirmation
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
        // User confirmed deletion
        $stmt = $conn->prepare("DELETE FROM mobiles WHERE id = :id");
        $stmt->bindParam(':id', $mobile_id);
        
        if ($stmt->execute()) {
            header("Location: view_all.php?deleted");
            exit();
        } else {
            header("Location: view_all.php?error=delete_failed");
            exit();
        }
    }
    
} catch (Exception $e) {
    error_log("Delete Mobile Error: " . $e->getMessage());
    header("Location: view_all.php?error=db_error");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Mobile Phone - Mobile Repository System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🗑️ Delete Mobile Phone</h1>
            <p>Confirm deletion of mobile phone</p>
        </div>
        
        <!-- Navigation Menu -->
        <nav class="nav-menu">
            <ul>
                <li><a href="dashboard.php">🏠 Home</a></li>
                <li><a href="add_mobile.php">➕ Add Mobile Phone</a></li>
                <li><a href="view_all.php">📋 View All Mobiles</a></li>
                <li><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </nav>
        
        <!-- Delete Confirmation -->
        <div class="card">
            <h2>⚠️ Confirm Deletion</h2>
            
            <div class="alert alert-error">
                <strong>⚠️ Warning:</strong> This action cannot be undone!
            </div>
            
            <div style="background: #f7fafc; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3>📱 Mobile Phone Details:</h3>
                <p><strong>ID:</strong> <?php echo $mobile['id']; ?></p>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($mobile['name']); ?></p>
                <p><strong>Brand:</strong> <?php echo htmlspecialchars($mobile['brand']); ?></p>
                <p><strong>Price:</strong> Rs.<?php echo number_format($mobile['price']); ?></p>
            </div>
            
            <p style="color: #e53e3e; font-weight: 600; margin-bottom: 20px;">
                Are you sure you want to delete this mobile phone permanently?
            </p>
            
            <form method="POST" style="display: inline;">
                <input type="hidden" name="confirm_delete" value="yes">
                <button type="submit" class="btn btn-danger">
                    🗑️ Yes, Delete This Mobile
                </button>
            </form>
            
            <a href="view_all.php" class="btn" style="margin-left: 10px;">
                ❌ Cancel, Go Back
            </a>
        </div>
    </div>
</body>
</html>
