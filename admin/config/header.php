<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Admin Panel'; ?> - BluSolv</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            overflow-y: auto;
        }
        
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            padding: 0 20px;
            font-size: 1.5em;
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
            border-left: 4px solid transparent;
        }
        
        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background-color: rgba(255,255,255,0.1);
            border-left: 4px solid #fff;
        }
        
        .sidebar ul li a i {
            margin-right: 10px;
            width: 20px;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
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
            font-size: 1.8em;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .content-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 1.5em;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            transition: all 0.3s ease;
            margin: 5px;
        }
        
        .btn-primary {
            background-color: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #5a67d8;
        }
        
        .btn-success {
            background-color: #48bb78;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #38a169;
        }
        
        .btn-danger {
            background-color: #f56565;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #e53e3e;
        }
        
        .btn-info {
            background-color: #0bc5ea;
            color: white;
        }
        
        .btn-info:hover {
            background-color: #00b3d7;
        }
        
        .btn-warning {
            background-color: #ed8936;
            color: white;
        }
        
        .btn-warning:hover {
            background-color: #dd6b20;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .table th {
            background-color: #f7fafc;
            font-weight: 600;
            color: #4a5568;
        }
        
        .table tbody tr:hover {
            background-color: #f7fafc;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-active {
            background-color: #c6f6d5;
            color: #22543d;
        }
        
        .status-inactive {
            background-color: #fed7d7;
            color: #742a2a;
        }
        
        .status-new {
            background-color: #bee3f8;
            color: #2a4365;
        }
        
        .status-processing {
            background-color: #feebc8;
            color: #744210;
        }
        
        .status-completed {
            background-color: #c6f6d5;
            color: #22543d;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            border-left: 4px solid;
        }
        
        .alert-success {
            background-color: #f0fff4;
            border-left-color: #48bb78;
            color: #22543d;
        }
        
        .alert-error {
            background-color: #fff5f5;
            border-left-color: #f56565;
            color: #742a2a;
        }
        
        .alert-warning {
            background-color: #fffbeb;
            border-left-color: #ed8936;
            color: #744210;
        }
        
        .alert-info {
            background-color: #ebf8ff;
            border-left-color: #0bc5ea;
            color: #2a4365;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 30px;
            gap: 5px;
        }
        
        .pagination a,
        .pagination span {
            padding: 8px 12px;
            text-decoration: none;
            border: 1px solid #e2e8f0;
            color: #4a5568;
            border-radius: 4px;
        }
        
        .pagination a:hover {
            background-color: #f7fafc;
        }
        
        .pagination .current {
            background-color: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            align-items: center;
        }
        
        .search-bar input {
            flex: 1;
            padding: 10px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
        }
        
        .image-preview {
            max-width: 100px;
            max-height: 100px;
            border-radius: 8px;
            margin-top: 10px;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            position: relative;
        }
        
        .close {
            position: absolute;
            right: 20px;
            top: 15px;
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #000;
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
            
            .table {
                font-size: 14px;
            }
            
            .table th,
            .table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <?php if (!isset($hide_sidebar) || !$hide_sidebar): ?>
    <div class="sidebar">
        <h2><i class="fas fa-cube"></i> BluSolv</h2>
        <ul>
            <li><a href="dashboard.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'class="active"' : ''; ?>>
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a></li>
            <li><a href="products.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'products.php') ? 'class="active"' : ''; ?>>
                <i class="fas fa-box"></i> View Products
            </a></li>
            <li><a href="add_product.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'add_product.php') ? 'class="active"' : ''; ?>>
                <i class="fas fa-plus"></i> Add Product
            </a></li>
            <li><a href="categories.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'categories.php') ? 'class="active"' : ''; ?>>
                <i class="fas fa-tags"></i> Categories
            </a></li>
            <li><a href="materials.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'materials.php') ? 'class="active"' : ''; ?>>
                <i class="fas fa-hammer"></i> Materials
            </a></li>
            <li><a href="requirements.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'requirements.php') ? 'class="active"' : ''; ?>>
                <i class="fas fa-clipboard-list"></i> Requirements
            </a></li>
            <li><a href="enquiries.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'enquiries.php') ? 'class="active"' : ''; ?>>
                <i class="fas fa-envelope"></i> Enquiries
            </a></li>
            <li><a href="../products.php" target="_blank">
                <i class="fas fa-external-link-alt"></i> View Site
            </a></li>
            <li><a href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a></li>
        </ul>
    </div>
    <?php endif; ?>
    
    <div class="main-content">
        <?php if (!isset($hide_header) || !$hide_header): ?>
        <div class="header">
            <h1><?php echo isset($page_title) ? $page_title : 'Admin Panel'; ?></h1>
            <div class="user-info">
                <span><i class="fas fa-user"></i> Welcome, Admin</span>
                <a href="logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
        <?php endif; ?>