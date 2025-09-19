<?php
// settings/db_connection.php

// Database connection settings
$servername = "localhost";
$username   = "root";     // adjust if not root
$password   = "";         // your MySQL password
$dbname     = "shoppn";   // matches your schema

// Create connection
$con = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
?>

