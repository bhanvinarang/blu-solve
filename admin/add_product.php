<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

session_start();

$host = 'localhost';
$username = 'blusolv_db';
$password = 'blusolv_db';
$database = 'blusolv_db';

try {
    $conn = new mysqli($host, $username, $password, $database);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $product_name = isset($_POST['product_name']) ? trim($_POST['product_name']) : '';
        $product_code = isset($_POST['product_code']) ? trim($_POST['product_code']) : '';
        $product_color = isset($_POST['product_color']) ? trim($_POST['product_color']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
        $material_id = isset($_POST['material_id']) ? intval($_POST['material_id']) : 0;
        $status = isset($_POST['status']) ? $_POST['status'] : 'active';
        $meta_title = isset($_POST['meta_title']) ? trim($_POST['meta_title']) : '';
        $meta_description = isset($_POST['meta_description']) ? trim($_POST['meta_description']) : '';
        $specifications = isset($_POST['specifications']) ? trim($_POST['specifications']) : '';
        
        if (empty($product_name)) {
            throw new Exception("Product name is required!");
        }
        if ($category_id <= 0) {
            throw new Exception("Please select a valid category!");
        }
        
        // Generate slug
        $product_slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $product_name)));
        $product_slug = preg_replace('/-+/', '-', $product_slug);
        $product_slug = trim($product_slug, '-');
        
        // Make slug unique
        $original_slug = $product_slug;
        $counter = 1;
        $check_slug = $conn->prepare("SELECT id FROM products WHERE product_slug = ?");
        $check_slug->bind_param("s", $product_slug);
        $check_slug->execute();
        $check_slug->store_result();
        
        while ($check_slug->num_rows > 0) {
            $product_slug = $original_slug . '-' . $counter;
            $counter++;
            $check_slug->bind_param("s", $product_slug);
            $check_slug->execute();
            $check_slug->store_result();
        }
        $check_slug->close();
        
        // Handle image uploads - Initialize with empty strings (NOT NULL)
        $product_image = '';
        $gallery_images = '';
        
        if (isset($_FILES['images']) && is_array($_FILES['images']['name']) && !empty($_FILES['images']['name'][0])) {
            $upload_dir = '../uploads/products/';
            
            // Create directory if not exists
            if (!is_dir($upload_dir)) {
                if (!mkdir($upload_dir, 0777, true)) {
                    throw new Exception("Failed to create upload directory");
                }
            }
            
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $max_files = 5;
            $max_size = 5 * 1024 * 1024; // 5MB
            
            $uploaded_images = [];
            $file_count = count($_FILES['images']['name']);
            
            // Process each file
            for ($i = 0; $i < min($file_count, $max_files); $i++) {
                // Check if file exists and no error
                if (isset($_FILES['images']['error'][$i]) && $_FILES['images']['error'][$i] === 0) {
                    $file_name = $_FILES['images']['name'][$i];
                    $file_tmp = $_FILES['images']['tmp_name'][$i];
                    $file_size = $_FILES['images']['size'][$i];
                    
                    // Check file size
                    if ($file_size > $max_size) {
                        throw new Exception("File $file_name is too large. Maximum 5MB allowed.");
                    }
                    
                    // Check file extension
                    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    
                    if (in_array($file_extension, $allowed_extensions)) {
                        // Verify it's actually an image
                        $check = @getimagesize($file_tmp);
                        if ($check !== false) {
                            // Generate unique filename
                            $new_filename = time() . '_' . uniqid() . '_' . $i . '.' . $file_extension;
                            $target_file = $upload_dir . $new_filename;
                            
                            // Move uploaded file
                            if (move_uploaded_file($file_tmp, $target_file)) {
                                $uploaded_images[] = 'uploads/products/' . $new_filename;
                            } else {
                                error_log("Failed to move file: " . $file_tmp . " to " . $target_file);
                            }
                        } else {
                            error_log("Invalid image file: " . $file_name);
                        }
                    } else {
                        error_log("Invalid file extension: " . $file_extension);
                    }
                }
            }
            
            // Set image paths if files were uploaded successfully
            if (!empty($uploaded_images)) {
                $product_image = trim($uploaded_images[0]);  // First image as product_image
                $gallery_images = implode(',', $uploaded_images);  // All images in gallery
                
                // Debug log
                error_log("=== IMAGE UPLOAD SUCCESS ===");
                error_log("Product Image: [" . $product_image . "]");
                error_log("Gallery Images: [" . $gallery_images . "]");
                error_log("Total uploaded: " . count($uploaded_images));
                error_log("First image from array: [" . $uploaded_images[0] . "]");
            } else {
                error_log("=== NO IMAGES UPLOADED ===");
            }
        }
        
        // CRITICAL: Double-check product_image is set correctly
        if (empty($product_image) && !empty($gallery_images)) {
            $temp_array = explode(',', $gallery_images);
            $product_image = trim($temp_array[0]);
            error_log("=== FALLBACK: product_image set from gallery ===");
            error_log("New product_image: [" . $product_image . "]");
        }
        
        // Debug values BEFORE SQL
        error_log("=== DEBUG VALUES ===");
        error_log("product_name: " . $product_name);
        error_log("product_code: " . $product_code);
        error_log("product_color: " . $product_color);
        error_log("category_id: " . $category_id);
        error_log("material_id: " . $material_id);
        error_log("product_image: '" . $product_image . "' (Length: " . strlen($product_image) . ")");
        error_log("gallery_images: '" . $gallery_images . "' (Length: " . strlen($gallery_images) . ")");
        
        // If product_image is empty but gallery has images, use first from gallery
        if (empty($product_image) && !empty($gallery_images)) {
            $gallery_array = explode(',', $gallery_images);
            $product_image = $gallery_array[0];
            error_log("FIXED: product_image set from gallery: " . $product_image);
        }
        
        // Use direct SQL without prepared statement for debugging
        $insert_sql = "INSERT INTO products (
            product_name, 
            product_code, 
            product_color, 
            product_slug, 
            description, 
            category_id, 
            material_id, 
            product_image, 
            gallery_images, 
            status, 
            meta_title, 
            meta_description, 
            specifications, 
            created_at
        ) VALUES (
            '" . $conn->real_escape_string($product_name) . "',
            '" . $conn->real_escape_string($product_code) . "',
            '" . $conn->real_escape_string($product_color) . "',
            '" . $conn->real_escape_string($product_slug) . "',
            '" . $conn->real_escape_string($description) . "',
            " . intval($category_id) . ",
            " . intval($material_id) . ",
            '" . $conn->real_escape_string($product_image) . "',
            '" . $conn->real_escape_string($gallery_images) . "',
            '" . $conn->real_escape_string($status) . "',
            '" . $conn->real_escape_string($meta_title) . "',
            '" . $conn->real_escape_string($meta_description) . "',
            '" . $conn->real_escape_string($specifications) . "',
            NOW()
        )";
        
        error_log("=== FINAL SQL ===");
        error_log($insert_sql);
        
        if (!$conn->query($insert_sql)) {
            throw new Exception("Insert failed: " . $conn->error);
        }
        
        $inserted_id = $conn->insert_id;
        error_log("Product inserted with ID: " . $inserted_id);
        $success_message = "Product added successfully!";
        
        header("Location: products.php?success=" . urlencode($success_message));
        exit();
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        error_log("ERROR: " . $error_message);
    }
}

// Get categories and materials
try {
    $categories = $conn->query("SELECT * FROM categories ORDER BY category_name");
    if (!$categories) {
        throw new Exception("Failed to load categories: " . $conn->error);
    }
    
    $materials = $conn->query("SELECT * FROM materials ORDER BY material_name");
    if (!$materials) {
        throw new Exception("Failed to load materials: " . $conn->error);
    }
} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin Panel</title>
    
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
        
        .content-section { 
            background: white; 
            padding: 40px; 
            border-radius: 0; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
            border: 2px solid var(--soft-taupe);
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
        
        .file-upload { 
            position: relative; 
            cursor: pointer; 
            background: linear-gradient(135deg, var(--cream-beige) 0%, var(--ivory) 100%); 
            border: 2px dashed var(--soft-taupe); 
            border-radius: 0; 
            padding: 40px 20px; 
            text-align: center; 
            width: 100%; 
            min-height: 150px;
            transition: all 0.3s ease;
        }
        
        .file-upload:hover { 
            background: var(--cream-beige); 
            border-color: var(--champagne-gold); 
        }
        
        .file-upload input[type=file] { 
            position: absolute; 
            left: -9999px; 
        }
        
        .upload-icon { 
            font-size: 48px; 
            margin-bottom: 15px; 
        }
        
        .preview-container { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); 
            gap: 20px; 
            margin-top: 25px; 
        }
        
        .preview-item { 
            position: relative; 
            border-radius: 0; 
            overflow: hidden; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
            border: 2px solid var(--soft-taupe);
        }
        
        .preview-image { 
            width: 100%; 
            height: 150px; 
            object-fit: cover; 
            display: block; 
        }
        
        .primary-badge { 
            position: absolute; 
            top: 10px; 
            left: 10px; 
            background: var(--champagne-gold); 
            color: var(--deep-navy); 
            padding: 5px 12px; 
            border-radius: 0; 
            font-size: 0.75rem; 
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        .image-counter { 
            background: var(--deep-navy); 
            color: var(--cream-beige); 
            padding: 10px 20px; 
            border-radius: 0; 
            font-size: 0.85rem; 
            font-weight: 500; 
            margin-top: 15px; 
            display: inline-block;
            letter-spacing: 1px;
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
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Blusolv Admin</h2>
        <ul>
            <li><a href="dashboard.php">📊 Dashboard</a></li>
            <li><a href="products.php">📦 Products</a></li>
            <li><a href="add_product.php" class="active">➕ Add Product</a></li>
            <li><a href="categories.php">📂 Categories</a></li>
            <li><a href="materials.php">🔧 Materials</a></li>
            <li><a href="enquiries.php">💬 Enquiries</a></li>
            <li><a href="settings.php">⚙️ Settings</a></li>
            <li><a href="../products.php" target="_blank">🌐 View Site</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Add New Product</h1>
            <div><a href="products.php" class="btn btn-secondary">Back to Products</a></div>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <div class="content-section">
            <form method="POST" enctype="multipart/form-data" id="productForm">
                <div class="form-group">
                    <label for="product_name">Product Name *</label>
                    <input type="text" id="product_name" name="product_name" class="form-control" required placeholder="Enter product name">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="product_code">Product Code</label>
                        <input type="text" id="product_code" name="product_code" class="form-control" placeholder="e.g., TB-LTH-001">
                    </div>
                    
                    <div class="form-group">
                        <label for="product_color">Product Color</label>
                        <input type="text" id="product_color" name="product_color" class="form-control" placeholder="e.g., Black, Brown, Navy Blue">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4" placeholder="Enter product description"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="category_id">Category *</label>
                        <select id="category_id" name="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            <?php while ($category = $categories->fetch_assoc()): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="material_id">Material</label>
                        <select id="material_id" name="material_id" class="form-control">
                            <option value="">Select Material</option>
                            <?php while ($material = $materials->fetch_assoc()): ?>
                                <option value="<?php echo $material['id']; ?>">
                                    <?php echo htmlspecialchars($material['material_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="status">Status *</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="specifications">Specifications</label>
                    <textarea id="specifications" name="specifications" class="form-control" rows="3" placeholder="Enter product specifications"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="meta_title">Meta Title (SEO)</label>
                    <input type="text" id="meta_title" name="meta_title" class="form-control" placeholder="Enter meta title">
                </div>
                
                <div class="form-group">
                    <label for="meta_description">Meta Description (SEO)</label>
                    <textarea id="meta_description" name="meta_description" class="form-control" rows="3" placeholder="Enter meta description"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="images">Product Images (Max 5) - First image will be the main product image</label>
                    <div class="file-upload" id="fileUpload" onclick="document.getElementById('images').click();">
                        <div class="upload-icon">📸</div>
                        <div style="font-size: 16px; font-weight: 600; margin-bottom: 8px;">Click to upload images or drag and drop</div>
                        <div style="font-size: 14px; color: #666;">JPG, JPEG, PNG or GIF (Max 5MB each)</div>
                        <input type="file" id="images" name="images[]" multiple accept="image/*">
                    </div>
                    <div id="imagePreview" class="preview-container"></div>
                    <div id="imageCounter"></div>
                </div>
                
                <div style="margin-top: 40px;">
                    <button type="submit" class="btn btn-primary">Add Product</button>
                    <a href="products.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        let selectedFiles = [];
        const maxFiles = 5;
        
        document.getElementById('images').addEventListener('change', function(e) {
            selectedFiles = Array.from(e.target.files).slice(0, maxFiles);
            updatePreview();
        });
        
        function updatePreview() {
            const preview = document.getElementById('imagePreview');
            const counter = document.getElementById('imageCounter');
            preview.innerHTML = '';
            
            selectedFiles.forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'preview-item';
                        div.innerHTML = `<img src="${e.target.result}" class="preview-image">
                            ${index === 0 ? '<div class="primary-badge">MAIN IMAGE</div>' : ''}`;
                        preview.appendChild(div);
                    }
                    reader.readAsDataURL(file);
                }
            });
            
            if (selectedFiles.length > 0) {
                counter.innerHTML = `<span class="image-counter">${selectedFiles.length} of ${maxFiles} images selected | First image = Product Image</span>`;
            }
        }
        
        document.getElementById('productForm').addEventListener('submit', function(e) {
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = '⏳ Adding Product...';
            btn.disabled = true;
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>