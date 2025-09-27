<?php
session_start();

// Clear all session variables
$_SESSION = [];

// Destroy session
session_destroy();

// Redirect to root index.php
header("Location: index.php");
exit;
?>


