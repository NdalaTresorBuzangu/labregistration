<?php
/**
 * Test Registration Script
 * This will help debug registration issues
 * DELETE THIS FILE AFTER FIXING THE ISSUE!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing Registration System</h2>";
echo "<hr>";

// Test 1: Check database connection
echo "<h3>Test 1: Database Connection</h3>";
require_once 'settings/db_cred.php';

$conn = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);

if ($conn) {
    echo "✅ <span style='color: green;'>Database connected successfully!</span><br>";
    echo "Host: " . SERVER . "<br>";
    echo "Database: " . DATABASE . "<br>";
} else {
    echo "❌ <span style='color: red;'>Database connection failed: " . mysqli_connect_error() . "</span><br>";
    exit;
}

echo "<hr>";

// Test 2: Check if customer table exists
echo "<h3>Test 2: Check Customer Table</h3>";
$result = mysqli_query($conn, "SHOW TABLES LIKE 'customer'");
if (mysqli_num_rows($result) > 0) {
    echo "✅ <span style='color: green;'>'customer' table exists</span><br>";
    
    // Show table structure
    $structure = mysqli_query($conn, "DESCRIBE customer");
    echo "<br><strong>Table Structure:</strong><br>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = mysqli_fetch_assoc($structure)) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "❌ <span style='color: red;'>'customer' table does NOT exist!</span><br>";
    echo "Please import db/dbforlab.sql first<br>";
    exit;
}

echo "<hr>";

// Test 3: Try to create a test user
echo "<h3>Test 3: Create Test User</h3>";

require_once 'classes/user_class.php';

$test_email = 'test_' . time() . '@example.com';
$test_name = 'Test User';
$test_password = 'Test123';
$test_phone = '+233123456789';
$test_role = 1;

echo "Attempting to create user:<br>";
echo "Email: <strong>{$test_email}</strong><br>";
echo "Name: <strong>{$test_name}</strong><br>";
echo "Phone: <strong>{$test_phone}</strong><br>";
echo "Role: <strong>{$test_role}</strong><br><br>";

try {
    $user = new User();
    echo "✅ User class instantiated successfully<br>";
    
    $user_id = $user->createUser($test_name, $test_email, $test_password, $test_phone, $test_role);
    
    if ($user_id) {
        echo "✅ <span style='color: green; font-weight: bold;'>SUCCESS! User created with ID: {$user_id}</span><br><br>";
        
        // Verify user was created
        $verify = mysqli_query($conn, "SELECT customer_id, customer_name, customer_email, user_role FROM customer WHERE customer_id = {$user_id}");
        if ($verify && $row = mysqli_fetch_assoc($verify)) {
            echo "<strong>Verified in database:</strong><br>";
            echo "ID: {$row['customer_id']}<br>";
            echo "Name: {$row['customer_name']}<br>";
            echo "Email: {$row['customer_email']}<br>";
            echo "Role: {$row['user_role']}<br>";
        }
    } else {
        echo "❌ <span style='color: red; font-weight: bold;'>FAILED to create user!</span><br>";
        echo "Error: Check error logs or database permissions<br>";
    }
    
} catch (Exception $e) {
    echo "❌ <span style='color: red; font-weight: bold;'>EXCEPTION: " . $e->getMessage() . "</span><br>";
    echo "Stack trace:<br><pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";

// Test 4: Count existing users
echo "<h3>Test 4: Current Users in Database</h3>";
$count_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM customer");
$count_row = mysqli_fetch_assoc($count_result);
echo "Total users in database: <strong>{$count_row['count']}</strong><br>";

mysqli_close($conn);

echo "<hr>";
echo "<h3>Summary</h3>";
echo "<p>If all tests pass above, your registration should work!</p>";
echo "<p>If there's an error, it will be shown above in red.</p>";
echo "<hr>";
echo "<p style='color: red; font-weight: bold;'>⚠️ DELETE THIS FILE AFTER TESTING!</p>";

?>

<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    h2 { color: #333; }
    h3 { color: #666; background: #f0f0f0; padding: 10px; }
    hr { margin: 20px 0; }
    table { border-collapse: collapse; margin-top: 10px; }
    th { background: #4CAF50; color: white; padding: 8px; }
    td { padding: 5px; }
</style>

