<?php
// admin/edit_product.php
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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    
    // ✅ DEBUG: Check all POST data
    error_log("POST Data: " . print_r($_POST, true));
    
    $product_name = trim($_POST['product_name']);
    $product_code = trim($_POST['product_code']);
    $product_color = trim($_POST['product_color']);
    $description = trim($_POST['description']);
    $category_id = intval($_POST['category_id']);
    $material_id = intval($_POST['material_id']);
    
    // ✅ FIX: Get status value - ENUM is case-sensitive
    $status = 'active'; // Default
    
    if (isset($_POST['status']) && !empty($_POST['status'])) {
        // IMPORTANT: Keep lowercase for ENUM match
        $status = strtolower(trim($_POST['status']));
        error_log("Status from POST (lowercase): " . $status);
    } else {
        error_log("Status NOT found in POST data!");
    }
    
    // ✅ Validate status - must match ENUM values exactly
    $valid_statuses = ['active', 'inactive', 'draft'];
    if (!in_array($status, $valid_statuses, true)) { // strict comparison
        error_log("Invalid status '$status', defaulting to 'active'");
        $status = 'active';
    }
    
    error_log("Final status to be saved (must match ENUM): " . $status);
    
    $specifications = trim($_POST['specifications']);
    $meta_title = trim($_POST['meta_title']);
    $meta_description = trim($_POST['meta_description']);
    
    if (!empty($product_name) && $category_id > 0) {
        $image_path = $_POST['current_image'];
        
        if (isset($_FILES['product_image']) && $_FILES['product_image']['name']) {
            $target_dir = "../uploads/products/";
            $file_name = basename($_FILES['product_image']['name']);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $new_file_name = uniqid() . "." . $file_ext;
            $target_file = $target_dir . $new_file_name;
            
            $allowed_exts = array("jpg", "jpeg", "png", "gif");
            
            if (in_array($file_ext, $allowed_exts)) {
                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
                    $image_path = "uploads/products/" . $new_file_name;
                } else {
                    $error_message = "Error uploading image.";
                }
            } else {
                $error_message = "Invalid file type. Only JPG, PNG, GIF allowed.";
            }
        }
        
        if (!isset($error_message)) {
            // ✅ Check current status before update
            $check_sql = "SELECT status FROM products WHERE id = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("i", $product_id);
            $check_stmt->execute();
            $old_status_result = $check_stmt->get_result();
            $old_status = $old_status_result->fetch_assoc()['status'];
            error_log("Current status in DB: " . $old_status);
            error_log("New status to update: " . $status);
            
            // ✅ Update query
            $update_sql = "UPDATE products SET 
                product_name = ?, 
                product_code = ?, 
                product_color = ?, 
                description = ?, 
                category_id = ?, 
                material_id = ?, 
                status = ?, 
                product_image = ?, 
                specifications = ?, 
                meta_title = ?, 
                meta_description = ?
                WHERE id = ?";
                
            $stmt = $conn->prepare($update_sql);
            
            if ($stmt) {
                // ✅ Bind parameters
                $stmt->bind_param(
                    "ssssiiissssi", 
                    $product_name, 
                    $product_code, 
                    $product_color, 
                    $description, 
                    $category_id, 
                    $material_id, 
                    $status, 
                    $image_path, 
                    $specifications, 
                    $meta_title, 
                    $meta_description, 
                    $product_id
                );
                
                error_log("Bound status value: " . $status);
                
                if ($stmt->execute()) {
                    error_log("Execute successful, affected rows: " . $stmt->affected_rows);
                    
                    // ✅ Try direct update as fallback
                    $direct_update = $conn->query("UPDATE products SET status = '$status' WHERE id = $product_id");
                    error_log("Direct update result: " . ($direct_update ? 'SUCCESS' : 'FAILED: ' . $conn->error));
                    
                    // ✅ Verify the update worked
                    $verify_sql = "SELECT status FROM products WHERE id = ?";
                    $verify_stmt = $conn->prepare($verify_sql);
                    $verify_stmt->bind_param("i", $product_id);
                    $verify_stmt->execute();
                    $verify_result = $verify_stmt->get_result();
                    $verify_row = $verify_result->fetch_assoc();
                    $updated_status = $verify_row['status'];
                    
                    error_log("Status after update in DB: " . $updated_status);
                    error_log("Expected status: " . $status);
                    error_log("Status match: " . ($updated_status === $status ? 'YES' : 'NO'));
                    
                    if ($updated_status === $status) {
                        $success_message = "✅ Product updated successfully! Status changed from '" . ucfirst($old_status) . "' to '" . ucfirst($status) . "'";
                    } else {
                        $error_message = "⚠️ Product updated but status NOT changed. DB shows: '" . $updated_status . "' but expected: '" . $status . "'";
                    }
                    
                    // Refresh product data
                    $fetch_sql = "SELECT * FROM products WHERE id = ?";
                    $fetch_stmt = $conn->prepare($fetch_sql);
                    $fetch_stmt->bind_param("i", $product_id);
                    $fetch_stmt->execute();
                    $result = $fetch_stmt->get_result();
                    $product = $result->fetch_assoc();
                } else {
                    $error_message = "Error updating product: " . $stmt->error;
                    error_log("SQL Error: " . $stmt->error);
                }
                $stmt->close();
            } else {
                $error_message = "Error preparing statement: " . $conn->error;
            }
        }
    } else {
        $error_message = "Please fill in all required fields.";
    }
}

// Fetch product data
$fetch_sql = "SELECT * FROM products WHERE id = ?";
$fetch_stmt = $conn->prepare($fetch_sql);
$fetch_stmt->bind_param("i", $product_id);
$fetch_stmt->execute();
$result = $fetch_stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: products.php');
    exit;
}

$product = $result->fetch_assoc();

// Fetch categories
$categories_sql = "SELECT id, category_name FROM categories ORDER BY category_name";
$categories_result = $conn->query($categories_sql);

// Fetch materials
$materials_sql = "SELECT id, material_name FROM materials ORDER BY material_name";
$materials_result = $conn->query($materials_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Admin Panel</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --deep-navy: #1a2332;
            --charcoal-grey: #3d3d3d;
            --champagne-gold: #d4a574;
            --cream-beige: #f4efe7;
            --soft-taupe: #d4c5b9;
            --ivory: #fffff0;
            --slate-grey: #6b7280;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Montserrat', sans-serif; 
            background-color: var(--ivory); 
            color: var(--charcoal-grey); 
        }
        
        .sidebar { 
            width: 250px; 
            height: 100vh; 
            background: linear-gradient(135deg, var(--deep-navy) 0%, var(--charcoal-grey) 100%); 
            color: white; 
            position: fixed; 
            left: 0; 
            top: 0; 
            padding: 20px 0; 
            box-shadow: 2px 0 10px rgba(0,0,0,0.3); 
            overflow-y: auto;
        }
        
        .sidebar h2 { 
            text-align: center; 
            margin-bottom: 30px; 
            padding: 0 20px; 
            font-family: 'Cormorant Garamond', serif;
            letter-spacing: 2px;
            font-weight: 400;
            font-size: 1.8rem;
        }
        
        .sidebar ul { list-style: none; }
        .sidebar ul li { margin-bottom: 5px; }
        
        .sidebar ul li a { 
            display: block; 
            padding: 15px 20px; 
            color: var(--cream-beige); 
            text-decoration: none; 
            transition: all 0.3s ease; 
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }
        
        .sidebar ul li a:hover, 
        .sidebar ul li a.active { 
            background-color: rgba(212, 165, 116, 0.2); 
            border-left: 4px solid var(--champagne-gold); 
        }
        
        .main-content { 
            margin-left: 250px; 
            padding: 20px; 
        }
        
        .header { 
            background: linear-gradient(135deg, var(--cream-beige) 0%, var(--ivory) 100%); 
            padding: 30px; 
            border-radius: 0; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
            margin-bottom: 30px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border: 2px solid var(--soft-taupe);
        }
        
        .header h1 { 
            color: var(--deep-navy); 
            font-family: 'Cormorant Garamond', serif;
            font-weight: 400;
            letter-spacing: 3px;
            font-size: 2.5rem;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 25px;
        }
        
        .content-section { 
            background: white; 
            padding: 40px; 
            border-radius: 0; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
            border: 2px solid var(--soft-taupe);
        }
        
        .section-title {
            font-size: 1.5rem;
            margin-bottom: 30px;
            color: var(--deep-navy);
            border-bottom: 2px solid var(--champagne-gold);
            padding-bottom: 15px;
            font-family: 'Cormorant Garamond', serif;
            letter-spacing: 2px;
            font-weight: 400;
        }
        
        .form-group { 
            margin-bottom: 25px; 
        }
        
        .form-group label { 
            display: block; 
            margin-bottom: 10px; 
            font-weight: 500; 
            color: var(--deep-navy); 
            font-size: 0.85rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        .form-control { 
            width: 100%; 
            padding: 14px 18px; 
            border: 2px solid var(--soft-taupe); 
            border-radius: 0; 
            font-size: 15px;
            font-family: 'Montserrat', sans-serif;
            background: white;
            color: var(--charcoal-grey);
            transition: all 0.3s ease;
        }
        
        .form-control:focus { 
            outline: none; 
            border-color: var(--champagne-gold); 
            box-shadow: 0 0 0 3px rgba(212, 165, 116, 0.1); 
        }
        
        textarea.form-control { 
            resize: vertical; 
            min-height: 120px; 
        }
        
        .alert { 
            padding: 18px 25px; 
            margin-bottom: 25px; 
            border-radius: 0; 
            border: 2px solid transparent;
            font-size: 0.95rem;
        }
        
        .alert-success { 
            background-color: #d4edda; 
            border-color: #28a745; 
            color: #155724; 
        }
        
        .alert-danger { 
            background-color: #f8d7da; 
            border-color: #dc3545; 
            color: #721c24; 
        }
        
        .btn { 
            padding: 16px 40px; 
            border: 2px solid transparent; 
            border-radius: 0; 
            cursor: pointer; 
            text-decoration: none; 
            display: inline-block; 
            font-size: 0.85rem; 
            font-weight: 500; 
            transition: all 0.4s ease; 
            margin-right: 15px;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-family: 'Montserrat', sans-serif;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary { 
            background-color: var(--deep-navy); 
            color: var(--cream-beige); 
            border-color: var(--deep-navy);
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--champagne-gold);
            transition: left 0.4s ease;
            z-index: 0;
        }
        
        .btn-primary:hover::before {
            left: 0;
        }
        
        .btn-primary:hover { 
            color: var(--deep-navy);
            border-color: var(--champagne-gold);
            transform: translateY(-2px); 
        }
        
        .btn-secondary { 
            background-color: transparent; 
            color: var(--charcoal-grey); 
            border-color: var(--soft-taupe);
        }
        
        .btn-secondary:hover { 
            background-color: var(--soft-taupe); 
            border-color: var(--champagne-gold);
        }
        
        .btn-container {
            display: flex;
            gap: 15px;
            margin-top: 40px;
        }
        
        .image-preview {
            width: 100%;
            max-width: 100%;
            border: 2px solid var(--soft-taupe);
            margin-top: 15px;
        }
        
        .image-upload-box {
            border: 2px dashed var(--soft-taupe);
            padding: 25px;
            border-radius: 0;
            text-align: center;
            background: linear-gradient(135deg, var(--cream-beige) 0%, var(--ivory) 100%);
            transition: all 0.3s ease;
        }
        
        .image-upload-box:hover {
            border-color: var(--champagne-gold);
        }
        
        .info-box {
            background: linear-gradient(135deg, var(--cream-beige) 0%, var(--ivory) 100%);
            padding: 25px;
            border-radius: 0;
            margin-top: 25px;
            border-left: 3px solid var(--champagne-gold);
        }
        
        .info-box p {
            margin-bottom: 12px;
            color: var(--charcoal-grey);
            font-size: 0.9rem;
        }
        
        .info-box strong {
            color: var(--deep-navy);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
        }
        
        .status-badge {
            padding: 6px 15px;
            border-radius: 0;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
            margin-top: 8px;
            letter-spacing: 1px;
        }
        
        .status-active {
            background-color: #28a745;
            color: white;
        }
        
        .status-inactive {
            background-color: var(--slate-grey);
            color: white;
        }
        
        .status-draft {
            background-color: #ffc107;
            color: var(--deep-navy);
        }
        
        .form-row { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 25px; 
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
            .form-row { 
                grid-template-columns: 1fr; 
            }
            .header { 
                flex-direction: column; 
                gap: 20px; 
            }
            .content-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Blusolv Admin</h2>
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
            <h1>Edit Product</h1>
            <div>
                <a href="products.php" class="btn btn-secondary">Back to Products</a>
            </div>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="content-grid">
            <div class="content-section">
                <h2 class="section-title">Update Product Details</h2>
                
                <form method="POST" enctype="multipart/form-data" id="productForm">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($product['product_image']); ?>">
                    
                    <div class="form-group">
                        <label for="product_name">Product Name *</label>
                        <input type="text" id="product_name" name="product_name" class="form-control" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="product_code">Product Code</label>
                            <input type="text" id="product_code" name="product_code" class="form-control" value="<?php echo htmlspecialchars($product['product_code'] ?? ''); ?>" placeholder="e.g., TB-LTH-001">
                        </div>
                        
                        <div class="form-group">
                            <label for="product_color">Product Color</label>
                            <input type="text" id="product_color" name="product_color" class="form-control" value="<?php echo htmlspecialchars($product['product_color'] ?? ''); ?>" placeholder="e.g., Black, Brown">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="category_id">Category *</label>
                            <select id="category_id" name="category_id" class="form-control" required>
                                <option value="">Select Category</option>
                                <?php while ($category = $categories_result->fetch_assoc()): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo $product['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['category_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="material_id">Material</label>
                            <select id="material_id" name="material_id" class="form-control">
                                <option value="0">Select Material</option>
                                <?php while ($material = $materials_result->fetch_assoc()): ?>
                                    <option value="<?php echo $material['id']; ?>" <?php echo $product['material_id'] == $material['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($material['material_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- ✅ FIXED STATUS DROPDOWN WITH DEBUG -->
                    <div class="form-group">
                        <label for="status">Status * 
                            <span style="color: #999; font-size: 0.75rem; font-weight: normal; text-transform: none;">
                                (Current: <strong id="currentStatusDisplay"><?php echo ucfirst($product['status']); ?></strong>)
                            </span>
                        </label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="active" <?php echo ($product['status'] === 'active') ? 'selected' : ''; ?>>✅ Active (Visible on site)</option>
                            <option value="inactive" <?php echo ($product['status'] === 'inactive') ? 'selected' : ''; ?>>❌ Inactive (Hidden from site)</option>
                            <option value="draft" <?php echo ($product['status'] === 'draft') ? 'selected' : ''; ?>>📝 Draft (Work in progress)</option>
                        </select>
                        <small style="display: block; margin-top: 8px; color: #666; font-size: 0.8rem;">
                            Database Status: <strong style="color: var(--deep-navy);"><?php echo $product['status']; ?></strong>
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label for="specifications">Specifications</label>
                        <textarea id="specifications" name="specifications" class="form-control" rows="3" placeholder="Enter product specifications"><?php echo htmlspecialchars($product['specifications'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="meta_title">Meta Title (SEO)</label>
                        <input type="text" id="meta_title" name="meta_title" class="form-control" value="<?php echo htmlspecialchars($product['meta_title'] ?? ''); ?>" placeholder="Enter meta title">
                    </div>
                    
                    <div class="form-group">
                        <label for="meta_description">Meta Description (SEO)</label>
                        <textarea id="meta_description" name="meta_description" class="form-control" rows="3" placeholder="Enter meta description"><?php echo htmlspecialchars($product['meta_description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="product_image">Product Image</label>
                        <div class="image-upload-box">
                            <input type="file" id="product_image" name="product_image" accept="image/*" onchange="previewImage(event)">
                            <p style="margin-top: 10px; color: #666; font-size: 14px;">Allowed: JPG, PNG, GIF (Max 5MB)</p>
                        </div>
                    </div>
                    
                    <div class="btn-container">
                        <button type="submit" class="btn btn-primary">Update Product</button>
                        <a href="products.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
            
            <div class="content-section">
                <h2 class="section-title">Product Info</h2>
                
                <div class="info-box">
                    <p><strong>Product ID:</strong> #<?php echo $product['id']; ?></p>
                    <p><strong>Created:</strong> <?php echo date('M j, Y', strtotime($product['created_at'])); ?></p>
                    <p><strong>Status:</strong><br>
                        <span class="status-badge status-<?php echo $product['status']; ?>">
                            <?php echo ucfirst($product['status']); ?>
                        </span>
                    </p>
                </div>
                
                <?php if ($product['product_image']): ?>
                    <div style="margin-top: 25px;">
                        <h4 style="margin-bottom: 15px; color: var(--deep-navy); font-family: 'Cormorant Garamond', serif; letter-spacing: 1px;">Current Image:</h4>
                        <img src="../<?php echo htmlspecialchars($product['product_image']); ?>" alt="Product Image" class="image-preview" id="currentImage">
                    </div>
                <?php endif; ?>
                
                <div style="margin-top: 25px; padding: 20px; background: linear-gradient(135deg, var(--cream-beige) 0%, var(--ivory) 100%); border-radius: 0; border-left: 3px solid var(--champagne-gold);">
                    <h4 style="margin-bottom: 15px; color: var(--deep-navy); font-size: 0.9rem; letter-spacing: 1px; text-transform: uppercase;">Quick Tips:</h4>
                    <ul style="margin-left: 20px; color: var(--charcoal-grey); line-height: 1.8; font-size: 0.85rem;">
                        <li>Update product details as needed</li>
                        <li>Change status to inactive to hide</li>
                        <li>Upload new image to replace current</li>
                        <li>Fields with * are required</li>
                        <li>Product code helps with inventory</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('currentImage');
                    if (preview) {
                        preview.src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        }
        
        // Status change indicator with visual feedback
        document.getElementById('status').addEventListener('change', function() {
            const selectedStatus = this.value;
            const currentDisplay = document.getElementById('currentStatusDisplay');
            
            console.log('Status dropdown changed to:', selectedStatus);
            console.log('Status field name:', this.name);
            console.log('Status field value:', this.value);
            
            // Visual feedback
            this.style.borderColor = '#28a745';
            this.style.backgroundColor = '#f0fff4';
            
            setTimeout(() => {
                this.style.borderColor = '';
                this.style.backgroundColor = '';
            }, 1000);
            
            // Update current display
            if (currentDisplay) {
                currentDisplay.textContent = selectedStatus.charAt(0).toUpperCase() + selectedStatus.slice(1);
                currentDisplay.style.color = selectedStatus === 'active' ? '#28a745' : 
                                             selectedStatus === 'inactive' ? '#dc3545' : '#ffc107';
            }
        });
        
        // Form validation and submission with detailed logging
        document.getElementById('productForm').addEventListener('submit', function(e) {
            const productName = document.getElementById('product_name').value.trim();
            const categoryId = document.getElementById('category_id').value;
            const statusField = document.getElementById('status');
            const status = statusField.value;
            
            // Debug: Log all form data before submit
            console.log('=== FORM SUBMISSION DEBUG ===');
            console.log('Product Name:', productName);
            console.log('Category ID:', categoryId);
            console.log('Status Field Name:', statusField.name);
            console.log('Status Field Value:', status);
            console.log('Status Selected Index:', statusField.selectedIndex);
            console.log('Status Options:', Array.from(statusField.options).map(o => o.value));
            
            // Create FormData to check what's being sent
            const formData = new FormData(this);
            console.log('FormData contents:');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            if (productName.length < 2) {
                e.preventDefault();
                alert('Product name must be at least 2 characters long.');
                return;
            }
            
            if (!categoryId) {
                e.preventDefault();
                alert('Please select a category.');
                return;
            }
            
            if (!status) {
                e.preventDefault();
                alert('Please select a status. Current value: ' + status);
                return;
            }
            
            // Visual confirmation
            if (confirm('Update product with status: ' + status.toUpperCase() + '?')) {
                const btn = this.querySelector('button[type="submit"]');
                btn.innerHTML = '⏳ Updating Status to ' + status.toUpperCase() + '...';
                btn.disabled = true;
            } else {
                e.preventDefault();
            }
        });
        
        // Check status field on page load
        window.addEventListener('load', function() {
            const statusField = document.getElementById('status');
            console.log('Page loaded - Status field value:', statusField.value);
            console.log('Status field name attribute:', statusField.getAttribute('name'));
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>