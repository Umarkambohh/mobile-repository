<?php
/**
 * Insert Mobile Script
 * Mobile Repository System
 * Handles insertion of new mobile phone records
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
    header("Location: add_mobile.php");
    exit();
}

// Get and sanitize form data
$mobile_name = trim($_POST['name'] ?? '');
$brand = trim($_POST['brand'] ?? '');
$price = trim($_POST['price'] ?? '');

// Store form data for error recovery
$query_params = http_build_query([
    'name' => $mobile_name,
    'brand' => $brand,
    'price' => $price
]);

// Validation
if (empty($mobile_name) || empty($brand) || empty($price)) {
    header("Location: add_mobile.php?error=empty&$query_params");
    exit();
}

// Validate name length
if (strlen($mobile_name) > 100) {
    header("Location: add_mobile.php?error=name_length&$query_params");
    exit();
}

// Validate brand length
if (strlen($brand) > 100) {
    header("Location: add_mobile.php?error=brand_length&$query_params");
    exit();
}

// Validate price
if (!is_numeric($price) || $price < 0 || $price > 999999) {
    header("Location: add_mobile.php?error=invalid_price&$query_params");
    exit();
}

// Convert price to integer
$price = (int)$price;

try {
    // Get database connection
    $conn = $database->getConnection();
    
    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO mobile_phone (mobile_name, brand, price) VALUES (:mobile_name, :brand, :price)");
    
    // Bind parameters
    $stmt->bindParam(':mobile_name', $mobile_name);
    $stmt->bindParam(':brand', $brand);
    $stmt->bindParam(':price', $price, PDO::PARAM_INT);
    
    // Execute the statement
    if ($stmt->execute()) {
        // Success - redirect to view_all.php with success message
        header("Location: view_all.php?added");
        exit();
    } else {
        // Execution failed
        header("Location: add_mobile.php?error=db_error&$query_params");
        exit();
    }
    
} catch (PDOException $e) {
    // Log error for debugging
    error_log("Insert Mobile Error: " . $e->getMessage());
    
    // Check for duplicate entry or other specific errors
    if ($e->getCode() == 23000) {
        // Duplicate entry or constraint violation
        header("Location: add_mobile.php?error=duplicate&$query_params");
    } else {
        // General database error
        header("Location: add_mobile.php?error=db_error&$query_params");
    }
    exit();
    
} catch (Exception $e) {
    // Handle other exceptions
    error_log("General Insert Mobile Error: " . $e->getMessage());
    header("Location: add_mobile.php?error=general&$query_params");
    exit();
}
?>
