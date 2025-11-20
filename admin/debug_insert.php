<?php
$conn = new mysqli('localhost', 'blusolv_db', 'blusolv_db', 'blusolv_db');

$product_image = 'uploads/products/test.jpg';
$gallery_images = 'uploads/products/test.jpg,uploads/products/test2.jpg';

echo "Product Image: [" . $product_image . "]<br>";
echo "Gallery Images: [" . $gallery_images . "]<br>";
echo "Product Image Length: " . strlen($product_image) . "<br>";

$sql = "INSERT INTO products (product_name, category_id, product_image, gallery_images, created_at) 
        VALUES ('Test Product', 31, '" . $conn->real_escape_string($product_image) . "', 
        '" . $conn->real_escape_string($gallery_images) . "', NOW())";

echo "<br>SQL: " . $sql . "<br><br>";

if ($conn->query($sql)) {
    echo "SUCCESS! ID: " . $conn->insert_id . "<br>";
    
    $result = $conn->query("SELECT product_image, gallery_images FROM products WHERE id = " . $conn->insert_id);
    $row = $result->fetch_assoc();
    
    echo "Saved product_image: [" . $row['product_image'] . "]<br>";
    echo "Saved gallery_images: [" . $row['gallery_images'] . "]<br>";
} else {
    echo "ERROR: " . $conn->error;
}
?>