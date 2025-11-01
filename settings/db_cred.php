<?php
//Database credentials
// Settings/db_cred.php

// define('DB_HOST', 'localhost');
// define('DB_USER', 'root');
// define('DB_PASS', '');
// define('DB_NAME', 'dbforlab');


if (!defined('SERVER')) {
    define('SERVER', getenv('DB_HOST') ?: 'localhost');
}

if (!defined('USERNAME')) {
    define('USERNAME', getenv('DB_USER') ?: 'root');
}

if (!defined('PASSWD')) {
    define('PASSWD', getenv('DB_PASS') ?: '');
}

if (!defined('DATABASE')) {
    define('DATABASE', getenv('DB_NAME') ?: 'shoppn');
}
?>