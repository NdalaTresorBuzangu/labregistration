<?php
// settings/db_connection.php

// Database connection settings
$servername = "localhost";
$username   = "tresor.ndala";     // adjust if not root
$password   = "Ndala1950@@";         // your MySQL password
$dbname     = "ecommerce_2025A_tresor_ndala";   // matches your schema

// Create connection
$con = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
?>

