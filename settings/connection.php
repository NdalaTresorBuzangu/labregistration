<?php

require_once __DIR__ . '/db_cred.php';

if (!isset($GLOBALS['con']) || !($GLOBALS['con'] instanceof mysqli)) {
    $mysqli = @mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);

    if (!$mysqli) {
        $errorMessage = sprintf(
            'Database connection failed (%s): %s',
            mysqli_connect_errno(),
            mysqli_connect_error()
        );
        error_log($errorMessage);
        die('Database connection failed. Please contact the administrator.');
    }

    mysqli_set_charset($mysqli, 'utf8mb4');
    $GLOBALS['con'] = $mysqli;
}

$con = $GLOBALS['con'];

?>


