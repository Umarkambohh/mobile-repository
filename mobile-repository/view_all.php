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

// Handle search
$search = trim($_GET['search'] ?? '');
$search_query = '';

try {
    $conn = $database->getConnection();
    
    // Build base query
    $base_query = "SELECT * FROM mobiles";
    $params = [];
    
    // Add search condition if search term provided
    if (!empty($search)) {
        $base_query .= " WHERE name LIKE :search OR brand LIKE :search";
        $search_param = "%$search%";
        $params[':search'] = $search_param;
        $search_query = $search;
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
                <li><a href="price_10_20.php">💰 Price Rs.10,000 – Rs.20,000</a></li>
                <li><a href="price_above_20.php">💎 Price Above Rs.20,000</a></li>
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
                    <a href="price_10_20.php" class="btn">💰 Rs.10K-Rs.20K</a>
                    <a href="price_above_20.php" class="btn">💎 Premium (>Rs.20K)</a>
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
            
            <!-- Search Form -->
            <div style="margin-bottom: 20px;">
                <form method="GET" action="view_all.php" style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Search by name or brand..." 
                        value="<?php echo htmlspecialchars($search_query); ?>"
                        style="flex: 1; min-width: 200px; padding: 10px; border: 2px solid #e2e8f0; border-radius: 8px;"
                    >
                    <button type="submit" class="btn">🔍 Search</button>
                    <?php if (!empty($search_query)): ?>
                        <a href="view_all.php" class="btn btn-danger">✖ Clear</a>
                    <?php endif; ?>
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
                                    <strong><?php echo htmlspecialchars($mobile['name']); ?></strong>
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
                                        <a 
                                            href="delete_mobile.php?id=<?php echo $mobile['id']; ?>" 
                                            class="btn btn-danger" 
                                            style="padding: 5px 10px; font-size: 12px; text-decoration: none;"
                                            title="Delete Mobile"
                                            onclick="return confirm('Are you sure you want to delete this mobile phone?')"
                                        >
                                            🗑️ Delete
                                        </a>
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
    
    <!-- JavaScript for table interactivity -->
    <script>
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
