<?php
// ajax_filter_products.php - FIXED FOR YOUR DATABASE STRUCTURE
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Database connection
$host = 'localhost';
$username = 'blusolv_db';
$password = 'blusolv_db';
$database = 'blusolv_db';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]);
    exit;
}

// Get filter parameters
$category = isset($_POST['category']) ? trim($_POST['category']) : '';
$material = isset($_POST['material']) ? trim($_POST['material']) : '';
$requirement = isset($_POST['requirement']) ? trim($_POST['requirement']) : '';

// Build SQL query - UPDATED FIELD NAMES TO MATCH YOUR DATABASE
$sql = "SELECT 
            p.id,
            p.product_name as name,
            p.description,
            p.price,
            p.product_image,
            p.gallery_images,
            p.product_code,
            p.nature_of_requirement,
            c.category_name as category,
            m.material_name as material,
            p.status
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN materials m ON p.material_id = m.id
        WHERE p.status = 'active'";

$conditions = [];
$params = [];
$types = '';

// Apply category filter
if (!empty($category)) {
    $conditions[] = "c.category_name = ?";
    $params[] = $category;
    $types .= 's';
}

// Apply material filter
if (!empty($material)) {
    $conditions[] = "m.material_name = ?";
    $params[] = $material;
    $types .= 's';
}

// Apply nature of requirement filter
if (!empty($requirement)) {
    $conditions[] = "p.nature_of_requirement = ?";
    $params[] = $requirement;
    $types .= 's';
}

if (!empty($conditions)) {
    $sql .= " AND " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY p.sort_order ASC, p.created_at DESC LIMIT 100";

try {
    if (!empty($params)) {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        
        $stmt->bind_param($types, ...$params);
        
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($sql);
        if (!$result) {
            throw new Exception('Query failed: ' . $conn->error);
        }
    }
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        // Handle product image
        $imagePath = $row['product_image'];
        
        // If image path is empty or null, use placeholder
        if (empty($imagePath)) {
            $imagePath = 'uploads/placeholder.jpg';
        } else {
            // Clean up the path
            $imagePath = trim($imagePath);
            
            // If path doesn't start with uploads/, add it
            if (strpos($imagePath, 'uploads/') !== 0 && !empty($imagePath)) {
                // Check if it's a full path
                if (strpos($imagePath, '/') !== false) {
                    // Extract filename from path
                    $filename = basename($imagePath);
                    $imagePath = 'uploads/' . $filename;
                } else {
                    // It's just a filename
                    $imagePath = 'uploads/' . $imagePath;
                }
            }
        }
        
        // Handle gallery images
        $galleryImages = [];
        if (!empty($row['gallery_images'])) {
            // Assuming gallery_images is stored as JSON or comma-separated
            $galleryData = $row['gallery_images'];
            
            // Try to decode as JSON first
            $decoded = json_decode($galleryData, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $galleryImages = $decoded;
            } else {
                // Try comma-separated
                $galleryImages = array_filter(array_map('trim', explode(',', $galleryData)));
            }
            
            // Clean gallery image paths
            foreach ($galleryImages as &$img) {
                if (!empty($img) && strpos($img, 'uploads/') !== 0) {
                    $img = 'uploads/' . basename($img);
                }
            }
        }
        
        $products[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'] ?? 'Unnamed Product',
            'description' => $row['description'] ?? '',
            'price' => $row['price'] ?? 0,
            'image' => $imagePath,
            'gallery_images' => $galleryImages,
            'product_code' => $row['product_code'] ?? '',
            'category' => $row['category'] ?? 'Uncategorized',
            'material' => $row['material'] ?? 'N/A',
            'nature_of_requirement' => $row['nature_of_requirement'] ?? ''
        ];
    }
    
    echo json_encode([
        'success' => true,
        'products' => $products,
        'count' => count($products),
        'filters_applied' => [
            'category' => $category,
            'material' => $material,
            'requirement' => $requirement
        ]
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>