<?php
// admin/settings.php
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

// Create settings table if it doesn't exist
$create_settings_table = "CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(255) UNIQUE NOT NULL,
    setting_value TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->query($create_settings_table);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $settings_to_update = [
        'company_name' => trim($_POST['company_name']),
        'admin_email' => trim($_POST['admin_email']),
        'contact_phone' => trim($_POST['contact_phone']),
        'contact_address' => trim($_POST['contact_address']),
        'company_website' => trim($_POST['company_website']),
        'business_hours' => trim($_POST['business_hours']),
        'default_currency' => $_POST['default_currency'],
        'timezone' => $_POST['timezone'],
        'email_notifications' => isset($_POST['email_notifications']) ? '1' : '0',
        'auto_reply_email' => isset($_POST['auto_reply_email']) ? '1' : '0'
    ];
    
    $success_count = 0;
    
    foreach ($settings_to_update as $key => $value) {
        $upsert_sql = "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
                       ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()";
        $stmt = $conn->prepare($upsert_sql);
        $stmt->bind_param("ss", $key, $value);
        
        if ($stmt->execute()) {
            $success_count++;
        }
    }
    
    if ($success_count > 0) {
        $success_message = "Settings updated successfully!";
    } else {
        $error_message = "Error updating settings.";
    }
}

// Get current settings
$settings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM settings");
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Default values if settings don't exist
$defaults = [
    'company_name' => 'BluSolv',
    'admin_email' => 'admin@blusolv.com',
    'contact_phone' => '+91-9876543210',
    'contact_address' => '123 Business Street, City, State - 123456',
    'company_website' => 'https://www.blusolv.com',
    'business_hours' => 'Mon-Fri 9:00 AM - 6:00 PM',
    'default_currency' => 'INR',
    'timezone' => 'Asia/Kolkata',
    'email_notifications' => '1',
    'auto_reply_email' => '1'
];

foreach ($defaults as $key => $default_value) {
    if (!isset($settings[$key])) {
        $settings[$key] = $default_value;
    }
}

// Get system statistics for dashboard
$system_stats = [];
$system_stats['total_products'] = $conn->query("SELECT COUNT(*) as count FROM products WHERE status != 'deleted'")->fetch_assoc()['count'];
$system_stats['total_categories'] = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];
$system_stats['total_materials'] = $conn->query("SELECT COUNT(*) as count FROM materials")->fetch_assoc()['count'];
$system_stats['total_enquiries'] = $conn->query("SELECT COUNT(*) as count FROM enquiries")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Panel</title>
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
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }
        
        .content-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .section-title {
            font-size: 1.3em;
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
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #667eea;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-right: 10px;
        }
        
        .btn-primary {
            background-color: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #5a67d8;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-danger {
            background-color: #f56565;
            color: white;
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
        
        .info-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .info-card h4 {
            color: #667eea;
            margin-bottom: 15px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e1e8ed;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .danger-zone {
            background: #fff5f5;
            border: 2px solid #fed7d7;
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .danger-zone h4 {
            color: #e53e3e;
            margin-bottom: 15px;
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
            
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
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
            <li><a href="materials.php">🔧 Materials</a></li>
            <li><a href="enquiries.php">💬 Enquiries</a></li>
            <li><a href="settings.php" class="active">⚙️ Settings</a></li>
            <li><a href="../products.php" target="_blank">🌐 View Site</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>System Settings</h1>
            <div>
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="content-grid">
            <!-- Settings Form -->
            <div class="content-section">
                <h2 class="section-title">General Settings</h2>
                
                <form method="POST" id="settingsForm">
                    <div class="form-group">
                        <label for="company_name">Company Name</label>
                        <input type="text" 
                               id="company_name" 
                               name="company_name" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($settings['company_name']); ?>" 
                               required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="admin_email">Admin Email</label>
                            <input type="email" 
                                   id="admin_email" 
                                   name="admin_email" 
                                   class="form-control" 
                                   value="<?php echo htmlspecialchars($settings['admin_email']); ?>" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_phone">Contact Phone</label>
                            <input type="text" 
                                   id="contact_phone" 
                                   name="contact_phone" 
                                   class="form-control" 
                                   value="<?php echo htmlspecialchars($settings['contact_phone']); ?>" 
                                   required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="contact_address">Contact Address</label>
                        <textarea id="contact_address" 
                                  name="contact_address" 
                                  class="form-control" 
                                  rows="3"><?php echo htmlspecialchars($settings['contact_address']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="company_website">Company Website</label>
                        <input type="url" 
                               id="company_website" 
                               name="company_website" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($settings['company_website']); ?>" 
                               placeholder="https://www.example.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="business_hours">Business Hours</label>
                        <input type="text" 
                               id="business_hours" 
                               name="business_hours" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($settings['business_hours']); ?>" 
                               placeholder="Mon-Fri 9:00 AM - 6:00 PM">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="default_currency">Default Currency</label>
                            <select id="default_currency" name="default_currency" class="form-control">
                                <option value="INR" <?php echo ($settings['default_currency'] == 'INR') ? 'selected' : ''; ?>>INR (₹)</option>
                                <option value="USD" <?php echo ($settings['default_currency'] == 'USD') ? 'selected' : ''; ?>>USD ($)</option>
                                <option value="EUR" <?php echo ($settings['default_currency'] == 'EUR') ? 'selected' : ''; ?>>EUR (€)</option>
                                <option value="GBP" <?php echo ($settings['default_currency'] == 'GBP') ? 'selected' : ''; ?>>GBP (£)</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="timezone">Timezone</label>
                            <select id="timezone" name="timezone" class="form-control">
                                <option value="Asia/Kolkata" <?php echo ($settings['timezone'] == 'Asia/Kolkata') ? 'selected' : ''; ?>>Asia/Kolkata</option>
                                <option value="America/New_York" <?php echo ($settings['timezone'] == 'America/New_York') ? 'selected' : ''; ?>>America/New_York</option>
                                <option value="Europe/London" <?php echo ($settings['timezone'] == 'Europe/London') ? 'selected' : ''; ?>>Europe/London</option>
                                <option value="Asia/Dubai" <?php echo ($settings['timezone'] == 'Asia/Dubai') ? 'selected' : ''; ?>>Asia/Dubai</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="border-top: 1px solid #e1e8ed; padding-top: 20px; margin-top: 30px;">
                        <h3 style="margin-bottom: 15px; color: #667eea;">Email Settings</h3>
                        
                        <div class="checkbox-group">
                            <input type="checkbox" 
                                   id="email_notifications" 
                                   name="email_notifications" 
                                   <?php echo ($settings['email_notifications'] == '1') ? 'checked' : ''; ?>>
                            <label for="email_notifications" style="margin-bottom: 0;">Enable email notifications for new enquiries</label>
                        </div>
                        
                        <div class="checkbox-group">
                            <input type="checkbox" 
                                   id="auto_reply_email" 
                                   name="auto_reply_email" 
                                   <?php echo ($settings['auto_reply_email'] == '1') ? 'checked' : ''; ?>>
                            <label for="auto_reply_email" style="margin-bottom: 0;">Send auto-reply emails to customers</label>
                        </div>
                    </div>
                    
                    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e1e8ed;">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                        <button type="reset" class="btn btn-secondary">Reset Form</button>
                    </div>
                </form>
            </div>
            
            <!-- System Information -->
            <div>
                <div class="content-section">
                    <h2 class="section-title">System Information</h2>
                    
                    <div class="info-card">
                        <h4>Database Statistics</h4>
                        <div class="info-item">
                            <span>Total Products:</span>
                            <strong><?php echo $system_stats['total_products']; ?></strong>
                        </div>
                        <div class="info-item">
                            <span>Total Categories:</span>
                            <strong><?php echo $system_stats['total_categories']; ?></strong>
                        </div>
                        <div class="info-item">
                            <span>Total Materials:</span>
                            <strong><?php echo $system_stats['total_materials']; ?></strong>
                        </div>
                        <div class="info-item">
                            <span>Total Enquiries:</span>
                            <strong><?php echo $system_stats['total_enquiries']; ?></strong>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <h4>Server Information</h4>
                        <div class="info-item">
                            <span>PHP Version:</span>
                            <strong><?php echo phpversion(); ?></strong>
                        </div>
                        <div class="info-item">
                            <span>MySQL Version:</span>
                            <strong><?php echo $conn->server_info; ?></strong>
                        </div>
                        <div class="info-item">
                            <span>Server Time:</span>
                            <strong><?php echo date('Y-m-d H:i:s'); ?></strong>
                        </div>
                        <div class="info-item">
                            <span>Upload Max Size:</span>
                            <strong><?php echo ini_get('upload_max_filesize'); ?></strong>
                        </div>
                    </div>
                </div>
                
                <!-- Backup and Maintenance -->
                <div class="content-section">
                    <h2 class="section-title">Backup & Maintenance</h2>
                    
                    <div style="margin-bottom: 20px;">
                        <h4 style="color: #667eea; margin-bottom: 10px;">Database Backup</h4>
                        <p style="color: #666; margin-bottom: 15px;">Create a backup of your database for safety.</p>
                        <a href="backup.php" class="btn btn-primary">Create Backup</a>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <h4 style="color: #667eea; margin-bottom: 10px;">Clear Cache</h4>
                        <p style="color: #666; margin-bottom: 15px;">Clear system cache and temporary files.</p>
                        <a href="clear_cache.php" class="btn btn-secondary">Clear Cache</a>
                    </div>
                    
                    <div class="danger-zone">
                        <h4>⚠️ Danger Zone</h4>
                        <p style="color: #666; margin-bottom: 15px;">These actions cannot be undone. Please be careful.</p>
                        <a href="reset_system.php" 
                           class="btn btn-danger" 
                           onclick="return confirm('This will reset all system settings. Are you sure?')">
                           Reset System Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Form validation
        document.getElementById('settingsForm').addEventListener('submit', function(e) {
            const requiredFields = ['company_name', 'admin_email', 'contact_phone'];
            let hasError = false;
            
            requiredFields.forEach(function(fieldId) {
                const field = document.getElementById(fieldId);
                if (!field.value.trim()) {
                    field.style.borderColor = '#f56565';
                    hasError = true;
                } else {
                    field.style.borderColor = '#e1e8ed';
                }
            });
            
            // Validate email format
            const emailField = document.getElementById('admin_email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailField.value)) {
                emailField.style.borderColor = '#f56565';
                hasError = true;
                alert('Please enter a valid email address.');
                e.preventDefault();
                return;
            }
            
            // Validate website URL if provided
            const websiteField = document.getElementById('company_website');
            if (websiteField.value && !websiteField.value.startsWith('http')) {
                websiteField.style.borderColor = '#f56565';
                hasError = true;
                alert('Website URL should start with http:// or https://');
                e.preventDefault();
                return;
            }
            
            if (hasError) {
                e.preventDefault();
                alert('Please fill in all required fields correctly.');
            }
        });
        
        // Auto-save indicator
        let timeoutId;
        document.querySelectorAll('.form-control, input[type="checkbox"]').forEach(function(field) {
            field.addEventListener('change', function() {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(function() {
                    // You can add auto-save functionality here if needed
                    console.log('Form data changed');
                }, 2000);
            });
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>