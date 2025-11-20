<?php
// ajax_submit_enquiry.php - Complete Updated Code for Existing Database
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in JSON response

// Database connection
$host = 'localhost';
$username = 'blusolv_db';
$password = 'blusolv_db';
$database = 'blusolv_db';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
    exit;
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");

// Get form data
$form_type = isset($_POST['form_type']) ? trim($_POST['form_type']) : 'enquiry';
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$product_name = isset($_POST['product_name']) ? trim($_POST['product_name']) : '';
$customer_name = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
$customer_email = isset($_POST['customer_email']) ? trim($_POST['customer_email']) : '';
$customer_phone = isset($_POST['customer_phone']) ? trim($_POST['customer_phone']) : '';
$company_name = isset($_POST['company_name']) ? trim($_POST['company_name']) : '';
$expected_quantity = isset($_POST['quantity']) ? trim($_POST['quantity']) : '';
$requirements = isset($_POST['requirements']) ? trim($_POST['requirements']) : '';

// Validate required fields
$errors = [];

if (empty($product_name)) {
    $errors[] = 'Product name is required';
}

if (empty($customer_name)) {
    $errors[] = 'Customer name is required';
}

if (empty($customer_email)) {
    $errors[] = 'Email address is required';
} elseif (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email address is required';
}

if (empty($customer_phone)) {
    $errors[] = 'Phone number is required';
}

if (empty($expected_quantity)) {
    $errors[] = 'Quantity is required';
}

if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'message' => implode(', ', $errors)
    ]);
    exit;
}

try {
    // Insert enquiry into existing database structure
    $sql = "INSERT INTO enquiries 
            (product_id, product_name, customer_name, customer_email, customer_phone, 
             company_name, expected_quantity, requirements, enquiry_date, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'new')";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param(
        "isssssss",
        $product_id,
        $product_name,
        $customer_name,
        $customer_email,
        $customer_phone,
        $company_name,
        $expected_quantity,
        $requirements
    );
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $enquiry_id = $conn->insert_id;
    $stmt->close();
    
    // Send email notification
    $admin_email = 'sales@blusolv.com'; // Admin email address
    $form_type_text = ($form_type === 'quote') ? 'Quote Request' : 'Product Enquiry';
    $subject = "New $form_type_text - $product_name (#$enquiry_id)";
    
    // HTML Email Template
    $html_message = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #1a2332, #3d4a5c); color: #f5f0e8; padding: 30px; text-align: center; }
            .header h1 { margin: 0; font-size: 28px; letter-spacing: 2px; }
            .content { background: #ffffff; padding: 30px; border: 2px solid #d4a574; }
            .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            .info-table tr { border-bottom: 1px solid #e8e3d8; }
            .info-table td { padding: 12px 8px; }
            .info-table td:first-child { font-weight: bold; color: #1a2332; width: 40%; }
            .info-table td:last-child { color: #555; }
            .requirements-box { background: #f5f0e8; padding: 15px; border-left: 3px solid #d4a574; margin: 15px 0; }
            .footer { text-align: center; padding: 20px; color: #888; font-size: 12px; }
            .badge { display: inline-block; background: #d4a574; color: #1a2332; padding: 5px 15px; border-radius: 3px; font-weight: bold; margin: 10px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🔔 New $form_type_text</h1>
                <span class='badge'>Enquiry ID: #$enquiry_id</span>
            </div>
            
            <div class='content'>
                <h2 style='color: #1a2332; border-bottom: 2px solid #d4a574; padding-bottom: 10px;'>Customer Details</h2>
                
                <table class='info-table'>
                    <tr>
                        <td>Product Name</td>
                        <td><strong>$product_name</strong></td>
                    </tr>
                    <tr>
                        <td>Customer Name</td>
                        <td>$customer_name</td>
                    </tr>
                    <tr>
                        <td>Email Address</td>
                        <td><a href='mailto:$customer_email'>$customer_email</a></td>
                    </tr>
                    <tr>
                        <td>Phone Number</td>
                        <td><a href='tel:$customer_phone'>$customer_phone</a></td>
                    </tr>";
    
    if (!empty($company_name)) {
        $html_message .= "
                    <tr>
                        <td>Company Name</td>
                        <td>$company_name</td>
                    </tr>";
    }
    
    $html_message .= "
                    <tr>
                        <td>Expected Quantity</td>
                        <td><strong>$expected_quantity</strong></td>
                    </tr>
                    <tr>
                        <td>Form Type</td>
                        <td><span class='badge' style='font-size: 12px;'>$form_type_text</span></td>
                    </tr>
                    <tr>
                        <td>Submitted On</td>
                        <td>" . date('d M Y, h:i A') . "</td>
                    </tr>
                </table>";
    
    if (!empty($requirements)) {
        $html_message .= "
                <h3 style='color: #1a2332; margin-top: 25px;'>Additional Requirements</h3>
                <div class='requirements-box'>
                    " . nl2br(htmlspecialchars($requirements)) . "
                </div>";
    }
    
    $html_message .= "
                <p style='margin-top: 30px; padding: 15px; background: #f9f9f9; border-left: 3px solid #d4a574;'>
                    <strong>⚡ Action Required:</strong> Please respond to this " . strtolower($form_type_text) . " as soon as possible.
                </p>
            </div>
            
            <div class='footer'>
                <p>This is an automated notification from Blusolv Product Enquiry System</p>
                <p>© " . date('Y') . " Blusolv. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Plain text version for email clients that don't support HTML
    $plain_message = "
========================================
NEW " . strtoupper($form_type_text) . " RECEIVED
========================================

Enquiry ID: #$enquiry_id
Date & Time: " . date('d M Y, h:i A') . "

----------------------------------------
CUSTOMER DETAILS
----------------------------------------
Product Name: $product_name
Customer Name: $customer_name
Email: $customer_email
Phone: $customer_phone
Company: " . (!empty($company_name) ? $company_name : 'Not provided') . "
Expected Quantity: $expected_quantity
Form Type: $form_type_text

" . (!empty($requirements) ? "
----------------------------------------
ADDITIONAL REQUIREMENTS
----------------------------------------
$requirements

" : "") . "
========================================
Please respond to this $form_type_text as soon as possible.

This is an automated notification from Blusolv Product Enquiry System.
    ";
    
    // Email headers for HTML email
    $boundary = md5(time());
    $headers = "From: Blusolv Enquiry System <noreply@blusolv.com>\r\n";
    $headers .= "Reply-To: $customer_email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";
    
    // Email body with both plain text and HTML
    $email_body = "--$boundary\r\n";
    $email_body .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $email_body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $email_body .= $plain_message . "\r\n";
    $email_body .= "--$boundary\r\n";
    $email_body .= "Content-Type: text/html; charset=UTF-8\r\n";
    $email_body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $email_body .= $html_message . "\r\n";
    $email_body .= "--$boundary--";
    
    // Send email to admin
    $email_sent = @mail($admin_email, $subject, $email_body, $headers);
    
    // Optional: Send confirmation email to customer
    $customer_subject = "Thank you for your " . strtolower($form_type_text) . " - Blusolv";
    $customer_html = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
            .header { background: linear-gradient(135deg, #1a2332, #3d4a5c); color: #f5f0e8; padding: 30px; text-align: center; border-radius: 5px 5px 0 0; }
            .header h1 { margin: 0; font-size: 24px; letter-spacing: 1px; }
            .content { background: #ffffff; padding: 30px; border-radius: 0 0 5px 5px; }
            .content p { margin: 15px 0; line-height: 1.8; }
            .highlight { background: #f5f0e8; padding: 15px; border-left: 3px solid #d4a574; margin: 20px 0; }
            .footer { text-align: center; padding: 20px; color: #888; font-size: 12px; }
            .button { display: inline-block; background: #d4a574; color: #1a2332; padding: 12px 30px; text-decoration: none; border-radius: 3px; margin: 15px 0; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>✓ Thank You for Your Interest!</h1>
            </div>
            <div class='content'>
                <p>Dear <strong>$customer_name</strong>,</p>
                
                <p>Thank you for your interest in <strong>$product_name</strong>.</p>
                
                <div class='highlight'>
                    <p style='margin: 5px 0;'><strong>Reference ID:</strong> #$enquiry_id</p>
                    <p style='margin: 5px 0;'><strong>Product:</strong> $product_name</p>
                    <p style='margin: 5px 0;'><strong>Quantity:</strong> $expected_quantity</p>
                </div>
                
                <p>We have successfully received your " . strtolower($form_type_text) . " and our sales team will review it shortly.</p>
                
                <p><strong>What happens next?</strong></p>
                <ul style='line-height: 2;'>
                    <li>Our team will review your request</li>
                    <li>We will contact you within <strong>24 hours</strong></li>
                    <li>" . ($form_type === 'quote' ? 'You will receive a detailed quotation' : 'We will provide all necessary information') . "</li>
                </ul>
                
                <p>If you have any urgent queries, please feel free to contact us directly at <strong>sales@blusolv.com</strong> or call us.</p>
                
                <p style='margin-top: 30px;'>Best regards,<br><strong>Blusolv Sales Team</strong></p>
            </div>
            <div class='footer'>
                <p>This is an automated confirmation email</p>
                <p>© " . date('Y') . " Blusolv. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $customer_headers = "From: Blusolv <noreply@blusolv.com>\r\n";
    $customer_headers .= "Reply-To: sales@blusolv.com\r\n";
    $customer_headers .= "MIME-Version: 1.0\r\n";
    $customer_headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // Send confirmation email to customer
    @mail($customer_email, $customer_subject, $customer_html, $customer_headers);
    
    // Success response
    echo json_encode([
        'success' => true,
        'message' => ($form_type === 'quote') 
            ? 'Quote request submitted successfully! We will contact you soon.' 
            : 'Enquiry submitted successfully! We will contact you soon.',
        'enquiry_id' => $enquiry_id,
        'email_sent' => $email_sent
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>