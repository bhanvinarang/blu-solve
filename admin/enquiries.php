<?php
// admin/enquiries.php
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

// Debug: Check if tables exist and show structure
if (isset($_GET['debug'])) {
    echo "<h3>Table Structure Debug:</h3>";
    
    // Check if enquiries table exists
    $result = $conn->query("SHOW TABLES LIKE 'enquiries'");
    if ($result->num_rows == 0) {
        echo "<p>Error: 'enquiries' table does not exist!</p>";
        echo "<p>Creating enquiries table...</p>";
        
        $create_enquiries = "CREATE TABLE enquiries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_name VARCHAR(255) NOT NULL,
            customer_email VARCHAR(255) NOT NULL,
            customer_phone VARCHAR(20),
            company_name VARCHAR(255),
            product_id INT,
            expected_quantity VARCHAR(50),
            nature_of_requirement_id INT,
            message TEXT,
            status ENUM('new', 'processing', 'quoted', 'completed') DEFAULT 'new',
            enquiry_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        
        if ($conn->query($create_enquiries)) {
            echo "<p>✅ Enquiries table created successfully!</p>";
        } else {
            echo "<p>❌ Error creating enquiries table: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>✅ Enquiries table exists</p>";
        
        // Show table structure
        $result = $conn->query("DESCRIBE enquiries");
        echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td><td>{$row['Default']}</td></tr>";
        }
        echo "</table>";
    }
    
    // Check products table
    $result = $conn->query("SHOW TABLES LIKE 'products'");
    if ($result->num_rows == 0) {
        echo "<p>Warning: 'products' table does not exist!</p>";
    } else {
        echo "<p>✅ Products table exists</p>";
    }
    
    exit();
}

// Handle status update
if (isset($_GET['update_status']) && isset($_GET['id'])) {
    $enquiry_id = intval($_GET['id']);
    $new_status = $_GET['update_status'];
    $allowed_statuses = ['new', 'processing', 'quoted', 'completed'];
    
    if (in_array($new_status, $allowed_statuses)) {
        $update_sql = "UPDATE enquiries SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $new_status, $enquiry_id);
        
        if ($stmt->execute()) {
            $success_message = "Enquiry status updated successfully!";
        } else {
            $error_message = "Error updating status: " . $conn->error;
        }
    }
}

// Handle enquiry deletion
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $enquiry_id = intval($_GET['id']);
    $delete_sql = "DELETE FROM enquiries WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $enquiry_id);
    
    if ($stmt->execute()) {
        $success_message = "Enquiry deleted successfully!";
    } else {
        $error_message = "Error deleting enquiry: " . $conn->error;
    }
}

// Filter handling
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Initialize variables
$enquiries_result = null;
$stats = ['total' => 0, 'new' => 0, 'processing' => 0, 'recent' => 0];

try {
    // Build WHERE clause based on filter and search
    $where_conditions = [];
    $params = [];
    $param_types = '';

    if ($filter != 'all') {
        switch ($filter) {
            case 'new':
                $where_conditions[] = "e.status = 'new'";
                break;
            case 'processing':
                $where_conditions[] = "e.status = 'processing'";
                break;
            case 'quoted':
                $where_conditions[] = "e.status = 'quoted'";
                break;
            case 'completed':
                $where_conditions[] = "e.status = 'completed'";
                break;
            case 'recent':
                $where_conditions[] = "e.enquiry_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
        }
    }

    if (!empty($search)) {
        $where_conditions[] = "(e.customer_name LIKE ? OR e.customer_email LIKE ? OR p.product_name LIKE ?)";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $param_types .= 'sss';
    }

    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    // Get enquiries with product information
    $enquiries_sql = "SELECT e.*, 
                            COALESCE(p.product_name, 'Unknown Product') as product_name
                      FROM enquiries e 
                      LEFT JOIN products p ON e.product_id = p.id 
                      $where_clause 
                      ORDER BY e.enquiry_date DESC";

    if (!empty($params)) {
        $stmt = $conn->prepare($enquiries_sql);
        if (!empty($param_types)) {
            $stmt->bind_param($param_types, ...$params);
        }
        $stmt->execute();
        $enquiries_result = $stmt->get_result();
    } else {
        $enquiries_result = $conn->query($enquiries_sql);
    }

    // Get statistics
    $result = $conn->query("SELECT COUNT(*) as count FROM enquiries");
    if ($result) $stats['total'] = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM enquiries WHERE status = 'new'");
    if ($result) $stats['new'] = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM enquiries WHERE status = 'processing'");
    if ($result) $stats['processing'] = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM enquiries WHERE enquiry_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    if ($result) $stats['recent'] = $result->fetch_assoc()['count'];

} catch (Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Enquiries - BluSolv Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
     :root {
            --deep-navy: #1a2332;
            --charcoal-grey: #3d3d3d;
            --champagne-gold: #d4a574;
            --cream-beige: #f4efe7;
            --soft-taupe: #d4c5b9;
            --ivory: #fffff0;
            --slate-grey: #6b7280;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--ivory);
            color: var(--charcoal-grey);
            min-height: 100vh;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Cormorant Garamond', serif;
            font-weight: 400;
        }
        
        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: linear-gradient(135deg, var(--deep-navy) 0%, var(--charcoal-grey) 100%);
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
            overflow-y: auto;
            z-index: 1000;
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
        
        .sidebar ul {
            list-style: none;
        }
        
        .sidebar ul li {
            margin-bottom: 5px;
        }
        
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
        
        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, var(--cream-beige) 0%, var(--ivory) 100%);
            padding: 30px;
            border-radius: 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 2px solid var(--soft-taupe);
        }
        
        .page-header h1 {
            color: var(--deep-navy);
            font-family: 'Cormorant Garamond', serif;
            font-weight: 400;
            letter-spacing: 3px;
            font-size: 2.5rem;
            margin: 0;
        }
        
        /* Statistics Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 2px solid var(--soft-taupe);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--champagne-gold);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--champagne-gold);
            margin-bottom: 10px;
            font-family: 'Cormorant Garamond', serif;
        }
        
        .stat-label {
            color: var(--slate-grey);
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Filters Section */
        .filters-section {
            background: white;
            padding: 25px 30px;
            border-radius: 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            border: 2px solid var(--soft-taupe);
        }
        
        .filters-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .filter-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            padding: 10px 22px;
            border: 2px solid var(--soft-taupe);
            background: transparent;
            color: var(--charcoal-grey);
            border-radius: 0;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.4s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .filter-btn.active,
        .filter-btn:hover {
            background-color: var(--deep-navy);
            color: var(--cream-beige);
            border-color: var(--deep-navy);
            transform: translateY(-2px);
        }
        
        .search-container {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .search-input {
            padding: 14px 18px;
            border: 2px solid var(--soft-taupe);
            border-radius: 0;
            font-size: 15px;
            width: 280px;
            font-family: 'Montserrat', sans-serif;
            background: white;
            color: var(--charcoal-grey);
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--champagne-gold);
            box-shadow: 0 0 0 3px rgba(212, 165, 116, 0.1);
        }
        
        .search-btn {
            padding: 16px 30px;
            border: 2px solid transparent;
            background-color: var(--deep-navy);
            color: var(--cream-beige);
            border-radius: 0;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.4s ease;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-color: var(--deep-navy);
        }
        
        .search-btn:hover {
            background-color: var(--champagne-gold);
            color: var(--deep-navy);
            border-color: var(--champagne-gold);
            transform: translateY(-2px);
        }
        
        /* Table Section */
        .table-section {
            background: white;
            border-radius: 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 2px solid var(--soft-taupe);
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        .enquiries-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .enquiries-table thead {
            background: linear-gradient(135deg, var(--deep-navy) 0%, var(--charcoal-grey) 100%);
            color: white;
        }
        
        .enquiries-table th {
            padding: 18px 15px;
            text-align: left;
            font-weight: 500;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid var(--champagne-gold);
        }
        
        .enquiries-table tbody tr {
            border-bottom: 1px solid var(--soft-taupe);
            transition: all 0.3s ease;
        }
        
        .enquiries-table tbody tr:hover {
            background: var(--cream-beige);
            transform: scale(1.005);
        }
        
        .enquiries-table td {
            padding: 18px 15px;
            font-size: 0.9rem;
            color: var(--charcoal-grey);
        }
        
        .enquiry-id {
            font-weight: 700;
            color: var(--champagne);
            font-size: 1rem;
        }
        
        .customer-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .customer-name {
            font-weight: 600;
            color: var(--navy);
        }
        
        .customer-detail {
            font-size: 0.8rem;
            color: var(--text-light);
        }
        
        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-new {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            color: #0d47a1;
            border: 1px solid #1976d2;
        }
        
        .status-processing {
            background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
            color: #e65100;
            border: 1px solid #f57c00;
        }
        
        .status-quoted {
            background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);
            color: #4a148c;
            border: 1px solid #7b1fa2;
        }
        
        .status-completed {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            color: #1b5e20;
            border: 1px solid #388e3c;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 8px 16px;
            border: 2px solid transparent;
            border-radius: 0;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 500;
            transition: all 0.4s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-view {
            background-color: var(--info);
            color: white;
            border-color: var(--info);
        }
        
        .btn-view:hover {
            background-color: transparent;
            color: var(--info);
        }
        
        .btn-process {
            background-color: var(--warning);
            color: var(--deep-navy);
            border-color: var(--warning);
        }
        
        .btn-process:hover {
            background-color: transparent;
            color: var(--warning);
        }
        
        .btn-quote {
            background-color: var(--champagne-gold);
            color: var(--deep-navy);
            border-color: var(--champagne-gold);
        }
        
        .btn-quote:hover {
            background-color: transparent;
            color: var(--champagne-gold);
        }
        
        .btn-complete {
            background-color: var(--success);
            color: white;
            border-color: var(--success);
        }
        
        .btn-complete:hover {
            background-color: transparent;
            color: var(--success);
        }
        
        .btn-delete {
            background-color: var(--danger);
            color: white;
            border-color: var(--danger);
        }
        
        .btn-delete:hover {
            background-color: transparent;
            color: var(--danger);
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-back {
            padding: 16px 40px;
            border: 2px solid transparent;
            background-color: transparent;
            color: var(--charcoal-grey);
            border-color: var(--soft-taupe);
            font-size: 0.85rem;
            letter-spacing: 2px;
        }
        
        .btn-back:hover {
            background-color: var(--soft-taupe);
            border-color: var(--champagne-gold);
        }
        
        /* Alert Messages */
        .alert {
            padding: 18px 25px;
            margin-bottom: 25px;
            border-radius: 0;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            border: 2px solid transparent;
            font-size: 0.95rem;
        }
        
        .alert-success {
            background-color: #d4edda;
            border-color: var(--success);
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-color: var(--danger);
            color: #721c24;
        }
        
        .alert::before {
            content: '✓';
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .alert-danger::before {
            content: '⚠';
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-light);
        }
        
        .empty-state-icon {
            font-size: 4rem;
            color: var(--champagne);
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            font-size: 1.5rem;
            color: var(--navy);
            margin-bottom: 10px;
        }
        
        .empty-state p {
            font-size: 1rem;
            margin-bottom: 20px;
        }
        
        .empty-state a {
            color: var(--champagne);
            font-weight: 600;
            text-decoration: none;
        }
        
        /* Responsive Design */
        @media (max-width: 1024px) {
            .sidebar {
                width: 250px;
            }
            
            .main-content {
                margin-left: 250px;
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            
            .page-header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .page-header h1 {
                font-size: 2rem;
            }
            
            .filters-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-buttons {
                justify-content: center;
            }
            
            .search-container {
                flex-direction: column;
            }
            
            .search-input {
                width: 100%;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .table-container {
                overflow-x: scroll;
            }
            
            .enquiries-table {
                font-size: 0.85rem;
            }
            
            .enquiries-table th,
            .enquiries-table td {
                padding: 12px 8px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
        
        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(212, 175, 55, 0.3);
            border-radius: 50%;
            border-top-color: var(--champagne);
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>BluSolv</h2>
            <p>Admin Panel</p>
        </div>
        <ul>
            <li><a href="dashboard.php"><span class="icon">📊</span>Dashboard</a></li>
            <li><a href="products.php"><span class="icon">📦</span>Products</a></li>
            <li><a href="add_product.php"><span class="icon">➕</span>Add Product</a></li>
            <li><a href="categories.php"><span class="icon">📂</span>Categories</a></li>
            <li><a href="materials.php"><span class="icon">🔧</span>Materials</a></li>
            <li><a href="enquiries.php" class="active"><span class="icon">💬</span>Enquiries</a></li>
            <li><a href="settings.php"><span class="icon">⚙️</span>Settings</a></li>
            <li><a href="../products.php" target="_blank"><span class="icon">🌐</span>View Site</a></li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1>Customer Enquiries</h1>
            <div class="page-header-actions">
                <a href="dashboard.php" class="btn btn-back">← Back to Dashboard</a>
            </div>
        </div>
        
        <!-- Alert Messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total']; ?></div>
                <div class="stat-label">Total Enquiries</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['new']; ?></div>
                <div class="stat-label">New Enquiries</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['processing']; ?></div>
                <div class="stat-label">Processing</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['recent']; ?></div>
                <div class="stat-label">This Week</div>
            </div>
        </div>
        
        <!-- Filters and Search -->
        <div class="filters-section">
            <div class="filters-container">
                <div class="filter-buttons">
                    <a href="enquiries.php?filter=processing" class="filter-btn <?php echo ($filter == 'processing') ? 'active' : ''; ?>">Processing</a>
                    <a href="enquiries.php?filter=quoted" class="filter-btn <?php echo ($filter == 'quoted') ? 'active' : ''; ?>">Quoted</a>
                    <a href="enquiries.php?filter=completed" class="filter-btn <?php echo ($filter == 'completed') ? 'active' : ''; ?>">Completed</a>
                    <a href="enquiries.php?filter=recent" class="filter-btn <?php echo ($filter == 'recent') ? 'active' : ''; ?>">Recent</a>
                </div>
                
                <form class="search-container" method="GET">
                    <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                    <input type="text" name="search" class="search-input" placeholder="Search by name, email or product..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="search-btn">Search</button>
                </form>
            </div>
        </div>
        
        <!-- Enquiries Table -->
        <div class="table-section">
            <div class="table-container">
                <table class="enquiries-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer Details</th>
                            <th>Product</th>
                            <th>Contact</th>
                            <th>Quantity</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($enquiries_result && $enquiries_result->num_rows > 0): ?>
                            <?php while ($enquiry = $enquiries_result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <span class="enquiry-id">#<?php echo str_pad($enquiry['id'], 4, '0', STR_PAD_LEFT); ?></span>
                                    </td>
                                    <td>
                                        <div class="customer-info">
                                            <span class="customer-name"><?php echo htmlspecialchars($enquiry['customer_name']); ?></span>
                                            <span class="customer-detail"><?php echo htmlspecialchars($enquiry['customer_email']); ?></span>
                                            <?php if (!empty($enquiry['company_name'])): ?>
                                                <span class="customer-detail">🏢 <?php echo htmlspecialchars($enquiry['company_name']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($enquiry['product_name']); ?></td>
                                    <td>
                                        <a href="tel:<?php echo htmlspecialchars($enquiry['customer_phone']); ?>" style="color: var(--champagne); text-decoration: none; font-weight: 500;">
                                            📞 <?php echo htmlspecialchars($enquiry['customer_phone']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($enquiry['expected_quantity']); ?></strong>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.85rem;">
                                            <div style="font-weight: 600;"><?php echo date('M j, Y', strtotime($enquiry['enquiry_date'])); ?></div>
                                            <div style="color: var(--text-light);"><?php echo date('g:i A', strtotime($enquiry['enquiry_date'])); ?></div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $enquiry['status']; ?>">
                                            <?php echo ucfirst($enquiry['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="view_enquiry.php?id=<?php echo $enquiry['id']; ?>" class="btn btn-view">View</a>
                                            
                                            <?php if ($enquiry['status'] == 'new'): ?>
                                                <a href="enquiries.php?update_status=processing&id=<?php echo $enquiry['id']; ?>&filter=<?php echo $filter; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                                                   class="btn btn-process">Process</a>
                                            <?php elseif ($enquiry['status'] == 'processing'): ?>
                                                <a href="enquiries.php?update_status=quoted&id=<?php echo $enquiry['id']; ?>&filter=<?php echo $filter; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                                                   class="btn btn-quote">Quote</a>
                                            <?php elseif ($enquiry['status'] == 'quoted'): ?>
                                                <a href="enquiries.php?update_status=completed&id=<?php echo $enquiry['id']; ?>&filter=<?php echo $filter; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                                                   class="btn btn-complete">Complete</a>
                                            <?php endif; ?>
                                            
                                            <a href="enquiries.php?delete=1&id=<?php echo $enquiry['id']; ?>&filter=<?php echo $filter; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                                               class="btn btn-delete" 
                                               onclick="return confirm('⚠️ Are you sure you want to delete this enquiry?\n\nCustomer: <?php echo htmlspecialchars($enquiry['customer_name']); ?>\nProduct: <?php echo htmlspecialchars($enquiry['product_name']); ?>\n\nThis action cannot be undone.')">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">📭</div>
                                        <?php if (isset($error_message)): ?>
                                            <h3>Database Error</h3>
                                            <p style="color: var(--danger);">⚠️ <?php echo htmlspecialchars($error_message); ?></p>
                                            <p><a href="enquiries.php?debug=1">Click here to debug database issues</a></p>
                                        <?php elseif (!empty($search) || $filter != 'all'): ?>
                                            <h3>No Results Found</h3>
                                            <p>No enquiries match your current filter or search criteria.</p>
                                            <p><a href="enquiries.php">Clear filters and view all enquiries</a></p>
                                        <?php else: ?>
                                            <h3>No Enquiries Yet</h3>
                                            <p>You haven't received any customer enquiries yet.</p>
                                            <p>Enquiries will appear here when customers submit their requests.</p>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Total Count Footer -->
        <?php if ($enquiries_result && $enquiries_result->num_rows > 0): ?>
            <div style="margin-top: 20px; text-align: center; color: var(--text-light); font-size: 0.9rem;">
                Showing <strong style="color: var(--champagne);"><?php echo $enquiries_result->num_rows; ?></strong> 
                <?php echo $enquiries_result->num_rows == 1 ? 'enquiry' : 'enquiries'; ?>
                <?php if ($filter != 'all'): ?>
                    in <strong style="color: var(--navy);"><?php echo ucfirst($filter); ?></strong> filter
                <?php endif; ?>
                <?php if (!empty($search)): ?>
                    matching "<strong style="color: var(--navy);"><?php echo htmlspecialchars($search); ?></strong>"
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Auto-refresh for new enquiries filter (every 30 seconds)
        <?php if ($filter == 'new'): ?>
        setTimeout(function() {
            window.location.reload();
        }, 30000);
        <?php endif; ?>
        
        // Highlight new enquiries with subtle animation
        document.addEventListener('DOMContentLoaded', function() {
            const newBadges = document.querySelectorAll('.status-new');
            newBadges.forEach(function(badge) {
                const row = badge.closest('tr');
                if (row) {
                    row.style.animation = 'highlightNew 2s ease-in-out';
                    row.style.backgroundColor = 'rgba(212, 175, 55, 0.05)';
                }
            });
        });
        
        // Add highlight animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes highlightNew {
                0%, 100% { background-color: transparent; }
                50% { background-color: rgba(212, 175, 55, 0.15); }
            }
        `;
        document.head.appendChild(style);
        
        // Confirm before leaving page if on new enquiries
        <?php if ($filter == 'new' && $stats['new'] > 0): ?>
        window.addEventListener('beforeunload', function(e) {
            if (document.querySelectorAll('.status-new').length > 0) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
        <?php endif; ?>
        
        // Search input auto-focus on Ctrl+K or Cmd+K
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                document.querySelector('.search-input').focus();
            }
        });
        
        // Enhanced delete confirmation
        document.querySelectorAll('.btn-delete').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                if (!confirm(this.getAttribute('onclick').replace('return confirm(\'', '').replace('\')', ''))) {
                    e.preventDefault();
                }
            });
        });
        
        // Add loading state to action buttons
        document.querySelectorAll('.btn-process, .btn-quote, .btn-complete').forEach(function(btn) {
            btn.addEventListener('click', function() {
                this.innerHTML = '<span class="loading"></span>';
                this.style.pointerEvents = 'none';
            });
        });
        
        // Table row click to view details (except on action buttons)
        document.querySelectorAll('.enquiries-table tbody tr').forEach(function(row) {
            row.addEventListener('click', function(e) {
                if (!e.target.closest('.action-buttons') && !e.target.closest('a')) {
                    const viewBtn = this.querySelector('.btn-view');
                    if (viewBtn) {
                        window.location.href = viewBtn.href;
                    }
                }
            });
            row.style.cursor = 'pointer';
        });
        
        // Auto-hide success/error messages after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 5000);
        
        // Show notification count in page title
        <?php if ($stats['new'] > 0): ?>
        document.title = '(<?php echo $stats['new']; ?>) Customer Enquiries - BluSolv Admin';
        <?php endif; ?>
        
        // Keyboard shortcuts info (press ? to show)
        document.addEventListener('keydown', function(e) {
            if (e.key === '?' && !e.target.matches('input, textarea')) {
                alert('⌨️ Keyboard Shortcuts:\n\n' +
                      'Ctrl/Cmd + K: Focus search\n' +
                      'Esc: Clear search\n' +
                      '?: Show this help');
            }
            if (e.key === 'Escape') {
                const searchInput = document.querySelector('.search-input');
                if (searchInput && searchInput.value) {
                    searchInput.value = '';
                    searchInput.focus();
                }
            }
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>