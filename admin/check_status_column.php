<?php
// check_status_column.php - Run this file once to check database structure

$host = 'localhost';
$username = 'blusolv_db';
$password = 'blusolv_db';
$database = 'blusolv_db';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Checking 'status' column in 'products' table</h2>";

// Check column structure
$result = $conn->query("DESCRIBE products");

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    foreach ($row as $value) {
        echo "<td>" . htmlspecialchars($value) . "</td>";
    }
    echo "</tr>";
    
    // Highlight status column
    if ($row['Field'] === 'status') {
        echo "<tr style='background: yellow;'><td colspan='6'>";
        echo "<strong>STATUS COLUMN FOUND!</strong><br>";
        echo "Type: <strong>" . $row['Type'] . "</strong><br>";
        echo "Default: <strong>" . ($row['Default'] ?? 'NULL') . "</strong>";
        echo "</td></tr>";
    }
}

echo "</table>";

// Check actual status values in database
echo "<h3>Current Status Values in Database:</h3>";
$status_check = $conn->query("SELECT id, product_name, status FROM products ORDER BY id DESC LIMIT 10");

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Product Name</th><th>Status (Current Value)</th></tr>";

while ($row = $status_check->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
    echo "<td><strong>" . htmlspecialchars($row['status']) . "</strong></td>";
    echo "</tr>";
}

echo "</table>";

// Try a direct update test
echo "<h3>Testing Direct Update:</h3>";
$test_id = 1; // Change this to your product ID
$test_result = $conn->query("UPDATE products SET status = 'inactive' WHERE id = $test_id");

if ($test_result) {
    echo "✅ Direct UPDATE query executed successfully<br>";
    
    // Check if it actually changed
    $verify = $conn->query("SELECT status FROM products WHERE id = $test_id");
    $verify_row = $verify->fetch_assoc();
    echo "Status after direct update: <strong>" . $verify_row['status'] . "</strong><br>";
} else {
    echo "❌ Direct UPDATE failed: " . $conn->error . "<br>";
}

// Check for triggers or constraints
echo "<h3>Checking for Triggers:</h3>";
$triggers = $conn->query("SHOW TRIGGERS WHERE `Table` = 'products'");
if ($triggers->num_rows > 0) {
    while ($trigger = $triggers->fetch_assoc()) {
        echo "⚠️ TRIGGER FOUND: " . $trigger['Trigger'] . " - " . $trigger['Event'] . "<br>";
    }
} else {
    echo "✅ No triggers found on products table<br>";
}

$conn->close();
?>