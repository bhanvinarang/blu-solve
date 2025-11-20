<?php
// admin/view_enquiry.php
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

// Get enquiry ID from URL
$enquiry_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($enquiry_id == 0) {
    header('Location: enquiries.php');
    exit();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    $allowed_statuses = ['new', 'processing', 'quoted', 'completed'];
    
    if (in_array($new_status, $allowed_statuses)) {
        $update_sql = "UPDATE enquiries SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $new_status, $enquiry_id);
        
        if ($stmt->execute()) {
            $success_message = "Status updated successfully!";
        } else {
            $error_message = "Error updating status: " . $conn->error;
        }
    }
}

// Handle admin notes update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_notes'])) {
    $admin_notes = trim($_POST['admin_notes']);
    
    // Check if admin_notes column exists, if not create it
    $check_column = $conn->query("SHOW COLUMNS FROM enquiries LIKE 'admin_notes'");
    if ($check_column->num_rows == 0) {
        $conn->query("ALTER TABLE enquiries ADD COLUMN admin_notes TEXT");
    }
    
    $update_sql = "UPDATE enquiries SET admin_notes = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $admin_notes, $enquiry_id);
    
    if ($stmt->execute()) {
        $success_message = "Notes updated successfully!";
    } else {
        $error_message = "Error updating notes: " . $conn->error;
    }
}

// Fetch enquiry details with product and nature of requirement
$enquiry_sql = "SELECT e.*, 
                       COALESCE(p.product_name, 'Unknown Product') as product_name,
                       p.product_code,
                       p.price,
                       p.image_url,
                       COALESCE(n.requirement_name, 'Not Specified') as requirement_type
                FROM enquiries e 
                LEFT JOIN products p ON e.product_id = p.id
                LEFT JOIN nature_of_requirements n ON e.nature_of_requirement_id = n.id
                WHERE e.id = ?";

$stmt = $conn->prepare($enquiry_sql);
$stmt->bind_param("i", $enquiry_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: enquiries.php');
    exit();
}

$enquiry = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Enquiry #<?php echo str_pad($enquiry_id, 4, '0', STR_PAD_LEFT); ?> - BluSolv Admin</title>
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
        
        .enquiry-id-badge {
            background: var(--champagne-gold);
            color: var(--deep-navy);
            padding: 10px 25px;
            border-radius: 0;
            font-size: 1.2rem;
            font-weight: 700;
            letter-spacing: 1px;
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
        
        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        /* Card */
        .card {
            background: white;
            padding: 30px;
            border-radius: 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 2px solid var(--soft-taupe);
        }
        
        .card-header {
            border-bottom: 2px solid var(--soft-taupe);
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        
        .card-header h2 {
            color: var(--deep-navy);
            font-size: 1.8rem;
            letter-spacing: 2px;
        }
        
        .info-group {
            margin-bottom: 25px;
        }
        
        .info-label {
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--slate-grey);
            margin-bottom: 8px;
        }
        
        .info-value {
            font-size: 1.1rem;
            color: var(--charcoal-grey);
            font-weight: 500;
        }
        
        .info-value a {
            color: var(--champagne-gold);
            text-decoration: none;
            font-weight: 600;
        }
        
        .info-value a:hover {
            text-decoration: underline;
        }
        
        /* Product Card */
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 0;
            border: 2px solid var(--soft-taupe);
            margin-bottom: 20px;
        }
        
        .product-name {
            font-size: 1.5rem;
            color: var(--deep-navy);
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .product-code {
            color: var(--slate-grey);
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
        
        .product-price {
            font-size: 1.8rem;
            color: var(--champagne-gold);
            font-weight: 700;
            font-family: 'Cormorant Garamond', serif;
        }
        
        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.85rem;
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
        
        /* Forms */
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--slate-grey);
            margin-bottom: 10px;
        }
        
        .form-select,
        .form-textarea {
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
        
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--champagne-gold);
            box-shadow: 0 0 0 3px rgba(212, 165, 116, 0.1);
        }
        
        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        /* Buttons */
        .btn {
            padding: 14px 30px;
            border: 2px solid transparent;
            border-radius: 0;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.4s ease;
            text-transform: uppercase;
            letter-spacing: 1.5px;
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
        
        .btn-secondary {
            background-color: transparent;
            color: var(--charcoal-grey);
            border-color: var(--soft-taupe);
        }
        
        .btn-secondary:hover {
            background-color: var(--soft-taupe);
            border-color: var(--champagne-gold);
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background-color: var(--danger);
            color: white;
            border-color: var(--danger);
        }
        
        .btn-danger:hover {
            background-color: transparent;
            color: var(--danger);
            transform: translateY(-2px);
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        
        /* Message Box */
        .message-box {
            background: var(--cream-beige);
            padding: 20px;
            border-left: 4px solid var(--champagne-gold);
            border-radius: 0;
            margin-top: 20px;
        }
        
        .message-box h3 {
            color: var(--deep-navy);
            margin-bottom: 10px;
            font-size: 1.2rem;
        }
        
        .message-box p {
            color: var(--charcoal-grey);
            line-height: 1.8;
            white-space: pre-wrap;
        }
        
        /* Timeline */
        .timeline-item {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--soft-taupe);
        }
        
        .timeline-item:last-child {
            border-bottom: none;
        }
        
        .timeline-icon {
            width: 40px;
            height: 40px;
            background: var(--champagne-gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        
        .timeline-content {
            flex: 1;
        }
        
        .timeline-title {
            font-weight: 600;
            color: var(--deep-navy);
            margin-bottom: 5px;
        }
        
        .timeline-date {
            font-size: 0.85rem;
            color: var(--slate-grey);
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
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
            }
            
            .page-header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .action-buttons {
                flex-direction: column;
            }
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
            <h1>Enquiry Details</h1>
            <span class="enquiry-id-badge">#<?php echo str_pad($enquiry_id, 4, '0', STR_PAD_LEFT); ?></span>
        </div>
        
        <!-- Alert Messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Main Details -->
            <div>
                <!-- Customer Information Card -->
                <div class="card">
                    <div class="card-header">
                        <h2>Customer Information</h2>
                    </div>
                    
                    <div class="info-group">
                        <div class="info-label">Customer Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($enquiry['customer_name']); ?></div>
                    </div>
                    
                    <div class="info-group">
                        <div class="info-label">Email Address</div>
                        <div class="info-value">
                            <a href="mailto:<?php echo htmlspecialchars($enquiry['customer_email']); ?>">
                                <?php echo htmlspecialchars($enquiry['customer_email']); ?>
                            </a>
                        </div>
                    </div>
                    
                    <div class="info-group">
                        <div class="info-label">Phone Number</div>
                        <div class="info-value">
                            <a href="tel:<?php echo htmlspecialchars($enquiry['customer_phone']); ?>">
                                <?php echo htmlspecialchars($enquiry['customer_phone']); ?>
                            </a>
                        </div>
                    </div>
                    
                    <?php if (!empty($enquiry['company_name'])): ?>
                    <div class="info-group">
                        <div class="info-label">Company Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($enquiry['company_name']); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Enquiry Details Card -->
                <div class="card" style="margin-top: 30px;">
                    <div class="card-header">
                        <h2>Enquiry Details</h2>
                    </div>
                    
                    <div class="info-group">
                        <div class="info-label">Expected Quantity</div>
                        <div class="info-value"><?php echo htmlspecialchars($enquiry['expected_quantity']); ?></div>
                    </div>
                    
                    <div class="info-group">
                        <div class="info-label">Requirement Type</div>
                        <div class="info-value"><?php echo htmlspecialchars($enquiry['requirement_type']); ?></div>
                    </div>
                    
                    <div class="info-group">
                        <div class="info-label">Enquiry Date</div>
                        <div class="info-value">
                            <?php echo date('F j, Y \a\t g:i A', strtotime($enquiry['enquiry_date'])); ?>
                        </div>
                    </div>
                    
                    <div class="info-group">
                        <div class="info-label">Current Status</div>
                        <div class="info-value">
                            <span class="status-badge status-<?php echo $enquiry['status']; ?>">
                                <?php echo ucfirst($enquiry['status']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <?php if (!empty($enquiry['message'])): ?>
                    <div class="message-box">
                        <h3>Customer Message</h3>
                        <p><?php echo nl2br(htmlspecialchars($enquiry['message'])); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Admin Notes Card -->
                <div class="card" style="margin-top: 30px;">
                    <div class="card-header">
                        <h2>Admin Notes</h2>
                    </div>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label class="form-label">Internal Notes</label>
                            <textarea name="admin_notes" class="form-textarea" placeholder="Add your notes here..."><?php echo isset($enquiry['admin_notes']) ? htmlspecialchars($enquiry['admin_notes']) : ''; ?></textarea>
                        </div>
                        <button type="submit" name="update_notes" class="btn btn-primary">Save Notes</button>
                    </form>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div>
                <!-- Product Card -->
                <div class="card">
                    <div class="card-header">
                        <h2>Product Details</h2>
                    </div>
                    
                    <?php if (!empty($enquiry['image_url'])): ?>
                    <img src="../<?php echo htmlspecialchars($enquiry['image_url']); ?>" alt="Product Image" class="product-image">
                    <?php endif; ?>
                    
                    <div class="product-name"><?php echo htmlspecialchars($enquiry['product_name']); ?></div>
                    
                    <?php if (!empty($enquiry['product_code'])): ?>
                    <div class="product-code">Code: <?php echo htmlspecialchars($enquiry['product_code']); ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($enquiry['price'])): ?>
                    <div class="product-price">₹<?php echo number_format($enquiry['price'], 2); ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Quick Actions Card -->
                <div class="card" style="margin-top: 30px;">
                    <div class="card-header">
                        <h2>Update Status</h2>
                    </div>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label class="form-label">Change Status</label>
                            <select name="status" class="form-select">
                                <option value="new" <?php echo $enquiry['status'] == 'new' ? 'selected' : ''; ?>>New</option>
                                <option value="processing" <?php echo $enquiry['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="quoted" <?php echo $enquiry['status'] == 'quoted' ? 'selected' : ''; ?>>Quoted</option>
                                <option value="completed" <?php echo $enquiry['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>
                        <button type="submit" name="update_status" class="btn btn-primary" style="width: 100%;">Update Status</button>
                    </form>
                </div>
                
                <!-- Quick Links Card -->
                <div class="card" style="margin-top: 30px;">
                    <div class="card-header">
                        <h2>Quick Actions</h2>
                    </div>
                    
                    <div class="action-buttons" style="flex-direction: column;">
                        <a href="mailto:<?php echo htmlspecialchars($enquiry['customer_email']); ?>" class="btn btn-primary">
                            📧 Send Email
                        </a>
                        <a href="tel:<?php echo htmlspecialchars($enquiry['customer_phone']); ?>" class="btn btn-primary">
                            📞 Call Customer
                        </a>
                        <a href="enquiries.php" class="btn btn-secondary">
                            ← Back to Enquiries
                        </a>
                        <a href="enquiries.php?delete=1&id=<?php echo $enquiry_id; ?>" 
                           class="btn btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this enquiry? This action cannot be undone.')">
                            🗑️ Delete Enquiry
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
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
        
        // Confirm before leaving if notes are edited
        let notesChanged = false;
        const notesTextarea = document.querySelector('textarea[name="admin_notes"]');
        const originalNotes = notesTextarea.value;
        
        notesTextarea.addEventListener('input', function() {
            notesChanged = this.value !== originalNotes;
        });
        
        window.addEventListener('beforeunload', function(e) {
            if (notesChanged) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
        
        // Reset notesChanged flag on form submit
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function() {
                notesChanged = false;
            });
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>