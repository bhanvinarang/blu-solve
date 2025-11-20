<?php
// debug_product_images.php - UPDATED FOR YOUR DATABASE STRUCTURE
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = 'localhost';
$username = 'blusolv_db';
$password = 'blusolv_db';
$database = 'blusolv_db';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>🔍 Product Image Debug Information</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; background: white; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    th { background-color: #667eea; color: white; font-weight: bold; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    tr:hover { background-color: #f0f0f0; }
    .success { color: #28a745; font-weight: bold; }
    .error { color: #dc3545; font-weight: bold; }
    .warning { color: #ffc107; font-weight: bold; }
    img { max-width: 80px; max-height: 80px; border: 2px solid #ddd; border-radius: 5px; }
    .btn { display: inline-block; padding: 10px 20px; margin: 10px 5px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
    .btn:hover { background: #5a67d8; }
    pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
</style>";

echo "<div class='container'>";

// Check database structure
echo "<h3>📊 Database Structure</h3>";
$structure = $conn->query("DESCRIBE products");
if ($structure) {
    echo "<table>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $structure->fetch_assoc()) {
        $highlight = in_array($row['Field'], ['product_image', 'gallery_images', 'category_id', 'material_id']) ? "style='background: #ffffcc;'" : "";
        echo "<tr $highlight>";
        echo "<td><strong>" . $row['Field'] . "</strong></td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Check products with images
echo "<h3>📦 Products with Images</h3>";
$result = $conn->query("SELECT 
    p.id, 
    p.product_name, 
    p.product_image, 
    p.gallery_images,
    p.product_code,
    c.category_name,
    m.material_name,
    p.status
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
LEFT JOIN materials m ON p.material_id = m.id
ORDER BY p.id DESC
LIMIT 20");

if ($result && $result->num_rows > 0) {
    echo "<p class='success'>✅ Found " . $result->num_rows . " products</p>";
    
    echo "<table>";
    echo "<tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Category</th>
            <th>Material</th>
            <th>Product Image</th>
            <th>File Exists?</th>
            <th>Preview</th>
            <th>Status</th>
          </tr>";
    
    while ($row = $result->fetch_assoc()) {
        $imagePath = $row['product_image'];
        $fileExists = false;
        $displayPath = '';
        
        // Check if file exists
        if (!empty($imagePath)) {
            $possiblePaths = [
                $imagePath,
                'uploads/' . basename($imagePath),
                '../uploads/' . basename($imagePath)
            ];
            
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $fileExists = true;
                    $displayPath = $path;
                    break;
                }
            }
        }
        
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td><strong>" . htmlspecialchars($row['product_name']) . "</strong><br><small>" . htmlspecialchars($row['product_code'] ?? 'No Code') . "</small></td>";
        echo "<td>" . htmlspecialchars($row['category_name'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($row['material_name'] ?? 'N/A') . "</td>";
        echo "<td><small>" . htmlspecialchars($imagePath ?? 'NULL') . "</small></td>";
        
        if (empty($imagePath)) {
            echo "<td class='warning'>⚠️ Empty</td>";
            echo "<td class='warning'>No image set</td>";
        } elseif ($fileExists) {
            echo "<td class='success'>✅ YES</td>";
            echo "<td><img src='" . htmlspecialchars($displayPath) . "' alt='Preview' onerror='this.src=\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80'%3E%3Crect fill='%23ddd' width='80' height='80'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' dy='.3em' fill='%23999'%3EERROR%3C/text%3E%3C/svg%3E\"'></td>";
        } else {
            echo "<td class='error'>❌ NO</td>";
            echo "<td class='error'>Not found</td>";
        }
        
        echo "<td>" . ($row['status'] == 'active' ? '🟢 Active' : '🔴 Inactive') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p class='error'>❌ No products found in database</p>";
}

// Statistics
echo "<h3>📈 Statistics</h3>";
$stats = [
    'Total Products' => $conn->query("SELECT COUNT(*) as cnt FROM products")->fetch_assoc()['cnt'],
    'Active Products' => $conn->query("SELECT COUNT(*) as cnt FROM products WHERE status = 'active'")->fetch_assoc()['cnt'],
    'Products with Images' => $conn->query("SELECT COUNT(*) as cnt FROM products WHERE product_image IS NOT NULL AND product_image != ''")->fetch_assoc()['cnt'],
    'Products without Images' => $conn->query("SELECT COUNT(*) as cnt FROM products WHERE product_image IS NULL OR product_image = ''")->fetch_assoc()['cnt'],
    'Total Categories' => $conn->query("SELECT COUNT(*) as cnt FROM categories")->fetch_assoc()['cnt'],
    'Total Materials' => $conn->query("SELECT COUNT(*) as cnt FROM materials")->fetch_assoc()['cnt']
];

echo "<table>";
foreach ($stats as $label => $value) {
    echo "<tr><th>$label</th><td><strong>$value</strong></td></tr>";
}
echo "</table>";

// Check uploads directory
echo "<h3>📁 Uploads Directory Check</h3>";
$uploadDirs = ['uploads/', '../uploads/'];
$foundDir = false;

foreach ($uploadDirs as $dir) {
    if (is_dir($dir)) {
        $foundDir = true;
        $realPath = realpath($dir);
        echo "<p class='success'>✅ Directory found: <strong>$realPath</strong></p>";
        
        $files = glob($dir . "*.{jpg,jpeg,png,gif,JPG,JPEG,PNG,GIF}", GLOB_BRACE);
        echo "<p>Found <strong>" . count($files) . "</strong> image files</p>";
        
        if (count($files) > 0) {
            echo "<div style='display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; margin: 20px 0;'>";
            foreach (array_slice($files, 0, 20) as $file) {
                echo "<div style='text-align: center; padding: 10px; background: #f9f9f9; border-radius: 5px;'>";
                echo "<img src='" . htmlspecialchars($file) . "' style='width: 100%; height: 80px; object-fit: cover;' alt='Image'>";
                echo "<small style='display: block; margin-top: 5px; word-break: break-all;'>" . basename($file) . "</small>";
                echo "</div>";
            }
            echo "</div>";
        }
        break;
    }
}

if (!$foundDir) {
    echo "<p class='error'>❌ Uploads directory not found!</p>";
    echo "<p>Please create: <code>uploads/</code> directory</p>";
}

// SQL Fix Queries
echo "<hr>";
echo "<h3>🔧 SQL Fix Queries</h3>";
echo "<p>Run these queries in phpMyAdmin if needed:</p>";

echo "<pre style='background: #2d2d2d; color: #f8f8f2; padding: 20px; border-radius: 5px;'>";
echo "-- 1. Fix image paths that don't start with 'uploads/'\n";
echo "UPDATE products \n";
echo "SET product_image = CONCAT('uploads/', SUBSTRING_INDEX(product_image, '/', -1))\n";
echo "WHERE product_image NOT LIKE 'uploads/%' \n";
echo "AND product_image IS NOT NULL \n";
echo "AND product_image != '';\n\n";

echo "-- 2. Set placeholder for products without images\n";
echo "UPDATE products \n";
echo "SET product_image = 'uploads/placeholder.jpg'\n";
echo "WHERE product_image IS NULL OR product_image = '';\n\n";

echo "-- 3. Check results\n";
echo "SELECT id, product_name, product_image, status \n";
echo "FROM products \n";
echo "LIMIT 10;";
echo "</pre>";

echo "<hr>";
echo "<h3>⚡ Quick Actions</h3>";
echo "<a href='create_placeholder.php' class='btn'>Create Placeholder Image</a>";
echo "<a href='products.php' class='btn' style='background: #28a745;'>Test Products Page</a>";
echo "<a href='ajax_filter_products.php' class='btn' style='background: #ffc107; color: #333;' onclick='testAjax(); return false;'>Test AJAX</a>";

echo "</div>";

$conn->close();
?>

<script>
function testAjax() {
    fetch('ajax_filter_products.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'category=&material=&requirement='
    })
    .then(response => response.json())
    .then(data => {
        console.log('AJAX Response:', data);
        alert('AJAX Test: ' + (data.success ? 'SUCCESS ✅' : 'FAILED ❌') + '\nProducts found: ' + data.count);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('AJAX Test Failed: ' + error);
    });
}
</script>