<?php
/**
 * Price Range 10,000 - 20,000
 * Mobile Repository System
 * Displays mobile phones with price between 10,000 and 20,000
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
    
    // Query for mobiles with price > 10000 AND < 20000
    $stmt = $conn->prepare("SELECT * FROM mobiles WHERE price > 10000 AND price < 20000 ORDER BY price ASC, name ASC");
    $stmt->execute();
    $mobiles = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Price 10-20k Error: " . $e->getMessage());
    $error_msg = "Unable to load mobile phone data. Please try again later.";
}

$count = count($mobiles);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Price 10,000 - 20,000 - Mobile Repository System</title>
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
                <li><a href="price_10_20.php">💰 Price 10,000 – 20,000</a></li>
                <li><a href="price_above_20.php">💎 Price Above 20,000</a></li>
                <li><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </nav>
        
        <!-- Content -->
        <div class="card">
            <!-- Statistics Header -->
            <div style="background: linear-gradient(135deg, #f093fb, #f5576c); color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                    <div>
                        <h2 style="margin-bottom: 5px;">📊 Budget Range Analysis</h2>
                        <p style="opacity: 0.9;">
                            Found <strong><?php echo $count; ?></strong> mobile phones in this price range
                        </p>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 2rem; font-weight: bold;">Rs.10K-Rs.20K</div>
                        <div style="font-size: 0.9rem; opacity: 0.9;">Price Range</div>
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
                    <a href="price_above_20.php" class="btn">💎 Premium (>Rs20K)</a>
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
                                <th>Price Range</th>
                                <th>Added Date</th>
                                <!-- <th>Value Score</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mobiles as $mobile): ?>
                            <?php 
                            // Calculate value score (simple algorithm based on price position in range)
                            $price_position = ($mobile['price'] - 10000) / 10000; // 0 to 1
                            $value_score = round((1 - $price_position) * 100, 0); // Lower price = higher score
                            
                            // Determine price category
                            if ($mobile['price'] <= 12000) {
                                $price_category = 'Budget';
                                $category_color = '#38a169';
                            } elseif ($mobile['price'] <= 15000) {
                                $price_category = 'Mid-Budget';
                                $category_color = '#805ad5';
                            } elseif ($mobile['price'] <= 18000) {
                                $price_category = 'Upper-Mid';
                                $category_color = '#ed8936';
                            } else {
                                $price_category = 'Near-Premium';
                                $category_color = '#e53e3e';
                            }
                            ?>
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
                                <td>
                                    <span style="background: <?php echo $category_color; ?>; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px;">
                                        <?php echo $price_category; ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($mobile['created_at'])); ?></td>
                                <!-- <td>
                                    <div style="display: flex; align-items: center; gap: 5px;">
                                        <div style="flex: 1; background: #e2e8f0; border-radius: 4px; height: 8px; overflow: hidden;">
                                            <div style="background: <?php echo $value_score >= 70 ? '#38a169' : ($value_score >= 40 ? '#ed8936' : '#e53e3e'); ?>; height: 100%; width: <?php echo $value_score; ?>%;"></div>
                                        </div>
                                        <small style="color: #718096;"><?php echo $value_score; ?>%</small>
                                    </div>
                                </td> -->
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
            <?php else: ?>
                <div class="alert alert-info">
                    <h3>📱 No Mobile Phones Found</h3>
                    <p>No mobile phones found in Rs.10,000 - Rs.20,000 price range.</p>
                    <div style="margin-top: 15px;">
                        <strong>Suggestions:</strong>
                        <ul style="margin-top: 10px; color: #4a5568;">
                            <li><a href="add_mobile.php">Add new mobile phones</a> in this price range</li>
                            <li><a href="view_all.php">View all mobile phones</a> to see available options</li>
                            <li><a href="price_10_20.php">Check budget mobile phones</a> in the Rs.10K-Rs.20K range</li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
