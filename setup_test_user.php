<?php
/**
 * Database Setup & Test User Creation
 * Creates a test user so you can test login functionality
 * DELETE THIS FILE AFTER SETUP!
 */

require_once 'settings/db_cred.php';

echo "<h2>Database Setup & Test User Creation</h2>";
echo "<hr>";

// Test connection
$conn = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);

if (!$conn) {
    die("❌ <strong>Database connection failed:</strong> " . mysqli_connect_error());
}

echo "✅ <strong>Database connected successfully!</strong><br>";
echo "Host: " . SERVER . "<br>";
echo "Database: " . DATABASE . "<br>";
echo "<hr>";

// Check if customer table exists
$result = mysqli_query($conn, "SHOW TABLES LIKE 'customer'");
if (mysqli_num_rows($result) == 0) {
    echo "<div style='color: red;'>";
    echo "❌ <strong>ERROR:</strong> 'customer' table does not exist!<br>";
    echo "Please import the database first: db/dbforlab.sql<br>";
    echo "</div>";
    exit;
}

echo "✅ 'customer' table exists<br><hr>";

// Check existing users
$result = mysqli_query($conn, "SELECT customer_id, customer_email, customer_name, user_role FROM customer");
$user_count = mysqli_num_rows($result);

echo "<strong>Existing Users in Database:</strong> {$user_count}<br>";

if ($user_count > 0) {
    echo "<table border='1' cellpadding='5' style='margin-top: 10px;'>";
    echo "<tr><th>ID</th><th>Email</th><th>Name</th><th>Role</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        $role_name = ($row['user_role'] == 2) ? 'Owner' : 'Customer';
        echo "<tr>";
        echo "<td>{$row['customer_id']}</td>";
        echo "<td>{$row['customer_email']}</td>";
        echo "<td>{$row['customer_name']}</td>";
        echo "<td>{$role_name} ({$row['user_role']})</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<hr>";

// Create test users if needed
if ($user_count == 0) {
    echo "<strong>No users found. Creating test users...</strong><br><br>";
    
    // Test passwords
    $password_customer = 'customer123';
    $password_owner = 'owner123';
    
    $hashed_customer = password_hash($password_customer, PASSWORD_DEFAULT);
    $hashed_owner = password_hash($password_owner, PASSWORD_DEFAULT);
    
    // Create test customer (role = 1)
    $sql1 = "INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) 
             VALUES ('Test Customer', 'customer@test.com', ?, 'Ghana', 'Accra', '+233123456789', 1)";
    
    $stmt1 = mysqli_prepare($conn, $sql1);
    mysqli_stmt_bind_param($stmt1, "s", $hashed_customer);
    
    if (mysqli_stmt_execute($stmt1)) {
        echo "✅ <span style='color: green;'>Test Customer created successfully!</span><br>";
        echo "   Email: <strong>customer@test.com</strong><br>";
        echo "   Password: <strong>customer123</strong><br>";
        echo "   Role: Customer (1)<br><br>";
    } else {
        echo "❌ Failed to create test customer: " . mysqli_error($conn) . "<br><br>";
    }
    
    // Create test owner (role = 2)
    $sql2 = "INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) 
             VALUES ('Test Owner', 'owner@test.com', ?, 'Ghana', 'Accra', '+233987654321', 2)";
    
    $stmt2 = mysqli_prepare($conn, $sql2);
    mysqli_stmt_bind_param($stmt2, "s", $hashed_owner);
    
    if (mysqli_stmt_execute($stmt2)) {
        echo "✅ <span style='color: green;'>Test Owner created successfully!</span><br>";
        echo "   Email: <strong>owner@test.com</strong><br>";
        echo "   Password: <strong>owner123</strong><br>";
        echo "   Role: Owner (2)<br><br>";
    } else {
        echo "❌ Failed to create test owner: " . mysqli_error($conn) . "<br><br>";
    }
    
    echo "<hr>";
}

echo "<h3>Test Login Credentials</h3>";
echo "<div style='background: #f0f0f0; padding: 15px; border-left: 4px solid #4CAF50;'>";
echo "<strong>Customer Account:</strong><br>";
echo "Email: <code>customer@test.com</code><br>";
echo "Password: <code>customer123</code><br><br>";

echo "<strong>Owner/Admin Account:</strong><br>";
echo "Email: <code>owner@test.com</code><br>";
echo "Password: <code>owner123</code><br>";
echo "</div>";

echo "<hr>";

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Go to: <a href='login/login.php'>login/login.php</a></li>";
echo "<li>Login with one of the test accounts above</li>";
echo "<li>If login works, <strong style='color: red;'>DELETE THIS FILE</strong> for security!</li>";
echo "</ol>";

echo "<hr>";
echo "<p style='color: red; font-weight: bold;'>⚠️ SECURITY WARNING: Delete this file after setup!</p>";

mysqli_close($conn);
?>

