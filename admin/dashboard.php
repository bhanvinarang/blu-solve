<?php
// admin/dashboard.php
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

// Get dashboard statistics
$stats = [];

// Total products
$result = $conn->query("SELECT COUNT(*) as count FROM products WHERE status = 'active'");
$stats['total_products'] = $result->fetch_assoc()['count'];

// Total enquiries
$result = $conn->query("SELECT COUNT(*) as count FROM enquiries");
$stats['total_enquiries'] = $result->fetch_assoc()['count'];

// New enquiries (last 7 days)
$result = $conn->query("SELECT COUNT(*) as count FROM enquiries WHERE enquiry_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$stats['new_enquiries'] = $result->fetch_assoc()['count'];

// Pending enquiries
$result = $conn->query("SELECT COUNT(*) as count FROM enquiries WHERE status = 'new'");
$stats['pending_enquiries'] = $result->fetch_assoc()['count'];

// Recent enquiries
$recent_enquiries = [];
$result = $conn->query("SELECT e.*, p.product_name FROM enquiries e 
                       LEFT JOIN products p ON e.product_id = p.id 
                       ORDER BY e.enquiry_date DESC LIMIT 10");
while ($row = $result->fetch_assoc()) {
    $recent_enquiries[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BluSolv Admin</title>
    
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
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--ivory);
            color: var(--charcoal-grey);
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
        
        /* Header */
        .header {
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
        
        .header h1 {
            color: var(--deep-navy);
            font-family: 'Cormorant Garamond', serif;
            font-weight: 400;
            letter-spacing: 3px;
            font-size: 2.5rem;
        }
        
        .quick-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        /* Statistics Cards */
        .stats-container {
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
            transition: all 0.3s ease;
            border: 2px solid var(--soft-taupe);
            position: relative;
            overflow: hidden;
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
        
        .stat-card h3 {
            color: var(--slate-grey);
            font-size: 0.85rem;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
            font-family: 'Montserrat', sans-serif;
        }
        
        .stat-card .number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--champagne-gold);
            margin-bottom: 10px;
            font-family: 'Cormorant Garamond', serif;
        }
        
        .stat-card p {
            color: var(--slate-grey);
            font-size: 0.85rem;
        }
        
        /* Content Section */
        .content-section {
            background: white;
            padding: 40px;
            border-radius: 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 2px solid var(--soft-taupe);
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 2rem;
            margin-bottom: 30px;
            color: var(--deep-navy);
            border-bottom: 2px solid var(--champagne-gold);
            padding-bottom: 15px;
            font-family: 'Cormorant Garamond', serif;
            letter-spacing: 2px;
        }
        
        /* Table */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .table thead {
            background: linear-gradient(135deg, var(--deep-navy) 0%, var(--charcoal-grey) 100%);
            color: white;
        }
        
        .table th {
            padding: 18px 15px;
            text-align: left;
            font-weight: 500;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid var(--champagne-gold);
        }
        
        .table tbody tr {
            border-bottom: 1px solid var(--soft-taupe);
            transition: all 0.3s ease;
        }
        
        .table tbody tr:hover {
            background: var(--cream-beige);
            transform: scale(1.005);
        }
        
        .table td {
            padding: 18px 15px;
            font-size: 0.9rem;
            color: var(--charcoal-grey);
        }
        
        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 0;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid;
        }
        
        .status-new {
            background: #e3f2fd;
            color: #0d47a1;
            border-color: #1976d2;
        }
        
        .status-processing {
            background: #fff3e0;
            color: #e65100;
            border-color: #f57c00;
        }
        
        .status-completed {
            background: #e8f5e9;
            color: #1b5e20;
            border-color: #388e3c;
        }
        
        /* Buttons */
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
            letter-spacing: 2px;
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
            transform: translateY(-2px);
        }
        
        .btn-success {
            background-color: #28a745;
            color: white;
            border-color: #28a745;
        }
        
        .btn-success:hover {
            background-color: transparent;
            color: #28a745;
            transform: translateY(-2px);
        }
        
        .btn-info {
            background-color: #17a2b8;
            color: white;
            border-color: #17a2b8;
        }
        
        .btn-info:hover {
            background-color: transparent;
            color: #17a2b8;
            transform: translateY(-2px);
        }
        
        .btn-view {
            padding: 8px 16px;
            font-size: 0.75rem;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--slate-grey);
        }
        
        .empty-state-icon {
            font-size: 4rem;
            color: var(--champagne-gold);
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            font-size: 1.5rem;
            color: var(--deep-navy);
            margin-bottom: 10px;
        }
        
        /* View All Section */
        .view-all-section {
            margin-top: 30px;
            text-align: center;
            padding-top: 20px;
            border-top: 2px solid var(--soft-taupe);
        }
        
        /* Responsive */
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
            
            .header h1 {
                font-size: 2rem;
            }
            
            .quick-actions {
                justify-content: center;
                flex-direction: column;
                width: 100%;
            }
            
            .quick-actions .btn {
                width: 100%;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .table {
                font-size: 0.85rem;
            }
            
            .table th,
            .table td {
                padding: 12px 8px;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Blusolv Admin</h2>
        <ul>
            <li><a href="dashboard.php" class="active">📊 Dashboard</a></li>
            <li><a href="products.php">📦 Products</a></li>
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
            <h1>Dashboard Overview</h1>
            <div class="quick-actions">
                <a href="add_product.php" class="btn btn-primary">Add New Product</a>
                <a href="enquiries.php" class="btn btn-info">View Enquiries</a>
                <a href="../products.php" target="_blank" class="btn btn-success">View Frontend</a>
            </div>
        </div>
        
        <div class="stats-container">
            <div class="stat-card">
                <h3>Total Products</h3>
                <div class="number"><?php echo $stats['total_products']; ?></div>
                <p>Active products in catalog</p>
            </div>
            
            <div class="stat-card">
                <h3>Total Enquiries</h3>
                <div class="number"><?php echo $stats['total_enquiries']; ?></div>
                <p>All time enquiries received</p>
            </div>
            
            <div class="stat-card">
                <h3>This Week</h3>
                <div class="number"><?php echo $stats['new_enquiries']; ?></div>
                <p>New enquiries in last 7 days</p>
            </div>
            
            <div class="stat-card">
                <h3>Pending</h3>
                <div class="number"><?php echo $stats['pending_enquiries']; ?></div>
                <p>Enquiries awaiting response</p>
            </div>
        </div>
        
        <div class="content-section">
            <h2 class="section-title">Recent Enquiries</h2>
            
            <?php if (empty($recent_enquiries)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <h3>No Enquiries Yet</h3>
                    <p>You haven't received any customer enquiries yet.</p>
                    <p>Enquiries will appear here when customers submit their requests.</p>
                </div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Quantity</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_enquiries as $enquiry): ?>
                            <tr>
                                <td><strong style="color: var(--champagne-gold);">#<?php echo str_pad($enquiry['id'], 4, '0', STR_PAD_LEFT); ?></strong></td>
                                <td><?php echo htmlspecialchars($enquiry['product_name'] ?? 'Unknown Product'); ?></td>
                                <td><strong><?php echo htmlspecialchars($enquiry['customer_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($enquiry['customer_email']); ?></td>
                                <td>
                                    <a href="tel:<?php echo htmlspecialchars($enquiry['customer_phone']); ?>" 
                                       style="color: var(--champagne-gold); text-decoration: none; font-weight: 500;">
                                        <?php echo htmlspecialchars($enquiry['customer_phone']); ?>
                                    </a>
                                </td>
                                <td><strong><?php echo htmlspecialchars($enquiry['expected_quantity']); ?></strong></td>
                                <td>
                                    <div style="font-size: 0.85rem;">
                                        <div style="font-weight: 600;"><?php echo date('M j, Y', strtotime($enquiry['enquiry_date'])); ?></div>
                                        <div style="color: var(--slate-grey);"><?php echo date('g:i A', strtotime($enquiry['enquiry_date'])); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $enquiry['status']; ?>">
                                        <?php echo ucfirst($enquiry['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="view_enquiry.php?id=<?php echo $enquiry['id']; ?>" class="btn btn-info btn-view">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="view-all-section">
                    <a href="enquiries.php" class="btn btn-primary">View All Enquiries</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Auto-refresh dashboard every 60 seconds
        setTimeout(function() {
            location.reload();
        }, 60000);
        
        // Highlight new enquiries
        document.addEventListener('DOMContentLoaded', function() {
            const newBadges = document.querySelectorAll('.status-new');
            newBadges.forEach(function(badge) {
                const row = badge.closest('tr');
                if (row) {
                    row.style.animation = 'highlightNew 2s ease-in-out';
                }
            });
        });
        
        // Add highlight animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes highlightNew {
                0%, 100% { background-color: transparent; }
                50% { background-color: rgba(212, 165, 116, 0.15); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>

<?php $conn->close(); ?>