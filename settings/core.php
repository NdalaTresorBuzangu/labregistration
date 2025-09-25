<?php
session_start();

//for header redirection
ob_start();

//check for login
if (!isset($_SESSION['id'])) {
    header("Location: ../Login/login_register.php");
    exit;
}

//function to check if user is logged in
function isloggedin() {
    if (!isset($_SESSION['id'])) {
        return false;
    } else {
        return true;
    }
}

//function to check if user is admin
function isAdmin() {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        return true;
    } else {
        return false;
    }
}
?>
