<?php
/**
 * View All Mobiles
 * Mobile Repository System
 * Displays all mobile phone records from database
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

// Initialize variables
$mobiles = [];
$error_msg = '';
$success_msg = '';

// Handle success messages
if (isset($_GET['deleted'])) {
    $success_msg = "Mobile phone deleted successfully!";
}

if (isset($_GET['added'])) {
    $success_msg = "Mobile phone added successfully!";
}

if (isset($_GET['updated'])) {
    $success_msg = "Mobile phone updated successfully!";
}

// Handle search and price range filter
$search = trim($_GET['search'] ?? '');
$search_query = '';
$price_range = $_GET['price_range'] ?? 'all';

try {
    $conn = $database->getConnection();
    
    // Build base query
    $base_query = "SELECT * FROM mobile_phone";
    $params = [];
    $where_conditions = [];
    
    // Add price range condition
    if ($price_range !== 'all') {
        switch ($price_range) {
            case 'price_10_20':
                $where_conditions[] = "price > 10000 AND price < 20000";
                break;
            case 'price_above_20':
                $where_conditions[] = "price > 20000";
                break;
        }
    }
    
    // Add search condition if search term provided
    if (!empty($search)) {
        $where_conditions[] = "(mobile_name LIKE :search OR brand LIKE :search)";
        $search_param = "%$search%";
        $params[':search'] = $search_param;
        $search_query = $search;
    }
    
    // Combine WHERE conditions
    if (!empty($where_conditions)) {
        $base_query .= " WHERE " . implode(' AND ', $where_conditions);
    }
    
    $base_query .= " ORDER BY created_at DESC";
    
    // Prepare and execute query
    $stmt = $conn->prepare($base_query);
    $stmt->execute($params);
    $mobiles = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("View All Mobiles Error: " . $e->getMessage());
    $error_msg = "Unable to load mobile phone data. Please try again later.";
}

// Get total count for statistics
$total_count = count($mobiles);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Mobiles - Mobile Repository System</title>
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
                <li><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </nav>
        
        <!-- Content -->
        <div class="card">
            <!-- Search and Actions -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                <div>
                    <h2>📱 Mobile Phone Inventory</h2>
                    <p style="color: #718096; margin-top: 5px;">
                        Total Records: <strong><?php echo $total_count; ?></strong> mobile phones
                    </p>
                </div>
                <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-top: 15px;">
                    <a href="add_mobile.php" class="btn btn-success">➕ Add New Mobile</a>
                    <a href="view_all.php" class="btn">📋 View All Mobiles</a>
                </div>
            </div>
            
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
            
            <!-- Search and Filter Form -->
            <div style="margin-bottom: 20px;">
                <form method="GET" action="view_all.php" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end;">
                    <div style="flex: 1; min-width: 200px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #4a5568;">Search:</label>
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Search by name or brand..." 
                            value="<?php echo htmlspecialchars($search_query); ?>"
                            style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 8px;"
                        >
                    </div>
                    
                    <div style="min-width: 200px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #4a5568;">Price Range:</label>
                        <select 
                            name="price_range" 
                            style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 8px; background: white;"
                            onchange="this.form.submit()"
                        >
                            <option value="all" <?php echo $price_range === 'all' ? 'selected' : ''; ?>>All Mobiles</option>
                            <option value="price_10_20" <?php echo $price_range === 'price_10_20' ? 'selected' : ''; ?>>Rs.10,000 - Rs.20,000</option>
                            <option value="price_above_20" <?php echo $price_range === 'price_above_20' ? 'selected' : ''; ?>>Above Rs.20,000</option>
                        </select>
                    </div>
                    
                    <div style="display: flex; gap: 5px;">
                        <button type="submit" class="btn">🔍 Search</button>
                        <?php if (!empty($search_query) || $price_range !== 'all'): ?>
                            <a href="view_all.php" class="btn btn-danger">✖ Clear All</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <!-- Mobile Phones Table -->
            <?php if (count($mobiles) > 0): ?>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Mobile Name</th>
                                <th>Brand</th>
                                <th>Price</th>
                                <th>Added Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mobiles as $mobile): ?>
                            <tr>
                                <td><?php echo $mobile['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($mobile['mobile_name']); ?></strong>
                                </td>
                                <td>
                                    <span style="background: #667eea; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                        <?php echo htmlspecialchars($mobile['brand']); ?>
                                    </span>
                                </td>
                                <td>
                                    <strong style="color: #38a169;">Rs.<?php echo number_format($mobile['price']); ?></strong>
                                </td>
                                <td><?php echo date('M d, Y h:i A', strtotime($mobile['created_at'])); ?></td>
                                <td>
                                    <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                        <!-- Edit Action -->
                                        <a 
                                            href="edit_mobile.php?id=<?php echo $mobile['id']; ?>" 
                                            class="btn" 
                                            style="padding: 5px 10px; font-size: 12px; background: #ed8936; text-decoration: none;"
                                            title="Edit Mobile"
                                        >
                                            ✏️ Edit
                                        </a>
                                        
                                        <!-- Delete Action -->
                                        <button 
                                            class="btn btn-danger" 
                                            style="padding: 5px 10px; font-size: 12px;"
                                            title="Delete Mobile"
                                            onclick="deleteMobile(<?php echo $mobile['id']; ?>, '<?php echo htmlspecialchars($mobile['name']); ?>')"
                                        >
                                            🗑️ Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
            <?php else: ?>
                <div class="alert alert-info">
                    <?php if (!empty($search_query)): ?>
                        No mobile phones found matching "<strong><?php echo htmlspecialchars($search_query); ?></strong>". 
                        <a href="view_all.php">View all mobiles</a> or 
                        <a href="add_mobile.php">add a new mobile phone</a>.
                    <?php else: ?>
                        No mobile phones found in the repository. 
                        <a href="add_mobile.php">Add your first mobile phone</a> to get started!
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- JavaScript for table interactivity and delete functionality -->
    <script>
        // Delete mobile function
        function deleteMobile(mobileId, mobileName) {
            if (confirm(`Are you sure you want to delete "${mobileName}"? This action cannot be undone!`)) {
                // Show loading state
                const button = event.target;
                const originalText = button.innerHTML;
                button.innerHTML = '⏳ Deleting...';
                button.disabled = true;
                
                // Send AJAX request
                fetch('delete_mobile_ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${mobileId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        showSuccessMessage(data.message);
                        
                        // Remove the row from table
                        const row = button.closest('tr');
                        row.style.transition = 'opacity 0.3s ease';
                        row.style.opacity = '0';
                        
                        setTimeout(() => {
                            row.remove();
                            updateMobileCount();
                        }, 300);
                    } else {
                        // Show error message
                        showErrorMessage(data.message);
                        
                        // Restore button
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorMessage('An error occurred while deleting the mobile phone');
                    
                    // Restore button
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
            }
        }
        
        // Show success message
        function showSuccessMessage(message) {
            // Remove any existing alerts
            const existingAlerts = document.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());
            
            // Create success alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success';
            alertDiv.innerHTML = message;
            
            // Insert at the top of the card
            const card = document.querySelector('.card');
            card.insertBefore(alertDiv, card.firstChild);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                alertDiv.style.transition = 'opacity 0.3s ease';
                alertDiv.style.opacity = '0';
                setTimeout(() => alertDiv.remove(), 300);
            }, 3000);
        }
        
        // Show error message
        function showErrorMessage(message) {
            // Remove any existing alerts
            const existingAlerts = document.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());
            
            // Create error alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-error';
            alertDiv.innerHTML = message;
            
            // Insert at the top of the card
            const card = document.querySelector('.card');
            card.insertBefore(alertDiv, card.firstChild);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                alertDiv.style.transition = 'opacity 0.3s ease';
                alertDiv.style.opacity = '0';
                setTimeout(() => alertDiv.remove(), 300);
            }, 5000);
        }
        
        // Update mobile count
        function updateMobileCount() {
            const countElement = document.querySelector('strong');
            const tableRows = document.querySelectorAll('.table tbody tr');
            if (countElement && tableRows) {
                countElement.textContent = tableRows.length;
            }
        }
        
        // Add some interactivity to table rows
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('.table tbody tr');
            rows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.02)';
                    this.style.transition = 'transform 0.2s ease';
                });
                
                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });
    </script>
</body>
</html>
