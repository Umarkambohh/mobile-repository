<?php
/**
 * Edit Mobile Form
 * Mobile Repository System
 * Form to edit existing mobile phone entries
 */

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get mobile ID from URL
$mobile_id = $_GET['id'] ?? '';

if (empty($mobile_id) || !is_numeric($mobile_id)) {
    header("Location: view_all.php?error=invalid_id");
    exit();
}

// Include database connection
require_once 'db.php';

// Initialize variables
$mobile = null;
$error_msg = '';
$success_msg = '';

// Handle success messages
if (isset($_GET['success'])) {
    $success_msg = "Mobile phone updated successfully!";
}

// Load mobile data
try {
    $conn = $database->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM mobiles WHERE id = :id");
    $stmt->bindParam(':id', $mobile_id);
    $stmt->execute();
    
    $mobile = $stmt->fetch();
    
    if (!$mobile) {
        header("Location: view_all.php?error=not_found");
        exit();
    }
    
} catch (Exception $e) {
    error_log("Edit Mobile Error: " . $e->getMessage());
    $error_msg = "Unable to load mobile phone data.";
}

// Handle form submission feedback
if (isset($_GET['error'])) {
    switch($_GET['error']) {
        case 'empty':
            $error_msg = "All fields are required!";
            break;
        case 'invalid_price':
            $error_msg = "Price must be a valid number!";
            break;
        case 'db_error':
            $error_msg = "Database error. Please try again.";
            break;
        default:
            $error_msg = "Failed to update mobile phone. Please try again.";
    }
}

// Restore form data if there was an error
$form_data = [
    'name' => $_GET['name'] ?? $mobile['name'] ?? '',
    'brand' => $_GET['brand'] ?? $mobile['brand'] ?? '',
    'price' => $_GET['price'] ?? $mobile['price'] ?? ''
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mobile Phone - Mobile Repository System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Navigation Menu -->
        <nav class="nav-menu">
            <ul>
                <li><a href="dashboard.php">🏠 Home</a></li>
                <li><a href="add_mobile.php">➕ Add Mobile Phone</a></li>
                <li><a href="view_all.php">📋 View All Mobiles</a></li>
                <li><a href="price_10_20.php">💰 Price Rs.10,000 – Rs.20,000</a></li>
                <li><a href="price_above_20.php">💎 Price Above Rs.20,000</a></li>
                <li><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </nav>
        
        <!-- Edit Mobile Form -->
        <div class="card">
            <h2>✏️ Edit Mobile Phone #<?php echo $mobile_id; ?></h2>
            
            <!-- Success Message -->
            <?php if ($success_msg): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success_msg); ?>
                </div>
            <?php endif; ?>
            
            <!-- Error Message -->
            <?php if ($error_msg): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error_msg); ?>
                </div>
            <?php endif; ?>
            
            <!-- Form -->
            <form action="update_mobile.php" method="POST" id="mobileForm">
                <input type="hidden" name="id" value="<?php echo $mobile_id; ?>">
                
                <div class="form-group">
                    <label for="name">Mobile Name *</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="<?php echo htmlspecialchars($form_data['name']); ?>"
                        required 
                        placeholder="e.g., iPhone 14 Pro"
                        maxlength="100"
                    >
                </div>
                
                <div class="form-group">
                    <label for="brand">Brand *</label>
                    <input 
                        type="text" 
                        id="brand" 
                        name="brand" 
                        value="<?php echo htmlspecialchars($form_data['brand']); ?>"
                        required 
                        placeholder="e.g., Apple"
                        maxlength="100"
                    >
                </div>
                
                <div class="form-group">
                    <label for="price">Price (Rs.) *</label>
                    <input 
                        type="number" 
                        id="price" 
                        name="price" 
                        value="<?php echo htmlspecialchars($form_data['price']); ?>"
                        required 
                        placeholder="e.g., 85000"
                        min="0"
                        max="999999"
                        step="1"
                    >
                </div>
                
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <button type="submit" class="btn btn-success">
                        💾 Update Mobile Phone
                    </button>
                    <button type="reset" class="btn btn-danger">
                        🔄 Reset Form
                    </button>
                    <a href="view_all.php" class="btn">
                        ❌ Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- JavaScript for client-side validation -->
    <script>
        document.getElementById('mobileForm').addEventListener('submit', function(e) {
            // Clear previous errors
            const errorElements = this.querySelectorAll('small[style*="color: #e53e3e"]');
            errorElements.forEach(el => el.remove());
            
            let isValid = true;
            
            // Validate name
            const name = document.getElementById('name').value.trim();
            if (name === '') {
                showError('name', 'Mobile name is required');
                isValid = false;
            } else if (name.length > 100) {
                showError('name', 'Mobile name must be 100 characters or less');
                isValid = false;
            }
            
            // Validate brand
            const brand = document.getElementById('brand').value.trim();
            if (brand === '') {
                showError('brand', 'Brand is required');
                isValid = false;
            } else if (brand.length > 100) {
                showError('brand', 'Brand must be 100 characters or less');
                isValid = false;
            }
            
            // Validate price
            const price = document.getElementById('price').value;
            if (price === '') {
                showError('price', 'Price is required');
                isValid = false;
            } else if (isNaN(price) || parseFloat(price) < 0) {
                showError('price', 'Price must be a valid positive number');
                isValid = false;
            } else if (parseFloat(price) > 999999) {
                showError('price', 'Price must be less than 1,000,000');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
        
        function showError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const error = document.createElement('small');
            error.style.color = '#e53e3e';
            error.style.display = 'block';
            error.style.marginTop = '5px';
            error.textContent = message;
            field.parentNode.appendChild(error);
        }
        
        // Reset button functionality
        document.querySelector('button[type="reset"]').addEventListener('click', function() {
            // Remove all error messages
            const errorElements = document.querySelectorAll('small[style*="color: #e53e3e"]');
            errorElements.forEach(el => el.remove());
        });
    </script>
</body>
</html>
