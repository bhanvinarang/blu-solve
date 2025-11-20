<?php
// admin/categories.php
session_start();

// Database connection
$host = 'localhost';
$username = 'blusolv_db';
$password = 'blusolv_db';
$database = 'blusolv_db';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle category deletion
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $category_id = intval($_GET['id']);
    
    // Check if category has products
    $check_sql = "SELECT COUNT(*) as count FROM products WHERE category_id = ? AND status != 'deleted'";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $category_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    
    if ($count > 0) {
        $error_message = "Cannot delete category. It has $count products associated with it.";
    } else {
        $delete_sql = "DELETE FROM categories WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $category_id);
        
        if ($stmt->execute()) {
            $success_message = "Category deleted successfully!";
        } else {
            $error_message = "Error deleting category: " . $conn->error;
        }
    }
}

// Handle add category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $category_name = trim($_POST['category_name']);
    $description = trim($_POST['description']);
    
    if (!empty($category_name)) {
        // Check if category already exists
        $check_sql = "SELECT id FROM categories WHERE category_name = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $category_name);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows > 0) {
            $error_message = "Category already exists!";
        } else {
            // Generate slug from category name
            $category_slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $category_name), '-'));
            
            // Ensure slug is unique by adding a number if needed
            $slug_check_sql = "SELECT id FROM categories WHERE category_slug = ?";
            $slug_check_stmt = $conn->prepare($slug_check_sql);
            $slug_check_stmt->bind_param("s", $category_slug);
            $slug_check_stmt->execute();
            
            $counter = 1;
            $original_slug = $category_slug;
            while ($slug_check_stmt->get_result()->num_rows > 0) {
                $category_slug = $original_slug . '-' . $counter;
                $slug_check_stmt->execute();
                $counter++;
            }
            
            $insert_sql = "INSERT INTO categories (category_name, category_slug, description, created_at) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("sss", $category_name, $category_slug, $description);
            
            if ($stmt->execute()) {
                $success_message = "Category added successfully!";
            } else {
                $error_message = "Error adding category: " . $conn->error;
            }
        }
    } else {
        $error_message = "Category name is required!";
    }
}

// Get all categories with product count
$categories_sql = "SELECT c.*, COUNT(p.id) as product_count 
                   FROM categories c 
                   LEFT JOIN products p ON c.id = p.category_id AND p.status != 'deleted'
                   GROUP BY c.id 
                   ORDER BY c.created_at DESC";
$categories_result = $conn->query($categories_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - Admin Panel</title>
    
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
            grid-template-columns: 1fr 450px;
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
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        
        .table th,
        .table td {
            padding: 15px 12px;
            text-align: left;
            border-bottom: 1px solid var(--soft-taupe);
            font-size: 0.9rem;
        }
        
        .table th {
            background: linear-gradient(135deg, var(--cream-beige) 0%, var(--ivory) 100%);
            font-weight: 500;
            color: var(--deep-navy);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.8rem;
        }
        
        .table tbody tr {
            transition: all 0.3s ease;
        }
        
        .table tbody tr:hover {
            background: var(--cream-beige);
            transform: scale(1.01);
        }
        
        .btn { 
            padding: 10px 20px; 
            border: 2px solid transparent; 
            border-radius: 0; 
            cursor: pointer; 
            text-decoration: none; 
            display: inline-block; 
            font-size: 0.75rem; 
            font-weight: 500; 
            transition: all 0.3s ease; 
            margin-right: 8px;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-family: 'Montserrat', sans-serif;
        }
        
        .btn-primary { 
            background-color: var(--deep-navy); 
            color: var(--cream-beige); 
            border-color: var(--deep-navy);
        }
        
        .btn-primary:hover { 
            background-color: var(--champagne-gold);
            color: var(--deep-navy);
            border-color: var(--champagne-gold);
        }
        
        .btn-danger { 
            background-color: transparent; 
            color: #dc3545; 
            border-color: #dc3545;
        }
        
        .btn-danger:hover { 
            background-color: #dc3545;
            color: white;
        }
        
        .btn-info { 
            background-color: var(--champagne-gold); 
            color: var(--deep-navy); 
            border-color: var(--champagne-gold);
        }
        
        .btn-info:hover { 
            background-color: var(--deep-navy);
            color: var(--cream-beige);
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
        
        .product-count {
            background-color: var(--champagne-gold);
            color: var(--deep-navy);
            padding: 5px 12px;
            border-radius: 0;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 1px;
        }
        
        .tips-box {
            padding: 25px;
            background: linear-gradient(135deg, var(--cream-beige) 0%, var(--ivory) 100%);
            border-radius: 0;
            margin-top: 30px;
            border-left: 3px solid var(--champagne-gold);
        }
        
        .tips-box h4 {
            margin-bottom: 15px;
            color: var(--deep-navy);
            font-size: 0.9rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
        }
        
        .tips-box ul {
            margin-left: 20px;
            color: var(--charcoal-grey);
            line-height: 1.8;
            font-size: 0.85rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 40px;
            color: var(--slate-grey);
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
            <li><a href="products.php">📦 Products</a></li>
            <li><a href="add_product.php">➕ Add Product</a></li>
            <li><a href="categories.php" class="active">📂 Categories</a></li>
            <li><a href="materials.php">🔧 Materials</a></li>
            <li><a href="enquiries.php">💬 Enquiries</a></li>
            <li><a href="settings.php">⚙️ Settings</a></li>
            <li><a href="../products.php" target="_blank">🌐 View Site</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Manage Categories</h1>
            <div>
                <a href="products.php" class="btn btn-info">Back to Products</a>
            </div>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">✓ <?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">✗ <?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="content-grid">
            <!-- Categories List -->
            <div class="content-section">
                <h2 class="section-title">All Categories</h2>
                
                <?php if ($categories_result->num_rows > 0): ?>
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Category Name</th>
                                    <th>Description</th>
                                    <th>Products</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($category = $categories_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong>#<?php echo $category['id']; ?></strong></td>
                                        <td><strong><?php echo htmlspecialchars($category['category_name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars(substr($category['description'], 0, 50)) . (strlen($category['description']) > 50 ? '...' : ''); ?></td>
                                        <td>
                                            <span class="product-count"><?php echo $category['product_count']; ?> Products</span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($category['created_at'])); ?></td>
                                        <td style="white-space: nowrap;">
                                            <a href="edit_category.php?id=<?php echo $category['id']; ?>" class="btn btn-info">Edit</a>
                                            <?php if ($category['product_count'] == 0): ?>
                                                <a href="categories.php?delete=1&id=<?php echo $category['id']; ?>" 
                                                   class="btn btn-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this category?')">Delete</a>
                                            <?php else: ?>
                                                <span style="font-size: 0.7rem; color: var(--slate-grey);">Has products</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No categories found. Add your first category using the form.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Add Category Form -->
            <div class="content-section">
                <h2 class="section-title">Add New Category</h2>
                
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="form-group">
                        <label for="category_name">Category Name *</label>
                        <input type="text" 
                               id="category_name" 
                               name="category_name" 
                               class="form-control" 
                               required 
                               placeholder="Enter category name">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" 
                                  name="description" 
                                  class="form-control" 
                                  rows="4" 
                                  placeholder="Enter category description"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Add Category</button>
                </form>
                
                <div class="tips-box">
                    <h4>Quick Tips:</h4>
                    <ul>
                        <li>Choose clear, descriptive names for categories</li>
                        <li>Categories help organize your products</li>
                        <li>You can only delete categories with no products</li>
                        <li>Categories are required when adding products</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-focus on category name input
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('category_name').focus();
        });
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const categoryName = document.getElementById('category_name').value.trim();
            
            if (categoryName.length < 2) {
                e.preventDefault();
                alert('Category name must be at least 2 characters long.');
                return;
            }
            
            if (categoryName.length > 100) {
                e.preventDefault();
                alert('Category name must be less than 100 characters.');
                return;
            }
            
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = 'Adding Category...';
            btn.disabled = true;
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>