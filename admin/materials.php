<?php
// admin/materials.php
session_start();

$host = 'localhost';
$username = 'blusolv_db';
$password = 'blusolv_db';
$database = 'blusolv_db';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle material deletion
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $material_id = intval($_GET['id']);
    
    $check_sql = "SELECT COUNT(*) as count FROM products WHERE material_id = ? AND status != 'deleted'";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $material_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    
    if ($count > 0) {
        $error_message = "Cannot delete material. It has $count products associated with it.";
    } else {
        $delete_sql = "DELETE FROM materials WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $material_id);
        
        if ($stmt->execute()) {
            $success_message = "Material deleted successfully!";
        } else {
            $error_message = "Error deleting material: " . $conn->error;
        }
    }
}

// Handle add material
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $material_name = trim($_POST['material_name']);
    $description = trim($_POST['description']);
    
    if (!empty($material_name)) {
        $check_sql = "SELECT id FROM materials WHERE material_name = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $material_name);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows > 0) {
            $error_message = "Material already exists!";
        } else {
            // Generate slug from material name
            $material_slug = strtolower(str_replace(' ', '-', $material_name));
            
            $insert_sql = "INSERT INTO materials (material_name, material_slug, description, created_at) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("sss", $material_name, $material_slug, $description);
            
            if ($stmt->execute()) {
                $success_message = "Material added successfully!";
            } else {
                $error_message = "Error adding material: " . $conn->error;
            }
        }
    } else {
        $error_message = "Material name is required!";
    }
}

// Get all materials with product count
$materials_sql = "SELECT m.*, COUNT(p.id) as product_count 
                  FROM materials m 
                  LEFT JOIN products p ON m.id = p.material_id AND p.status != 'deleted'
                  GROUP BY m.id 
                  ORDER BY m.created_at DESC";
$materials_result = $conn->query($materials_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materials - Admin Panel</title>
    
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
            <li><a href="categories.php">📂 Categories</a></li>
            <li><a href="materials.php" class="active">🔧 Materials</a></li>
            <li><a href="enquiries.php">💬 Enquiries</a></li>
            <li><a href="settings.php">⚙️ Settings</a></li>
            <li><a href="../products.php" target="_blank">🌐 View Site</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Manage Materials</h1>
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
            <!-- Materials List -->
            <div class="content-section">
                <h2 class="section-title">All Materials</h2>
                
                <?php if ($materials_result->num_rows > 0): ?>
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Material Name</th>
                                    <th>Description</th>
                                    <th>Products</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($material = $materials_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong>#<?php echo $material['id']; ?></strong></td>
                                        <td><strong><?php echo htmlspecialchars($material['material_name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars(substr($material['description'], 0, 40)) . (strlen($material['description']) > 40 ? '...' : ''); ?></td>
                                        <td>
                                            <span class="product-count"><?php echo $material['product_count']; ?> Products</span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($material['created_at'])); ?></td>
                                        <td style="white-space: nowrap;">
                                            <a href="edit_material.php?id=<?php echo $material['id']; ?>" class="btn btn-info">Edit</a>
                                            <?php if ($material['product_count'] == 0): ?>
                                                <a href="materials.php?delete=1&id=<?php echo $material['id']; ?>" 
                                                   class="btn btn-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this material?')">Delete</a>
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
                        <p>No materials found. Add your first material using the form.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Add Material Form -->
            <div class="content-section">
                <h2 class="section-title">Add New Material</h2>
                
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="form-group">
                        <label for="material_name">Material Name *</label>
                        <input type="text" 
                               id="material_name" 
                               name="material_name" 
                               class="form-control" 
                               required 
                               placeholder="Enter material name">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" 
                                  name="description" 
                                  class="form-control" 
                                  rows="4" 
                                  placeholder="Enter material description"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Add Material</button>
                </form>
                
                <div class="tips-box">
                    <h4>Quick Tips:</h4>
                    <ul>
                        <li>Choose clear, descriptive material names</li>
                        <li>Add detailed information about each material</li>
                        <li>You can only delete materials with no products</li>
                        <li>Materials help categorize product types</li>
                        <li>Common examples: Leather, Canvas, Nylon, Jute</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('material_name').focus();
        });
        
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
            
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = 'Adding Material...';
            btn.disabled = true;
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>