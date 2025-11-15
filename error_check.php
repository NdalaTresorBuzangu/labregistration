<?php
// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h2>Error Check</h2>";

// Test 1: Check if login.php can be loaded
echo "<h3>1. Testing login.php includes:</h3>";
try {
    $loginPath = __DIR__ . '/login.php';
    if (file_exists($loginPath)) {
        echo "✅ login.php exists<br>";
        
        // Test connection.php
        $connPath = __DIR__ . '/settings/connection.php';
        if (file_exists($connPath)) {
            echo "✅ settings/connection.php exists<br>";
            require_once $connPath;
            echo "✅ settings/connection.php loaded successfully<br>";
        } else {
            echo "❌ settings/connection.php NOT FOUND<br>";
        }
        
        // Test user_controller.php
        $userCtrlPath = __DIR__ . '/controllers/user_controller.php';
        if (file_exists($userCtrlPath)) {
            echo "✅ controllers/user_controller.php exists<br>";
            require_once $userCtrlPath;
            echo "✅ controllers/user_controller.php loaded successfully<br>";
        } else {
            echo "❌ controllers/user_controller.php NOT FOUND<br>";
        }
    } else {
        echo "❌ login.php NOT FOUND<br>";
    }
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "<br>";
    echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
} catch (Error $e) {
    echo "❌ FATAL ERROR: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
    echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
}

// Test 2: Check database connection
echo "<h3>2. Testing database connection:</h3>";
try {
    require_once __DIR__ . '/settings/db_cred.php';
    if (defined('SERVER') && defined('USERNAME') && defined('DATABASE')) {
        echo "✅ Database constants defined<br>";
        echo "SERVER: " . SERVER . "<br>";
        echo "USERNAME: " . USERNAME . "<br>";
        echo "DATABASE: " . DATABASE . "<br>";
        
        $testConn = @mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);
        if ($testConn) {
            echo "✅ Database connection successful!<br>";
            mysqli_close($testConn);
        } else {
            echo "❌ Database connection FAILED: " . mysqli_connect_error() . "<br>";
        }
    } else {
        echo "❌ Database constants NOT defined<br>";
    }
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "<br>";
}

// Test 3: Check file paths
echo "<h3>3. Checking file paths:</h3>";
$filesToCheck = [
    'login.php',
    'register.php',
    'settings/connection.php',
    'settings/db_cred.php',
    'settings/db_class.php',
    'controllers/user_controller.php',
    'classes/user_class.php',
    'actions/register_user_action.php',
    'js/register.js',
    'CSS/app.css'
];

foreach ($filesToCheck as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file NOT FOUND<br>";
    }
}

// Test 4: PHP version and settings
echo "<h3>4. PHP Information:</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current directory: " . __DIR__ . "<br>";
echo "Script path: " . __FILE__ . "<br>";

?>

