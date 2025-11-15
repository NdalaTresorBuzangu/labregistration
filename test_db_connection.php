<?php
// Test database connection with different credential combinations
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connection Test</h2>";

// Test 1: Current credentials
echo "<h3>Test 1: Current Credentials</h3>";
$server = 'localhost';
$username = 'tresorndala';
$password = 'Ndala1950@@';
$database = 'u628771162_dbase';

echo "Attempting connection with:<br>";
echo "Server: $server<br>";
echo "Username: $username<br>";
echo "Database: $database<br>";
echo "Password: " . str_repeat('*', strlen($password)) . "<br><br>";

$conn = @mysqli_connect($server, $username, $password, $database);

if ($conn) {
    echo "✅ <strong>SUCCESS!</strong> Connection established.<br>";
    echo "MySQL Version: " . mysqli_get_server_info($conn) . "<br>";
    mysqli_close($conn);
} else {
    echo "❌ <strong>FAILED:</strong> " . mysqli_connect_error() . "<br>";
    echo "Error Number: " . mysqli_connect_errno() . "<br><br>";
    
    // Test 2: Try without database (to check if user exists)
    echo "<h3>Test 2: Testing User Authentication (without database)</h3>";
    $conn2 = @mysqli_connect($server, $username, $password);
    if ($conn2) {
        echo "✅ User authentication works! User exists.<br>";
        echo "❌ But database '$database' might not exist or user doesn't have access to it.<br>";
        
        // List available databases
        echo "<h4>Available databases for this user:</h4>";
        $result = mysqli_query($conn2, "SHOW DATABASES");
        if ($result) {
            echo "<ul>";
            while ($row = mysqli_fetch_array($result)) {
                $dbName = $row[0];
                // Skip system databases
                if (!in_array($dbName, ['information_schema', 'performance_schema', 'mysql', 'sys'])) {
                    echo "<li>$dbName</li>";
                }
            }
            echo "</ul>";
        }
        mysqli_close($conn2);
    } else {
        echo "❌ User authentication failed: " . mysqli_connect_error() . "<br>";
        echo "<p><strong>Possible issues:</strong></p>";
        echo "<ul>";
        echo "<li>Username is incorrect</li>";
        echo "<li>Password is incorrect</li>";
        echo "<li>User doesn't exist</li>";
        echo "</ul>";
    }
}

echo "<hr>";
echo "<h3>How to Fix:</h3>";
echo "<ol>";
echo "<li>Log into Hostinger hPanel</li>";
echo "<li>Go to <strong>Databases</strong> → <strong>MySQL Databases</strong></li>";
echo "<li>Check your database name (should start with 'u628771162_')</li>";
echo "<li>Check your MySQL username (might be 'u628771162_tresorndala' or similar)</li>";
echo "<li>Check your MySQL password</li>";
echo "<li>Make sure the user has permissions to access the database</li>";
echo "</ol>";

echo "<h3>Common Hostinger Database Naming:</h3>";
echo "<ul>";
echo "<li>Database name: Usually starts with your account prefix (e.g., 'u628771162_dbase')</li>";
echo "<li>Username: Usually 'u628771162_tresorndala' or just 'tresorndala'</li>";
echo "<li>Both are case-sensitive!</li>";
echo "</ul>";

?>

