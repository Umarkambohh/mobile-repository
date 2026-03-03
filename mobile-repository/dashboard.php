<?php
/**
 * Dashboard/Home Page
 * Mobile Repository System
 * Main dashboard after successful login
 */

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection for statistics
require_once 'db.php';

try {
    $conn = $database->getConnection();
    
    // Get total mobile count
    $stmt = $conn->query("SELECT COUNT(*) as total FROM mobiles");
    $total_mobiles = $stmt->fetch()['total'];
    
    // Get count of mobiles between 10k-20k
    $stmt = $conn->query("SELECT COUNT(*) as count FROM mobiles WHERE price > 10000 AND price < 20000");
    $mobile_10_20 = $stmt->fetch()['count'];
    
    // Get count of mobiles above 20k
    $stmt = $conn->query("SELECT COUNT(*) as count FROM mobiles WHERE price > 20000");
    $mobile_above_20 = $stmt->fetch()['count'];
    
    // Get latest 5 mobiles
    $stmt = $conn->query("SELECT * FROM mobiles ORDER BY created_at DESC LIMIT 5");
    $latest_mobiles = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    $error_msg = "Unable to load dashboard data.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mobile Repository System</title>
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
                <li><a href="price_10_20.php">💰 Price Rs.10,000 – Rs.20,000</a></li>
                <li><a href="price_above_20.php">💎 Price Above Rs.20,000</a></li>
                <li><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </nav>
        
        <!-- Dashboard Content -->
        <div class="card">
            <h2>📊 Dashboard Overview</h2>
            
            <?php if (isset($error_msg)): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error_msg); ?>
                </div>
            <?php endif; ?>
            
            <!-- Statistics Cards -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
                <div style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 20px; border-radius: 10px; text-align: center;">
                    <h3 style="font-size: 2rem; margin-bottom: 10px;"><?php echo $total_mobiles ?? 0; ?></h3>
                    <p>Total Mobile Phones</p>
                </div>
                
                <div style="background: linear-gradient(135deg, #f093fb, #f5576c); color: white; padding: 20px; border-radius: 10px; text-align: center;">
                    <h3 style="font-size: 2rem; margin-bottom: 10px;"><?php echo $mobile_10_20 ?? 0; ?></h3>
                    <p>Price 10k-20k Range</p>
                </div>
                
                <div style="background: linear-gradient(135deg, #4facfe, #00f2fe); color: white; padding: 20px; border-radius: 10px; text-align: center;">
                    <h3 style="font-size: 2rem; margin-bottom: 10px;"><?php echo $mobile_above_20 ?? 0; ?></h3>
                    <p>Above 20k Price</p>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div style="margin-bottom: 30px;">
                <h3>🚀 Quick Actions</h3>
                <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-top: 15px;">
                    <a href="add_mobile.php" class="btn btn-success">➕ Add New Mobile</a>
                    <a href="view_all.php" class="btn">📋 View All Mobiles</a>
                    <a href="price_10_20.php" class="btn">💰 Rs.10K-Rs.20K</a>
                    <a href="price_above_20.php" class="btn">💎 Premium (>Rs.20K)</a>
                </div>
            </div>
            
            <!-- Latest Mobiles -->
            <?php if (isset($latest_mobiles) && count($latest_mobiles) > 0): ?>
            <div>
                <h3>📱 Latest Added Mobile Phones</h3>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Brand</th>
                                <th>Price</th>
                                <th>Added Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($latest_mobiles as $mobile): ?>
                            <tr>
                                <td><?php echo $mobile['id']; ?></td>
                                <td><?php echo htmlspecialchars($mobile['name']); ?></td>
                                <td><?php echo htmlspecialchars($mobile['brand']); ?></td>
                                <td>Rs.<?php echo number_format($mobile['price']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($mobile['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (count($latest_mobiles) >= 5): ?>
                <div class="text-center mt-20">
                    <a href="view_all.php" class="btn">View All Mobiles</a>
                </div>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="alert alert-info">
                No mobile phones found in the database. <a href="add_mobile.php">Add your first mobile phone</a> to get started!
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
