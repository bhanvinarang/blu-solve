<?php
// admin/edit_material.php
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
    header('Location: materials.php');
    exit;
}

$material_id = intval($_GET['id']);

// Handle form submission (update material)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $material_name = trim($_POST['material_name']);
    $description = trim($_POST['description']);
    
    if (!empty($material_name)) {
        // Check if material name already exists (excluding current material)
        $check_sql = "SELECT id FROM materials WHERE material_name = ? AND id != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $material_name, $material_id);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows > 0) {
            $error_message = "Material name already exists!";
        } else {
            $update_sql = "UPDATE materials SET material_name = ?, description = ? WHERE id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("ssi", $material_name, $description, $material_id);
            
            if ($stmt->execute()) {
                $success_message = "Material updated successfully!";
            } else {
                $error_message = "Error updating material: " . $conn->error;
            }
        }
    } else {
        $error_message = "Material name is required!";
    }
}

// Fetch material details
$fetch_sql = "SELECT * FROM materials WHERE id = ?";
$fetch_stmt = $conn->prepare($fetch_sql);
$fetch_stmt->bind_param("i", $material_id);
$fetch_stmt->execute();
$result = $fetch_stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: materials.php');
    exit;
}

$material = $result->fetch_assoc();

// Get product count for this material
$product_count_sql = "SELECT COUNT(*) as count FROM products WHERE material_id = ? AND status != 'deleted'";
$product_count_stmt = $conn->prepare($product_count_sql);
$product_count_stmt->bind_param("i", $material_id);
$product_count_stmt->execute();
$product_count_result = $product_count_stmt->get_result();
$product_count = $product_count_result->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Material - Admin Panel</title>
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
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 16px;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-control:disabled {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid transparent;
        }
        
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
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
            margin-right: 10px;
            margin-top: 10px;
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
        
        .btn-container {
            display: flex;
            gap: 10px;
            margin-top: 30px;
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
        
        .status-badge {
            display: inline-block;
            background-color: #48bb78;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 5px;
        }
        
        .product-badge {
            display: inline-block;
            background-color: #667eea;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
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
            }
            
            .content-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>BluSolv Admin</h2>
        <ul>
            <li><a href="dashboard.php">📊 Dashboard</a></li>
            <li><a href="products.php">📦 Products</a></li>
            <li><a href="add_product.php">➕ Add Product</a></li>
            <li><a href="categories.php">📂 Categories</a></li>
            <li><a href="materials.php" class="active">🔧 Materials</a></li>
            <li><a href="enquiries.php">💬 Enquiries</a></li>
            <li><a href="settings.php">⚙️ Settings</a></li>
            <li><a href="../products.php" target="_blank">🌐 View Site</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Edit Material</h1>
            <div>
                <a href="materials.php" class="btn btn-secondary">Back to Materials</a>
            </div>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="content-grid">
            <!-- Material Edit Form -->
            <div class="content-section">
                <h2 class="section-title">Update Material Details</h2>
                
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    
                    <div class="form-group">
                        <label for="material_id">Material ID</label>
                        <input type="text" 
                               id="material_id" 
                               class="form-control" 
                               value="<?php echo $material['id']; ?>" 
                               disabled>
                    </div>
                    
                    <div class="form-group">
                        <label for="material_name">Material Name *</label>
                        <input type="text" 
                               id="material_name" 
                               name="material_name" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($material['material_name']); ?>" 
                               required 
                               placeholder="Enter material name">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" 
                                  name="description" 
                                  class="form-control" 
                                  rows="5" 
                                  placeholder="Enter material description"><?php echo htmlspecialchars($material['description']); ?></textarea>
                    </div>
                    
                    <div class="btn-container">
                        <button type="submit" class="btn btn-primary">Update Material</button>
                        <a href="materials.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
            
            <!-- Material Info Sidebar -->
            <div class="content-section">
                <h2 class="section-title">Material Info</h2>
                
                <div class="info-box">
                    <p><strong>Material ID:</strong> <?php echo $material['id']; ?></p>
                    <p><strong>Created:</strong> <?php echo date('M j, Y - h:i A', strtotime($material['created_at'])); ?></p>
                </div>
                
                <div style="margin-top: 20px; padding: 15px; background-color: #e8f5e8; border-radius: 8px; border-left: 4px solid #48bb78;">
                    <p style="margin-bottom: 8px; color: #388e3c;"><strong>Products Using This Material:</strong></p>
                    <div class="product-badge"><?php echo $product_count; ?></div>
                </div>
                
                <div style="margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 8px;">
                    <h4 style="margin-bottom: 10px; color: #667eea; font-size: 14px;">Tips:</h4>
                    <ul style="margin-left: 20px; color: #666; line-height: 1.6; font-size: 13px;">
                        <li>Update material details as needed</li>
                        <li>Material ID cannot be changed</li>
                        <li>All fields marked with * are required</li>
                        <li>Changes apply to all linked products</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const materialName = document.getElementById('material_name').value.trim();
            
            if (materialName.length < 2) {
                e.preventDefault();
                alert('Material name must be at least 2 characters long.');
                return;
            }
            
            if (materialName.length > 100) {
                e.preventDefault();
                alert('Material name must be less than 100 characters.');
                return;
            }
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>