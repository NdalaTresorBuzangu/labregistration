<?php
// Settings/db_cred.php
// Database credentials for Tresor Ndala's E-Commerce hosting environment (Hostinger)

if (!defined('SERVER')) {
    define('SERVER', 'localhost');
}

if (!defined('USERNAME')) {
    // Hostinger MySQL username (full format with account prefix)
    define('USERNAME', 'u628771162_tresorndala');
}

if (!defined('PASSWD')) {
    // Hostinger MySQL password
    define('PASSWD', 'Ndala1950@@');
}

if (!defined('DATABASE')) {
    // Hostinger database name
    define('DATABASE', 'u628771162_dbase');
}

// Note: Connection is handled by connection.php, not here
// This file only defines constants
?>