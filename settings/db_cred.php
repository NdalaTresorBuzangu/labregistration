<?php
// Settings/db_cred.php
// Database credentials for Tresor Ndala’s E-Commerce hosting environment

if (!defined('SERVER')) {
    // Remote hosting server (not localhost)
    define('SERVER', 'localhost');
}

if (!defined('USERNAME')) {
    // Your MySQL username
    define('USERNAME', 'tresor.ndala');
}

if (!defined('PASSWD')) {
    // 🔐 Replace with your NEW MySQL password after first login
    define('PASSWD', 'Ndala1950@@');
}

if (!defined('DATABASE')) {
    // Your database name
    define('DATABASE', 'ecommerce_2025A_tresor_ndala');
}

// Create connection
$conn = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);

// Check connection
if (!$conn) {
    die("❌ Database connection failed: " . mysqli_connect_error());
}

// Optional: echo for testing
// echo "✅ Database connected successfully!";
?>
