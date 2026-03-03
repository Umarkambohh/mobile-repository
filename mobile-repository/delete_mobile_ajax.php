<?php
/**
 * Delete Mobile Ajax Handler
 * Mobile Repository System
 * Handles mobile phone deletion via AJAX
 */

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Include database connection
require_once 'db.php';

// Get mobile ID from POST
$mobile_id = $_POST['id'] ?? '';

// Validation
if (empty($mobile_id) || !is_numeric($mobile_id)) {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'message' => 'Invalid mobile ID']);
    exit();
}

$mobile_id = (int)$mobile_id;

try {
    // Get database connection
    $conn = $database->getConnection();
    
    // Check if mobile exists
    $stmt = $conn->prepare("SELECT id, mobile_name, brand, price FROM mobile_phone WHERE id = :id");
    $stmt->bindParam(':id', $mobile_id);
    $stmt->execute();
    
    $mobile = $stmt->fetch();
    
    if (!$mobile) {
        header("Content-Type: application/json");
        echo json_encode(['success' => false, 'message' => 'Mobile phone not found']);
        exit();
    }
    
    // Delete the mobile
    $stmt = $conn->prepare("DELETE FROM mobile_phone WHERE id = :id");
    $stmt->bindParam(':id', $mobile_id);
    
    if ($stmt->execute()) {
        header("Content-Type: application/json");
        echo json_encode([
            'success' => true, 
            'message' => 'Mobile phone deleted successfully!',
            'mobile_name' => $mobile['mobile_name'],
            'mobile_brand' => $mobile['brand']
        ]);
        exit();
    } else {
        header("Content-Type: application/json");
        echo json_encode(['success' => false, 'message' => 'Failed to delete mobile phone']);
        exit();
    }
    
} catch (Exception $e) {
    error_log("Delete Mobile AJAX Error: " . $e->getMessage());
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
    exit();
}
?>
