<?php
/**
 * Price Above 20,000
 * Mobile Repository System
 * Displays mobile phones with price above 20,000
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

try {
    $conn = $database->getConnection();
    
    // Query for mobiles with price > 20000
    $stmt = $conn->prepare("SELECT * FROM mobiles WHERE price > 20000 ORDER BY price DESC, name ASC");
    $stmt->execute();
    $mobiles = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Price Above 20k Error: " . $e->getMessage());
    $error_msg = "Unable to load mobile phone data. Please try again later.";
}

$count = count($mobiles);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Price Above 20,000 - Mobile Repository System</title>
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
                <li><a href="price_10_20.php">💰 Budget (Rs.10K-Rs.20K)</a></li>
                <li><a href="price_above_20.php">💎 Price Above 20,000</a></li>
                <li><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </nav>
        
        <!-- Content -->
        <div class="card">
            <!-- Statistics Header -->
            <div style="background: linear-gradient(135deg, #4facfe, #00f2fe); color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                    <div>
                        <h2 style="margin-bottom: 5px;">🏆 Premium Collection</h2>
                        <p style="opacity: 0.9;">
                            Found <strong><?php echo $count; ?></strong> premium mobile phones
                        </p>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 2rem; font-weight: bold;">>Rs.20K</div>
                        <div style="font-size: 0.9rem; opacity: 0.9;">Premium Range</div>
                    </div>
                </div>
            </div>
            
            <!-- Error Message -->
            <?php if ($error_msg): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error_msg); ?>
                </div>
            <?php endif; ?>
            
            <!-- Quick Navigation -->
            <div style="margin-bottom: 20px;">
                <h3>🚀 Quick Navigation</h3>
                <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px;">
                    <a href="dashboard.php" class="btn">🏠 Dashboard</a>
                    <a href="view_all.php" class="btn">📋 All Mobiles</a>
                    <a href="price_10_20.php" class="btn">💰 Budget (Rs10K-Rs20K)</a>
                    <a href="add_mobile.php" class="btn btn-success">➕ Add Mobile</a>
                </div>
            </div>
            
            <!-- Mobile Phones Table -->
            <?php if ($count > 0): ?>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Mobile Name</th>
                                <th>Brand</th>
                                <th>Price</th>
                                <!-- <th>Premium Tier</th> -->
                                <th>Added Date</th>
                                <!-- <th>Rating</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mobiles as $mobile): ?>
                            <?php 
                            // Determine premium tier
                            if ($mobile['price'] <= 30000) {
                                $tier = 'Mid-Premium';
                                $tier_color = '#805ad5';
                                $stars = 3;
                            } elseif ($mobile['price'] <= 50000) {
                                $tier = 'Premium';
                                $tier_color = '#ed8936';
                                $stars = 4;
                            } elseif ($mobile['price'] <= 75000) {
                                $tier = 'High-End';
                                $tier_color = '#e53e3e';
                                $stars = 4.5;
                            } else {
                                $tier = 'Flagship';
                                $tier_color = '#d69e2e';
                                $stars = 5;
                            }
                            ?>
                            <tr>
                                <td><?php echo $mobile['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($mobile['name']); ?></strong>
                                    <?php if ($mobile['price'] >= 75000): ?>
                                       
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="background: #667eea; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                        <?php echo htmlspecialchars($mobile['brand']); ?>
                                    </span>
                                </td>
                                <td>
                                    <strong style="color: #e53e3e; font-size: 1.1em;">Rs.<?php echo number_format($mobile['price']); ?></strong>
                                </td>
                                
                                <td><?php echo date('M d, Y', strtotime($mobile['created_at'])); ?></td>
                                
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
            <?php else: ?>
                <div class="alert alert-info">
                    <h3>💎 No Premium Mobile Phones Found</h3>
                    <p>No mobile phones found above Rs.20,000 in the repository.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
