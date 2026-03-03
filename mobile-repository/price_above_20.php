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
        <!-- Header -->
        <div class="header">
            <h1>💎 Premium Mobile Phones: Above Rs.20,000</h1>
            <p>High-end mobile phones with advanced features and premium quality</p>
        </div>
        
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
                    <a href="price_10_20.php" class="btn">💰 Budget (₹10K-₹20K)</a>
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
                                <th>Premium Tier</th>
                                <th>Added Date</th>
                                <th>Rating</th>
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
                                        <span style="background: #d69e2e; color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px; margin-left: 5px;">
                                            ⭐ FLAGSHIP
                                        </span>
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
                                <td>
                                    <span style="background: <?php echo $tier_color; ?>; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                        <?php echo $tier; ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($mobile['created_at'])); ?></td>
                                <td>
                                    <div style="color: #d69e2e;">
                                        <?php 
                                        // Display star rating
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $stars) {
                                                echo '⭐';
                                            } elseif ($i - 0.5 <= $stars) {
                                                echo '✨';
                                            } else {
                                                echo '☆';
                                            }
                                        }
                                        ?>
                                        <small style="color: #718096;">(<?php echo $stars; ?>/5)</small>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Premium Statistics -->
                <div style="margin-top: 30px; padding: 20px; background: #f7fafc; border-radius: 8px;">
                    <h3>📊 Premium Market Analysis</h3>
                    
                    <?php 
                    // Calculate premium statistics
                    $total_value = array_sum(array_column($mobiles, 'price'));
                    $avg_price = $count > 0 ? $total_value / $count : 0;
                    $most_expensive = null;
                    $least_expensive = null;
                    $max_price = 0;
                    $min_price = PHP_INT_MAX;
                    
                    foreach ($mobiles as $mobile) {
                        if ($mobile['price'] > $max_price) {
                            $max_price = $mobile['price'];
                            $most_expensive = $mobile;
                        }
                        if ($mobile['price'] < $min_price) {
                            $min_price = $mobile['price'];
                            $least_expensive = $mobile;
                        }
                    }
                    
                    // Count by tier
                    $tier_counts = [
                        'Mid-Premium' => 0,
                        'Premium' => 0,
                        'High-End' => 0,
                        'Flagship' => 0
                    ];
                    
                    foreach ($mobiles as $mobile) {
                        if ($mobile['price'] <= 30000) {
                            $tier_counts['Mid-Premium']++;
                        } elseif ($mobile['price'] <= 50000) {
                            $tier_counts['Premium']++;
                        } elseif ($mobile['price'] <= 75000) {
                            $tier_counts['High-End']++;
                        } else {
                            $tier_counts['Flagship']++;
                        }
                    }
                    ?>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
                        <div style="text-align: center; padding: 15px; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <h4 style="color: #4a5568; margin-bottom: 10px;">Average Price</h4>
                            <div style="font-size: 1.5rem; font-weight: bold; color: #e53e3e;">Rs.<?php echo number_format($avg_price, 0); ?></div>
                        </div>
                        
                        <div style="text-align: center; padding: 15px; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <h4 style="color: #4a5568; margin-bottom: 10px;">Most Expensive</h4>
                            <div style="font-size: 1.2rem; font-weight: bold; color: #d69e2e;">
                                <?php if ($most_expensive): ?>
                                    Rs.<?php echo number_format($most_expensive['price']); ?>
                                    <div style="font-size: 0.8rem; color: #718096; font-weight: normal;">
                                        <?php echo htmlspecialchars($most_expensive['name']); ?>
                                    </div>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div style="text-align: center; padding: 15px; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <h4 style="color: #4a5568; margin-bottom: 10px;">Least Expensive</h4>
                            <div style="font-size: 1.2rem; font-weight: bold; color: #38a169;">
                                <?php if ($least_expensive): ?>
                                    Rs.<?php echo number_format($least_expensive['price']); ?>
                                    <div style="font-size: 0.8rem; color: #718096; font-weight: normal;">
                                        <?php echo htmlspecialchars($least_expensive['name']); ?>
                                    </div>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div style="text-align: center; padding: 15px; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <h4 style="color: #4a5568; margin-bottom: 10px;">Total Value</h4>
                            <div style="font-size: 1.5rem; font-weight: bold; color: #667eea;">Rs.<?php echo number_format($total_value); ?></div>
                        </div>
                    </div>
                    
                    <!-- Tier Distribution -->
                    <div style="margin-top: 25px;">
                        <h4 style="margin-bottom: 15px;">🏆 Premium Tier Distribution</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
                            <?php foreach ($tier_counts as $tier => $count): ?>
                            <?php 
                            $colors = [
                                'Mid-Premium' => '#805ad5',
                                'Premium' => '#ed8936',
                                'High-End' => '#e53e3e',
                                'Flagship' => '#d69e2e'
                            ];
                            $color = $colors[$tier];
                            ?>
                            <div style="text-align: center; padding: 15px; background: <?php echo $color; ?>20; border: 2px solid <?php echo $color; ?>; border-radius: 8px;">
                                <div style="font-size: 1.8rem; font-weight: bold; color: <?php echo $color; ?>;"><?php echo $count; ?></div>
                                <div style="color: #4a5568; font-weight: 600;"><?php echo $tier; ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Premium Insights -->
                <div style="margin-top: 20px; padding: 20px; background: linear-gradient(135deg, #667eea10, #764ba210); border-left: 4px solid #667eea; border-radius: 4px;">
                    <h3>💎 Premium Insights</h3>
                    <div style="margin-top: 15px; color: #4a5568; line-height: 1.8;">
                        <p><strong>Market Position:</strong> Premium mobile phones represent the high-end segment with advanced features, superior build quality, and cutting-edge technology.</p>
                        <div style="margin-top: 10px;">
                            <strong>Key Features in Premium Segment:</strong>
                            <ul style="margin-top: 5px;">
                                <li>Advanced camera systems with multiple lenses</li>
                                <li>High-refresh-rate displays (90Hz-120Hz)</li>
                                <li>Premium build materials (glass, metal, ceramic)</li>
                                <li>Powerful processors and ample RAM</li>
                                <li>5G connectivity and future-proof technology</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
            <?php else: ?>
                <div class="alert alert-info">
                    <h3>💎 No Premium Mobile Phones Found</h3>
                    <p>No mobile phones found above Rs.20,000 in the repository.</p>
                    <div style="margin-top: 15px;">
                        <strong>Suggestions:</strong>
                        <ul style="margin-top: 10px; color: #4a5568;">
                            <li><a href="add_mobile.php">Add premium mobile phones</a> to expand the high-end collection</li>
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
