<?php
// thank-you.php - Thank You Page after Form Submission
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get parameters from URL
$type = isset($_GET['type']) ? $_GET['type'] : 'enquiry';
$enquiry_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product_name = isset($_GET['product']) ? urldecode($_GET['product']) : '';

// If no enquiry ID, redirect to home
if ($enquiry_id == 0) {
    header('Location: index.php');
    exit;
}

$pageTitle = ($type === 'quote') ? 'Quote Request Received' : 'Enquiry Submitted';
$formType = ($type === 'quote') ? 'Quote Request' : 'Product Enquiry';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Blusolv</title>
    
    <?php include 'head.php'?>
    <style>
        .thank-you-container {
            max-width: 800px;
            margin: 80px auto;
            padding: 60px 40px;
            text-align: center;
            background: linear-gradient(135deg, var(--cream-beige) 0%, var(--ivory) 100%);
            border: 3px solid var(--champagne-gold);
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }

        .success-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 30px;
            background: linear-gradient(135deg, #28a745, #20c997);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scaleIn 0.5s ease-out;
        }

        .success-icon svg {
            width: 60px;
            height: 60px;
            stroke: white;
            stroke-width: 3;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
            animation: checkmark 0.8s ease-out 0.3s forwards;
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
        }

        @keyframes scaleIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes checkmark {
            to {
                stroke-dashoffset: 0;
            }
        }

        .thank-you-title {
            font-size: 3rem;
            font-weight: 300;
            color: var(--deep-navy);
            margin-bottom: 20px;
            letter-spacing: 3px;
            font-family: 'Cormorant Garamond', serif;
        }

        .thank-you-subtitle {
            font-size: 1.2rem;
            color: var(--slate-grey);
            margin-bottom: 40px;
            line-height: 1.8;
            font-family: 'Montserrat', sans-serif;
        }

        .details-box {
            background: white;
            padding: 35px;
            margin: 40px 0;
            border-left: 4px solid var(--champagne-gold);
            text-align: left;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .details-box h3 {
            font-size: 1.5rem;
            color: var(--deep-navy);
            margin-bottom: 25px;
            font-family: 'Cormorant Garamond', serif;
            letter-spacing: 2px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid var(--soft-taupe);
            font-family: 'Montserrat', sans-serif;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 500;
            color: var(--deep-navy);
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }

        .detail-value {
            color: var(--charcoal-grey);
            font-weight: 400;
            text-align: right;
        }

        .reference-badge {
            display: inline-block;
            background: linear-gradient(135deg, var(--deep-navy), var(--charcoal-grey));
            color: var(--cream-beige);
            padding: 15px 35px;
            margin: 30px 0;
            font-size: 1.1rem;
            font-weight: 500;
            letter-spacing: 2px;
            border: 2px solid var(--champagne-gold);
            font-family: 'Montserrat', sans-serif;
        }

        .info-message {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 5px;
            margin: 30px 0;
            border-left: 4px solid #17a2b8;
        }

        .info-message p {
            margin: 10px 0;
            color: var(--charcoal-grey);
            line-height: 1.8;
            font-family: 'Montserrat', sans-serif;
        }

        .info-message strong {
            color: var(--deep-navy);
        }

        .action-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 18px 45px;
            border: 2px solid transparent;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            letter-spacing: 2px;
            text-transform: uppercase;
            text-decoration: none;
            transition: all 0.4s ease;
            font-family: 'Montserrat', sans-serif;
            display: inline-block;
        }

        .btn-primary {
            background: var(--deep-navy);
            color: var(--cream-beige);
            border-color: var(--deep-navy);
        }

        .btn-primary:hover {
            background: var(--champagne-gold);
            color: var(--deep-navy);
            border-color: var(--champagne-gold);
            transform: translateY(-3px);
        }

        .btn-secondary {
            background: transparent;
            color: var(--deep-navy);
            border-color: var(--champagne-gold);
        }

        .btn-secondary:hover {
            background: var(--champagne-gold);
            color: var(--deep-navy);
            transform: translateY(-3px);
        }

        .timeline {
            margin: 40px 0;
            padding: 30px;
            background: white;
            border-radius: 5px;
            text-align: left;
        }

        .timeline h4 {
            font-size: 1.3rem;
            color: var(--deep-navy);
            margin-bottom: 25px;
            font-family: 'Cormorant Garamond', serif;
            text-align: center;
        }

        .timeline-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-left: 40px;
            position: relative;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 8px;
            width: 12px;
            height: 12px;
            background: var(--champagne-gold);
            border-radius: 50%;
            border: 3px solid var(--deep-navy);
        }

        .timeline-item::after {
            content: '';
            position: absolute;
            left: 15px;
            top: 20px;
            width: 2px;
            height: calc(100% + 10px);
            background: var(--soft-taupe);
        }

        .timeline-item:last-child::after {
            display: none;
        }

        .timeline-content {
            font-family: 'Montserrat', sans-serif;
        }

        .timeline-content strong {
            color: var(--deep-navy);
            display: block;
            margin-bottom: 5px;
        }

        .timeline-content span {
            color: var(--slate-grey);
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .thank-you-container {
                margin: 40px 20px;
                padding: 40px 25px;
            }

            .thank-you-title {
                font-size: 2rem;
            }

            .thank-you-subtitle {
                font-size: 1rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }

            .detail-item {
                flex-direction: column;
                gap: 8px;
            }

            .detail-value {
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'?>
    
    <div class="thank-you-container">
        <div class="success-icon">
            <svg viewBox="0 0 52 52">
                <path d="M14 27l7 7 16-16"/>
            </svg>
        </div>
        
        <h1 class="thank-you-title">Thank You!</h1>
        <p class="thank-you-subtitle">
            Your <?php echo strtolower($formType); ?> has been successfully submitted.<br>
            We appreciate your interest in our products.
        </p>
        
        <div class="reference-badge">
            Reference ID: #<?php echo str_pad($enquiry_id, 6, '0', STR_PAD_LEFT); ?>
        </div>
        
        <div class="details-box">
            <h3>Submission Details</h3>
            <div class="detail-item">
                <span class="detail-label">Request Type</span>
                <span class="detail-value"><?php echo $formType; ?></span>
            </div>
            <?php if (!empty($product_name)): ?>
            <div class="detail-item">
                <span class="detail-label">Product</span>
                <span class="detail-value"><?php echo htmlspecialchars($product_name); ?></span>
            </div>
            <?php endif; ?>
            <div class="detail-item">
                <span class="detail-label">Submission Date</span>
                <span class="detail-value"><?php echo date('d M Y, h:i A'); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Status</span>
                <span class="detail-value" style="color: #28a745; font-weight: 600;">✓ Received</span>
            </div>
        </div>
        
        <div class="info-message">
            <p><strong>📧 What happens next?</strong></p>
            <p>• You will receive a confirmation email shortly</p>
            <p>• Our sales team will review your <?php echo strtolower($formType); ?></p>
            <p>• We will contact you within <strong>24 hours</strong> with a response</p>
            <p>• Keep your reference ID for future communication</p>
        </div>
        
        <div class="timeline">
            <h4>Expected Timeline</h4>
            <div class="timeline-item">
                <div class="timeline-content">
                    <strong>Step 1: Confirmation</strong>
                    <span>Immediate - Check your email for confirmation</span>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content">
                    <strong>Step 2: Review</strong>
                    <span>Within 4 hours - Our team reviews your request</span>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content">
                    <strong>Step 3: Response</strong>
                    <span>Within 24 hours - We'll contact you with details</span>
                </div>
            </div>
            <?php if ($type === 'quote'): ?>
            <div class="timeline-item">
                <div class="timeline-content">
                    <strong>Step 4: Quote Delivery</strong>
                    <span>Within 48 hours - Detailed quotation sent</span>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="action-buttons">
            <a href="products.php" class="btn btn-primary">Browse More Products</a>
            <a href="index.php" class="btn btn-secondary">Back to Home</a>
        </div>
    </div>
    
    <?php include 'footer.php'?>
</body>
</html>