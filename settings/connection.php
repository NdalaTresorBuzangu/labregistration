<?php

// Check if db_cred.php exists before requiring it
$dbCredPath = __DIR__ . '/db_cred.php';
if (!file_exists($dbCredPath)) {
    error_log('db_cred.php not found at: ' . $dbCredPath);
    throw new Exception('Database credentials file (db_cred.php) not found in settings folder');
}

require_once $dbCredPath;

if (!isset($GLOBALS['con']) || !($GLOBALS['con'] instanceof mysqli)) {
    // Check if constants are defined
    if (!defined('SERVER') || !defined('USERNAME') || !defined('PASSWD') || !defined('DATABASE')) {
        error_log('Database credentials not defined. Check settings/db_cred.php');
        http_response_code(500);
        die('Database configuration error. Please check server configuration.');
    }
    
    $mysqli = @mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);

    if (!$mysqli) {
        $errorMessage = sprintf(
            'Database connection failed (%s): %s',
            mysqli_connect_errno(),
            mysqli_connect_error()
        );
        error_log($errorMessage);
        // Don't die in production - return error instead
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strpos($_SERVER['REQUEST_URI'], 'error_check.php') !== false) {
            die('Database connection failed: ' . mysqli_connect_error());
        }
        http_response_code(500);
        die('Database connection failed. Please contact the administrator.');
    }

    mysqli_set_charset($mysqli, 'utf8mb4');
    $GLOBALS['con'] = $mysqli;
}

$con = $GLOBALS['con'];

?>


