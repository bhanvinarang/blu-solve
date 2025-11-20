<?php
// product-detail.php - WITH SINGLE ENQUIRE NOW BUTTON
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Configuration
class Database {
    private $host = 'localhost';
    private $username = 'blusolv_db';
    private $password = 'blusolv_db';
    private $database = 'blusolv_db';
    public $connection;
    
    public function __construct() {
        $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database);
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }
}

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id == 0) {
    header('Location: products.php');
    exit;
}

// Fetch product details
$db = new Database();
$query = "SELECT 
            p.*,
            c.category_name,
            m.material_name
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.id
          LEFT JOIN materials m ON p.material_id = m.id
          WHERE p.id = ? AND p.status = 'active'";

$stmt = $db->connection->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header('Location: products.php');
    exit;
}

// Fix product image path
$productImage = $product['product_image'];
if (empty($productImage)) {
    $productImage = 'uploads/placeholder.jpg';
} elseif (strpos($productImage, 'uploads/') !== 0) {
    $productImage = 'uploads/' . basename($productImage);
}

// Parse gallery images
$galleryImages = [];
if (!empty($product['gallery_images'])) {
    $decoded = json_decode($product['gallery_images'], true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $galleryImages = $decoded;
    } else {
        $galleryImages = array_filter(array_map('trim', explode(',', $product['gallery_images'])));
    }
    
    foreach ($galleryImages as &$img) {
        if (!empty($img) && strpos($img, 'uploads/') !== 0) {
            $img = 'uploads/' . basename($img);
        }
    }
}

if (empty($galleryImages)) {
    $galleryImages = array_fill(0, 4, $productImage);
}

// Fetch related products
$related_query = "SELECT 
                    p.*,
                    c.category_name,
                    m.material_name
                  FROM products p
                  LEFT JOIN categories c ON p.category_id = c.id
                  LEFT JOIN materials m ON p.material_id = m.id
                  WHERE p.category_id = ? 
                  AND p.id != ? 
                  AND p.status = 'active'
                  ORDER BY p.sort_order ASC, p.created_at DESC
                  LIMIT 4";

$related_stmt = $db->connection->prepare($related_query);
$related_stmt->bind_param("ii", $product['category_id'], $product_id);
$related_stmt->execute();
$related_result = $related_stmt->get_result();
$related_products = [];
while ($row = $related_result->fetch_assoc()) {
    $relatedImage = $row['product_image'];
    if (empty($relatedImage)) {
        $relatedImage = 'uploads/placeholder.jpg';
    } elseif (strpos($relatedImage, 'uploads/') !== 0) {
        $relatedImage = 'uploads/' . basename($relatedImage);
    }
    $row['fixed_image'] = $relatedImage;
    $related_products[] = $row;
}

$pageTitle = !empty($product['meta_title']) ? $product['meta_title'] : $product['product_name'];
$pageDescription = !empty($product['meta_description']) ? $product['meta_description'] : substr($product['description'], 0, 160);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Product Details</title>
    
    <?php include 'head.php'?>
    <style>
.breadcrumb {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 0.85rem;
    letter-spacing: 1px;
    text-transform: uppercase;
    font-weight: 400;
    font-family: 'Montserrat', sans-serif;
}

.breadcrumb a {
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
}

.breadcrumb a:hover {
    color: var(--champagne-gold);
}

.breadcrumb span {
    color: var(--slate-grey);
}

.product-detail-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 60px 20px;
}

.product-main-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    margin-bottom: 80px;
}

.product-image-section {
    position: sticky;
    top: 20px;
    height: fit-content;
}

.main-image-container {
    width: 100%;
    height: 600px;
    position: relative;
    overflow: hidden;
    border: 2px solid var(--soft-taupe);
    margin-bottom: 20px;
    cursor: zoom-in;
    background: white;
    user-select: none;
}

.main-image-container.zooming {
    cursor: grab;
}

.main-image-container.zooming:active {
    cursor: grabbing;
}

.main-product-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: transform 0.3s ease;
    transform-origin: center center;
    pointer-events: none;
}

.zoom-controls {
    position: absolute;
    bottom: 15px;
    right: 15px;
    display: flex;
    gap: 10px;
    z-index: 10;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.main-image-container:hover .zoom-controls {
    opacity: 1;
}

.zoom-btn {
    background: rgba(26, 35, 50, 0.95);
    color: #f5f0e8;
    border: 1px solid #d4a574;
    width: 40px;
    height: 40px;
    border-radius: 0;
    cursor: pointer;
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.zoom-btn:hover {
    background: #d4a574;
    color: #1a2332;
    transform: scale(1.15);
}

.zoom-indicator {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(26, 35, 50, 0.95);
    color: #f5f0e8;
    padding: 10px 18px;
    font-size: 13px;
    letter-spacing: 1.5px;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
    z-index: 10;
    border: 1px solid #d4a574;
}

.zoom-indicator.show {
    opacity: 1;
}

.zoom-instructions {
    position: absolute;
    bottom: 15px;
    left: 15px;
    background: rgba(26, 35, 50, 0.95);
    color: #f5f0e8;
    padding: 8px 15px;
    font-size: 11px;
    letter-spacing: 1px;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
    z-index: 9;
    border: 1px solid #d4a574;
}

.main-image-container:hover .zoom-instructions {
    opacity: 1;
}

.product-thumbnails {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
}

.thumbnail {
    width: 100%;
    height: 100px;
    object-fit: cover;
    cursor: pointer;
    border: 2px solid var(--soft-taupe);
    transition: all 0.3s ease;
    background: white;
}

.thumbnail:hover,
.thumbnail.active {
    border-color: var(--champagne-gold);
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(212, 165, 116, 0.3);
}

.product-info-section {
    padding: 20px 0;
}

.product-code {
    color: var(--slate-grey);
    font-size: 0.85rem;
    margin-bottom: 15px;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    font-weight: 400;
    font-family: 'Montserrat', sans-serif;
}

.product-title {
    font-size: 3rem;
    font-weight: 300;
    color: var(--deep-navy);
    margin-bottom: 25px;
    line-height: 1.2;
    letter-spacing: 3px;
    font-family: 'Cormorant Garamond', serif;
}

.product-meta {
    display: flex;
    gap: 15px;
    margin-bottom: 35px;
    flex-wrap: wrap;
}

.meta-badge {
    background: linear-gradient(135deg, var(--deep-navy), var(--charcoal-grey));
    color: var(--cream-beige);
    padding: 10px 25px;
    border-radius: 0;
    font-size: 0.8rem;
    font-weight: 400;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    border: 1px solid var(--champagne-gold);
    font-family: 'Montserrat', sans-serif;
}

.product-description {
    font-size: 1rem;
    line-height: 2;
    color: var(--charcoal-grey);
    margin-bottom: 40px;
    padding: 30px;
    background: linear-gradient(135deg, var(--cream-beige) 0%, var(--ivory) 100%);
    border-left: 3px solid var(--champagne-gold);
    font-weight: 300;
    font-family: 'Montserrat', sans-serif;
}

.basic-info-table {
    width: 100%;
    margin-bottom: 40px;
    border-collapse: collapse;
    background: white;
    border: 1px solid var(--soft-taupe);
}

.basic-info-table tr {
    border-bottom: 1px solid var(--soft-taupe);
    transition: background 0.3s ease;
}

.basic-info-table tr:hover {
    background: var(--cream-beige);
}

.basic-info-table td {
    padding: 18px 20px;
    font-size: 0.95rem;
    font-family: 'Montserrat', sans-serif;
}

.basic-info-table td:first-child {
    font-weight: 500;
    color: var(--deep-navy);
    width: 220px;
    letter-spacing: 1px;
    text-transform: uppercase;
    font-size: 0.85rem;
}

.basic-info-table td:last-child {
    color: var(--charcoal-grey);
    font-weight: 300;
}

.action-buttons {
    display: flex;
    gap: 20px;
    margin-top: 40px;
}

.enquiry-btn {
    padding: 18px 50px;
    border: 2px solid transparent;
    border-radius: 0;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    letter-spacing: 2px;
    text-transform: uppercase;
    transition: all 0.4s ease;
    font-family: 'Montserrat', sans-serif;
    width: 100%;
    background: var(--deep-navy);
    color: var(--cream-beige);
    border-color: var(--deep-navy);
    position: relative;
    overflow: hidden;
}

.enquiry-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: var(--champagne-gold);
    transition: left 0.4s ease;
    z-index: 0;
}

.enquiry-btn:hover::before {
    left: 0;
}

.enquiry-btn:hover {
    color: var(--deep-navy);
    border-color: var(--champagne-gold);
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(212, 165, 116, 0.4);
}

.enquiry-btn span {
    position: relative;
    z-index: 1;
}

.specifications-section {
    margin-top: 50px;
    padding: 40px;
    background: linear-gradient(135deg, var(--cream-beige) 0%, var(--ivory) 100%);
    border: 2px solid var(--soft-taupe);
}

.specifications-title {
    font-size: 1.8rem;
    font-weight: 400;
    color: var(--deep-navy);
    margin-bottom: 30px;
    letter-spacing: 2px;
    text-transform: uppercase;
    font-family: 'Cormorant Garamond', serif;
}

.specifications-content {
    background: white;
    padding: 30px;
    border-left: 3px solid var(--champagne-gold);
    font-size: 1rem;
    line-height: 2;
    color: var(--charcoal-grey);
    font-family: 'Montserrat', sans-serif;
    white-space: pre-line;
}

.related-products-section {
    margin-top: 100px;
    padding-top: 80px;
    border-top: 2px solid var(--soft-taupe);
}

.section-title {
    font-size: 2.8rem;
    font-weight: 300;
    color: var(--deep-navy);
    text-align: center;
    margin-bottom: 60px;
    letter-spacing: 3px;
    text-transform: uppercase;
    font-family: 'Cormorant Garamond', serif;
}

.related-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 35px;
}

.related-product-card {
    background: var(--cream-beige);
    border: 2px solid var(--soft-taupe);
    text-align: center;
    transition: all 0.5s ease;
    cursor: pointer;
    overflow: hidden;
}

.related-product-card:hover {
    transform: translateY(-12px);
    box-shadow: 0 25px 60px rgba(0,0,0,0.2);
    border-color: var(--champagne-gold);
}

.related-product-image {
    width: 100%;
    height: 250px;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.related-product-card:hover .related-product-image {
    transform: scale(1.1);
}

.related-product-title {
    font-size: 1.3rem;
    font-weight: 400;
    color: var(--deep-navy);
    margin: 20px 0 10px;
    font-family: 'Cormorant Garamond', serif;
    letter-spacing: 1.5px;
    padding: 0 20px;
}

.related-product-category {
    color: var(--slate-grey);
    font-size: 0.85rem;
    margin-bottom: 20px;
    letter-spacing: 1px;
    text-transform: uppercase;
    font-family: 'Montserrat', sans-serif;
}

.view-details-btn {
    background: var(--deep-navy);
    color: var(--cream-beige);
    padding: 12px 30px;
    border: 2px solid var(--deep-navy);
    cursor: pointer;
    font-size: 0.8rem;
    font-weight: 500;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    text-decoration: none;
    display: inline-block;
    transition: all 0.4s ease;
    margin-bottom: 20px;
    font-family: 'Montserrat', sans-serif;
}

.view-details-btn:hover {
    background: var(--champagne-gold);
    color: var(--deep-navy);
    border-color: var(--champagne-gold);
}

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(26, 35, 50, 0.95);
    overflow-y: auto;
}

.modal-content {
    background: linear-gradient(135deg, var(--cream-beige) 0%, var(--ivory) 100%);
    margin: 3% auto;
    padding: 50px;
    width: 90%;
    max-width: 700px;
    position: relative;
    border: 2px solid var(--champagne-gold);
}

.modal-title {
    font-size: 2.5rem;
    color: var(--deep-navy);
    margin-bottom: 15px;
    text-align: center;
    letter-spacing: 3px;
    text-transform: uppercase;
    font-weight: 300;
    font-family: 'Cormorant Garamond', serif;
}

.modal-subtitle {
    color: var(--slate-grey);
    margin-bottom: 40px;
    font-size: 0.95rem;
    text-align: center;
    line-height: 1.6;
    font-family: 'Montserrat', sans-serif;
}

.close {
    position: absolute;
    right: 30px;
    top: 25px;
    color: var(--slate-grey);
    font-size: 35px;
    font-weight: 300;
    cursor: pointer;
    transition: all 0.3s ease;
}

.close:hover {
    color: var(--champagne-gold);
    transform: rotate(90deg);
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
    font-family: 'Montserrat', sans-serif;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 14px 18px;
    border: 2px solid var(--soft-taupe);
    font-size: 15px;
    background-color: white;
    color: var(--charcoal-grey);
    font-family: 'Montserrat', sans-serif;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--champagne-gold);
}

.form-group textarea {
    height: 130px;
    resize: vertical;
}

.submit-btn {
    background: var(--deep-navy);
    color: var(--cream-beige);
    padding: 18px 50px;
    border: 2px solid var(--deep-navy);
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    width: 100%;
    letter-spacing: 2px;
    text-transform: uppercase;
    transition: all 0.4s ease;
    font-family: 'Montserrat', sans-serif;
    position: relative;
    overflow: hidden;
}

.submit-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: var(--champagne-gold);
    transition: left 0.4s ease;
    z-index: 0;
}

.submit-btn:hover::before {
    left: 0;
}

.submit-btn:hover {
    color: var(--deep-navy);
    border-color: var(--champagne-gold);
}

.submit-btn span {
    position: relative;
    z-index: 1;
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

@media (max-width: 968px) {
    .product-main-section {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    
    .product-image-section {
        position: relative;
        top: 0;
    }
    
    .main-image-container {
        height: 450px;
    }
    
    .product-title {
        font-size: 2.2rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .product-title {
        font-size: 1.8rem;
    }
    
    .product-thumbnails {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .main-image-container {
        height: 350px;
    }
    
    .modal-content {
        padding: 30px 20px;
        margin: 10% auto;
    }
}
    </style>
</head>
<body>
    <?php include 'header.php'?>
    
    <section class="hero-section">
        <div class="hero-content">
            <div class="breadcrumb">
                <a href="index.php">Home</a>
                <span>›</span>
                <a href="products.php">Products</a>
                <span>›</span>
                <span><?php echo htmlspecialchars($product['product_name']); ?></span>
            </div>
        </div>
    </section>
    
    <div class="product-detail-container">
        <div class="product-main-section">
            <div class="product-image-section">
                <div class="main-image-container" id="imageContainer">
                    <img src="<?php echo htmlspecialchars($productImage); ?>" 
                         alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                         class="main-product-image" 
                         id="mainImage"
                         onerror="this.src='uploads/placeholder.jpg'">
                    
                    <div class="zoom-controls">
                        <button class="zoom-btn" id="zoomIn" title="Zoom In">+</button>
                        <button class="zoom-btn" id="zoomOut" title="Zoom Out">−</button>
                        <button class="zoom-btn" id="zoomReset" title="Reset">⟲</button>
                    </div>
                    
                    <div class="zoom-indicator" id="zoomIndicator">100%</div>
                    
                    <div class="zoom-instructions">
                        Ctrl+Scroll to Zoom • Click to Zoom • Drag to Pan
                    </div>
                </div>
                
                <div class="product-thumbnails">
                    <?php foreach (array_slice($galleryImages, 0, 4) as $index => $galleryImg): ?>
                    <img src="<?php echo htmlspecialchars($galleryImg); ?>" 
                         alt="Thumbnail <?php echo $index + 1; ?>"
                         class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" 
                         onclick="changeImage(this)"
                         onerror="this.src='uploads/placeholder.jpg'">
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="product-info-section">
                <div class="product-code">
                    Code: <?php echo htmlspecialchars(!empty($product['product_code']) ? $product['product_code'] : 'N/A'); ?>
                </div>
                
                <h1 class="product-title">
                    <?php echo htmlspecialchars($product['product_name']); ?>
                </h1>
                
                <div class="product-meta">
                    <span class="meta-badge"><?php echo htmlspecialchars(!empty($product['category_name']) ? $product['category_name'] : 'Uncategorized'); ?></span>
                    <span class="meta-badge"><?php echo htmlspecialchars(!empty($product['material_name']) ? $product['material_name'] : 'N/A'); ?></span>
                    <?php if (!empty($product['product_color'])): ?>
                    <span class="meta-badge"><?php echo htmlspecialchars($product['product_color']); ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="product-description">
                    <p>
                        <?php 
                        $description = !empty($product['description']) ? $product['description'] : 'It is available with many pockets, two or three partitions, various sizes, designs, color, pattern along with zipper to keep the things safe.';
                        echo nl2br(htmlspecialchars($description)); 
                        ?>
                    </p>
                </div>
                
                <table class="basic-info-table">
                    <tr>
                        <td>Category</td>
                        <td><?php echo htmlspecialchars(!empty($product['category_name']) ? $product['category_name'] : 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <td>Material</td>
                        <td><?php echo htmlspecialchars(!empty($product['material_name']) ? $product['material_name'] : 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <td>Product Code</td>
                        <td><?php echo htmlspecialchars(!empty($product['product_code']) ? $product['product_code'] : 'N/A'); ?></td>
                    </tr>
                    <?php if (!empty($product['product_color'])): ?>
                    <tr>
                        <td>Color</td>
                        <td><?php echo htmlspecialchars($product['product_color']); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
                
                <div class="action-buttons">
                    <button class="enquiry-btn" onclick="openEnquiry()">
                        <span>Enquire Now</span>
                    </button>
                </div>
                
                <?php if (!empty($product['specifications'])): ?>
                <div class="specifications-section">
                    <h3 class="specifications-title">Specifications</h3>
                    <div class="specifications-content">
                        <?php echo nl2br(htmlspecialchars($product['specifications'])); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (count($related_products) > 0): ?>
        <div class="related-products-section">
            <h2 class="section-title">Related Products</h2>
            <div class="related-products-grid">
                <?php foreach ($related_products as $related): ?>
                <div class="related-product-card" onclick="window.location.href='product-detail.php?id=<?php echo $related['id']; ?>'">
                    <img src="<?php echo htmlspecialchars($related['fixed_image']); ?>" 
                         alt="<?php echo htmlspecialchars($related['product_name']); ?>" 
                         class="related-product-image"
                         onerror="this.src='uploads/placeholder.jpg'">
                    <div class="related-product-title">
                        <?php echo htmlspecialchars($related['product_name']); ?>
                    </div>
                    <div class="related-product-category">
                        <?php echo htmlspecialchars(!empty($related['category_name']) ? $related['category_name'] : 'Uncategorized'); ?>
                    </div>
                    <a href="product-detail.php?id=<?php echo $related['id']; ?>" class="view-details-btn">View Details</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Enquiry Modal -->
    <div id="enquiryModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEnquiry()">&times;</span>
            <h2 class="modal-title">Product Enquiry</h2>
            <p class="modal-subtitle">Interested in this product? Fill out the form below and we'll get back to you soon.</p>
            
            <form id="enquiryForm">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>">
                <input type="hidden" name="form_type" value="enquiry">
                
                <div class="form-group" style="background: linear-gradient(135deg, var(--champagne-gold) 0%, var(--soft-taupe) 100%); padding: 15px; margin-bottom: 25px; border-left: 4px solid var(--deep-navy);">
                    <p style="margin: 0; color: var(--deep-navy); font-weight: 600; font-size: 1rem;">Selected Product: <span style="color: var(--charcoal-grey); font-weight: 400;"><?php echo htmlspecialchars($product['product_name']); ?></span></p>
                </div>
                
                <div class="form-group">
                    <label for="enquiry_customer_name">Full Name *</label>
                    <input type="text" id="enquiry_customer_name" name="customer_name" required placeholder="Enter your full name">
                </div>
                
                <div class="form-group">
                    <label for="enquiry_customer_email">Email Address *</label>
                    <input type="email" id="enquiry_customer_email" name="customer_email" required placeholder="your.email@example.com">
                </div>
                
                <div class="form-group">
                    <label for="enquiry_customer_phone">Phone Number *</label>
                    <input type="tel" id="enquiry_customer_phone" name="customer_phone" required placeholder="+91 XXXXXXXXXX">
                </div>
                
                <div class="form-group">
                    <label for="enquiry_company_name">Company Name</label>
                    <input type="text" id="enquiry_company_name" name="company_name" placeholder="Your company name (optional)">
                </div>
                
                <div class="form-group">
                    <label for="enquiry_quantity">Expected Quantity *</label>
                    <select id="enquiry_quantity" name="quantity" required>
                        <option value="">Select Quantity Range</option>
                        <option value="1-50">1-50 pieces</option>
                        <option value="51-100">51-100 pieces</option>
                        <option value="101-500">101-500 pieces</option>
                        <option value="501-1000">501-1000 pieces</option>
                        <option value="1000+">1000+ pieces</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="enquiry_requirements">Additional Requirements</label>
                    <textarea id="enquiry_requirements" name="requirements" placeholder="Please describe any specific requirements, customizations, or questions..."></textarea>
                </div>
                
                <button type="submit" class="submit-btn">
                    <span>Submit Enquiry</span>
                </button>
            </form>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        (function() {
            let currentZoom = 1;
            let isDragging = false;
            let startX = 0, startY = 0;
            let translateX = 0, translateY = 0;
            const minZoom = 1;
            const maxZoom = 3;
            const zoomStep = 0.1;

            function initImageZoom() {
                const mainImage = document.getElementById('mainImage');
                const imageContainer = document.getElementById('imageContainer');
                
                if (!mainImage || !imageContainer) return;

                function updateZoomIndicator() {
                    const indicator = document.getElementById('zoomIndicator');
                    if (indicator) {
                        indicator.textContent = Math.round(currentZoom * 100) + '%';
                        indicator.classList.add('show');
                        setTimeout(() => indicator.classList.remove('show'), 1500);
                    }
                }

                function applyTransform() {
                    mainImage.style.transform = 'scale(' + currentZoom + ') translate(' + translateX + 'px, ' + translateY + 'px)';
                    imageContainer.classList.toggle('zooming', currentZoom > 1);
                }

                function zoom(delta, clientX, clientY) {
                    const rect = imageContainer.getBoundingClientRect();
                    const x = clientX - rect.left;
                    const y = clientY - rect.top;
                    
                    const oldZoom = currentZoom;
                    currentZoom += delta;
                    currentZoom = Math.max(minZoom, Math.min(maxZoom, currentZoom));
                    
                    if (currentZoom !== oldZoom) {
                        const zoomRatio = currentZoom / oldZoom;
                        translateX = (translateX - x / oldZoom) * zoomRatio + x / currentZoom;
                        translateY = (translateY - y / oldZoom) * zoomRatio + y / currentZoom;
                        
                        constrainTranslation();
                        applyTransform();
                        updateZoomIndicator();
                    }
                }

                function constrainTranslation() {
                    if (currentZoom <= 1) {
                        translateX = 0;
                        translateY = 0;
                        return;
                    }

                    const rect = imageContainer.getBoundingClientRect();
                    const scaledWidth = rect.width * currentZoom;
                    const scaledHeight = rect.height * currentZoom;
                    
                    const maxTranslateX = (scaledWidth - rect.width) / (2 * currentZoom);
                    const maxTranslateY = (scaledHeight - rect.height) / (2 * currentZoom);
                    
                    translateX = Math.max(-maxTranslateX, Math.min(maxTranslateX, translateX));
                    translateY = Math.max(-maxTranslateY, Math.min(maxTranslateY, translateY));
                }

                window.resetZoom = function() {
                    currentZoom = 1;
                    translateX = 0;
                    translateY = 0;
                    applyTransform();
                    updateZoomIndicator();
                }

                imageContainer.addEventListener('wheel', function(e) {
                    if (e.ctrlKey || e.metaKey) {
                        e.preventDefault();
                        const delta = e.deltaY > 0 ? -zoomStep : zoomStep;
                        zoom(delta, e.clientX, e.clientY);
                    }
                }, { passive: false });

                imageContainer.addEventListener('click', function(e) {
                    if (e.target.classList.contains('zoom-btn') || e.target.closest('.zoom-btn')) return;
                    
                    if (currentZoom === 1) {
                        zoom(1, e.clientX, e.clientY);
                    } else {
                        resetZoom();
                    }
                });

                imageContainer.addEventListener('mousedown', function(e) {
                    if (currentZoom > 1 && e.button === 0 && !e.target.classList.contains('zoom-btn')) {
                        isDragging = true;
                        startX = e.clientX - translateX * currentZoom;
                        startY = e.clientY - translateY * currentZoom;
                        e.preventDefault();
                    }
                });

                document.addEventListener('mousemove', function(e) {
                    if (isDragging) {
                        translateX = (e.clientX - startX) / currentZoom;
                        translateY = (e.clientY - startY) / currentZoom;
                        constrainTranslation();
                        applyTransform();
                    }
                });

                document.addEventListener('mouseup', function() {
                    isDragging = false;
                });

                let touchStartDistance = 0;
                let touchStartZoom = 1;

                imageContainer.addEventListener('touchstart', function(e) {
                    if (e.touches.length === 2) {
                        e.preventDefault();
                        const touch1 = e.touches[0];
                        const touch2 = e.touches[1];
                        touchStartDistance = Math.hypot(
                            touch2.clientX - touch1.clientX,
                            touch2.clientY - touch1.clientY
                        );
                        touchStartZoom = currentZoom;
                    }
                }, { passive: false });

                imageContainer.addEventListener('touchmove', function(e) {
                    if (e.touches.length === 2) {
                        e.preventDefault();
                        const touch1 = e.touches[0];
                        const touch2 = e.touches[1];
                        const currentDistance = Math.hypot(
                            touch2.clientX - touch1.clientX,
                            touch2.clientY - touch1.clientY
                        );
                        
                        const scale = currentDistance / touchStartDistance;
                        currentZoom = touchStartZoom * scale;
                        currentZoom = Math.max(minZoom, Math.min(maxZoom, currentZoom));
                        
                        constrainTranslation();
                        applyTransform();
                        updateZoomIndicator();
                    }
                }, { passive: false });

                document.getElementById('zoomIn').addEventListener('click', function(e) {
                    e.stopPropagation();
                    const rect = imageContainer.getBoundingClientRect();
                    zoom(zoomStep * 3, rect.left + rect.width / 2, rect.top + rect.height / 2);
                });

                document.getElementById('zoomOut').addEventListener('click', function(e) {
                    e.stopPropagation();
                    const rect = imageContainer.getBoundingClientRect();
                    zoom(-zoomStep * 3, rect.left + rect.width / 2, rect.top + rect.height / 2);
                });

                document.getElementById('zoomReset').addEventListener('click', function(e) {
                    e.stopPropagation();
                    resetZoom();
                });

                document.addEventListener('keydown', function(e) {
                    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
                    
                    const rect = imageContainer.getBoundingClientRect();
                    const centerX = rect.left + rect.width / 2;
                    const centerY = rect.top + rect.height / 2;
                    
                    if (e.key === '+' || e.key === '=') {
                        zoom(zoomStep * 3, centerX, centerY);
                        e.preventDefault();
                    } else if (e.key === '-' || e.key === '_') {
                        zoom(-zoomStep * 3, centerX, centerY);
                        e.preventDefault();
                    } else if (e.key === '0') {
                        resetZoom();
                        e.preventDefault();
                    }
                });

                console.log('Image zoom initialized');
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initImageZoom);
            } else {
                initImageZoom();
            }
        })();

        $(document).ready(function() {
            window.changeImage = function(thumbnail) {
                const mainImage = document.getElementById('mainImage');
                
                if (mainImage && thumbnail) {
                    mainImage.src = thumbnail.src;
                    
                    if (typeof window.resetZoom === 'function') {
                        window.resetZoom();
                    }
                    
                    document.querySelectorAll('.thumbnail').forEach(thumb => {
                        thumb.classList.remove('active');
                    });
                    thumbnail.classList.add('active');
                }
            }
            
            const firstThumbnail = document.querySelector('.thumbnail');
            if (firstThumbnail) {
                firstThumbnail.classList.add('active');
            }
            
            document.addEventListener('keydown', function(e) {
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
                
                const thumbnails = Array.from(document.querySelectorAll('.thumbnail'));
                const activeIndex = thumbnails.findIndex(t => t.classList.contains('active'));
                
                if (e.key === 'ArrowRight' && activeIndex < thumbnails.length - 1) {
                    changeImage(thumbnails[activeIndex + 1]);
                    e.preventDefault();
                } else if (e.key === 'ArrowLeft' && activeIndex > 0) {
                    changeImage(thumbnails[activeIndex - 1]);
                    e.preventDefault();
                }
            });
            
            // Enquiry Modal Functions
            const enquiryModal = document.getElementById('enquiryModal');
            
            window.openEnquiry = function() {
                if (enquiryModal) {
                    enquiryModal.style.display = 'block';
                    document.body.style.overflow = 'hidden';
                }
            }
            
            window.closeEnquiry = function() {
                if (enquiryModal) {
                    enquiryModal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            }
            
            // Close modal on outside click
            window.onclick = function(event) {
                if (event.target == enquiryModal) {
                    closeEnquiry();
                }
            }
            
            // Close modal on ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    if (enquiryModal && enquiryModal.style.display === 'block') {
                        closeEnquiry();
                    }
                }
            });
            
            // Enquiry Form Submission
            $('#enquiryForm').on('submit', function(e) {
                e.preventDefault();
                
                const submitBtn = $(this).find('.submit-btn');
                const originalText = submitBtn.find('span').text();
                
                submitBtn.prop('disabled', true).find('span').text('Submitting...');
                
                const formData = $(this).serialize();
                
                $.ajax({
                    url: 'ajax_submit_enquiry.php',
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            alert('Thank you for your enquiry! We will contact you soon.');
                            closeEnquiry();
                            $('#enquiryForm')[0].reset();
                        } else {
                            alert('Error: ' + response.message);
                        }
                        submitBtn.prop('disabled', false).find('span').text(originalText);
                    },
                    error: function() {
                        alert('Error submitting enquiry. Please try again.');
                        submitBtn.prop('disabled', false).find('span').text(originalText);
                    }
                });
            });
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
    
    <?php include 'footer.php'?>
</body>
</html>
<?php $db->connection->close(); ?>