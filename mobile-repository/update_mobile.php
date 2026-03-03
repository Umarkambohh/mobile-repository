<?php
/**
 * Update Mobile Script
 * Mobile Repository System
 * Handles updating existing mobile phone records
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

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: view_all.php");
    exit();
}

// Get and sanitize form data
$mobile_id = trim($_POST['id'] ?? '');
$name = trim($_POST['name'] ?? '');
$brand = trim($_POST['brand'] ?? '');
$price = trim($_POST['price'] ?? '');

// Store form data for error recovery
$query_params = http_build_query([
    'id' => $mobile_id,
    'name' => $name,
    'brand' => $brand,
    'price' => $price
]);

// Validation
if (empty($mobile_id) || empty($name) || empty($brand) || empty($price)) {
    header("Location: edit_mobile.php?id=$mobile_id&error=empty&$query_params");
    exit();
}

// Validate mobile ID
if (!is_numeric($mobile_id)) {
    header("Location: view_all.php?error=invalid_id");
    exit();
}

// Validate name length
if (strlen($name) > 100) {
    header("Location: edit_mobile.php?id=$mobile_id&error=name_length&$query_params");
    exit();
}

// Validate brand length
if (strlen($brand) > 100) {
    header("Location: edit_mobile.php?id=$mobile_id&error=brand_length&$query_params");
    exit();
}

// Validate price
if (!is_numeric($price) || $price < 0 || $price > 999999) {
    header("Location: edit_mobile.php?id=$mobile_id&error=invalid_price&$query_params");
    exit();
}

// Convert price to integer
$price = (int)$price;
$mobile_id = (int)$mobile_id;

try {
    // Get database connection
    $conn = $database->getConnection();
    
    // Check if mobile exists
    $stmt = $conn->prepare("SELECT id FROM mobiles WHERE id = :id");
    $stmt->bindParam(':id', $mobile_id);
    $stmt->execute();
    
    if (!$stmt->fetch()) {
        header("Location: view_all.php?error=not_found");
        exit();
    }
    
    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE mobiles SET name = :name, brand = :brand, price = :price WHERE id = :id");
    
    // Bind parameters
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':brand', $brand);
    $stmt->bindParam(':price', $price, PDO::PARAM_INT);
    $stmt->bindParam(':id', $mobile_id, PDO::PARAM_INT);
    
    // Execute the statement
    if ($stmt->execute()) {
        // Success - redirect with success message
        header("Location: edit_mobile.php?id=$mobile_id&success");
        exit();
    } else {
        // Execution failed
        header("Location: edit_mobile.php?id=$mobile_id&error=db_error&$query_params");
        exit();
    }
    
} catch (PDOException $e) {
    // Log error for debugging
    error_log("Update Mobile Error: " . $e->getMessage());
    
    // Check for duplicate entry or other specific errors
    if ($e->getCode() == 23000) {
        // Duplicate entry or constraint violation
        header("Location: edit_mobile.php?id=$mobile_id&error=duplicate&$query_params");
    } else {
        // General database error
        header("Location: edit_mobile.php?id=$mobile_id&error=db_error&$query_params");
    }
    exit();
    
} catch (Exception $e) {
    // Handle other exceptions
    error_log("General Update Mobile Error: " . $e->getMessage());
    header("Location: edit_mobile.php?id=$mobile_id&error=general&$query_params");
    exit();
}
?>
