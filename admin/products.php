<?php
// admin/products.php
session_start();

$host = 'localhost';
$username = 'blusolv_db';
$password = 'blusolv_db';
$database = 'blusolv_db';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle product deletion
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    
    if ($product_id > 0) {
        // Permanently delete the product
        $delete_sql = "DELETE FROM products WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        
        if (!$stmt) {
            $error_message = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("i", $product_id);
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $success_message = "Product deleted successfully!";
                } else {
                    $error_message = "Product not found!";
                }
            } else {
                $error_message = "Error deleting product: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $error_message = "Invalid product ID!";
    }
    
    // Refresh the page after 1 second
    header("Refresh: 1; url=products.php");
}

// Get all products (excluding deleted ones)
$products_sql = "SELECT p.*, c.category_name, m.material_name 
                 FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.id 
                 LEFT JOIN materials m ON p.material_id = m.id 
                 WHERE p.status != 'deleted'
                 ORDER BY p.created_at DESC";
$products_result = $conn->query($products_sql);

if (!$products_result) {
    die("Query error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Admin Panel</title>
    
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
        
        .btn-success { 
            background-color: transparent; 
            color: var(--deep-navy); 
            border-color: var(--champagne-gold);
        }
        
        .btn-success:hover { 
            background-color: var(--champagne-gold);
            color: var(--deep-navy);
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
        
        .status-badge {
            padding: 6px 15px;
            border-radius: 0;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .status-active {
            background-color: var(--champagne-gold);
            color: var(--deep-navy);
        }
        
        .status-inactive {
            background-color: var(--slate-grey);
            color: white;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border: 2px solid var(--soft-taupe);
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
        
        .search-box {
            margin-bottom: 25px;
        }
        
        .search-box input {
            padding: 14px 18px;
            border: 2px solid var(--soft-taupe);
            border-radius: 0;
            width: 100%;
            max-width: 400px;
            font-size: 15px;
            transition: all 0.3s ease;
            font-family: 'Montserrat', sans-serif;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: var(--champagne-gold);
            box-shadow: 0 0 0 3px rgba(212, 165, 116, 0.1);
        }
        
        .product-count {
            color: var(--deep-navy);
            font-weight: 500;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }
        
        .product-count span {
            color: var(--champagne-gold);
            font-weight: 600;
        }
        
        .empty-state {
            text-align: center;
            padding: 80px 40px;
            color: var(--slate-grey);
        }
        
        .empty-state .icon {
            font-size: 64px;
            margin-bottom: 25px;
            color: var(--champagne-gold);
        }
        
        .empty-state h3 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: var(--deep-navy);
            font-family: 'Cormorant Garamond', serif;
            letter-spacing: 2px;
        }
        
        .empty-state p {
            margin-bottom: 30px;
            font-size: 1rem;
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
            .search-box input {
                max-width: 100%;
            }
            .table {
                font-size: 0.8rem;
            }
            .table th,
            .table td {
                padding: 10px 8px;
            }
            .btn {
                padding: 8px 12px;
                font-size: 0.7rem;
                margin-bottom: 5px;
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
            <h1>All Products</h1>
            <div>
                <a href="add_product.php" class="btn btn-primary">Add New Product</a>
                <a href="categories.php" class="btn btn-info">Manage Categories</a>
            </div>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">✓ <?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">✗ <?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <div class="content-section">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search products by name, code, or category..." onkeyup="searchProducts()">
            </div>
            
            <div style="margin-bottom: 20px;">
                <span class="product-count">Total Products: <span><?php echo $products_result->num_rows; ?></span></span>
            </div>
            
            <?php if ($products_result->num_rows > 0): ?>
                <div style="overflow-x: auto;">
                    <table class="table" id="productsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Product Name</th>
                                <th>Code</th>
                                <th>Category</th>
                                <th>Material</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $products_result->data_seek(0);
                            while ($product = $products_result->fetch_assoc()): 
                            ?>
                                <tr>
                                    <td><strong>#<?php echo $product['id']; ?></strong></td>
                                    <td>
                                        <?php if (!empty($product['product_image'])): ?>
                                            <img src="../<?php echo htmlspecialchars($product['product_image']); ?>" 
                                                 alt="Product Image" class="product-image" title="<?php echo htmlspecialchars($product['product_name']); ?>">
                                        <?php else: ?>
                                            <div style="width: 60px; height: 60px; background: var(--cream-beige); border: 2px solid var(--soft-taupe); display: flex; align-items: center; justify-content: center; font-size: 0.7rem; color: var(--slate-grey);">
                                                No Image
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($product['product_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($product['product_code'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($product['material_name'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo htmlspecialchars($product['status']); ?>">
                                            <?php echo ucfirst(htmlspecialchars($product['status'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($product['created_at'])); ?></td>
                                    <td style="white-space: nowrap;">
                                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-info" title="Edit">Edit</a>
                                        <a href="../product-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-success" title="View" target="_blank">View</a>
                                        <a href="products.php?delete=1&id=<?php echo $product['id']; ?>" 
                                           class="btn btn-danger" 
                                           title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone.');">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="icon">📦</div>
                    <h3>No Products Found</h3>
                    <p>Get started by adding your first product to the inventory</p>
                    <a href="add_product.php" class="btn btn-primary">Add Your First Product</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function searchProducts() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("productsTable");
            
            if (!table) return;
            
            tr = table.getElementsByTagName("tr");
            
            for (i = 0; i < tr.length; i++) {
                // Search in product name (column 2), code (column 3), and category (column 4)
                let productName = tr[i].getElementsByTagName("td")[2];
                let productCode = tr[i].getElementsByTagName("td")[3];
                let productCategory = tr[i].getElementsByTagName("td")[4];
                
                if (productName || productCode || productCategory) {
                    let nameValue = productName ? (productName.textContent || productName.innerText) : "";
                    let codeValue = productCode ? (productCode.textContent || productCode.innerText) : "";
                    let categoryValue = productCategory ? (productCategory.textContent || productCategory.innerText) : "";
                    
                    let combinedText = (nameValue + " " + codeValue + " " + categoryValue).toUpperCase();
                    
                    if (combinedText.indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>