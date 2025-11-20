<?php
// test_status_update.php - ENUM specific test

$host = 'localhost';
$username = 'blusolv_db';
$password = 'blusolv_db';
$database = 'blusolv_db';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 1;
$new_status = isset($_GET['status']) ? strtolower(trim($_GET['status'])) : 'inactive';

// Validate against ENUM values
$valid = ['active', 'inactive', 'draft'];
if (!in_array($new_status, $valid)) {
    die("Invalid status. Must be: active, inactive, or draft");
}

echo "<h2>Testing ENUM Status Update for Product ID: $product_id</h2>";
echo "<p style='background: #ffffcc; padding: 10px; border-left: 4px solid #ffcc00;'>";
echo "⚠️ ENUM Column: Values MUST be lowercase and exact: 'active', 'inactive', 'draft'";
echo "</p>";

// Get current status
$current = $conn->query("SELECT id, product_name, status FROM products WHERE id = $product_id");
$product = $current->fetch_assoc();

if (!$product) {
    die("Product not found!");
}

echo "<h3>Before Update:</h3>";
echo "Product: " . htmlspecialchars($product['product_name']) . "<br>";
echo "Current Status (from DB): <strong style='color: blue;'>" . htmlspecialchars($product['status']) . "</strong><br>";
echo "New Status (to set): <strong style='color: green;'>" . htmlspecialchars($new_status) . "</strong><br><br>";

// Method 1: Direct Query with ENUM value
echo "<h3>Method 1: Direct Query (Recommended for ENUM)</h3>";
$query1 = "UPDATE products SET status = '$new_status' WHERE id = $product_id";
echo "Query: <code>$query1</code><br>";
$result1 = $conn->query($query1);
echo "Result: " . ($result1 ? '✅ Success' : '❌ Failed: ' . $conn->error) . "<br>";
echo "Affected Rows: <strong>" . $conn->affected_rows . "</strong> ";
if ($conn->affected_rows == 0) {
    echo "(No change - value might already be '$new_status')";
}
echo "<br><br>";

// Verify immediately
$verify1 = $conn->query("SELECT status FROM products WHERE id = $product_id");
$status_after_1 = $verify1->fetch_assoc()['status'];
echo "Status in DB after Method 1: <strong style='color: " . ($status_after_1 === $new_status ? 'green' : 'red') . "'>" . $status_after_1 . "</strong><br>";
echo "Match: " . ($status_after_1 === $new_status ? '✅ YES' : '❌ NO') . "<br><br>";

// Method 2: Prepared Statement (Like in edit form)
echo "<h3>Method 2: Prepared Statement</h3>";
$stmt = $conn->prepare("UPDATE products SET status = ? WHERE id = ?");
$stmt->bind_param("si", $new_status, $product_id);
echo "Binding: status = '$new_status' (lowercase), id = $product_id<br>";
$result2 = $stmt->execute();
echo "Result: " . ($result2 ? '✅ Success' : '❌ Failed: ' . $stmt->error) . "<br>";
echo "Affected Rows: <strong>" . $stmt->affected_rows . "</strong><br><br>";

// Verify
$verify2 = $conn->query("SELECT status FROM products WHERE id = $product_id");
$status_after_2 = $verify2->fetch_assoc()['status'];
echo "Status in DB after Method 2: <strong style='color: " . ($status_after_2 === $new_status ? 'green' : 'red') . "'>" . $status_after_2 . "</strong><br>";
echo "Match: " . ($status_after_2 === $new_status ? '✅ YES' : '❌ NO') . "<br><br>";

// Test with WRONG case (should fail for strict ENUM)
echo "<h3>Method 3: Testing Case Sensitivity</h3>";
$wrong_case = strtoupper($new_status); // Try UPPERCASE
echo "Trying to set: <strong>'$wrong_case'</strong> (uppercase)<br>";
$result3 = $conn->query("UPDATE products SET status = '$wrong_case' WHERE id = $product_id");
echo "Result: " . ($result3 ? '✅ Query executed' : '❌ Failed: ' . $conn->error) . "<br>";

$verify3 = $conn->query("SELECT status FROM products WHERE id = $product_id");
$status_after_3 = $verify3->fetch_assoc()['status'];
echo "Status in DB after uppercase attempt: <strong>" . $status_after_3 . "</strong><br>";
echo "Note: ENUM auto-converts case in some MySQL versions<br><br>";

// Final status
$final = $conn->query("SELECT status FROM products WHERE id = $product_id");
$final_status = $final->fetch_assoc()['status'];

echo "<hr>";
echo "<h3>Final Result:</h3>";
echo "<div style='background: " . ($final_status === $new_status ? '#d4edda' : '#f8d7da') . "; padding: 20px; border-radius: 5px;'>";
echo "<h2>Final Status in Database: <strong>" . htmlspecialchars($final_status) . "</strong></h2>";

if ($final_status === $new_status) {
    echo "<p style='color: green; font-weight: bold; font-size: 18px;'>✅ STATUS UPDATE WORKING PERFECTLY!</p>";
    echo "<p>ENUM column is accepting the value correctly.</p>";
} else {
    echo "<p style='color: red; font-weight: bold; font-size: 18px;'>❌ STATUS UPDATE FAILED</p>";
    echo "<p>Expected: '$new_status' but got: '$final_status'</p>";
    echo "<p>Check: Database permissions, triggers, or collation settings</p>";
}
echo "</div>";

echo "<hr>";
echo "<h3>Quick Test Actions:</h3>";
echo "<a href='?id=$product_id&status=active' style='padding: 10px 20px; background: #28a745; color: white; text-decoration: none; margin: 5px; display: inline-block;'>Set to ACTIVE</a> ";
echo "<a href='?id=$product_id&status=inactive' style='padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; margin: 5px; display: inline-block;'>Set to INACTIVE</a> ";
echo "<a href='?id=$product_id&status=draft' style='padding: 10px 20px; background: #ffc107; color: black; text-decoration: none; margin: 5px; display: inline-block;'>Set to DRAFT</a>";

echo "<hr>";
echo "<h3>All Products Status:</h3>";
$all = $conn->query("SELECT id, product_name, status FROM products ORDER BY id DESC LIMIT 10");
echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #333; color: white;'><th>ID</th><th>Product Name</th><th>Status</th><th>Actions</th></tr>";
while ($row = $all->fetch_assoc()) {
    $bg = $row['status'] === 'active' ? '#d4edda' : ($row['status'] === 'inactive' ? '#f8d7da' : '#fff3cd');
    echo "<tr style='background: $bg;'>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
    echo "<td><strong>" . htmlspecialchars($row['status']) . "</strong></td>";
    echo "<td>";
    echo "<a href='?id=" . $row['id'] . "&status=active'>Active</a> | ";
    echo "<a href='?id=" . $row['id'] . "&status=inactive'>Inactive</a> | ";
    echo "<a href='?id=" . $row['id'] . "&status=draft'>Draft</a>";
    echo "</td>";
    echo "</tr>";
}
echo "</table>";

$conn->close();
?>