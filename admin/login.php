<?php
// admin/login.php - Updated to handle both old and new password formats
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Database connection
    $host = 'localhost';
    $db_username = 'blusolv_db';
    $db_password = 'blusolv_db';
    $database = 'blusolv_db';
    
    $conn = new mysqli($host, $db_username, $db_password, $database);
    
    if ($conn->connect_error) {
        $error_message = "Database connection failed!";
    } else {
        // Check if admin_users table exists, if not create default admin
        $result = $conn->query("SHOW TABLES LIKE 'admin_users'");
        
        if ($result->num_rows == 0) {
            // Create admin_users table
            $create_table = "CREATE TABLE `admin_users` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `username` varchar(50) NOT NULL,
                `email` varchar(100) NOT NULL,
                `password` varchar(255) NOT NULL,
                `full_name` varchar(100) NOT NULL,
                `role` enum('super_admin','admin','manager') DEFAULT 'admin',
                `status` enum('active','inactive') DEFAULT 'active',
                `last_login` timestamp NULL DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`),
                UNIQUE KEY `username` (`username`),
                UNIQUE KEY `email` (`email`)
            )";
            
            if ($conn->query($create_table)) {
                // Insert default admin user
                $default_password = password_hash('admin123', PASSWORD_DEFAULT);
                $insert_admin = "INSERT INTO admin_users (username, email, password, full_name, role) 
                               VALUES ('admin', 'admin@blusolv.com', '$default_password', 'Administrator', 'super_admin')";
                $conn->query($insert_admin);
            }
        }
        
        // Authenticate user
        $stmt = $conn->prepare("SELECT id, username, password, full_name, role FROM admin_users WHERE username = ? AND status = 'active'");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            $password_valid = false;
            
            // Check if it's a new password hash (starts with $2y$)
            if (substr($user['password'], 0, 4) === '$2y$') {
                // New password hash - use password_verify
                $password_valid = password_verify($password, $user['password']);
            } else {
                // Old password hash - check multiple common formats
                $input_hash_md5 = md5($password);
                $input_hash_sha256 = hash('sha256', $password);
                $input_hash_sha1 = sha1($password);
                
                if ($user['password'] === $input_hash_md5 || 
                    $user['password'] === $input_hash_sha256 || 
                    $user['password'] === $input_hash_sha1 ||
                    $user['password'] === $password) { // Plain text check
                    $password_valid = true;
                    
                    // Upgrade to new password hash
                    $new_hash = password_hash($password, PASSWORD_DEFAULT);
                    $update_stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
                    $update_stmt->bind_param("si", $new_hash, $user['id']);
                    $update_stmt->execute();
                }
            }
            
            if ($password_valid) {
                // Login successful
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_name'] = $user['full_name'];
                $_SESSION['admin_role'] = $user['role'];
                
                // Update last login
                $update_login = $conn->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                $update_login->bind_param("i", $user['id']);
                $update_login->execute();
                
                header('Location: dashboard.php');
                exit();
            } else {
                $error_message = "Invalid username or password!";
            }
        } else {
            $error_message = "Invalid username or password!";
        }
        
        $conn->close();
    }
}

$page_title = "Admin Login";
$hide_sidebar = true;
$hide_header = true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - BluSolv</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 2em;
        }
        
        .login-header p {
            color: #666;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 600;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .form-group i {
            position: absolute;
            margin-left: 12px;
            margin-top: 12px;
            color: #999;
        }
        
        .form-group input[type="text"],
        .form-group input[type="password"] {
            padding-left: 40px;
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .error-message {
            background-color: #fff5f5;
            color: #742a2a;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #f56565;
        }
        
        .default-credentials {
            margin-top: 30px;
            padding: 20px;
            background-color: #f7fafc;
            border-radius: 8px;
            border-left: 4px solid #0bc5ea;
        }
        
        .default-credentials h4 {
            color: #2a4365;
            margin-bottom: 10px;
        }
        
        .default-credentials p {
            color: #4a5568;
            font-size: 14px;
        }
        
        .credentials {
            font-family: monospace;
            background-color: #e2e8f0;
            padding: 5px 8px;
            border-radius: 4px;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-cube"></i> BluSolv</h1>
            <p>Admin Panel Login</p>
        </div>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <div style="position: relative;">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div style="position: relative;">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" required>
                </div>
            </div>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        
        <div class="default-credentials">
            <h4><i class="fas fa-info-circle"></i> Default Login Credentials</h4>
            <p><strong>Username:</strong> <span class="credentials">admin</span></p>
            <p><strong>Password:</strong> <span class="credentials">admin123</span></p>
            <p style="margin-top: 10px; font-size: 12px; color: #666;">
                <i class="fas fa-exclamation-triangle"></i> Please change the default password after first login
            </p>
        </div>
    </div>
</body>
</html>