<?php
/**
 * Add Mobile Form
 * Mobile Repository System
 * Form to add new mobile phone entries
 */

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables for form data and errors
$form_data = [
    'name' => '',
    'brand' => '',
    'price' => ''
];

$errors = [];
$success_msg = '';

// Handle form submission feedback
if (isset($_GET['success'])) {
    $success_msg = "Mobile phone added successfully!";
}

if (isset($_GET['error'])) {
    switch($_GET['error']) {
        case 'empty':
            $errors['general'] = "All fields are required!";
            break;
        case 'invalid_price':
            $errors['price'] = "Price must be a valid number!";
            break;
        case 'db_error':
            $errors['general'] = "Database error. Please try again.";
            break;
        default:
            $errors['general'] = "Failed to add mobile phone. Please try again.";
    }
}

// Restore form data if there was an error
if (isset($_GET['name'])) {
    $form_data['name'] = htmlspecialchars($_GET['name']);
}
if (isset($_GET['brand'])) {
    $form_data['brand'] = htmlspecialchars($_GET['brand']);
}
if (isset($_GET['price'])) {
    $form_data['price'] = htmlspecialchars($_GET['price']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Mobile Phone - Mobile Repository System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
       
        <!-- Navigation Menu -->
        <nav class="nav-menu">
            <ul>
                <li><a href="dashboard.php">🏠 Home</a></li>
                <li><a href="add_mobile.php">➕ Add Mobile Phone</a></li>
                <li><a href="view_all.php">📋 View All Mobiles</a></li>
                <li><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </nav>
        
        <!-- Add Mobile Form -->
        <div class="card">
            <h2>➕ Add New Mobile Phone</h2>
            
            <!-- Success Message -->
            <?php if ($success_msg): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success_msg); ?>
                </div>
            <?php endif; ?>
            
            <!-- General Error Message -->
            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($errors['general']); ?>
                </div>
            <?php endif; ?>
            
            <!-- Form -->
            <form action="insert_mobile.php" method="POST" id="mobileForm">
                <div class="form-group">
                    <label for="name">Mobile Name *</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="<?php echo $form_data['name']; ?>"
                        required 
                        placeholder="e.g., iPhone 14 Pro"
                        maxlength="100"
                    >
                    <?php if (isset($errors['name'])): ?>
                        <small style="color: #e53e3e; display: block; margin-top: 5px;">
                            <?php echo htmlspecialchars($errors['name']); ?>
                        </small>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="brand">Brand *</label>
                    <input 
                        type="text" 
                        id="brand" 
                        name="brand" 
                        value="<?php echo $form_data['brand']; ?>"
                        required 
                        placeholder="e.g., Apple"
                        maxlength="100"
                    >
                    <?php if (isset($errors['brand'])): ?>
                        <small style="color: #e53e3e; display: block; margin-top: 5px;">
                            <?php echo htmlspecialchars($errors['brand']); ?>
                        </small>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="price">Price (Rs.) *</label>
                    <input 
                        type="number" 
                        id="price" 
                        name="price" 
                        value="<?php echo $form_data['price']; ?>"
                        required 
                        placeholder="e.g., 85000"
                        min="0"
                        max="999999"
                        step="1"
                    >
                    <?php if (isset($errors['price'])): ?>
                        <small style="color: #e53e3e; display: block; margin-top: 5px;">
                            <?php echo htmlspecialchars($errors['price']); ?>
                        </small>
                    <?php endif; ?>
                </div>
                
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <button type="submit" class="btn btn-success">
                        💾 Add Mobile Phone
                    </button>
                    <button type="reset" class="btn btn-danger">
                        🔄 Reset Form
                    </button>
                    <a href="dashboard.php" class="btn">
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
