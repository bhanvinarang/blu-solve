<?php
// admin/view_product.php
session_start();

$host = 'localhost';
$username = 'blusolv_db';
$password = 'blusolv_db';
$database = 'blusolv_db';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: products.php');
    exit;
}

$product_id = intval($_GET['id']);

// Fetch product details with category and material
$fetch_sql = "SELECT p.*, c.category_name, m.material_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              LEFT JOIN materials m ON p.material_id = m.id 
              WHERE p.id = ?";
$fetch_stmt = $conn->prepare($fetch_sql);
$fetch_stmt->bind_param("i", $product_id);
$fetch_stmt->execute();
$result = $fetch_stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: products.php');
    exit;
}

$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?> - Admin Panel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f6fa;
            color: #333;
        }
        
        .sidebar {
            width: 250px;
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            padding: 0 20px;
        }
        
        .sidebar ul {
            list-style: none;
        }
        
        .sidebar ul li {
            margin-bottom: 5px;
        }
        
        .sidebar ul li a {
            display: block;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background-color: rgba(255,255,255,0.1);
            border-left: 4px solid #fff;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            color: #333;
            font-size: 28px;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            transition: all 0.3s ease;
            margin-left: 10px;
        }
        
        .btn-primary {
            background-color: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #5568d3;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .btn-info {
            background-color: #0bc5ea;
            color: white;
        }
        
        .btn-info:hover {
            background-color: #09a0c8;
        }
        
        .btn-danger {
            background-color: #f56565;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #e53e3e;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 20px;
        }
        
        .content-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .section-title {
            font-size: 1.2em;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        
        .product-image-container {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .product-image {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            max-height: 400px;
        }
        
        .product-image.no-image {
            width: 100%;
            height: 300px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: #999;
            border-radius: 10px;
        }
        
        .info-group {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e1e8ed;
        }
        
        .info-group:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #667eea;
            font-size: 13px;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        
        .info-value {
            color: #333;
            font-size: 16px;
            line-height: 1.6;
        }
        
        .price-display {
            display: inline-block;
            background-color: #48bb78;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 24px;
            font-weight: bold;
            margin-top: 5px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 5px;
        }
        
        .status-active {
            background-color: #e8f5e8;
            color: #388e3c;
        }
        
        .status-inactive {
            background-color: #fff3e0;
            color: #f57c00;
        }
        
        .status-deleted {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .description-box {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            line-height: 1.8;
            color: #555;
            margin-top: 10px;
        }
        
        .description-box.empty {
            color: #999;
            font-style: italic;
        }
        
        .metadata {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 15px;
        }
        
        .meta-item {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        
        .meta-label {
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .meta-value {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .info-box {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            border-left: 4px solid #667eea;
        }
        
        .info-box p {
            margin-bottom: 8px;
            color: #555;
            font-size: 14px;
        }
        
        .info-box strong {
            color: #333;
        }
        
        .tag {
            display: inline-block;
            background-color: #e3f2fd;
            color: #1976d2;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .header h1 {
                font-size: 22px;
            }
            
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .metadata {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                margin-left: 0;
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>BluSolv Admin</h2>
        <ul>
            <li><a href="dashboard.php">📊 Dashboard</a></li>
            <li><a href="products.php" class="active">📦 Products</a></li>
            <li><a href="add_product.php">➕ Add Product</a></li>
            <li><a href="categories.php">📂 Categories</a></li>
            <li><a href="materials.php">🔧 Materials</a></li>
            <li><a href="enquiries.php">💬 Enquiries</a></li>
            <li><a href="settings.php">⚙️ Settings</a></li>
            <li><a href="../products.php" target="_blank">🌐 View Site</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
            <div class="action-buttons" style="display: flex; gap: 10px;">
                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">Edit Product</a>
                <a href="products.php" class="btn btn-secondary">Back</a>
            </div>
        </div>
        
        <div class="content-grid">
            <!-- Main Product Details -->
            <div class="content-section">
                <h2 class="section-title">Product Details</h2>
                
                <!-- Product Image -->
                <div class="product-image-container">
                    <?php if (!empty($product['product_image'])): ?>
                        <img src="../<?php echo htmlspecialchars($product['product_image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                             class="product-image">
                    <?php else: ?>
                        <div class="product-image no-image">📷</div>
                    <?php endif; ?>
                </div>
                
                <!-- Product Information -->
                <div class="info-group">
                    <div class="info-label">Product Name</div>
                    <div class="info-value"><?php echo htmlspecialchars($product['product_name']); ?></div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">Price</div>
                    <div class="price-display">₹<?php echo number_format($product['price'], 2); ?></div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">Category</div>
                    <div class="info-value">
                        <span class="tag"><?php echo htmlspecialchars($product['category_name'] ?? 'Not Assigned'); ?></span>
                    </div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">Material</div>
                    <div class="info-value">
                        <span class="tag"><?php echo htmlspecialchars($product['material_name'] ?? 'Not Assigned'); ?></span>
                    </div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">Status</div>
                    <div>
                        <span class="status-badge status-<?php echo $product['status']; ?>">
                            <?php echo ucfirst($product['status']); ?>
                        </span>
                    </div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">Description</div>
                    <?php if (!empty($product['description'])): ?>
                        <div class="description-box"><?php echo nl2br(htmlspecialchars($product['description'])); ?></div>
                    <?php else: ?>
                        <div class="description-box empty">No description provided</div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Product Info Sidebar -->
            <div class="content-section">
                <h2 class="section-title">Quick Info</h2>
                
                <div class="metadata">
                    <div class="meta-item">
                        <div class="meta-label">Product ID</div>
                        <div class="meta-value">#<?php echo $product['id']; ?></div>
                    </div>
                    
                    <div class="meta-item">
                        <div class="meta-label">Status</div>
                        <div class="meta-value"><?php echo ucfirst($product['status']); ?></div>
                    </div>
                    
                    <div class="meta-item">
                        <div class="meta-label">Created Date</div>
                        <div class="meta-value"><?php echo date('M j, Y', strtotime($product['created_at'])); ?></div>
                    </div>
                    
                    <div class="meta-item">
                        <div class="meta-label">Created Time</div>
                        <div class="meta-value"><?php echo date('h:i A', strtotime($product['created_at'])); ?></div>
                    </div>
                </div>
                
                <div class="info-box">
                    <p><strong>Category:</strong><br><?php echo htmlspecialchars($product['category_name'] ?? 'Not Assigned'); ?></p>
                    <p style="margin-top: 15px;"><strong>Material:</strong><br><?php echo htmlspecialchars($product['material_name'] ?? 'Not Assigned'); ?></p>
                    <p style="margin-top: 15px;"><strong>Price:</strong><br>
                        <span style="font-size: 18px; color: #48bb78; font-weight: bold;">₹<?php echo number_format($product['price'], 2); ?></span>
                    </p>
                </div>
                
                <div style="margin-top: 20px;">
                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary" style="width: 100%; text-align: center;">Edit This Product</a>
                </div>
                
                <div style="margin-top: 15px; padding: 15px; background-color: #f8f9fa; border-radius: 8px;">
                    <h4 style="margin-bottom: 10px; color: #667eea; font-size: 13px; font-weight: 600;">Quick Actions:</h4>
                    <ul style="margin-left: 20px; color: #666; line-height: 1.8; font-size: 12px;">
                        <li>Click Edit to modify product details</li>
                        <li>Change product status or price</li>
                        <li>Upload a new product image</li>
                        <li>Update category or material</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>